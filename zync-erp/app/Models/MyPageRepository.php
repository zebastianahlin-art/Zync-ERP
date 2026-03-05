<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * MyPageRepository – fetches all data needed for Min Sida (FAS E).
 */
class MyPageRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    // ── KPI ────────────────────────────────────────────────────────────────────

    public function kpi(int $userId, ?int $employeeId): array
    {
        $kpi = [
            'open_work_orders' => 0,
            'active_tasks'     => 0,
            'expiring_certs'   => 0,
            'open_tickets'     => 0,
        ];

        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM work_orders
                 WHERE assigned_to = ? AND is_deleted = 0
                   AND status NOT IN ('closed','cancelled','archived')"
            );
            $stmt->execute([$userId]);
            $kpi['open_work_orders'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {}

        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM project_tasks
                 WHERE assigned_to = ? AND is_deleted = 0
                   AND status IN ('todo','in_progress')"
            );
            $stmt->execute([$userId]);
            $kpi['active_tasks'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {}

        if ($employeeId !== null) {
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT COUNT(*) FROM certificates
                     WHERE employee_id = ? AND is_deleted = 0
                       AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)"
                );
                $stmt->execute([$employeeId]);
                $kpi['expiring_certs'] = (int) $stmt->fetchColumn();
            } catch (\Throwable $e) {}
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM cs_tickets
                 WHERE is_deleted = 0
                   AND (created_by = ? OR assigned_to = ?)
                   AND status NOT IN ('closed','resolved')"
            );
            $stmt->execute([$userId, $userId]);
            $kpi['open_tickets'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {}

        return $kpi;
    }

    // ── Employee info ──────────────────────────────────────────────────────────

    public function employeeInfo(?int $employeeId): ?array
    {
        if ($employeeId === null) {
            return null;
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT e.*, d.name AS department_name,
                        CONCAT(m.first_name, " ", m.last_name) AS manager_name
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.id
                 LEFT JOIN employees m ON e.manager_id = m.id
                 WHERE e.id = ? AND e.is_deleted = 0'
            );
            $stmt->execute([$employeeId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ── Certificates ───────────────────────────────────────────────────────────

    public function employeeCertificates(?int $employeeId): array
    {
        if ($employeeId === null) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT c.*, ct.name AS type_name
                 FROM certificates c
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.employee_id = ? AND c.is_deleted = 0
                 ORDER BY c.expiry_date ASC
                 LIMIT 10'
            );
            $stmt->execute([$employeeId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ── Attendance records ─────────────────────────────────────────────────────

    public function recentAttendance(?int $employeeId, int $limit = 5): array
    {
        if ($employeeId === null) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM attendance_records
                 WHERE employee_id = ? AND is_deleted = 0
                 ORDER BY date DESC
                 LIMIT ?'
            );
            $stmt->execute([$employeeId, $limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ── Payslips ───────────────────────────────────────────────────────────────

    public function recentPayslips(?int $employeeId, int $limit = 5): array
    {
        if ($employeeId === null) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT ps.*, pp.name AS period_name, pp.period_from, pp.period_to
                 FROM payroll_payslips ps
                 LEFT JOIN payroll_periods pp ON ps.period_id = pp.id
                 WHERE ps.employee_id = ? AND ps.is_deleted = 0
                 ORDER BY pp.period_from DESC
                 LIMIT ?'
            );
            $stmt->execute([$employeeId, $limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function allPayslips(?int $employeeId): array
    {
        if ($employeeId === null) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT ps.*, pp.name AS period_name, pp.period_from, pp.period_to
                 FROM payroll_payslips ps
                 LEFT JOIN payroll_periods pp ON ps.period_id = pp.id
                 WHERE ps.employee_id = ? AND ps.is_deleted = 0
                 ORDER BY pp.period_from DESC'
            );
            $stmt->execute([$employeeId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function findPayslip(int $id, ?int $employeeId): ?array
    {
        if ($employeeId === null) {
            return null;
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT ps.*, pp.name AS period_name, pp.period_from, pp.period_to,
                        e.first_name, e.last_name
                 FROM payroll_payslips ps
                 LEFT JOIN payroll_periods pp ON ps.period_id = pp.id
                 LEFT JOIN employees e ON ps.employee_id = e.id
                 WHERE ps.id = ? AND ps.employee_id = ? AND ps.is_deleted = 0'
            );
            $stmt->execute([$id, $employeeId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ── Employment contract ────────────────────────────────────────────────────

    public function contract(?int $employeeId): ?array
    {
        if ($employeeId === null) {
            return null;
        }
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM employee_contracts
                 WHERE employee_id = ? AND is_deleted = 0
                 ORDER BY start_date DESC
                 LIMIT 1'
            );
            $stmt->execute([$employeeId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ── Tickets ────────────────────────────────────────────────────────────────

    public function tickets(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT t.*, c.name AS customer_name, u.full_name AS assigned_name
                 FROM cs_tickets t
                 LEFT JOIN customers c ON t.customer_id = c.id
                 LEFT JOIN users u ON t.assigned_to = u.id
                 WHERE t.is_deleted = 0 AND (t.created_by = ? OR t.assigned_to = ?)
                 ORDER BY t.created_at DESC'
            );
            $stmt->execute([$userId, $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ── Calendar events ────────────────────────────────────────────────────────

    public function calendarEvents(int $userId, ?int $employeeId, string $from, string $to): array
    {
        $events = [];

        // Work orders assigned to user
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, title, planned_start AS start_date, status
                 FROM work_orders
                 WHERE assigned_to = ? AND is_deleted = 0
                   AND planned_start IS NOT NULL
                   AND planned_start >= ? AND planned_start <= ?
                 ORDER BY planned_start ASC"
            );
            $stmt->execute([$userId, $from, $to]);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $events[] = [
                    'id'    => 'wo_' . $row['id'],
                    'title' => $row['title'],
                    'date'  => substr((string) $row['start_date'], 0, 10),
                    'type'  => 'work_order',
                    'color' => 'blue',
                    'url'   => '/maintenance/work-orders/' . $row['id'],
                ];
            }
        } catch (\Throwable $e) {}

        if ($employeeId !== null) {
            // Training sessions
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT ts.id, tc.name AS title, ts.start_date
                     FROM training_participants tp
                     JOIN training_sessions ts ON tp.session_id = ts.id
                     JOIN training_courses tc ON ts.course_id = tc.id
                     WHERE tp.employee_id = ? AND tp.is_deleted = 0
                       AND ts.start_date BETWEEN ? AND ?
                     ORDER BY ts.start_date ASC"
                );
                $stmt->execute([$employeeId, $from, $to]);
                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $events[] = [
                        'id'    => 'ts_' . $row['id'],
                        'title' => (string) $row['title'],
                        'date'  => (string) $row['start_date'],
                        'type'  => 'training',
                        'color' => 'green',
                        'url'   => '/hr/training',
                    ];
                }
            } catch (\Throwable $e) {}

            // Expiring certificates
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT c.id, ct.name AS title, c.expiry_date
                     FROM certificates c
                     LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                     WHERE c.employee_id = ? AND c.is_deleted = 0
                       AND c.expiry_date BETWEEN ? AND ?
                     ORDER BY c.expiry_date ASC"
                );
                $stmt->execute([$employeeId, $from, $to]);
                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $events[] = [
                        'id'    => 'cert_' . $row['id'],
                        'title' => 'Certifikat löper ut: ' . ($row['title'] ?? 'Okänt'),
                        'date'  => (string) $row['expiry_date'],
                        'type'  => 'certificate',
                        'color' => 'red',
                        'url'   => '/hr/certificates',
                    ];
                }
            } catch (\Throwable $e) {}

            // Absence/vacation records
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT id, type, date
                     FROM attendance_records
                     WHERE employee_id = ? AND is_deleted = 0
                       AND type IN ('vacation','absence','sick')
                       AND date BETWEEN ? AND ?
                     ORDER BY date ASC"
                );
                $stmt->execute([$employeeId, $from, $to]);
                $typeLabels = [
                    'vacation' => 'Semester',
                    'absence'  => 'Frånvaro',
                    'sick'     => 'Sjukfrånvaro',
                ];
                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    $events[] = [
                        'id'    => 'att_' . $row['id'],
                        'title' => $typeLabels[$row['type']] ?? (string) $row['type'],
                        'date'  => (string) $row['date'],
                        'type'  => 'attendance',
                        'color' => 'yellow',
                        'url'   => '/hr/attendance',
                    ];
                }
            } catch (\Throwable $e) {}
        }

        usort($events, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return $events;
    }
}
