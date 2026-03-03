<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class PayrollRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Periods ──────────────────────────────────

    public function allPeriods(?int $year = null): array
    {
        $sql = "SELECT pp.*, 
                    (SELECT COUNT(*) FROM payroll_records WHERE period_id = pp.id) AS record_count,
                    (SELECT SUM(net_salary) FROM payroll_records WHERE period_id = pp.id) AS total_net
                FROM payroll_periods pp";
        $params = [];
        if ($year) {
            $sql .= " WHERE pp.year = ?";
            $params[] = $year;
        }
        $sql .= " ORDER BY pp.year DESC, pp.month DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPeriod(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM payroll_periods WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createPeriod(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO payroll_periods (year, month, start_date, end_date, status) VALUES (?, ?, ?, ?, 'draft')");
        $stmt->execute([$data['year'], $data['month'], $data['start_date'], $data['end_date']]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePeriodStatus(int $id, string $status, ?int $approvedBy = null): void
    {
        if ($status === 'approved' && $approvedBy) {
            $stmt = $this->db->prepare("UPDATE payroll_periods SET status = ?, approved_by = ?, approved_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $approvedBy, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE payroll_periods SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
        }
    }

    public function deletePeriod(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM payroll_periods WHERE id = ? AND status = 'draft'");
        $stmt->execute([$id]);
    }

    public function availableYears(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT year FROM payroll_periods ORDER BY year DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ── Records ──────────────────────────────────

    public function recordsForPeriod(int $periodId): array
    {
        $stmt = $this->db->prepare(
            "SELECT pr.*, e.employee_number, e.first_name, e.last_name, e.title, d.name AS department_name
             FROM payroll_records pr
             JOIN employees e ON pr.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE pr.period_id = ?
             ORDER BY e.last_name, e.first_name"
        );
        $stmt->execute([$periodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findRecord(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT pr.*, e.employee_number, e.first_name, e.last_name, e.salary AS default_salary
             FROM payroll_records pr
             JOIN employees e ON pr.employee_id = e.id
             WHERE pr.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createRecord(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO payroll_records (period_id, employee_id, base_salary, overtime_hours, overtime_amount, bonus, deductions, tax, net_salary, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['period_id'], $data['employee_id'], $data['base_salary'],
            $data['overtime_hours'] ?? 0, $data['overtime_amount'] ?? 0,
            $data['bonus'] ?? 0, $data['deductions'] ?? 0, $data['tax'] ?? 0,
            $data['net_salary'], $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateRecord(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE payroll_records SET base_salary = ?, overtime_hours = ?, overtime_amount = ?, bonus = ?, deductions = ?, tax = ?, net_salary = ?, notes = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['base_salary'], $data['overtime_hours'] ?? 0, $data['overtime_amount'] ?? 0,
            $data['bonus'] ?? 0, $data['deductions'] ?? 0, $data['tax'] ?? 0,
            $data['net_salary'], $data['notes'] ?? null, $id,
        ]);
    }

    public function deleteRecord(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM payroll_records WHERE id = ?");
        $stmt->execute([$id]);
    }

    /** Generate records for all active employees in a period */
    public function generateRecords(int $periodId): int
    {
        $existing = $this->db->prepare("SELECT employee_id FROM payroll_records WHERE period_id = ?");
        $existing->execute([$periodId]);
        $existingIds = $existing->fetchAll(PDO::FETCH_COLUMN);

        $sql = "SELECT id, salary FROM employees WHERE status = 'active' AND is_deleted = 0";
        if (!empty($existingIds)) {
            $placeholders = implode(',', array_fill(0, count($existingIds), '?'));
            $sql .= " AND id NOT IN ($placeholders)";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($existingIds);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $insert = $this->db->prepare(
            "INSERT INTO payroll_records (period_id, employee_id, base_salary, net_salary) VALUES (?, ?, ?, ?)"
        );

        $count = 0;
        foreach ($employees as $emp) {
            $salary = (float) ($emp['salary'] ?? 0);
            $insert->execute([$periodId, $emp['id'], $salary, $salary]);
            $count++;
        }
        return $count;
    }

    // ── Stats ────────────────────────────────────

    public function stats(): array
    {
        $s = [];
        $stmt = $this->db->query("SELECT COUNT(*) FROM payroll_periods");
        $s['total_periods'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM payroll_periods WHERE status = 'draft'");
        $s['draft_periods'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COALESCE(SUM(net_salary), 0) FROM payroll_records pr JOIN payroll_periods pp ON pr.period_id = pp.id WHERE pp.status = 'paid'");
        $s['total_paid'] = (float) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(DISTINCT employee_id) FROM payroll_records");
        $s['employees_with_payroll'] = (int) $stmt->fetchColumn();

        return $s;
    }
}
