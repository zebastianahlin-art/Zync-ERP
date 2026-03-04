<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class AttendanceRepository
{
    public function allEmployees(): array
    {
        return Database::pdo()->query(
            'SELECT id, first_name, last_name FROM employees WHERE is_deleted = 0 ORDER BY last_name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function recent(int $limit = 50): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT r.*, e.first_name, e.last_name
             FROM attendance_records r
             LEFT JOIN employees e ON r.employee_id = e.id
             WHERE r.is_deleted = 0
             ORDER BY r.date DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO attendance_records (employee_id, date, type, time_in, time_out, notes, created_by)
             VALUES (:employee_id, :date, :type, :time_in, :time_out, :notes, :created_by)'
        );
        $stmt->execute([
            'employee_id' => $data['employee_id'],
            'date'        => $data['date'],
            'type'        => $data['type'] ?? 'presence',
            'time_in'     => $data['time_in'] ?: null,
            'time_out'    => $data['time_out'] ?: null,
            'notes'       => $data['notes'] ?: null,
            'created_by'  => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function allBalances(): array
    {
        return Database::pdo()->query(
            'SELECT b.*, e.first_name, e.last_name
             FROM attendance_balances b
             LEFT JOIN employees e ON b.employee_id = e.id
             WHERE b.is_deleted = 0
             ORDER BY e.last_name ASC, b.year DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
