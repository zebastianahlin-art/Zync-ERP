<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class WorkOrderRepository
{
    public function all(bool $archived = false): array
    {
        $statusFilter = $archived ? "wo.status IN ('closed','cancelled')" : "wo.status NOT IN ('closed','cancelled')";
        $sql = "SELECT wo.*,
                       e.name AS equipment_name,
                       u1.full_name AS assigned_to_name,
                       u2.full_name AS created_by_name
                FROM work_orders wo
                LEFT JOIN equipment e ON wo.equipment_id = e.id
                LEFT JOIN users u1 ON wo.assigned_to = u1.id
                LEFT JOIN users u2 ON wo.created_by = u2.id
                WHERE wo.is_deleted = 0 AND {$statusFilter}
                ORDER BY wo.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wo.*,
                    e.name AS equipment_name,
                    u1.full_name AS assigned_to_name,
                    u2.full_name AS assigned_by_name,
                    u3.full_name AS completed_by_name,
                    u4.full_name AS created_by_name,
                    fr.fault_number
             FROM work_orders wo
             LEFT JOIN equipment e ON wo.equipment_id = e.id
             LEFT JOIN users u1 ON wo.assigned_to = u1.id
             LEFT JOIN users u2 ON wo.assigned_by = u2.id
             LEFT JOIN users u3 ON wo.completed_by = u3.id
             LEFT JOIN users u4 ON wo.created_by = u4.id
             LEFT JOIN fault_reports fr ON wo.fault_report_id = fr.id
             WHERE wo.id = ? AND wo.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getUnassigned(): array
    {
        $sql = "SELECT wo.*, e.name AS equipment_name
                FROM work_orders wo
                LEFT JOIN equipment e ON wo.equipment_id = e.id
                WHERE wo.is_deleted = 0 AND wo.status = 'draft' AND wo.assigned_to IS NULL
                ORDER BY FIELD(wo.priority,'critical','high','medium','low'), wo.created_at ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPendingApproval(): array
    {
        $sql = "SELECT wo.*, u.full_name AS assigned_to_name
                FROM work_orders wo
                LEFT JOIN users u ON wo.assigned_to = u.id
                WHERE wo.is_deleted = 0 AND wo.status = 'completed'
                ORDER BY wo.completed_at ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getByAssignee(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT wo.*, e.name AS equipment_name
             FROM work_orders wo
             LEFT JOIN equipment e ON wo.equipment_id = e.id
             WHERE wo.is_deleted = 0 AND wo.assigned_to = ? AND wo.status NOT IN ('closed','cancelled')
             ORDER BY FIELD(wo.priority,'critical','high','medium','low'), wo.created_at ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTeamStats(): array
    {
        $sql = "SELECT u.id, u.full_name,
                       COUNT(wo.id) AS total_orders,
                       SUM(CASE WHEN wo.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                       SUM(CASE WHEN wo.status = 'completed' THEN 1 ELSE 0 END) AS completed,
                       COALESCE(SUM(wo.actual_hours), 0) AS total_hours
                FROM users u
                LEFT JOIN work_orders wo ON wo.assigned_to = u.id AND wo.is_deleted = 0
                    AND wo.status NOT IN ('closed','cancelled')
                WHERE u.is_active = 1
                GROUP BY u.id, u.full_name
                HAVING total_orders > 0
                ORDER BY in_progress DESC, total_orders DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTimeEntries(int $workOrderId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT te.*, u.full_name AS user_name, ab.full_name AS approved_by_name
             FROM work_order_time_entries te
             LEFT JOIN users u ON te.user_id = u.id
             LEFT JOIN users ab ON te.approved_by = ab.id
             WHERE te.work_order_id = ?
             ORDER BY te.work_date DESC, te.id DESC"
        );
        $stmt->execute([$workOrderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getParts(int $workOrderId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT p.*, a.article_number, a.name AS article_name,
                    u.full_name AS added_by_name, ab.full_name AS approved_by_name
             FROM work_order_parts p
             LEFT JOIN articles a ON p.article_id = a.id
             LEFT JOIN users u ON p.added_by = u.id
             LEFT JOIN users ab ON p.approved_by = ab.id
             WHERE p.work_order_id = ?
             ORDER BY p.id ASC"
        );
        $stmt->execute([$workOrderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $number = $this->generateOrderNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO work_orders
             (wo_number, title, description, type, equipment_id,
              fault_report_id, priority, planned_start, planned_end,
              estimated_hours, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['title'],
            $data['description'] ?? null,
            $data['type'] ?? 'corrective',
            $data['equipment_id'] ?: null,
            $data['fault_report_id'] ?: null,
            $data['priority'] ?? 'medium',
            $data['planned_start'] ?: null,
            $data['planned_end'] ?: null,
            $data['estimated_hours'] ?: null,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET title=?, description=?, type=?, equipment_id=?,
             priority=?, planned_start=?, planned_end=?,
             estimated_hours=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['type'] ?? 'corrective',
            $data['equipment_id'] ?: null,
            $data['priority'] ?? 'medium',
            $data['planned_start'] ?: null,
            $data['planned_end'] ?: null,
            $data['estimated_hours'] ?: null,
            $id,
        ]);
    }

    public function assign(int $id, int $assignedTo, int $assignedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='assigned', assigned_to=?, assigned_by=?, assigned_at=NOW() WHERE id=?"
        );
        $stmt->execute([$assignedTo, $assignedBy, $id]);
    }

    public function start(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='in_progress', actual_start=NOW() WHERE id=?"
        );
        $stmt->execute([$id]);
    }

    public function complete(int $id, string $notes = ''): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='completed', completed_at=NOW(), action_taken=? WHERE id=?"
        );
        $stmt->execute([$notes ?: null, $id]);
    }

    public function submitForApproval(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='completed' WHERE id=?"
        );
        $stmt->execute([$id]);
    }

    public function approve(int $id, int $userId, string $notes = ''): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='closed', completed_by=?, notes=? WHERE id=?"
        );
        $stmt->execute([$userId, $notes ?: null, $id]);
    }

    public function reject(int $id, int $userId, string $reason = ''): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='in_progress', notes=? WHERE id=?"
        );
        $stmt->execute([$reason ?: null, $id]);
    }

    public function close(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='closed', completed_by=? WHERE id=?"
        );
        $stmt->execute([$userId, $id]);
    }

    public function archive(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_orders SET status='cancelled' WHERE id=?"
        );
        $stmt->execute([$id]);
    }

    public function addTimeEntry(int $workOrderId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO work_order_time_entries
             (work_order_id, user_id, work_date, hours, description, is_overtime, hourly_rate)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $workOrderId,
            $data['user_id'],
            $data['work_date'],
            $data['hours'],
            $data['description'] ?? null,
            isset($data['is_overtime']) ? 1 : 0,
            $data['hourly_rate'] ?: null,
        ]);
        $entryId = (int) Database::pdo()->lastInsertId();
        $this->recalcTotals($workOrderId);
        return $entryId;
    }

    public function deleteTimeEntry(int $entryId, int $workOrderId): void
    {
        $stmt = Database::pdo()->prepare("DELETE FROM work_order_time_entries WHERE id=? AND work_order_id=?");
        $stmt->execute([$entryId, $workOrderId]);
        $this->recalcTotals($workOrderId);
    }

    public function approveTimeEntry(int $entryId, int $approvedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_order_time_entries SET is_approved=1, approved_by=?, approved_at=NOW() WHERE id=?"
        );
        $stmt->execute([$approvedBy, $entryId]);
    }

    public function addPart(int $workOrderId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO work_order_parts
             (work_order_id, article_id, quantity, unit_price, added_by, notes)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $workOrderId,
            $data['article_id'] ?: null,
            $data['quantity'] ?? 1,
            $data['unit_price'] ?? 0,
            $data['added_by'],
            $data['notes'] ?? null,
        ]);
        $partId = (int) Database::pdo()->lastInsertId();
        $this->recalcTotals($workOrderId);
        return $partId;
    }

    public function deletePart(int $partId, int $workOrderId): void
    {
        $stmt = Database::pdo()->prepare("DELETE FROM work_order_parts WHERE id=? AND work_order_id=?");
        $stmt->execute([$partId, $workOrderId]);
        $this->recalcTotals($workOrderId);
    }

    public function approvePart(int $partId, int $approvedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_order_parts SET is_approved=1, approved_by=?, approved_at=NOW() WHERE id=?"
        );
        $stmt->execute([$approvedBy, $partId]);
    }

    public function approveAll(int $workOrderId, int $approvedBy): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE work_order_time_entries SET is_approved=1, approved_by=?, approved_at=NOW() WHERE work_order_id=? AND is_approved=0"
        );
        $stmt->execute([$approvedBy, $workOrderId]);
        $stmt2 = Database::pdo()->prepare(
            "UPDATE work_order_parts SET is_approved=1, approved_by=?, approved_at=NOW() WHERE work_order_id=? AND is_approved=0"
        );
        $stmt2->execute([$approvedBy, $workOrderId]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE work_orders SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function recalcTotals(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "SELECT COALESCE(SUM(hours), 0) FROM work_order_time_entries WHERE work_order_id = ?"
        );
        $stmt->execute([$id]);
        $hours = (float) $stmt->fetchColumn();

        $stmt2 = Database::pdo()->prepare(
            "UPDATE work_orders SET actual_hours=? WHERE id=?"
        );
        $stmt2->execute([$hours, $id]);
    }

    public function generateOrderNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare("SELECT COUNT(*) FROM work_orders WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "AO-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
