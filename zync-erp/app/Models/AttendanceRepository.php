<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class AttendanceRepository
{
    public function all(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT ar.*, u.full_name AS employee_name, a.full_name AS approver_name
             FROM attendance_records ar
             LEFT JOIN users u ON u.id = ar.employee_id
             LEFT JOIN users a ON a.id = ar.approved_by
             WHERE ar.is_deleted = 0
             ORDER BY ar.start_date DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT ar.*, u.full_name AS employee_name
             FROM attendance_records ar
             LEFT JOIN users u ON u.id = ar.employee_id
             WHERE ar.id = ? AND ar.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO attendance_records (employee_id, record_type, start_date, end_date, status, notes)
             VALUES (:employee_id, :record_type, :start_date, :end_date, :status, :notes)'
        );
        $stmt->execute([
            'employee_id' => $data['employee_id'],
            'record_type' => $data['record_type'] ?? 'vacation',
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date'],
            'status'      => $data['status'] ?? 'pending',
            'notes'       => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE attendance_records SET employee_id = :employee_id, record_type = :record_type,
             start_date = :start_date, end_date = :end_date, status = :status, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'employee_id' => $data['employee_id'],
            'record_type' => $data['record_type'] ?? 'vacation',
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date'],
            'status'      => $data['status'] ?? 'pending',
            'notes'       => $data['notes'] ?? null,
            'id'          => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE attendance_records SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function allEmployees(): array
    {
        $stmt = Database::pdo()->query('SELECT id, full_name FROM users WHERE is_deleted = 0 AND is_active = 1 ORDER BY full_name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
