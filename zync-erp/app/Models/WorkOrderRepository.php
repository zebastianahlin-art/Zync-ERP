<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class WorkOrderRepository
{
    public function all(): array
    {
        $sql = "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                       d.name AS department_name,
                       u.full_name AS assigned_to_name, u2.full_name AS created_by_name
                FROM work_orders wo
                LEFT JOIN machines m ON wo.machine_id = m.id
                LEFT JOIN equipment e ON wo.equipment_id = e.id
                LEFT JOIN departments d ON wo.department_id = d.id
                LEFT JOIN users u ON wo.assigned_to = u.id
                LEFT JOIN users u2 ON wo.created_by = u2.id
                WHERE wo.is_deleted = 0
                ORDER BY wo.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                    d.name AS department_name,
                    u.full_name AS assigned_to_name, u2.full_name AS created_by_name,
                    u3.full_name AS approved_by_name, u4.full_name AS closed_by_name
             FROM work_orders wo
             LEFT JOIN machines m ON wo.machine_id = m.id
             LEFT JOIN equipment e ON wo.equipment_id = e.id
             LEFT JOIN departments d ON wo.department_id = d.id
             LEFT JOIN users u ON wo.assigned_to = u.id
             LEFT JOIN users u2 ON wo.created_by = u2.id
             LEFT JOIN users u3 ON wo.approved_by = u3.id
             LEFT JOIN users u4 ON wo.closed_by = u4.id
             WHERE wo.id = ? AND wo.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO work_orders
             (order_number, title, description, machine_id, equipment_id, department_id,
              fault_report_id, work_type, priority, planned_start, estimated_hours,
              notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['title'],
            $data['description'] ?? null,
            $data['machine_id'] ?: null,
            $data['equipment_id'] ?: null,
            $data['department_id'] ?: null,
            $data['fault_report_id'] ?: null,
            $data['work_type'] ?? null,
            $data['priority'] ?? 'normal',
            $data['planned_start'] ?: null,
            $data['estimated_hours'] ?: null,
            $data['notes'] ?? null,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders
             SET title = ?, description = ?, machine_id = ?, equipment_id = ?,
                 department_id = ?, work_type = ?, priority = ?, planned_start = ?,
                 estimated_hours = ?, notes = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['machine_id'] ?: null,
            $data['equipment_id'] ?: null,
            $data['department_id'] ?: null,
            $data['work_type'] ?? null,
            $data['priority'] ?? 'normal',
            $data['planned_start'] ?: null,
            $data['estimated_hours'] ?: null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE work_orders SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function generateNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM work_orders WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "AO-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatus(int $id, string $status, ?int $userId = null, ?string $notes = null, ?string $reason = null): void
    {
        $extra = '';
        $params = [$status];
        if ($status === 'assigned') {
            $extra = ', assigned_at = NOW(), assigned_to = ?';
            $params[] = $userId;
        } elseif ($status === 'in_progress') {
            $extra = ', started_at = NOW()';
        } elseif ($status === 'work_completed') {
            $extra = ', completed_at = NOW(), completion_notes = ?';
            $params[] = $notes;
        } elseif ($status === 'approved') {
            $extra = ', approved_by = ?, approved_at = NOW(), approval_notes = ?';
            $params[] = $userId;
            $params[] = $notes;
        } elseif ($status === 'rejected') {
            $extra = ', rejected_reason = ?';
            $params[] = $reason;
        } elseif ($status === 'closed') {
            $extra = ', closed_at = NOW(), closed_by = ?';
            $params[] = $userId;
        } elseif ($status === 'archived') {
            $extra = ', archived_at = NOW()';
        }
        $params[] = $id;
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status = ?{$extra} WHERE id = ?"
        );
        $stmt->execute($params);
    }

    public function assign(int $id, int $assignedTo, int $assignedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders
             SET assigned_to = ?, assigned_by = ?, assigned_at = NOW(), status = 'assigned'
             WHERE id = ?"
        );
        $stmt->execute([$assignedTo, $assignedBy, $id]);
    }

    public function getByStatus(string $status): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                    d.name AS department_name, u.full_name AS assigned_to_name
             FROM work_orders wo
             LEFT JOIN machines m ON wo.machine_id = m.id
             LEFT JOIN equipment e ON wo.equipment_id = e.id
             LEFT JOIN departments d ON wo.department_id = d.id
             LEFT JOIN users u ON wo.assigned_to = u.id
             WHERE wo.status = ? AND wo.is_deleted = 0
             ORDER BY wo.created_at DESC"
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAssignedTo(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                    d.name AS department_name, u.full_name AS assigned_to_name
             FROM work_orders wo
             LEFT JOIN machines m ON wo.machine_id = m.id
             LEFT JOIN equipment e ON wo.equipment_id = e.id
             LEFT JOIN departments d ON wo.department_id = d.id
             LEFT JOIN users u ON wo.assigned_to = u.id
             WHERE wo.assigned_to = ? AND wo.is_deleted = 0
             ORDER BY wo.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUnassigned(): array
    {
        $sql = "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                       d.name AS department_name
                FROM work_orders wo
                LEFT JOIN machines m ON wo.machine_id = m.id
                LEFT JOIN equipment e ON wo.equipment_id = e.id
                LEFT JOIN departments d ON wo.department_id = d.id
                WHERE wo.assigned_to IS NULL AND wo.status = 'reported' AND wo.is_deleted = 0
                ORDER BY wo.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPendingApproval(): array
    {
        $sql = "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                       d.name AS department_name, u.full_name AS assigned_to_name
                FROM work_orders wo
                LEFT JOIN machines m ON wo.machine_id = m.id
                LEFT JOIN equipment e ON wo.equipment_id = e.id
                LEFT JOIN departments d ON wo.department_id = d.id
                LEFT JOIN users u ON wo.assigned_to = u.id
                WHERE wo.status = 'pending_approval' AND wo.is_deleted = 0
                ORDER BY wo.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getArchived(): array
    {
        $sql = "SELECT wo.*, m.name AS machine_name, e.name AS equipment_name,
                       d.name AS department_name, u.full_name AS assigned_to_name
                FROM work_orders wo
                LEFT JOIN machines m ON wo.machine_id = m.id
                LEFT JOIN equipment e ON wo.equipment_id = e.id
                LEFT JOIN departments d ON wo.department_id = d.id
                LEFT JOIN users u ON wo.assigned_to = u.id
                WHERE wo.status = 'archived' AND wo.is_deleted = 0
                ORDER BY wo.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addTimeEntry(int $woId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO work_order_time_entries
             (work_order_id, user_id, work_date, hours, description)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $woId,
            $data['user_id'],
            $data['work_date'],
            $data['hours'],
            $data['description'] ?? null,
        ]);
        $insertId = (int) Database::pdo()->lastInsertId();
        $this->recalcTotals($woId);
        return $insertId;
    }

    public function getTimeEntries(int $woId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wote.*, u.full_name AS user_name
             FROM work_order_time_entries wote
             LEFT JOIN users u ON wote.user_id = u.id
             WHERE wote.work_order_id = ?
             ORDER BY wote.work_date DESC"
        );
        $stmt->execute([$woId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function removeTimeEntry(int $id, int $woId): void
    {
        $stmt = Database::pdo()->prepare(
            "DELETE FROM work_order_time_entries WHERE id = ? AND work_order_id = ?"
        );
        $stmt->execute([$id, $woId]);
        $this->recalcTotals($woId);
    }

    public function approveTimeEntry(int $id, int $approvedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_order_time_entries
             SET is_approved = 1, approved_by = ?, approved_at = NOW()
             WHERE id = ?"
        );
        $stmt->execute([$approvedBy, $id]);
    }

    public function addPart(int $woId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO work_order_parts
             (work_order_id, article_id, quantity, unit_price, added_by, notes)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $woId,
            $data['article_id'] ?: null,
            $data['quantity'] ?? 1,
            $data['unit_price'] ?? 0,
            $data['added_by'],
            $data['notes'] ?? null,
        ]);
        $insertId = (int) Database::pdo()->lastInsertId();
        $this->recalcTotals($woId);
        return $insertId;
    }

    public function getParts(int $woId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wop.*, a.article_number, a.name AS article_name, a.unit,
                    u.full_name AS added_by_name
             FROM work_order_parts wop
             LEFT JOIN articles a ON wop.article_id = a.id
             LEFT JOIN users u ON wop.added_by = u.id
             WHERE wop.work_order_id = ?"
        );
        $stmt->execute([$woId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function removePart(int $id, int $woId): void
    {
        $stmt = Database::pdo()->prepare(
            "DELETE FROM work_order_parts WHERE id = ? AND work_order_id = ?"
        );
        $stmt->execute([$id, $woId]);
        $this->recalcTotals($woId);
    }

    public function approvePart(int $id, int $approvedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_order_parts
             SET is_approved = 1, approved_by = ?, approved_at = NOW()
             WHERE id = ?"
        );
        $stmt->execute([$approvedBy, $id]);
    }

    public function recalcTotals(int $woId): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders
             SET total_hours = (
                     SELECT COALESCE(SUM(hours), 0)
                     FROM work_order_time_entries
                     WHERE work_order_id = ?
                 ),
                 total_material_cost = (
                     SELECT COALESCE(SUM(total_price), 0)
                     FROM work_order_parts
                     WHERE work_order_id = ?
                 ),
                 total_cost = (
                     SELECT COALESCE(SUM(total_price), 0)
                     FROM work_order_parts
                     WHERE work_order_id = ?
                 )
             WHERE id = ?"
        );
        $stmt->execute([$woId, $woId, $woId, $woId]);
    }

    public function getStatistics(): array
    {
        $statuses = [
            'reported', 'assigned', 'in_progress', 'work_completed',
            'pending_approval', 'approved', 'rejected', 'closed', 'archived',
        ];
        $counts = [];
        foreach ($statuses as $status) {
            $stmt = Database::pdo()->prepare(
                "SELECT COUNT(*) FROM work_orders WHERE status = ? AND is_deleted = 0"
            );
            $stmt->execute([$status]);
            $counts[$status] = (int) $stmt->fetchColumn();
        }

        $stmtOpen = Database::pdo()->query(
            "SELECT COUNT(*) FROM work_orders WHERE status NOT IN ('closed', 'archived') AND is_deleted = 0"
        );
        $totalOpen = (int) $stmtOpen->fetchColumn();

        $stmtAvg = Database::pdo()->query(
            "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at))
             FROM work_orders
             WHERE completed_at IS NOT NULL AND is_deleted = 0"
        );
        $avgCompletion = $stmtAvg->fetchColumn();

        return array_merge($counts, [
            'total_open'          => $totalOpen,
            'avg_completion_hours' => $avgCompletion !== null ? round((float) $avgCompletion, 2) : null,
        ]);
    }
}
