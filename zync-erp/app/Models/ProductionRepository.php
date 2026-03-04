<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class ProductionRepository
{
    // ─── Production Lines ─────────────────────────────────────

    public function allLines(): array
    {
        $stmt = Database::pdo()->query('SELECT * FROM production_lines WHERE is_deleted = 0 ORDER BY sort_order ASC, id DESC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findLine(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM production_lines WHERE id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createLine(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_lines (name, description, department_id, equipment_id, capacity, status, sort_order)
             VALUES (:name, :description, :department_id, :equipment_id, :capacity, :status, :sort_order)'
        );
        $stmt->execute([
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'department_id' => $data['department_id'] ?: null,
            'equipment_id'  => $data['equipment_id'] ?: null,
            'capacity'      => $data['capacity'] ?? null,
            'status'        => $data['status'] ?? 'active',
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateLine(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_lines SET name = :name, description = :description, department_id = :department_id,
             equipment_id = :equipment_id, capacity = :capacity, status = :status, sort_order = :sort_order
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'department_id' => $data['department_id'] ?: null,
            'equipment_id'  => $data['equipment_id'] ?: null,
            'capacity'      => $data['capacity'] ?? null,
            'status'        => $data['status'] ?? 'active',
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
            'id'            => $id,
        ]);
    }

    public function deleteLine(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE production_lines SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Production Orders ────────────────────────────────────

    public function allOrders(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT po.*, pl.name AS line_name
             FROM production_orders po
             LEFT JOIN production_lines pl ON pl.id = po.production_line_id
             WHERE po.is_deleted = 0
             ORDER BY po.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findOrder(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT po.*, pl.name AS line_name
             FROM production_orders po
             LEFT JOIN production_lines pl ON pl.id = po.production_line_id
             WHERE po.id = ? AND po.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createOrder(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_orders (order_number, product_name, production_line_id, quantity, unit,
             planned_start, planned_end, status, priority, notes)
             VALUES (:order_number, :product_name, :production_line_id, :quantity, :unit,
             :planned_start, :planned_end, :status, :priority, :notes)'
        );
        $stmt->execute([
            'order_number'       => $data['order_number'],
            'product_name'       => $data['product_name'],
            'production_line_id' => $data['production_line_id'] ?: null,
            'quantity'           => $data['quantity'],
            'unit'               => $data['unit'] ?? 'st',
            'planned_start'      => $data['planned_start'] ?: null,
            'planned_end'        => $data['planned_end'] ?: null,
            'status'             => $data['status'] ?? 'planned',
            'priority'           => $data['priority'] ?? 'normal',
            'notes'              => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateOrder(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_orders SET order_number = :order_number, product_name = :product_name,
             production_line_id = :production_line_id, quantity = :quantity, unit = :unit,
             planned_start = :planned_start, planned_end = :planned_end, actual_start = :actual_start,
             actual_end = :actual_end, status = :status, priority = :priority, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'order_number'       => $data['order_number'],
            'product_name'       => $data['product_name'],
            'production_line_id' => $data['production_line_id'] ?: null,
            'quantity'           => $data['quantity'],
            'unit'               => $data['unit'] ?? 'st',
            'planned_start'      => $data['planned_start'] ?: null,
            'planned_end'        => $data['planned_end'] ?: null,
            'actual_start'       => $data['actual_start'] ?: null,
            'actual_end'         => $data['actual_end'] ?: null,
            'status'             => $data['status'] ?? 'planned',
            'priority'           => $data['priority'] ?? 'normal',
            'notes'              => $data['notes'] ?? null,
            'id'                 => $id,
        ]);
    }

    public function deleteOrder(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE production_orders SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Production Stock ─────────────────────────────────────

    public function allStock(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT ps.*, a.name AS article_name, a.article_number
             FROM production_stock ps
             LEFT JOIN articles a ON a.id = ps.article_id
             WHERE ps.is_deleted = 0
             ORDER BY ps.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createStockItem(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_stock (article_id, warehouse_type, quantity, unit, location, min_stock_level, notes)
             VALUES (:article_id, :warehouse_type, :quantity, :unit, :location, :min_stock_level, :notes)'
        );
        $stmt->execute([
            'article_id'      => $data['article_id'] ?: null,
            'warehouse_type'  => $data['warehouse_type'] ?? 'raw_material',
            'quantity'        => $data['quantity'] ?? 0,
            'unit'            => $data['unit'] ?? 'st',
            'location'        => $data['location'] ?? null,
            'min_stock_level' => $data['min_stock_level'] ?: null,
            'notes'           => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateStockItem(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_stock SET article_id = :article_id, warehouse_type = :warehouse_type,
             quantity = :quantity, unit = :unit, location = :location, min_stock_level = :min_stock_level,
             notes = :notes WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'article_id'      => $data['article_id'] ?: null,
            'warehouse_type'  => $data['warehouse_type'] ?? 'raw_material',
            'quantity'        => $data['quantity'] ?? 0,
            'unit'            => $data['unit'] ?? 'st',
            'location'        => $data['location'] ?? null,
            'min_stock_level' => $data['min_stock_level'] ?: null,
            'notes'           => $data['notes'] ?? null,
            'id'              => $id,
        ]);
    }
}
