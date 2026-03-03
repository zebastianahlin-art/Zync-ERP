<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class ProductionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Dashboard stats ──────────────────────────────

    public function stats(): array
    {
        $s = [];

        $stmt = $this->db->query("SELECT COUNT(*) FROM production_lines WHERE is_deleted = 0");
        $s['total_lines'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM production_lines WHERE status = 'active' AND is_deleted = 0");
        $s['active_lines'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM production_orders WHERE is_deleted = 0");
        $s['total_orders'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM production_orders WHERE status = 'in_progress' AND is_deleted = 0");
        $s['in_progress'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM production_orders WHERE status = 'planned' AND is_deleted = 0");
        $s['planned'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM production_orders WHERE status = 'completed' AND is_deleted = 0");
        $s['completed'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COALESCE(SUM(quantity_produced), 0) FROM production_orders WHERE is_deleted = 0");
        $s['total_produced'] = (float) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COALESCE(SUM(quantity_scrapped), 0) FROM production_orders WHERE is_deleted = 0");
        $s['total_scrapped'] = (float) $stmt->fetchColumn();

        return $s;
    }

    // ── Lines ────────────────────────────────────────

    public function allLines(): array
    {
        $sql = "SELECT pl.*, d.name AS department_name, m.name AS machine_name
                FROM production_lines pl
                LEFT JOIN departments d ON d.id = pl.department_id
                LEFT JOIN machines m ON m.id = pl.machine_id
                WHERE pl.is_deleted = 0
                ORDER BY pl.sort_order, pl.name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findLine(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM production_lines WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createLine(array $data): int
    {
        $sql = "INSERT INTO production_lines (name, code, machine_id, department_id, description, capacity_per_hour, status, sort_order, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->prepare($sql)->execute([
            $data['name'],
            $data['code'],
            $data['machine_id'] ?: null,
            $data['department_id'] ?: null,
            $data['description'] ?: null,
            $data['capacity_per_hour'] ?: null,
            $data['status'] ?? 'active',
            (int) ($data['sort_order'] ?? 0),
            $data['created_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateLine(int $id, array $data): void
    {
        $sql = "UPDATE production_lines SET name=?, code=?, machine_id=?, department_id=?, description=?, capacity_per_hour=?, status=?, sort_order=? WHERE id=?";
        $this->db->prepare($sql)->execute([
            $data['name'],
            $data['code'],
            $data['machine_id'] ?: null,
            $data['department_id'] ?: null,
            $data['description'] ?: null,
            $data['capacity_per_hour'] ?: null,
            $data['status'] ?? 'active',
            (int) ($data['sort_order'] ?? 0),
            $id,
        ]);
    }

    public function deleteLine(int $id): void
    {
        $this->db->prepare("UPDATE production_lines SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function activeLines(): array
    {
        $sql = "SELECT id, name, code FROM production_lines WHERE status = 'active' AND is_deleted = 0 ORDER BY sort_order, name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Orders ───────────────────────────────────────

    public function allOrders(?string $status = null): array
    {
        $sql = "SELECT po.*, pl.name AS line_name, a.name AS article_name, a.article_number,
                       CONCAT(e.first_name, ' ', e.last_name) AS assigned_name
                FROM production_orders po
                LEFT JOIN production_lines pl ON pl.id = po.line_id
                LEFT JOIN articles a ON a.id = po.article_id
                LEFT JOIN employees e ON e.id = po.assigned_to
                WHERE po.is_deleted = 0";
        $params = [];
        if ($status) {
            $sql .= " AND po.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY FIELD(po.priority, 'urgent','high','normal','low'), po.planned_start ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOrder(int $id): ?array
    {
        $sql = "SELECT po.*, pl.name AS line_name, a.name AS article_name, a.article_number,
                       CONCAT(e.first_name, ' ', e.last_name) AS assigned_name
                FROM production_orders po
                LEFT JOIN production_lines pl ON pl.id = po.line_id
                LEFT JOIN articles a ON a.id = po.article_id
                LEFT JOIN employees e ON e.id = po.assigned_to
                WHERE po.id = ? AND po.is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function nextOrderNumber(): string
    {
        $year = date('y');
        $stmt = $this->db->query("SELECT order_number FROM production_orders WHERE order_number LIKE 'PO{$year}%' ORDER BY id DESC LIMIT 1");
        $last = $stmt->fetchColumn();
        if ($last) {
            $seq = (int) substr($last, 4) + 1;
        } else {
            $seq = 1;
        }
        return 'PO' . $year . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function createOrder(array $data): int
    {
        $sql = "INSERT INTO production_orders (order_number, line_id, article_id, quantity_planned, unit, status, priority, planned_start, planned_end, description, notes, assigned_to, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->prepare($sql)->execute([
            $data['order_number'],
            $data['line_id'] ?: null,
            $data['article_id'] ?: null,
            $data['quantity_planned'] ?? 0,
            $data['unit'] ?? 'st',
            $data['status'] ?? 'draft',
            $data['priority'] ?? 'normal',
            $data['planned_start'] ?: null,
            $data['planned_end'] ?: null,
            $data['description'] ?: null,
            $data['notes'] ?: null,
            $data['assigned_to'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateOrder(int $id, array $data): void
    {
        $sql = "UPDATE production_orders SET line_id=?, article_id=?, quantity_planned=?, unit=?, status=?, priority=?, planned_start=?, planned_end=?, description=?, notes=?, assigned_to=? WHERE id=?";
        $this->db->prepare($sql)->execute([
            $data['line_id'] ?: null,
            $data['article_id'] ?: null,
            $data['quantity_planned'] ?? 0,
            $data['unit'] ?? 'st',
            $data['status'] ?? 'draft',
            $data['priority'] ?? 'normal',
            $data['planned_start'] ?: null,
            $data['planned_end'] ?: null,
            $data['description'] ?: null,
            $data['notes'] ?: null,
            $data['assigned_to'] ?: null,
            $id,
        ]);
    }

    public function updateOrderStatus(int $id, string $status): void
    {
        $extra = '';
        if ($status === 'in_progress') {
            $extra = ', actual_start = COALESCE(actual_start, NOW())';
        } elseif ($status === 'completed' || $status === 'cancelled') {
            $extra = ', actual_end = NOW()';
        }
        $this->db->prepare("UPDATE production_orders SET status = ? {$extra} WHERE id = ?")->execute([$status, $id]);
    }

    public function deleteOrder(int $id): void
    {
        $this->db->prepare("UPDATE production_orders SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    // ── BOM ──────────────────────────────────────────

    public function getBom(int $productArticleId): array
    {
        $sql = "SELECT b.*, a.name AS material_name, a.article_number, a.unit AS material_unit
                FROM production_bom b
                JOIN articles a ON a.id = b.material_article_id
                WHERE b.product_article_id = ?
                ORDER BY b.sort_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productArticleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addBomLine(int $productId, int $materialId, float $qty, string $unit): int
    {
        $sql = "INSERT INTO production_bom (product_article_id, material_article_id, quantity_per_unit, unit) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity_per_unit = VALUES(quantity_per_unit)";
        $this->db->prepare($sql)->execute([$productId, $materialId, $qty, $unit]);
        return (int) $this->db->lastInsertId();
    }

    public function removeBomLine(int $bomId): void
    {
        $this->db->prepare("DELETE FROM production_bom WHERE id = ?")->execute([$bomId]);
    }

    // ── Production Log ───────────────────────────────

    public function getLog(int $orderId): array
    {
        $sql = "SELECT pl.*, CONCAT(e.first_name, ' ', e.last_name) AS logged_by_name
                FROM production_log pl
                LEFT JOIN employees e ON e.id = pl.logged_by
                WHERE pl.order_id = ?
                ORDER BY pl.logged_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addLog(array $data): int
    {
        $sql = "INSERT INTO production_log (order_id, line_id, quantity_good, quantity_scrap, shift, logged_by, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->prepare($sql)->execute([
            $data['order_id'],
            $data['line_id'] ?? null,
            $data['quantity_good'] ?? 0,
            $data['quantity_scrap'] ?? 0,
            $data['shift'] ?? null,
            $data['logged_by'] ?? null,
            $data['notes'] ?? null,
        ]);

        // Uppdatera totaler på ordern
        $this->db->prepare("UPDATE production_orders SET
            quantity_produced = quantity_produced + ?,
            quantity_scrapped = quantity_scrapped + ?
            WHERE id = ?")->execute([
            $data['quantity_good'] ?? 0,
            $data['quantity_scrap'] ?? 0,
            $data['order_id'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ── Material Usage ───────────────────────────────

    public function getMaterialUsage(int $orderId): array
    {
        $sql = "SELECT mu.*, a.name AS article_name, a.article_number, w.name AS warehouse_name
                FROM production_material_usage mu
                JOIN articles a ON a.id = mu.article_id
                LEFT JOIN warehouses w ON w.id = mu.warehouse_id
                WHERE mu.order_id = ?
                ORDER BY mu.created_at";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMaterialUsage(array $data): int
    {
        $sql = "INSERT INTO production_material_usage (order_id, article_id, quantity_planned, quantity_used, warehouse_id, registered_by)
                VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->prepare($sql)->execute([
            $data['order_id'],
            $data['article_id'],
            $data['quantity_planned'] ?? 0,
            $data['quantity_used'] ?? 0,
            $data['warehouse_id'] ?? null,
            $data['registered_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    // ── Time Log ─────────────────────────────────────

    public function getTimeLog(int $orderId): array
    {
        $sql = "SELECT tl.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name
                FROM production_time_log tl
                LEFT JOIN employees e ON e.id = tl.employee_id
                WHERE tl.order_id = ?
                ORDER BY tl.start_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTimeLog(array $data): int
    {
        $sql = "INSERT INTO production_time_log (order_id, employee_id, start_time, end_time, break_minutes, description)
                VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->prepare($sql)->execute([
            $data['order_id'],
            $data['employee_id'] ?? null,
            $data['start_time'],
            $data['end_time'] ?? null,
            $data['break_minutes'] ?? 0,
            $data['description'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
