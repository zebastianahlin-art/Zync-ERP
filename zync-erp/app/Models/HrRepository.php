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
}
