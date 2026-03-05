<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;

class HrRepository
{
    public function stats(): array
    {
        $pdo = Database::pdo();
        $stats = [
            'total_employees' => 0,
            'open_positions' => 0,
            'upcoming_trainings' => 0,
            'expiring_certificates' => 0,
            'expired_certificates' => 0,
            'open_payroll_periods' => 0,
        ];
        try {
            $stats['total_employees'] = (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE is_deleted = 0 AND status = 'active'")->fetchColumn();
        } catch (\Exception $e) {
            try {
                $stats['total_employees'] = (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE is_deleted = 0")->fetchColumn();
            } catch (\Exception $e2) {}
        }
        try {
            $stats['open_positions'] = (int) $pdo->query("SELECT COUNT(*) FROM recruitment_positions WHERE is_deleted = 0 AND status = 'open'")->fetchColumn();
        } catch (\Exception $e) {}
        try {
            $stats['upcoming_trainings'] = (int) $pdo->query("SELECT COUNT(*) FROM training_sessions WHERE is_deleted = 0 AND status = 'planned' AND start_date >= CURDATE()")->fetchColumn();
        } catch (\Exception $e) {}
        try {
            $stats['expiring_certificates'] = (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_deleted = 0 AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
        } catch (\Exception $e) {}
        try {
            $stats['expired_certificates'] = (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_deleted = 0 AND expiry_date < CURDATE()")->fetchColumn();
        } catch (\Exception $e) {}
        try {
            $stats['open_payroll_periods'] = (int) $pdo->query("SELECT COUNT(*) FROM payroll_periods WHERE is_deleted = 0 AND status = 'open'")->fetchColumn();
        } catch (\Exception $e) {}
        return $stats;
    }

    public function upcomingEvents(): array
    {
        try {
            return Database::pdo()->query(
                "SELECT ts.*, tc.name AS course_name
                 FROM training_sessions ts
                 LEFT JOIN training_courses tc ON ts.course_id = tc.id
                 WHERE ts.is_deleted = 0
                   AND ts.start_date >= CURDATE()
                   AND ts.start_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
                 ORDER BY ts.start_date ASC
                 LIMIT 10"
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function recentActivities(): array
    {
        $pdo = Database::pdo();
        $activities = [];
        try {
            $rows = $pdo->query(
                "SELECT 'employee' AS type, CONCAT(first_name, ' ', last_name) AS label, created_at AS ts, id
                 FROM employees WHERE is_deleted = 0
                 ORDER BY created_at DESC LIMIT 5"
            )->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $activities[] = ['type' => 'employee', 'label' => 'Ny anställd: ' . $r['label'], 'ts' => $r['ts'], 'id' => $r['id']];
            }
        } catch (\Exception $e) {}

        try {
            $rows = $pdo->query(
                "SELECT c.id, CONCAT(e.first_name, ' ', e.last_name) AS emp_name, ct.name AS cert_name, c.created_at
                 FROM certificates c
                 LEFT JOIN employees e ON c.employee_id = e.id
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.is_deleted = 0
                 ORDER BY c.created_at DESC LIMIT 5"
            )->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $activities[] = ['type' => 'certificate', 'label' => 'Certifikat: ' . ($r['cert_name'] ?? '?') . ' → ' . ($r['emp_name'] ?? '?'), 'ts' => $r['created_at'], 'id' => $r['id']];
            }
        } catch (\Exception $e) {}

        usort($activities, fn($a, $b) => strcmp((string)($b['ts'] ?? ''), (string)($a['ts'] ?? '')));
        return array_slice($activities, 0, 10);
    }

    public function departmentDistribution(): array
    {
        try {
            return Database::pdo()->query(
                "SELECT d.name AS department_name, COUNT(e.id) AS employee_count
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.id
                 WHERE e.is_deleted = 0 AND (e.status = 'active' OR e.status IS NULL)
                 GROUP BY e.department_id, d.name
                 ORDER BY employee_count DESC
                 LIMIT 10"
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function statsPreviousMonth(): array
    {
        $pdo = Database::pdo();
        $stats = ['total_employees' => 0, 'open_positions' => 0];
        try {
            $lastMonth = date('Y-m-d', strtotime('-1 month'));
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM employees WHERE is_deleted = 0 AND (status = 'active' OR status IS NULL) AND created_at <= ?"
            );
            $stmt->execute([$lastMonth]);
            $stats['total_employees'] = (int) $stmt->fetchColumn();
        } catch (\Exception $e) {}
        return $stats;
    }

    public function expiringCertificatesWidget(int $days = 60): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                "SELECT c.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name, ct.name AS certificate_type_name
                 FROM certificates c
                 LEFT JOIN employees e ON c.employee_id = e.id
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.is_deleted = 0
                   AND c.expiry_date IS NOT NULL
                   AND c.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
                 ORDER BY c.expiry_date ASC
                 LIMIT 10"
            );
            $stmt->execute(['days' => $days]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
}
