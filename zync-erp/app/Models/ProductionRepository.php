<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ProductionRepository
{
    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'lines'   => (int) $pdo->query('SELECT COUNT(*) FROM production_lines WHERE is_deleted = 0')->fetchColumn(),
            'orders'  => (int) $pdo->query('SELECT COUNT(*) FROM production_orders WHERE is_deleted = 0')->fetchColumn(),
            'stock'   => (int) $pdo->query('SELECT COUNT(*) FROM production_stock WHERE is_deleted = 0')->fetchColumn(),
        ];
    }

    public function recentOrders(int $limit = 5): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT o.*, l.name AS line_name
             FROM production_orders o
             LEFT JOIN production_lines l ON o.line_id = l.id
             WHERE o.is_deleted = 0
             ORDER BY o.created_at DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allLines(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM production_lines WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findLine(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM production_lines WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createLine(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_lines (name, code, description, status, created_by)
             VALUES (:name, :code, :description, :status, :created_by)'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?: null,
            'status'      => $data['status'] ?? 'active',
            'created_by'  => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateLine(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_lines SET name = :name, code = :code, description = :description,
             status = :status WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?: null,
            'status'      => $data['status'] ?? 'active',
            'id'          => $id,
        ]);
    }

    public function deleteLine(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE production_lines SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function allOrders(): array
    {
        return Database::pdo()->query(
            'SELECT o.*, l.name AS line_name
             FROM production_orders o
             LEFT JOIN production_lines l ON o.line_id = l.id
             WHERE o.is_deleted = 0
             ORDER BY o.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allStock(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM production_stock WHERE is_deleted = 0 ORDER BY location ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ─── Products ─────────────────────────────────────────────────────────

    public function allProducts(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM products WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findProduct(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM products WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createProduct(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO products
             (product_number, name, description, category, datasheet_url, composition,
              weight, weight_unit, dimensions, sku, barcode, unit_price, currency,
              production_line_id, min_stock_level, lead_time_days, status, created_by)
             VALUES
             (:product_number, :name, :description, :category, :datasheet_url, :composition,
              :weight, :weight_unit, :dimensions, :sku, :barcode, :unit_price, :currency,
              :production_line_id, :min_stock_level, :lead_time_days, :status, :created_by)'
        );
        $stmt->execute([
            'product_number'     => $data['product_number'],
            'name'               => $data['name'],
            'description'        => $data['description'] ?: null,
            'category'           => $data['category'] ?: null,
            'datasheet_url'      => $data['datasheet_url'] ?: null,
            'composition'        => $data['composition'] ?: null,
            'weight'             => $data['weight'] !== '' ? $data['weight'] : null,
            'weight_unit'        => $data['weight_unit'] ?? 'kg',
            'dimensions'         => $data['dimensions'] ?: null,
            'sku'                => $data['sku'] ?: null,
            'barcode'            => $data['barcode'] ?: null,
            'unit_price'         => $data['unit_price'] !== '' ? $data['unit_price'] : null,
            'currency'           => $data['currency'] ?? 'SEK',
            'production_line_id' => $data['production_line_id'] ?: null,
            'min_stock_level'    => $data['min_stock_level'] !== '' ? $data['min_stock_level'] : null,
            'lead_time_days'     => $data['lead_time_days'] !== '' ? $data['lead_time_days'] : null,
            'status'             => $data['status'] ?? 'active',
            'created_by'         => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateProduct(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE products SET
             product_number = :product_number, name = :name, description = :description,
             category = :category, datasheet_url = :datasheet_url, composition = :composition,
             weight = :weight, weight_unit = :weight_unit, dimensions = :dimensions,
             sku = :sku, barcode = :barcode, unit_price = :unit_price, currency = :currency,
             production_line_id = :production_line_id, min_stock_level = :min_stock_level,
             lead_time_days = :lead_time_days, status = :status
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'product_number'     => $data['product_number'],
            'name'               => $data['name'],
            'description'        => $data['description'] ?: null,
            'category'           => $data['category'] ?: null,
            'datasheet_url'      => $data['datasheet_url'] ?: null,
            'composition'        => $data['composition'] ?: null,
            'weight'             => $data['weight'] !== '' ? $data['weight'] : null,
            'weight_unit'        => $data['weight_unit'] ?? 'kg',
            'dimensions'         => $data['dimensions'] ?: null,
            'sku'                => $data['sku'] ?: null,
            'barcode'            => $data['barcode'] ?: null,
            'unit_price'         => $data['unit_price'] !== '' ? $data['unit_price'] : null,
            'currency'           => $data['currency'] ?? 'SEK',
            'production_line_id' => $data['production_line_id'] ?: null,
            'min_stock_level'    => $data['min_stock_level'] !== '' ? $data['min_stock_level'] : null,
            'lead_time_days'     => $data['lead_time_days'] !== '' ? $data['lead_time_days'] : null,
            'status'             => $data['status'] ?? 'active',
            'id'                 => $id,
        ]);
    }

    public function deleteProduct(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE products SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Orders CRUD ──────────────────────────────────────────────────────

    public function findOrder(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT o.*, l.name AS line_name
             FROM production_orders o
             LEFT JOIN production_lines l ON o.line_id = l.id
             WHERE o.id = ? AND o.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createOrder(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_orders
             (order_number, line_id, article_id, quantity, planned_start, planned_end, status, notes, created_by)
             VALUES
             (:order_number, :line_id, :article_id, :quantity, :planned_start, :planned_end, :status, :notes, :created_by)'
        );
        $stmt->execute([
            'order_number' => $data['order_number'],
            'line_id'      => $data['line_id'] ?: null,
            'article_id'   => $data['article_id'] ?: null,
            'quantity'     => $data['quantity'] ?? 0,
            'planned_start'=> $data['planned_start'] ?: null,
            'planned_end'  => $data['planned_end'] ?: null,
            'status'       => $data['status'] ?? 'planned',
            'notes'        => $data['notes'] ?: null,
            'created_by'   => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateOrder(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_orders SET
             order_number = :order_number, line_id = :line_id, article_id = :article_id,
             quantity = :quantity, planned_start = :planned_start, planned_end = :planned_end,
             status = :status, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'order_number' => $data['order_number'],
            'line_id'      => $data['line_id'] ?: null,
            'article_id'   => $data['article_id'] ?: null,
            'quantity'     => $data['quantity'] ?? 0,
            'planned_start'=> $data['planned_start'] ?: null,
            'planned_end'  => $data['planned_end'] ?: null,
            'status'       => $data['status'] ?? 'planned',
            'notes'        => $data['notes'] ?: null,
            'id'           => $id,
        ]);
    }

    public function deleteOrder(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE production_orders SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function updateOrderStatus(int $id, string $status): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_orders SET status = ? WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$status, $id]);
    }

    // ─── Stock management ─────────────────────────────────────────────────

    public function allStockLocations(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM production_stock WHERE is_deleted = 0 ORDER BY location ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findStockEntry(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM production_stock WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createStockEntry(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_stock (article_id, location, quantity, unit, created_by)
             VALUES (:article_id, :location, :quantity, :unit, :created_by)'
        );
        $stmt->execute([
            'article_id' => $data['article_id'] ?: null,
            'location'   => $data['location'] ?? '',
            'quantity'   => $data['quantity'] ?? 0,
            'unit'       => $data['unit'] ?? 'st',
            'created_by' => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function deleteStockEntry(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE production_stock SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function moveStock(int $id, string $newLocation): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_stock SET location = ? WHERE id = ?'
        );
        $stmt->execute([$newLocation, $id]);
    }
}
