<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class InventoryRepository
{
    public function allStock(): array
    {
        return Database::pdo()->query(
            'SELECT s.*, a.name AS article_name, a.article_number,
                    w.name AS warehouse_name
             FROM stock s
             LEFT JOIN articles a ON s.article_id = a.id
             LEFT JOIN warehouses w ON s.warehouse_id = w.id
             WHERE s.is_deleted = 0
             ORDER BY a.name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findStock(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT s.*, a.name AS article_name, a.article_number, a.unit,
                    w.name AS warehouse_name
             FROM stock s
             LEFT JOIN articles a ON s.article_id = a.id
             LEFT JOIN warehouses w ON s.warehouse_id = w.id
             WHERE s.id = ? AND s.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function transactionsForStock(int $stockId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT t.*, u.username AS created_by_name
             FROM stock_transactions t
             LEFT JOIN users u ON t.created_by = u.id
             WHERE t.stock_id = ?
             ORDER BY t.created_at DESC
             LIMIT 50'
        );
        $stmt->execute([$stockId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allWarehouses(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM warehouses WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
