<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class PayrollRepository
{
    public function allPeriods(): array
    {
        $stmt = Database::pdo()->query('SELECT * FROM payroll_periods WHERE is_deleted = 0 ORDER BY period_start DESC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findPeriod(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM payroll_periods WHERE id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createPeriod(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO payroll_periods (name, period_start, period_end, status)
             VALUES (:name, :period_start, :period_end, :status)'
        );
        $stmt->execute([
            'name'         => $data['name'],
            'period_start' => $data['period_start'],
            'period_end'   => $data['period_end'],
            'status'       => $data['status'] ?? 'open',
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updatePeriod(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE payroll_periods SET name = :name, period_start = :period_start,
             period_end = :period_end, status = :status WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'         => $data['name'],
            'period_start' => $data['period_start'],
            'period_end'   => $data['period_end'],
            'status'       => $data['status'] ?? 'open',
            'id'           => $id,
        ]);
    }

    public function allPayslips(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT pp.*, per.name AS period_name, per.period_start, per.period_end,
             e.full_name AS employee_name
             FROM payroll_payslips pp
             JOIN payroll_periods per ON per.id = pp.period_id
             LEFT JOIN users e ON e.id = pp.employee_id
             WHERE pp.is_deleted = 0
             ORDER BY pp.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findPayslip(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT pp.*, per.name AS period_name, per.period_start, per.period_end,
             e.full_name AS employee_name
             FROM payroll_payslips pp
             JOIN payroll_periods per ON per.id = pp.period_id
             LEFT JOIN users e ON e.id = pp.employee_id
             WHERE pp.id = ? AND pp.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createPayslip(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO payroll_payslips (period_id, employee_id, gross_salary, tax_deduction,
             net_salary, overtime_hours, overtime_amount, other_deductions, other_additions, notes)
             VALUES (:period_id, :employee_id, :gross_salary, :tax_deduction, :net_salary,
             :overtime_hours, :overtime_amount, :other_deductions, :other_additions, :notes)'
        );
        $stmt->execute([
            'period_id'        => $data['period_id'],
            'employee_id'      => $data['employee_id'],
            'gross_salary'     => $data['gross_salary'] ?? 0,
            'tax_deduction'    => $data['tax_deduction'] ?? 0,
            'net_salary'       => $data['net_salary'] ?? 0,
            'overtime_hours'   => $data['overtime_hours'] ?? 0,
            'overtime_amount'  => $data['overtime_amount'] ?? 0,
            'other_deductions' => $data['other_deductions'] ?? 0,
            'other_additions'  => $data['other_additions'] ?? 0,
            'notes'            => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function allEmployees(): array
    {
        $stmt = Database::pdo()->query('SELECT id, full_name FROM users WHERE is_deleted = 0 AND is_active = 1 ORDER BY full_name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
