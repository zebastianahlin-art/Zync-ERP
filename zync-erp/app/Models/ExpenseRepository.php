<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ExpenseRepository
{
    public function all(array $filters = []): array
    {
        $where  = ['r.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'r.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['employee_id'])) {
            $where[] = 'r.employee_id = ?';
            $params[] = (int) $filters['employee_id'];
        }

        $sql = 'SELECT r.*, e.first_name, e.last_name,
                       CONCAT(e.first_name, \' \', e.last_name) AS employee_name
                FROM expense_reports r
                LEFT JOIN employees e ON e.id = r.employee_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY r.created_at DESC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT r.*, e.first_name, e.last_name,
                    CONCAT(e.first_name, \' \', e.last_name) AS employee_name,
                    u.full_name AS approved_by_name
             FROM expense_reports r
             LEFT JOIN employees e ON e.id = r.employee_id
             LEFT JOIN users u ON u.id = r.approved_by
             WHERE r.id = ? AND r.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $reportNumber = $this->generateReportNumber();
        $stmt = Database::pdo()->prepare(
            'INSERT INTO expense_reports
             (report_number, employee_id, title, description, trip_start, trip_end,
              destination, purpose, currency, status, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $reportNumber,
            (int) $data['employee_id'],
            $data['title'],
            $data['description'] ?: null,
            $data['trip_start'] ?: null,
            $data['trip_end'] ?: null,
            $data['destination'] ?: null,
            $data['purpose'] ?: null,
            $data['currency'] ?? 'SEK',
            $data['status'] ?? 'draft',
            $data['notes'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE expense_reports
             SET employee_id=?, title=?, description=?, trip_start=?, trip_end=?,
                 destination=?, purpose=?, currency=?, notes=?
             WHERE id=? AND is_deleted=0'
        );
        $stmt->execute([
            (int) $data['employee_id'],
            $data['title'],
            $data['description'] ?: null,
            $data['trip_start'] ?: null,
            $data['trip_end'] ?: null,
            $data['destination'] ?: null,
            $data['purpose'] ?: null,
            $data['currency'] ?? 'SEK',
            $data['notes'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE expense_reports SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function submit(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE expense_reports SET status = 'submitted' WHERE id = ? AND is_deleted = 0"
        )->execute([$id]);
    }

    public function approve(int $id, int $userId): void
    {
        Database::pdo()->prepare(
            "UPDATE expense_reports SET status = 'approved', approved_by = ?, approved_at = NOW()
             WHERE id = ? AND is_deleted = 0"
        )->execute([$userId, $id]);
    }

    public function reject(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE expense_reports SET status = 'rejected' WHERE id = ? AND is_deleted = 0"
        )->execute([$id]);
    }

    public function addLine(int $reportId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO expense_report_lines
             (report_id, expense_date, category, description, amount, currency, receipt_ref, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $reportId,
            $data['expense_date'],
            $data['category'] ?? 'other',
            $data['description'],
            (float) $data['amount'],
            $data['currency'] ?? 'SEK',
            $data['receipt_ref'] ?: null,
            $data['notes'] ?: null,
        ]);
        $lineId = (int) Database::pdo()->lastInsertId();
        $this->recalcTotal($reportId);
        return $lineId;
    }

    public function removeLine(int $lineId): void
    {
        $stmt = Database::pdo()->prepare('SELECT report_id FROM expense_report_lines WHERE id = ?');
        $stmt->execute([$lineId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        Database::pdo()->prepare('DELETE FROM expense_report_lines WHERE id = ?')->execute([$lineId]);

        if ($row) {
            $this->recalcTotal((int) $row['report_id']);
        }
    }

    public function lines(int $reportId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM expense_report_lines WHERE report_id = ? ORDER BY expense_date ASC, id ASC'
        );
        $stmt->execute([$reportId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function recalcTotal(int $reportId): void
    {
        Database::pdo()->prepare(
            'UPDATE expense_reports
             SET total_amount = (SELECT COALESCE(SUM(amount), 0) FROM expense_report_lines WHERE report_id = ?)
             WHERE id = ?'
        )->execute([$reportId, $reportId]);
    }

    public function myReports(int $employeeId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM expense_reports WHERE employee_id = ? AND is_deleted = 0 ORDER BY created_at DESC'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allEmployees(): array
    {
        return Database::pdo()->query(
            "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name
             FROM employees WHERE is_deleted = 0 ORDER BY last_name ASC, first_name ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function generateReportNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM expense_reports WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $seq = ((int) $stmt->fetchColumn()) + 1;
        return sprintf('RR-%d-%04d', $year, $seq);
    }
}
