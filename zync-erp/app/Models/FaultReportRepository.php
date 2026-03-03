<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class FaultReportRepository
{
    public function all(): array
    {
        $sql = "SELECT fr.*, 
                       m.name AS machine_name, e.name AS equipment_name,
                       d.name AS department_name,
                       u1.full_name AS reported_by_name,
                       u2.full_name AS assigned_to_name
                FROM fault_reports fr
                LEFT JOIN machines m ON fr.machine_id = m.id
                LEFT JOIN equipment e ON fr.equipment_id = e.id
                LEFT JOIN departments d ON fr.department_id = d.id
                LEFT JOIN users u1 ON fr.reported_by = u1.id
                LEFT JOIN users u2 ON fr.assigned_to = u2.id
                WHERE fr.is_deleted = 0
                ORDER BY fr.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT fr.*,
                    m.name AS machine_name, e.name AS equipment_name,
                    d.name AS department_name,
                    u1.full_name AS reported_by_name,
                    u2.full_name AS acknowledged_by_name,
                    u3.full_name AS assigned_to_name,
                    u4.full_name AS assigned_by_name
             FROM fault_reports fr
             LEFT JOIN machines m ON fr.machine_id = m.id
             LEFT JOIN equipment e ON fr.equipment_id = e.id
             LEFT JOIN departments d ON fr.department_id = d.id
             LEFT JOIN users u1 ON fr.reported_by = u1.id
             LEFT JOIN users u2 ON fr.acknowledged_by = u2.id
             LEFT JOIN users u3 ON fr.assigned_to = u3.id
             LEFT JOIN users u4 ON fr.assigned_by = u4.id
             WHERE fr.id = ? AND fr.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateFaultNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO fault_reports
             (fault_number, title, description, machine_id, equipment_id, location,
              department_id, fault_type, priority, reported_by, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['title'],
            $data['description'] ?? null,
            $data['machine_id'] ?: null,
            $data['equipment_id'] ?: null,
            $data['location'] ?? null,
            $data['department_id'] ?: null,
            $data['fault_type'] ?? 'other',
            $data['priority'] ?? 'normal',
            $data['reported_by'],
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports SET title=?, description=?, machine_id=?, equipment_id=?,
             location=?, department_id=?, fault_type=?, priority=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['machine_id'] ?: null,
            $data['equipment_id'] ?: null,
            $data['location'] ?? null,
            $data['department_id'] ?: null,
            $data['fault_type'] ?? 'other',
            $data['priority'] ?? 'normal',
            $id,
        ]);
    }

    public function acknowledge(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports SET status='acknowledged', acknowledged_by=?, acknowledged_at=NOW() WHERE id=?"
        );
        $stmt->execute([$userId, $id]);
    }

    public function assign(int $id, int $assignedTo, int $assignedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports SET status='assigned', assigned_to=?, assigned_by=?, assigned_at=NOW() WHERE id=?"
        );
        $stmt->execute([$assignedTo, $assignedBy, $id]);
    }

    public function linkWorkOrder(int $id, int $workOrderId): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports SET work_order_id=?, status='in_progress' WHERE id=?"
        );
        $stmt->execute([$workOrderId, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE fault_reports SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function generateFaultNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare("SELECT COUNT(*) FROM fault_reports WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "FA-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
