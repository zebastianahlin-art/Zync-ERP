<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class DashboardRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function availableWidgets(int $roleLevel): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM dashboard_widgets
             WHERE is_active = 1 AND min_role_level <= ? AND is_deleted = 0
             ORDER BY sort_order ASC"
        );
        $stmt->execute([$roleLevel]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function userWidgets(int $userId, int $roleLevel): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dw.*, udw.id AS udw_id, udw.sort_order AS user_sort_order,
                    udw.width AS user_width, udw.is_visible, udw.widget_id
             FROM user_dashboard_widgets udw
             JOIN dashboard_widgets dw ON dw.id = udw.widget_id
             WHERE udw.user_id = ?
               AND udw.is_visible = 1
               AND dw.min_role_level <= ?
               AND dw.is_deleted = 0
             ORDER BY udw.sort_order ASC"
        );
        $stmt->execute([$userId, $roleLevel]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addWidget(int $userId, int $widgetId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT IGNORE INTO user_dashboard_widgets (user_id, widget_id, sort_order)
             VALUES (?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 10 FROM user_dashboard_widgets udw2 WHERE udw2.user_id = ?))"
        );
        $stmt->execute([$userId, $widgetId, $userId]);
    }

    public function removeWidget(int $userId, int $widgetId): void
    {
        $check = $this->pdo->prepare(
            "SELECT is_mandatory FROM dashboard_widgets WHERE id = ?"
        );
        $check->execute([$widgetId]);
        $widget = $check->fetch(\PDO::FETCH_ASSOC);

        if ($widget && (int) $widget['is_mandatory'] === 0) {
            $stmt = $this->pdo->prepare(
                "DELETE FROM user_dashboard_widgets WHERE user_id = ? AND widget_id = ?"
            );
            $stmt->execute([$userId, $widgetId]);
        }
    }

    public function reorderWidgets(int $userId, array $widgetIds): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE user_dashboard_widgets SET sort_order = ? WHERE user_id = ? AND widget_id = ?"
        );
        foreach ($widgetIds as $order => $widgetId) {
            $stmt->execute([$order * 10, $userId, $widgetId]);
        }
    }

    public function initUserWidgets(int $userId, int $roleLevel): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, sort_order FROM dashboard_widgets
             WHERE is_active = 1
               AND is_deleted = 0
               AND min_role_level <= ?
               AND (is_mandatory = 1 OR category IN ('kpi','shortcut','list'))
             ORDER BY sort_order ASC"
        );
        $stmt->execute([$roleLevel]);
        $widgets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $insert = $this->pdo->prepare(
            "INSERT IGNORE INTO user_dashboard_widgets (user_id, widget_id, sort_order)
             VALUES (?, ?, ?)"
        );
        foreach ($widgets as $w) {
            $insert->execute([$userId, $w['id'], $w['sort_order']]);
        }
    }

    // ── KPI Methods ──────────────────────────────────────────────────────────

    public function kpiMaintenance(): array
    {
        try {
            $open = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM work_orders
                 WHERE status NOT IN ('closed','cancelled') AND is_deleted = 0"
            )->fetchColumn();

            $closedMonth = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM work_orders
                 WHERE status = 'closed' AND is_deleted = 0
                   AND MONTH(updated_at) = MONTH(NOW()) AND YEAR(updated_at) = YEAR(NOW())"
            )->fetchColumn();

            $faultsToday = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM fault_reports
                 WHERE DATE(created_at) = CURDATE() AND is_deleted = 0"
            )->fetchColumn();

            return [
                'open_orders'    => $open,
                'closed_month'   => $closedMonth,
                'faults_today'   => $faultsToday,
            ];
        } catch (\Throwable $e) {
            return ['open_orders' => 0, 'closed_month' => 0, 'faults_today' => 0];
        }
    }

    public function kpiInventory(): array
    {
        try {
            $total = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM articles WHERE is_deleted = 0"
            )->fetchColumn();

            $belowMin = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM articles
                 WHERE is_deleted = 0 AND min_stock_level IS NOT NULL
                   AND stock_quantity < min_stock_level"
            )->fetchColumn();

            $totalValue = (float) $this->pdo->query(
                "SELECT COALESCE(SUM(stock_quantity * COALESCE(purchase_price, 0)), 0)
                 FROM articles WHERE is_deleted = 0"
            )->fetchColumn();

            return [
                'total_articles' => $total,
                'below_minimum'  => $belowMin,
                'total_value'    => $totalValue,
            ];
        } catch (\Throwable $e) {
            return ['total_articles' => 0, 'below_minimum' => 0, 'total_value' => 0.0];
        }
    }

    public function kpiPurchasing(): array
    {
        try {
            $activeOrders = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM purchase_orders
                 WHERE status IN ('sent','confirmed','partially_received') AND is_deleted = 0"
            )->fetchColumn();

            $pendingReqs = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM purchase_requisitions
                 WHERE status = 'pending_approval' AND is_deleted = 0"
            )->fetchColumn();

            $activeAgreements = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM purchase_agreements
                 WHERE status = 'active' AND is_deleted = 0"
            )->fetchColumn();

            return [
                'active_orders'     => $activeOrders,
                'pending_reqs'      => $pendingReqs,
                'active_agreements' => $activeAgreements,
            ];
        } catch (\Throwable $e) {
            return ['active_orders' => 0, 'pending_reqs' => 0, 'active_agreements' => 0];
        }
    }

    public function kpiFinance(): array
    {
        try {
            $unpaidOut = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM invoices_outgoing
                 WHERE status != 'paid' AND is_deleted = 0"
            )->fetchColumn();

            $unpaidIn = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM invoices_incoming
                 WHERE status != 'paid' AND is_deleted = 0"
            )->fetchColumn();

            $monthRevenue = (float) $this->pdo->query(
                "SELECT COALESCE(SUM(total_amount), 0) FROM invoices_outgoing
                 WHERE is_deleted = 0
                   AND MONTH(invoice_date) = MONTH(NOW()) AND YEAR(invoice_date) = YEAR(NOW())"
            )->fetchColumn();

            return [
                'unpaid_out'    => $unpaidOut,
                'unpaid_in'     => $unpaidIn,
                'month_revenue' => $monthRevenue,
            ];
        } catch (\Throwable $e) {
            return ['unpaid_out' => 0, 'unpaid_in' => 0, 'month_revenue' => 0.0];
        }
    }

    public function kpiSafety(): array
    {
        try {
            $openRisks = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM risk_reports WHERE status = 'open' AND is_deleted = 0"
            )->fetchColumn();

            $upcomingAudits = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM supplier_audits
                 WHERE scheduled_date > NOW() AND is_deleted = 0"
            )->fetchColumn();

            $overdueResources = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM emergency_resources
                 WHERE next_inspection_date < NOW() AND is_deleted = 0"
            )->fetchColumn();

            return [
                'open_risks'        => $openRisks,
                'upcoming_audits'   => $upcomingAudits,
                'overdue_resources' => $overdueResources,
            ];
        } catch (\Throwable $e) {
            return ['open_risks' => 0, 'upcoming_audits' => 0, 'overdue_resources' => 0];
        }
    }

    public function kpiProduction(): array
    {
        try {
            $activeOrders = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM production_orders
                 WHERE status NOT IN ('completed','cancelled') AND is_deleted = 0"
            )->fetchColumn();

            $productCount = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM products WHERE is_deleted = 0"
            )->fetchColumn();

            $locationCount = 0;
            try {
                $locationCount = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM storage_locations WHERE is_deleted = 0"
                )->fetchColumn();
            } catch (\Throwable $e2) {
                try {
                    $locationCount = (int) $this->pdo->query(
                        "SELECT COUNT(*) FROM warehouses WHERE is_deleted = 0"
                    )->fetchColumn();
                } catch (\Throwable $e3) {
                    $locationCount = 0;
                }
            }

            return [
                'active_orders'  => $activeOrders,
                'product_count'  => $productCount,
                'location_count' => $locationCount,
            ];
        } catch (\Throwable $e) {
            return ['active_orders' => 0, 'product_count' => 0, 'location_count' => 0];
        }
    }

    public function kpiSales(): array
    {
        try {
            $activeQuotes = 0;
            try {
                $activeQuotes = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM sales_quotes
                     WHERE status NOT IN ('accepted','rejected','expired') AND is_deleted = 0"
                )->fetchColumn();
            } catch (\Throwable $e2) {
                $activeQuotes = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM quotes
                     WHERE status NOT IN ('accepted','rejected','expired') AND is_deleted = 0"
                )->fetchColumn();
            }

            $activeOrders = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM sales_orders
                 WHERE status NOT IN ('delivered','cancelled') AND is_deleted = 0"
            )->fetchColumn();

            $priceLists = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM price_lists WHERE is_deleted = 0"
            )->fetchColumn();

            return [
                'active_quotes' => $activeQuotes,
                'active_orders' => $activeOrders,
                'price_lists'   => $priceLists,
            ];
        } catch (\Throwable $e) {
            return ['active_quotes' => 0, 'active_orders' => 0, 'price_lists' => 0];
        }
    }

    public function kpiHr(): array
    {
        try {
            $employees = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM employees WHERE is_deleted = 0 AND status = 'active'"
            )->fetchColumn();

            $absentToday = 0;
            try {
                $absentToday = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM attendance_records
                     WHERE DATE(date) = CURDATE() AND type IN ('sick','absence')"
                )->fetchColumn();
            } catch (\Throwable $e2) {
                $absentToday = 0;
            }

            $pendingExpenses = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM expense_reports WHERE status = 'pending' AND is_deleted = 0"
            )->fetchColumn();

            return [
                'employees'       => $employees,
                'absent_today'    => $absentToday,
                'pending_expenses'=> $pendingExpenses,
            ];
        } catch (\Throwable $e) {
            return ['employees' => 0, 'absent_today' => 0, 'pending_expenses' => 0];
        }
    }

    public function kpiProjects(): array
    {
        try {
            $activeProjects = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM projects WHERE status = 'active' AND is_deleted = 0"
            )->fetchColumn();

            $openTasks = (int) $this->pdo->query(
                "SELECT COUNT(*) FROM project_tasks
                 WHERE status IN ('todo','in_progress') AND is_deleted = 0"
            )->fetchColumn();

            $totalBudget = (float) $this->pdo->query(
                "SELECT COALESCE(SUM(planned_budget), 0) FROM projects
                 WHERE status = 'active' AND is_deleted = 0"
            )->fetchColumn();

            return [
                'active_projects' => $activeProjects,
                'open_tasks'      => $openTasks,
                'total_budget'    => $totalBudget,
            ];
        } catch (\Throwable $e) {
            return ['active_projects' => 0, 'open_tasks' => 0, 'total_budget' => 0.0];
        }
    }

    public function kpiCs(): array
    {
        try {
            $open = 0;
            $inProgress = 0;
            $resolvedWeek = 0;

            try {
                $open = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM support_tickets WHERE status = 'open' AND is_deleted = 0"
                )->fetchColumn();
                $inProgress = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM support_tickets WHERE status = 'in_progress' AND is_deleted = 0"
                )->fetchColumn();
                $resolvedWeek = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM support_tickets
                     WHERE status = 'resolved' AND is_deleted = 0
                       AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
                )->fetchColumn();
            } catch (\Throwable $e2) {
                $open = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM cs_tickets WHERE status = 'open' AND is_deleted = 0"
                )->fetchColumn();
                $inProgress = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM cs_tickets WHERE status = 'in_progress' AND is_deleted = 0"
                )->fetchColumn();
                $resolvedWeek = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM cs_tickets
                     WHERE status = 'resolved' AND is_deleted = 0
                       AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
                )->fetchColumn();
            }

            return [
                'open'         => $open,
                'in_progress'  => $inProgress,
                'resolved_week'=> $resolvedWeek,
            ];
        } catch (\Throwable $e) {
            return ['open' => 0, 'in_progress' => 0, 'resolved_week' => 0];
        }
    }

    public function recentWorkorders(int $limit = 5): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT wo.id, wo.title, wo.status, wo.priority, wo.created_at,
                        COALESCE(e.name, m.name, '') AS equipment_name
                 FROM work_orders wo
                 LEFT JOIN equipment e ON wo.equipment_id = e.id
                 LEFT JOIN machines m ON wo.machine_id = m.id
                 WHERE wo.is_deleted = 0
                 ORDER BY wo.created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function recentInvoices(int $limit = 5): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT io.id, io.invoice_number, io.invoice_date, io.due_date,
                        io.total_amount, io.status, c.name AS customer_name
                 FROM invoices_outgoing io
                 LEFT JOIN customers c ON io.customer_id = c.id
                 WHERE io.is_deleted = 0
                 ORDER BY io.created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function overdueResources(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT id, name, resource_type, location, next_inspection_date
                 FROM emergency_resources
                 WHERE next_inspection_date < NOW() AND is_deleted = 0
                 ORDER BY next_inspection_date ASC"
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
