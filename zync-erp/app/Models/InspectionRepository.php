<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class InspectionRepository
{
    public function all(): array
    {
        $sql = "SELECT i.*, e.name AS equipment_name, m.name AS machine_name,
                       d.name AS department_name, u.full_name AS inspector_name
                FROM inspections i
                LEFT JOIN equipment e ON i.equipment_id = e.id
                LEFT JOIN machines m ON i.machine_id = m.id
                LEFT JOIN departments d ON i.department_id = d.id
                LEFT JOIN users u ON i.inspector_id = u.id
                WHERE i.is_deleted = 0
                ORDER BY i.scheduled_date DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT i.*, e.name AS equipment_name, m.name AS machine_name,
                    d.name AS department_name, u.full_name AS inspector_name
             FROM inspections i
             LEFT JOIN equipment e ON i.equipment_id = e.id
             LEFT JOIN machines m ON i.machine_id = m.id
             LEFT JOIN departments d ON i.department_id = d.id
             LEFT JOIN users u ON i.inspector_id = u.id
             WHERE i.id = ? AND i.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO inspections
             (inspection_number, title, description, equipment_id, machine_id, department_id,
              inspector_id, inspection_type, scheduled_date, status, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['title'],
            $data['description'] ?? null,
            $data['equipment_id'] ?: null,
            $data['machine_id'] ?: null,
            $data['department_id'] ?: null,
            $data['inspector_id'] ?: null,
            $data['inspection_type'] ?? null,
            $data['scheduled_date'] ?: null,
            $data['status'] ?? 'scheduled',
            $data['notes'] ?? null,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE inspections
             SET title = ?, description = ?, equipment_id = ?, machine_id = ?,
                 department_id = ?, inspector_id = ?, inspection_type = ?,
                 scheduled_date = ?, status = ?, notes = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['equipment_id'] ?: null,
            $data['machine_id'] ?: null,
            $data['department_id'] ?: null,
            $data['inspector_id'] ?: null,
            $data['inspection_type'] ?? null,
            $data['scheduled_date'] ?: null,
            $data['status'] ?? 'scheduled',
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE inspections SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function generateNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM inspections WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "INS-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function getOverdue(): array
    {
        $sql = "SELECT i.*, e.name AS equipment_name, m.name AS machine_name,
                       d.name AS department_name, u.full_name AS inspector_name
                FROM inspections i
                LEFT JOIN equipment e ON i.equipment_id = e.id
                LEFT JOIN machines m ON i.machine_id = m.id
                LEFT JOIN departments d ON i.department_id = d.id
                LEFT JOIN users u ON i.inspector_id = u.id
                WHERE i.status NOT IN ('completed', 'cancelled')
                  AND i.scheduled_date < CURDATE()
                  AND i.is_deleted = 0
                ORDER BY i.scheduled_date ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getScheduled(): array
    {
        $sql = "SELECT i.*, e.name AS equipment_name, m.name AS machine_name,
                       d.name AS department_name, u.full_name AS inspector_name
                FROM inspections i
                LEFT JOIN equipment e ON i.equipment_id = e.id
                LEFT JOIN machines m ON i.machine_id = m.id
                LEFT JOIN departments d ON i.department_id = d.id
                LEFT JOIN users u ON i.inspector_id = u.id
                WHERE i.status = 'scheduled' AND i.is_deleted = 0
                ORDER BY i.scheduled_date ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function complete(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE inspections
             SET completed_date = ?, status = 'completed', result = ?,
                 notes = ?, next_inspection_date = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['completed_date'],
            $data['result'] ?? null,
            $data['notes'] ?? null,
            $data['next_inspection_date'] ?: null,
            $id,
        ]);
    }
}
