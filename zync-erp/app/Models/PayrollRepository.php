<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PayrollRepository
{
    public function allPeriods(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT * FROM payroll_periods WHERE is_deleted = 0 ORDER BY period_from DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function findPeriod(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT * FROM payroll_periods WHERE id = ? AND is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function createPeriod(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO payroll_periods (name, period_from, period_to, status, created_by)
             VALUES (:name, :period_from, :period_to, :status, :created_by)'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'period_from' => $data['period_from'],
            'period_to'   => $data['period_to'],
            'status'      => $data['status'] ?? 'open',
            'created_by'  => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function periodPayslips(int $periodId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT ps.*, e.first_name, e.last_name
                 FROM payroll_payslips ps
                 LEFT JOIN employees e ON ps.employee_id = e.id
                 WHERE ps.period_id = ? AND ps.is_deleted = 0
                 ORDER BY e.last_name ASC'
            );
            $stmt->execute([$periodId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function updatePeriod(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE payroll_periods SET name = :name, period_from = :period_from, period_to = :period_to
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'period_from' => $data['period_from'] ?: null,
            'period_to'   => $data['period_to'] ?: null,
            'id'          => $id,
        ]);
    }

    public function deletePeriod(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE payroll_periods SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function closePeriod(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE payroll_periods SET status = 'locked' WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
    }

    public function createPayslip(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO payroll_payslips
             (period_id, employee_id, base_pay, ob_amount, overtime_amount, gross_pay, deductions, tax_amount, net_pay, status, notes, created_by)
             VALUES (:period_id, :employee_id, :base_pay, :ob_amount, :overtime_amount, :gross_pay, :deductions, :tax_amount, :net_pay, :status, :notes, :created_by)'
        );
        $stmt->execute([
            'period_id'       => $data['period_id'],
            'employee_id'     => $data['employee_id'],
            'base_pay'        => $data['base_pay'] ?? 0,
            'ob_amount'       => $data['ob_amount'] ?? 0,
            'overtime_amount' => $data['overtime_amount'] ?? 0,
            'gross_pay'       => $data['gross_pay'] ?? 0,
            'deductions'      => $data['deductions'] ?? 0,
            'tax_amount'      => $data['tax_amount'] ?? 0,
            'net_pay'         => $data['net_pay'] ?? 0,
            'status'          => $data['status'] ?? 'draft',
            'notes'           => $data['notes'] ?: null,
            'created_by'      => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function findPayslip(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT ps.*, e.first_name, e.last_name, pp.name AS period_name
                 FROM payroll_payslips ps
                 LEFT JOIN employees e ON ps.employee_id = e.id
                 LEFT JOIN payroll_periods pp ON ps.period_id = pp.id
                 WHERE ps.id = ? AND ps.is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updatePayslip(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE payroll_payslips SET
             base_pay = :base_pay, ob_amount = :ob_amount, overtime_amount = :overtime_amount,
             gross_pay = :gross_pay, deductions = :deductions, tax_amount = :tax_amount,
             net_pay = :net_pay, status = :status, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'base_pay'        => $data['base_pay'] ?? 0,
            'ob_amount'       => $data['ob_amount'] ?? 0,
            'overtime_amount' => $data['overtime_amount'] ?? 0,
            'gross_pay'       => $data['gross_pay'] ?? 0,
            'deductions'      => $data['deductions'] ?? 0,
            'tax_amount'      => $data['tax_amount'] ?? 0,
            'net_pay'         => $data['net_pay'] ?? 0,
            'status'          => $data['status'] ?? 'draft',
            'notes'           => $data['notes'] ?: null,
            'id'              => $id,
        ]);
    }

    public function deletePayslip(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE payroll_payslips SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function generatePayslips(int $periodId): int
    {
        $pdo = Database::pdo();
        $employees = $pdo->query(
            "SELECT id, salary FROM employees WHERE is_deleted = 0 AND status = 'active'"
        )->fetchAll(\PDO::FETCH_ASSOC);

        $existing = $pdo->prepare('SELECT employee_id FROM payroll_payslips WHERE period_id = ? AND is_deleted = 0');
        $existing->execute([$periodId]);
        $existingIds = array_column($existing->fetchAll(\PDO::FETCH_ASSOC), 'employee_id');

        $count = 0;
        foreach ($employees as $emp) {
            if (in_array($emp['id'], $existingIds)) {
                continue;
            }
            $gross = (float) ($emp['salary'] ?? 0);
            $this->createPayslip([
                'period_id'   => $periodId,
                'employee_id' => $emp['id'],
                'base_pay'    => $gross,
                'gross_pay'   => $gross,
                'net_pay'     => $gross,
                'status'      => 'draft',
            ]);
            $count++;
        }
        return $count;
    }

    public function payslipsByEmployee(int $employeeId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT ps.*, pp.name AS period_name
                 FROM payroll_payslips ps
                 LEFT JOIN payroll_periods pp ON ps.period_id = pp.id
                 WHERE ps.employee_id = ? AND ps.is_deleted = 0
                 ORDER BY pp.period_from DESC'
            );
            $stmt->execute([$employeeId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function employeePayslipHistory(int $employeeId): array
    {
        return $this->payslipsByEmployee($employeeId);
    }

    public function allEmployees(): array
    {
        try {
            return Database::pdo()->query(
                "SELECT id, first_name, last_name FROM employees WHERE is_deleted = 0 AND status = 'active' ORDER BY last_name ASC"
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function periodPayslipCount(int $periodId): int
    {
        try {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM payroll_payslips WHERE period_id = ? AND is_deleted = 0');
            $stmt->execute([$periodId]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
