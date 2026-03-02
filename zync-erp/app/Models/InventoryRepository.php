<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class InventoryRepository
{
    /* ── Warehouses ── */

    public function allWarehouses(): array
    {
        return Database::pdo()->query('SELECT * FROM warehouses WHERE is_deleted = 0 ORDER BY name')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findWarehouse(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM warehouses WHERE id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createWarehouse(array $d): int
    {
        $stmt = Database::pdo()->prepare('INSERT INTO warehouses (name, code, address, city) VALUES (?,?,?,?)');
        $stmt->execute([$d['name'], strtoupper($d['code']), $d['address'] ?: null, $d['city'] ?: null]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateWarehouse(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare('UPDATE warehouses SET name=?, code=?, address=?, city=?, is_active=? WHERE id=? AND is_deleted=0');
        $stmt->execute([$d['name'], strtoupper($d['code']), $d['address'] ?: null, $d['city'] ?: null, (int)($d['is_active'] ?? 1), $id]);
    }

    public function deleteWarehouse(int $id): void
    {
        Database::pdo()->prepare('UPDATE warehouses SET is_deleted=1 WHERE id=?')->execute([$id]);
    }

    public function warehouseCodeExists(string $code, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM warehouses WHERE code=? AND id!=? AND is_deleted=0');
            $stmt->execute([strtoupper($code), $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM warehouses WHERE code=? AND is_deleted=0');
            $stmt->execute([strtoupper($code)]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /* ── Stock overview ── */

    public function stockOverview(?int $warehouseId = null, ?string $search = null): array
    {
        $sql = '
            SELECT a.id AS article_id, a.article_number, a.name AS article_name,
                   a.unit, a.category, a.purchase_price, a.selling_price,
                   w.id AS warehouse_id, w.name AS warehouse_name, w.code AS warehouse_code,
                   COALESCE(s.quantity, 0) AS quantity,
                   s.min_quantity, s.max_quantity
            FROM articles a
            CROSS JOIN warehouses w
            LEFT JOIN stock s ON s.article_id = a.id AND s.warehouse_id = w.id
            WHERE a.is_deleted = 0 AND a.is_active = 1
              AND w.is_deleted = 0 AND w.is_active = 1
        ';
        $params = [];

        if ($warehouseId) {
            $sql .= ' AND w.id = ?';
            $params[] = $warehouseId;
        }
        if ($search) {
            $sql .= ' AND (a.name LIKE ? OR a.article_number LIKE ? OR a.category LIKE ?)';
            $like = '%' . $search . '%';
            $params = array_merge($params, [$like, $like, $like]);
        }

        $sql .= ' ORDER BY a.name, w.name';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Grouped by article with total quantity across warehouses */
    public function stockSummary(?string $search = null): array
    {
        $sql = '
            SELECT a.id, a.article_number, a.name, a.unit, a.category,
                   a.purchase_price, a.selling_price,
                   COALESCE(SUM(s.quantity), 0) AS total_quantity,
                   MIN(s.min_quantity) AS min_quantity
            FROM articles a
            LEFT JOIN stock s ON s.article_id = a.id
            WHERE a.is_deleted = 0 AND a.is_active = 1
        ';
        $params = [];

        if ($search) {
            $sql .= ' AND (a.name LIKE ? OR a.article_number LIKE ? OR a.category LIKE ?)';
            $like = '%' . $search . '%';
            $params = array_merge($params, [$like, $like, $like]);
        }

        $sql .= ' GROUP BY a.id ORDER BY a.name';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /* ── Stock movements ── */

    public function adjustStock(int $articleId, int $warehouseId, float $qty, string $type, ?string $note, ?int $userId): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            // Upsert stock row
            $stmt = $pdo->prepare('
                INSERT INTO stock (article_id, warehouse_id, quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
            ');
            $delta = in_array($type, ['in', 'adjust']) ? $qty : -$qty;
            $stmt->execute([$articleId, $warehouseId, $delta]);

            // Log transaction
            $stmt = $pdo->prepare('
                INSERT INTO stock_transactions (article_id, warehouse_id, type, quantity, note, created_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$articleId, $warehouseId, $type, $qty, $note ?: null, $userId]);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function setMinMax(int $articleId, int $warehouseId, ?float $min, ?float $max): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('
            INSERT INTO stock (article_id, warehouse_id, quantity, min_quantity, max_quantity)
            VALUES (?, ?, 0, ?, ?)
            ON DUPLICATE KEY UPDATE min_quantity = VALUES(min_quantity), max_quantity = VALUES(max_quantity)
        ');
        $stmt->execute([$articleId, $warehouseId, $min, $max]);
    }

    /** Transaction history */
    public function transactions(?int $articleId = null, ?int $warehouseId = null, int $limit = 100): array
    {
        $sql = '
            SELECT t.*, a.article_number, a.name AS article_name, a.unit,
                   w.name AS warehouse_name, w.code AS warehouse_code,
                   CONCAT(u.full_name) AS user_name
            FROM stock_transactions t
            JOIN articles a ON t.article_id = a.id
            JOIN warehouses w ON t.warehouse_id = w.id
            LEFT JOIN users u ON t.created_by = u.id
            WHERE 1=1
        ';
        $params = [];

        if ($articleId) {
            $sql .= ' AND t.article_id = ?';
            $params[] = $articleId;
        }
        if ($warehouseId) {
            $sql .= ' AND t.warehouse_id = ?';
            $params[] = $warehouseId;
        }

        $sql .= ' ORDER BY t.created_at DESC LIMIT ?';
        $params[] = $limit;

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /* ── Stats ── */

    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'total_articles'  => (int) $pdo->query("SELECT COUNT(*) FROM articles WHERE is_deleted=0 AND is_active=1")->fetchColumn(),
            'total_value'     => (float) $pdo->query("SELECT COALESCE(SUM(s.quantity * a.purchase_price), 0) FROM stock s JOIN articles a ON s.article_id = a.id WHERE a.is_deleted=0")->fetchColumn(),
            'low_stock'       => (int) $pdo->query("SELECT COUNT(*) FROM stock s WHERE s.min_quantity IS NOT NULL AND s.quantity < s.min_quantity")->fetchColumn(),
            'warehouses'      => (int) $pdo->query("SELECT COUNT(*) FROM warehouses WHERE is_deleted=0 AND is_active=1")->fetchColumn(),
        ];
    }

    /** Articles for dropdown */
    public function allArticles(): array
    {
        return Database::pdo()->query("SELECT id, article_number, name, unit FROM articles WHERE is_deleted=0 AND is_active=1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
