<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class FaultReportRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(?string $status = null, ?string $priority = null, ?int $equipmentId = null): array
    {
        $sql = '
            SELECT fr.*, e.name AS equipment_name, e.equipment_number,
                   u.full_name AS reporter_name
            FROM fault_reports fr
            JOIN equipment e ON fr.equipment_id = e.id
            JOIN users u ON fr.reported_by = u.id
            WHERE fr.is_deleted = 0
        ';
        $params = [];
        if ($status) { $sql .= ' AND fr.status = ?'; $params[] = $status; }
        if ($priority) { $sql .= ' AND fr.priority = ?'; $params[] = $priority; }
        if ($equipmentId) { $sql .= ' AND fr.equipment_id = ?'; $params[] = $equipmentId; }
        $sql .= ' ORDER BY FIELD(fr.priority,"critical","high","medium","low"), fr.created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT fr.*, e.name AS equipment_name, e.equipment_number,
                   u.full_name AS reporter_name
            FROM fault_reports fr
            JOIN equipment e ON fr.equipment_id = e.id
            JOIN users u ON fr.reported_by = u.id
            WHERE fr.id = ? AND fr.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO fault_reports
                (report_number, equipment_id, title, description, fault_type, priority, status, reported_by, image_path, image_name, notes)
            VALUES (:report_number, :equipment_id, :title, :description, :fault_type, :priority, :status, :reported_by, :image_path, :image_name, :notes)
        ');
        $stmt->execute([
            'report_number' => $data['report_number'],
            'equipment_id'  => $data['equipment_id'],
            'title'         => $data['title'],
            'description'   => $data['description'],
            'fault_type'    => $data['fault_type'],
            'priority'      => $data['priority'],
            'status'        => $data['status'] ?? 'reported',
            'reported_by'   => $data['reported_by'],
            'image_path'    => $data['image_path'] ?? null,
            'image_name'    => $data['image_name'] ?? null,
            'notes'         => $data['notes'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE fault_reports SET
                equipment_id = :equipment_id, title = :title, description = :description,
                fault_type = :fault_type, priority = :priority, status = :status,
                image_path = :image_path, image_name = :image_name, notes = :notes
            WHERE id = :id AND is_deleted = 0
        ');
        $stmt->execute([
            'id'           => $id,
            'equipment_id' => $data['equipment_id'],
            'title'        => $data['title'],
            'description'  => $data['description'],
            'fault_type'   => $data['fault_type'],
            'priority'     => $data['priority'],
            'status'       => $data['status'],
            'image_path'   => $data['image_path'] ?? null,
            'image_name'   => $data['image_name'] ?? null,
            'notes'        => $data['notes'] ?? null,
        ]);
    }

    public function updateStatus(int $id, string $status, ?int $userId = null): void
    {
        $extra = '';
        $params = ['status' => $status, 'id' => $id];
        if ($status === 'acknowledged') {
            $extra = ', acknowledged_by = :ack_by, acknowledged_at = NOW()';
            $params['ack_by'] = $userId;
        } elseif ($status === 'resolved' || $status === 'closed') {
            $extra = ', resolved_at = NOW()';
        }
        $this->pdo->prepare("UPDATE fault_reports SET status = :status{$extra} WHERE id = :id")->execute($params);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('UPDATE fault_reports SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function nextNumber(): string
    {
        $year = date('Y');
        $stmt = $this->pdo->prepare("SELECT report_number FROM fault_reports WHERE report_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute(["FA-{$year}-%"]);
        $last = $stmt->fetchColumn();
        $num = $last ? (int) substr($last, -4) + 1 : 1;
        return "FA-{$year}-" . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function stats(): array
    {
        return [
            'total'        => (int) $this->pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0")->fetchColumn(),
            'reported'     => (int) $this->pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0 AND status = 'reported'")->fetchColumn(),
            'in_progress'  => (int) $this->pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0 AND status = 'in_progress'")->fetchColumn(),
            'resolved'     => (int) $this->pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0 AND status = 'resolved'")->fetchColumn(),
            'critical'     => (int) $this->pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0 AND priority = 'critical' AND status NOT IN ('resolved','closed')")->fetchColumn(),
        ];
    }

    public function allEquipment(): array
    {
        return $this->pdo->query("SELECT id, equipment_number, name FROM equipment WHERE is_deleted = 0 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
