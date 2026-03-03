<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class FaultReportRepository
{
    public function all(): array
    {
        $sql = "SELECT f.*, m.name AS machine_name, e.name AS equipment_name,
                       u.full_name AS reported_by_name, u2.full_name AS assigned_to_name,
                       d.name AS department_name
                FROM fault_reports f
                LEFT JOIN machines m ON f.machine_id = m.id
                LEFT JOIN equipment e ON f.equipment_id = e.id
                LEFT JOIN users u ON f.reported_by = u.id
                LEFT JOIN users u2 ON f.assigned_to = u2.id
                LEFT JOIN departments d ON f.department_id = d.id
                WHERE f.is_deleted = 0
                ORDER BY f.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT f.*, m.name AS machine_name, e.name AS equipment_name,
                    u.full_name AS reported_by_name, u2.full_name AS assigned_to_name,
                    u3.full_name AS acknowledged_by_name, d.name AS department_name
             FROM fault_reports f
             LEFT JOIN machines m ON f.machine_id = m.id
             LEFT JOIN equipment e ON f.equipment_id = e.id
             LEFT JOIN users u ON f.reported_by = u.id
             LEFT JOIN users u2 ON f.assigned_to = u2.id
             LEFT JOIN users u3 ON f.acknowledged_by = u3.id
             LEFT JOIN departments d ON f.department_id = d.id
             WHERE f.id = ? AND f.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
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
            $data['fault_type'] ?? null,
            $data['priority'] ?? 'normal',
            $data['reported_by'],
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports
             SET title = ?, description = ?, machine_id = ?, equipment_id = ?, location = ?,
                 department_id = ?, fault_type = ?, priority = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['machine_id'] ?: null,
            $data['equipment_id'] ?: null,
            $data['location'] ?? null,
            $data['department_id'] ?: null,
            $data['fault_type'] ?? null,
            $data['priority'] ?? 'normal',
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE fault_reports SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function generateNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM fault_reports WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "FA-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatus(int $id, string $status, ?int $userId = null): void
    {
        $extra = '';
        $params = [$status];
        if ($status === 'acknowledged') {
            $extra = ', acknowledged_by = ?, acknowledged_at = NOW()';
            $params[] = $userId;
        } elseif ($status === 'assigned') {
            $extra = ', assigned_to = ?, assigned_by = ?, assigned_at = NOW()';
            $params[] = $userId;
            $params[] = $userId;
        } elseif ($status === 'resolved') {
            $extra = ', resolved_at = NOW()';
        }
        $params[] = $id;
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports SET status = ?{$extra} WHERE id = ?"
        );
        $stmt->execute($params);
    }

    public function assign(int $id, int $assignedTo, int $assignedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports
             SET assigned_to = ?, assigned_by = ?, assigned_at = NOW(), status = 'assigned'
             WHERE id = ?"
        );
        $stmt->execute([$assignedTo, $assignedBy, $id]);
    }

    public function getOpen(): array
    {
        $sql = "SELECT f.*, m.name AS machine_name, e.name AS equipment_name,
                       u.full_name AS reported_by_name, u2.full_name AS assigned_to_name,
                       d.name AS department_name
                FROM fault_reports f
                LEFT JOIN machines m ON f.machine_id = m.id
                LEFT JOIN equipment e ON f.equipment_id = e.id
                LEFT JOIN users u ON f.reported_by = u.id
                LEFT JOIN users u2 ON f.assigned_to = u2.id
                LEFT JOIN departments d ON f.department_id = d.id
                WHERE f.status NOT IN ('resolved', 'closed') AND f.is_deleted = 0
                ORDER BY f.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getByMachine(int $machineId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT f.*, m.name AS machine_name, e.name AS equipment_name,
                    u.full_name AS reported_by_name, u2.full_name AS assigned_to_name,
                    d.name AS department_name
             FROM fault_reports f
             LEFT JOIN machines m ON f.machine_id = m.id
             LEFT JOIN equipment e ON f.equipment_id = e.id
             LEFT JOIN users u ON f.reported_by = u.id
             LEFT JOIN users u2 ON f.assigned_to = u2.id
             LEFT JOIN departments d ON f.department_id = d.id
             WHERE f.machine_id = ? AND f.is_deleted = 0
             ORDER BY f.created_at DESC"
        );
        $stmt->execute([$machineId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function linkWorkOrder(int $faultId, int $workOrderId): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fault_reports SET work_order_id = ? WHERE id = ?"
        );
        $stmt->execute([$workOrderId, $faultId]);
    }
}
