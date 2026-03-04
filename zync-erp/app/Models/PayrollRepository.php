<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PayrollRepository
{
    public function allPeriods(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM payroll_periods WHERE is_deleted = 0 ORDER BY period_from DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findPeriod(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM payroll_periods WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
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
        $stmt = Database::pdo()->prepare(
            'SELECT ps.*, e.first_name, e.last_name
             FROM payroll_payslips ps
             LEFT JOIN employees e ON ps.employee_id = e.id
             WHERE ps.period_id = ? AND ps.is_deleted = 0
             ORDER BY e.last_name ASC'
        );
        $stmt->execute([$periodId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
