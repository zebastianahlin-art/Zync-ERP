<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class WorkOrderRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(?string $status = null, ?string $type = null, ?int $assignedTo = null, ?int $equipmentId = null): array
    {
        $sql = '
            SELECT wo.*, e.name AS equipment_name, e.equipment_number,
                   ua.full_name AS assigned_name,
                   ub.full_name AS assigner_name
            FROM work_orders wo
            JOIN equipment e ON wo.equipment_id = e.id
            LEFT JOIN users ua ON wo.assigned_to = ua.id
            LEFT JOIN users ub ON wo.assigned_by = ub.id
            WHERE wo.is_deleted = 0
        ';
        $params = [];
        if ($status) { $sql .= ' AND wo.status = ?'; $params[] = $status; }
        if ($type) { $sql .= ' AND wo.type = ?'; $params[] = $type; }
        if ($assignedTo) { $sql .= ' AND wo.assigned_to = ?'; $params[] = $assignedTo; }
        if ($equipmentId) { $sql .= ' AND wo.equipment_id = ?'; $params[] = $equipmentId; }
        $sql .= ' ORDER BY FIELD(wo.priority,"critical","high","medium","low"), wo.created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT wo.*, e.name AS equipment_name, e.equipment_number,
                   ua.full_name AS assigned_name,
                   ub.full_name AS assigner_name,
                   fr.report_number AS fault_report_number
            FROM work_orders wo
            JOIN equipment e ON wo.equipment_id = e.id
            LEFT JOIN users ua ON wo.assigned_to = ua.id
            LEFT JOIN users ub ON wo.assigned_by = ub.id
            LEFT JOIN fault_reports fr ON wo.fault_report_id = fr.id
            WHERE wo.id = ? AND wo.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO work_orders
                (wo_number, fault_report_id, equipment_id, title, description, type, priority, status,
                 assigned_to, assigned_by, assigned_at, planned_start, planned_end, estimated_hours, notes, created_by)
            VALUES
                (:wo_number, :fault_report_id, :equipment_id, :title, :description, :type, :priority, :status,
                 :assigned_to, :assigned_by, :assigned_at, :planned_start, :planned_end, :estimated_hours, :notes, :created_by)
        ');
        $stmt->execute([
            'wo_number'       => $data['wo_number'],
            'fault_report_id' => $data['fault_report_id'] ?: null,
            'equipment_id'    => $data['equipment_id'],
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'type'            => $data['type'] ?? 'corrective',
            'priority'        => $data['priority'] ?? 'medium',
            'status'          => $data['status'] ?? 'draft',
            'assigned_to'     => $data['assigned_to'] ?: null,
            'assigned_by'     => $data['assigned_by'] ?? null,
            'assigned_at'     => $data['assigned_to'] ? date('Y-m-d H:i:s') : null,
            'planned_start'   => $data['planned_start'] ?: null,
            'planned_end'     => $data['planned_end'] ?: null,
            'estimated_hours' => $data['estimated_hours'] ?: null,
            'notes'           => $data['notes'] ?? null,
            'created_by'      => $data['created_by'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE work_orders SET
                equipment_id = :equipment_id, title = :title, description = :description,
                type = :type, priority = :priority, status = :status,
                assigned_to = :assigned_to, assigned_by = :assigned_by,
                planned_start = :planned_start, planned_end = :planned_end,
                estimated_hours = :estimated_hours, notes = :notes,
                root_cause = :root_cause, action_taken = :action_taken
            WHERE id = :id AND is_deleted = 0
        ');
        $stmt->execute([
            'id'              => $id,
            'equipment_id'    => $data['equipment_id'],
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'type'            => $data['type'],
            'priority'        => $data['priority'],
            'status'          => $data['status'],
            'assigned_to'     => $data['assigned_to'] ?: null,
            'assigned_by'     => $data['assigned_by'] ?? null,
            'planned_start'   => $data['planned_start'] ?: null,
            'planned_end'     => $data['planned_end'] ?: null,
            'estimated_hours' => $data['estimated_hours'] ?: null,
            'notes'           => $data['notes'] ?? null,
            'root_cause'      => $data['root_cause'] ?? null,
            'action_taken'    => $data['action_taken'] ?? null,
        ]);
    }

    public function updateStatus(int $id, string $status, ?int $userId = null): void
    {
        $extra = '';
        $params = ['status' => $status, 'id' => $id];
        if ($status === 'in_progress') {
            $extra = ', actual_start = COALESCE(actual_start, NOW())';
        } elseif ($status === 'completed') {
            $extra = ', actual_end = NOW(), completed_by = :completed_by, completed_at = NOW()';
            $params['completed_by'] = $userId;
        }
        $this->pdo->prepare("UPDATE work_orders SET status = :status{$extra} WHERE id = :id")->execute($params);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('UPDATE work_orders SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function nextNumber(): string
    {
        $year = date('Y');
        $stmt = $this->pdo->prepare("SELECT wo_number FROM work_orders WHERE wo_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute(["WO-{$year}-%"]);
        $last = $stmt->fetchColumn();
        $num = $last ? (int) substr($last, -4) + 1 : 1;
        return "WO-{$year}-" . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function stats(): array
    {
        return [
            'total'       => (int) $this->pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0")->fetchColumn(),
            'open'        => (int) $this->pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0 AND status IN ('draft','planned','assigned','in_progress','on_hold')")->fetchColumn(),
            'in_progress' => (int) $this->pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0 AND status = 'in_progress'")->fetchColumn(),
            'completed'   => (int) $this->pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0 AND status = 'completed'")->fetchColumn(),
            'overdue'     => (int) $this->pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0 AND planned_end < NOW() AND status NOT IN ('completed','closed','cancelled')")->fetchColumn(),
        ];
    }

    // --- Tid ---
    public function timeEntries(int $woId): array
    {
        $stmt = $this->pdo->prepare("SELECT t.*, u.full_name AS user_name FROM work_order_time t JOIN users u ON t.user_id = u.id WHERE t.work_order_id = ? ORDER BY t.date DESC");
        $stmt->execute([$woId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addTime(int $woId, int $userId, string $date, float $hours, ?string $desc): void
    {
        $this->pdo->prepare('INSERT INTO work_order_time (work_order_id, user_id, date, hours, description) VALUES (?,?,?,?,?)')->execute([$woId, $userId, $date, $hours, $desc]);
        // Uppdatera total
        $total = $this->pdo->prepare('SELECT SUM(hours) FROM work_order_time WHERE work_order_id = ?');
        $total->execute([$woId]);
        $this->pdo->prepare('UPDATE work_orders SET actual_hours = ? WHERE id = ?')->execute([$total->fetchColumn(), $woId]);
    }

    // --- Kommentarer ---
    public function comments(int $woId): array
    {
        $stmt = $this->pdo->prepare("SELECT c.*, u.full_name AS user_name FROM work_order_comments c JOIN users u ON c.user_id = u.id WHERE c.work_order_id = ? ORDER BY c.created_at ASC");
        $stmt->execute([$woId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addComment(int $woId, int $userId, string $comment, string $type = 'comment'): void
    {
        $this->pdo->prepare('INSERT INTO work_order_comments (work_order_id, user_id, comment, type) VALUES (?,?,?,?)')->execute([$woId, $userId, $comment, $type]);
    }

    // --- Material ---
    public function materials(int $woId): array
    {
        $stmt = $this->pdo->prepare("SELECT m.*, a.article_number, a.name AS article_name, a.unit, u.full_name AS withdrawn_name FROM work_order_materials m JOIN articles a ON m.article_id = a.id LEFT JOIN users u ON m.withdrawn_by = u.id WHERE m.work_order_id = ? ORDER BY m.withdrawn_at DESC");
        $stmt->execute([$woId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function withdrawMaterial(int $woId, int $articleId, float $qty, int $userId, ?string $notes): void
    {
        $this->pdo->prepare('INSERT INTO work_order_materials (work_order_id, article_id, quantity, withdrawn_by, notes) VALUES (?,?,?,?,?)')->execute([$woId, $articleId, $qty, $userId, $notes]);
    }

    public function allEquipment(): array
    {
        return $this->pdo->query("SELECT id, equipment_number, name FROM equipment WHERE is_deleted = 0 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allUsers(): array
    {
        return $this->pdo->query("SELECT id, full_name FROM users WHERE is_deleted = 0 AND is_active = 1 ORDER BY full_name")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
