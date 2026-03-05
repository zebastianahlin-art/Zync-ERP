<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ReportRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    private function period(array $filters): array
    {
        return [
            'from' => $filters['from'] ?? date('Y-m-01'),
            'to'   => $filters['to']   ?? date('Y-m-d'),
        ];
    }

    public function maintenanceReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $statusSummary = $this->pdo->prepare(
                "SELECT status, COUNT(*) AS cnt
                 FROM work_orders
                 WHERE is_deleted = 0 AND created_at BETWEEN ? AND ?
                 GROUP BY status ORDER BY cnt DESC"
            );
            $statusSummary->execute([$from, $to . ' 23:59:59']);
            $data = $statusSummary->fetchAll(\PDO::FETCH_ASSOC);

            $summary = $this->pdo->prepare(
                "SELECT
                    COUNT(*) AS total_orders,
                    SUM(CASE WHEN status NOT IN ('closed','cancelled') THEN 1 ELSE 0 END) AS open_orders,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) AS closed_orders,
                    COALESCE(SUM(actual_cost), 0) AS total_cost
                 FROM work_orders WHERE is_deleted = 0 AND created_at BETWEEN ? AND ?"
            );
            $summary->execute([$from, $to . ' 23:59:59']);
            $s = $summary->fetch(\PDO::FETCH_ASSOC) ?: [];

            return ['summary' => $s, 'data' => $data, 'period' => $period];
        } catch (\Throwable $e) {
            return ['summary' => ['total_orders'=>0,'open_orders'=>0,'closed_orders'=>0,'total_cost'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function inventoryReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $where = 'WHERE a.is_deleted = 0';
            $params = [];
            if (!empty($filters['location'])) {
                $where .= ' AND a.location LIKE ?';
                $params[] = '%' . $filters['location'] . '%';
            }

            $stmt = $this->pdo->prepare(
                "SELECT a.article_number, a.name, a.unit, a.stock_quantity,
                        a.min_stock_level, a.purchase_price,
                        (a.stock_quantity * COALESCE(a.purchase_price, 0)) AS value
                 FROM articles a $where ORDER BY a.name ASC"
            );
            $stmt->execute($params);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total      = count($data);
            $belowMin   = count(array_filter($data, fn($r) => $r['min_stock_level'] !== null && $r['stock_quantity'] < $r['min_stock_level']));
            $totalValue = array_sum(array_column($data, 'value'));

            return [
                'summary' => ['total_articles'=>$total,'below_minimum'=>$belowMin,'total_value'=>$totalValue,'locations'=>0],
                'data'    => $data,
                'period'  => $period,
            ];
        } catch (\Throwable $e) {
            return ['summary' => ['total_articles'=>0,'below_minimum'=>0,'total_value'=>0,'locations'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function purchasingReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $stmt = $this->pdo->prepare(
                "SELECT s.name AS supplier_name, COUNT(po.id) AS order_count,
                        COALESCE(SUM(po.total_amount), 0) AS total_amount,
                        MIN(po.created_at) AS first_order, MAX(po.created_at) AS last_order
                 FROM purchase_orders po
                 LEFT JOIN suppliers s ON po.supplier_id = s.id
                 WHERE po.is_deleted = 0 AND po.created_at BETWEEN ? AND ?
                 GROUP BY po.supplier_id, s.name ORDER BY total_amount DESC"
            );
            $stmt->execute([$from, $to . ' 23:59:59']);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sum = $this->pdo->prepare(
                "SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount),0) AS total_amount,
                        SUM(CASE WHEN status IN ('sent','confirmed','partially_received') THEN 1 ELSE 0 END) AS active_orders
                 FROM purchase_orders WHERE is_deleted = 0 AND created_at BETWEEN ? AND ?"
            );
            $sum->execute([$from, $to . ' 23:59:59']);
            $s = $sum->fetch(\PDO::FETCH_ASSOC) ?: [];

            $agreements = (int)$this->pdo->query("SELECT COUNT(*) FROM purchase_agreements WHERE status='active' AND is_deleted=0")->fetchColumn();
            $s['active_agreements'] = $agreements;

            return ['summary' => $s, 'data' => $data, 'period' => $period];
        } catch (\Throwable $e) {
            return ['summary' => ['total_orders'=>0,'total_amount'=>0,'active_orders'=>0,'active_agreements'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function financeReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $stmt = $this->pdo->prepare(
                "SELECT DATE_FORMAT(invoice_date, '%Y-%m') AS month,
                        COALESCE(SUM(total_amount), 0) AS revenue
                 FROM invoices_outgoing
                 WHERE is_deleted = 0 AND invoice_date BETWEEN ? AND ?
                 GROUP BY month ORDER BY month ASC"
            );
            $stmt->execute([$from, $to]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $unpaidOut = (int)$this->pdo->prepare("SELECT COUNT(*) FROM invoices_outgoing WHERE status != 'paid' AND is_deleted = 0")->execute([]) ?: 0;
            $unpaidIn  = (int)$this->pdo->prepare("SELECT COUNT(*) FROM invoices_incoming WHERE status != 'paid' AND is_deleted = 0")->execute([]) ?: 0;

            $s1 = $this->pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM invoices_outgoing WHERE is_deleted=0 AND invoice_date BETWEEN ? AND ?");
            $s1->execute([$from, $to]); $revenue = (float)$s1->fetchColumn();

            $s2 = $this->pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM invoices_incoming WHERE is_deleted=0 AND invoice_date BETWEEN ? AND ?");
            $s2->execute([$from, $to]); $costs = (float)$s2->fetchColumn();

            $unpaidStmt = $this->pdo->query("SELECT COUNT(*) FROM invoices_outgoing WHERE status != 'paid' AND is_deleted=0");
            $unpaidCount = (int)$unpaidStmt->fetchColumn();

            return [
                'summary' => ['revenue'=>$revenue, 'costs'=>$costs, 'result'=>$revenue-$costs, 'unpaid_invoices'=>$unpaidCount],
                'data'    => $data,
                'period'  => $period,
            ];
        } catch (\Throwable $e) {
            return ['summary' => ['revenue'=>0,'costs'=>0,'result'=>0,'unpaid_invoices'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function safetyReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $stmt = $this->pdo->prepare(
                "SELECT category, status, COUNT(*) AS cnt
                 FROM risk_reports WHERE is_deleted = 0 AND created_at BETWEEN ? AND ?
                 GROUP BY category, status ORDER BY category, status"
            );
            $stmt->execute([$from, $to . ' 23:59:59']);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sum = $this->pdo->prepare(
                "SELECT
                    SUM(CASE WHEN status='open' THEN 1 ELSE 0 END) AS open_risks,
                    SUM(CASE WHEN status='closed' THEN 1 ELSE 0 END) AS closed_risks,
                    COUNT(*) AS total
                 FROM risk_reports WHERE is_deleted=0 AND created_at BETWEEN ? AND ?"
            );
            $sum->execute([$from, $to . ' 23:59:59']);
            $s = $sum->fetch(\PDO::FETCH_ASSOC) ?: ['open_risks'=>0,'closed_risks'=>0,'total'=>0];

            $audits = 0;
            try {
                $audits = (int)$this->pdo->query("SELECT COUNT(*) FROM supplier_audits WHERE is_deleted=0")->fetchColumn();
            } catch (\Throwable $e2) {}

            $drills = 0;
            try {
                $drills = (int)$this->pdo->query("SELECT COUNT(*) FROM emergency_drills WHERE is_deleted=0")->fetchColumn();
            } catch (\Throwable $e2) {}

            $s['audits'] = $audits;
            $s['drills'] = $drills;

            return ['summary' => $s, 'data' => $data, 'period' => $period];
        } catch (\Throwable $e) {
            return ['summary' => ['open_risks'=>0,'closed_risks'=>0,'total'=>0,'audits'=>0,'drills'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function productionReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $stmt = $this->pdo->prepare(
                "SELECT status, COUNT(*) AS cnt
                 FROM production_orders WHERE is_deleted=0 AND created_at BETWEEN ? AND ?
                 GROUP BY status ORDER BY cnt DESC"
            );
            $stmt->execute([$from, $to . ' 23:59:59']);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sum = $this->pdo->prepare(
                "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status NOT IN ('completed','cancelled') THEN 1 ELSE 0 END) AS active_orders,
                    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed_orders
                 FROM production_orders WHERE is_deleted=0 AND created_at BETWEEN ? AND ?"
            );
            $sum->execute([$from, $to . ' 23:59:59']);
            $s = $sum->fetch(\PDO::FETCH_ASSOC) ?: [];

            $products = 0;
            try {
                $products = (int)$this->pdo->query("SELECT COUNT(*) FROM products WHERE is_deleted=0")->fetchColumn();
            } catch (\Throwable $e2) {}
            $s['products'] = $products;

            return ['summary' => $s, 'data' => $data, 'period' => $period];
        } catch (\Throwable $e) {
            return ['summary' => ['total'=>0,'active_orders'=>0,'completed_orders'=>0,'products'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function salesReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $data = [];
            $activeQuotes = 0;
            $activeOrders = 0;
            $customers = 0;

            try {
                $stmt = $this->pdo->prepare(
                    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month, COUNT(*) AS orders,
                            COALESCE(SUM(total_amount),0) AS amount
                     FROM sales_orders WHERE is_deleted=0 AND created_at BETWEEN ? AND ?
                     GROUP BY month ORDER BY month ASC"
                );
                $stmt->execute([$from, $to . ' 23:59:59']);
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $activeOrders = (int)$this->pdo->query("SELECT COUNT(*) FROM sales_orders WHERE status NOT IN ('delivered','cancelled') AND is_deleted=0")->fetchColumn();

                try {
                    $activeQuotes = (int)$this->pdo->query("SELECT COUNT(*) FROM sales_quotes WHERE status NOT IN ('accepted','rejected','expired') AND is_deleted=0")->fetchColumn();
                } catch (\Throwable $e3) {
                    $activeQuotes = (int)$this->pdo->query("SELECT COUNT(*) FROM quotes WHERE status NOT IN ('accepted','rejected','expired') AND is_deleted=0")->fetchColumn();
                }
            } catch (\Throwable $e2) {}

            try {
                $customers = (int)$this->pdo->query("SELECT COUNT(*) FROM customers WHERE is_deleted=0")->fetchColumn();
            } catch (\Throwable $e2) {}

            return [
                'summary' => ['active_quotes'=>$activeQuotes,'active_orders'=>$activeOrders,'customers'=>$customers,'revenue'=>array_sum(array_column($data,'amount'))],
                'data'    => $data,
                'period'  => $period,
            ];
        } catch (\Throwable $e) {
            return ['summary' => ['active_quotes'=>0,'active_orders'=>0,'customers'=>0,'revenue'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function hrReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $stmt = $this->pdo->prepare(
                "SELECT e.id, e.employee_number, e.first_name, e.last_name,
                        e.department_id, e.position, e.status
                 FROM employees e WHERE e.is_deleted=0 ORDER BY e.last_name, e.first_name"
            );
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total   = count($data);
            $active  = count(array_filter($data, fn($r) => $r['status'] === 'active'));

            $absentToday = 0;
            try {
                $a = $this->pdo->query("SELECT COUNT(*) FROM attendance_records WHERE DATE(date)=CURDATE() AND type IN ('sick','absence')");
                $absentToday = (int)$a->fetchColumn();
            } catch (\Throwable $e2) {}

            $pendingExpenses = 0;
            try {
                $pendingExpenses = (int)$this->pdo->query("SELECT COUNT(*) FROM expense_reports WHERE status='pending' AND is_deleted=0")->fetchColumn();
            } catch (\Throwable $e2) {}

            return [
                'summary' => ['total_employees'=>$total,'active_employees'=>$active,'absent_today'=>$absentToday,'pending_expenses'=>$pendingExpenses],
                'data'    => $data,
                'period'  => $period,
            ];
        } catch (\Throwable $e) {
            return ['summary' => ['total_employees'=>0,'active_employees'=>0,'absent_today'=>0,'pending_expenses'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function projectReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $where  = 'WHERE p.is_deleted = 0';
            $params = [];
            if (!empty($filters['status'])) {
                $where  .= ' AND p.status = ?';
                $params[] = $filters['status'];
            }

            $stmt = $this->pdo->prepare(
                "SELECT p.id, p.name, p.status, p.start_date, p.end_date,
                        p.budget, p.manager_id,
                        (SELECT COUNT(*) FROM project_tasks pt WHERE pt.project_id=p.id AND pt.is_deleted=0) AS task_count
                 FROM projects p $where ORDER BY p.created_at DESC"
            );
            $stmt->execute($params);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sum = $this->pdo->prepare(
                "SELECT
                    SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) AS active_projects,
                    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed_projects,
                    SUM(CASE WHEN status='on_hold' THEN 1 ELSE 0 END) AS on_hold_projects,
                    COALESCE(SUM(budget),0) AS total_budget
                 FROM projects WHERE is_deleted=0"
            );
            $sum->execute();
            $s = $sum->fetch(\PDO::FETCH_ASSOC) ?: [];

            return ['summary' => $s, 'data' => $data, 'period' => $period];
        } catch (\Throwable $e) {
            return ['summary' => ['active_projects'=>0,'completed_projects'=>0,'on_hold_projects'=>0,'total_budget'=>0], 'data' => [], 'period' => $period];
        }
    }

    public function csReport(array $filters = []): array
    {
        $period = $this->period($filters);
        try {
            $from = $period['from'];
            $to   = $period['to'];

            $table = 'support_tickets';
            try {
                $this->pdo->query("SELECT 1 FROM support_tickets LIMIT 1");
            } catch (\Throwable $e2) {
                $table = 'cs_tickets';
            }

            $stmt = $this->pdo->prepare(
                "SELECT status, COUNT(*) AS cnt
                 FROM $table WHERE is_deleted=0 AND created_at BETWEEN ? AND ?
                 GROUP BY status ORDER BY cnt DESC"
            );
            $stmt->execute([$from, $to . ' 23:59:59']);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sum = $this->pdo->prepare(
                "SELECT
                    SUM(CASE WHEN status='open' THEN 1 ELSE 0 END) AS open_tickets,
                    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress,
                    SUM(CASE WHEN status='resolved' THEN 1 ELSE 0 END) AS resolved,
                    COUNT(*) AS total
                 FROM $table WHERE is_deleted=0 AND created_at BETWEEN ? AND ?"
            );
            $sum->execute([$from, $to . ' 23:59:59']);
            $s = $sum->fetch(\PDO::FETCH_ASSOC) ?: ['open_tickets'=>0,'in_progress'=>0,'resolved'=>0,'total'=>0];
            $s['avg_response'] = '–';

            return ['summary' => $s, 'data' => $data, 'period' => $period];
        } catch (\Throwable $e) {
            return ['summary' => ['open_tickets'=>0,'in_progress'=>0,'resolved'=>0,'total'=>0,'avg_response'=>'–'], 'data' => [], 'period' => $period];
        }
    }
}
