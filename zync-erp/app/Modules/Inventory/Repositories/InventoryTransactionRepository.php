<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Repositories;

use App\Core\Database;
use PDO;

final class InventoryTransactionRepository
{
    public function getTransactions(): array
    {
        $stmt = Database::pdo()->query("
            SELECT
                t.*,
                a.article_number,
                a.name AS article_name,
                w.name AS warehouse_name,
                u.full_name AS created_by_name
            FROM inventory_transactions t
            LEFT JOIN articles a ON t.article_id = a.id
            LEFT JOIN warehouses w ON t.warehouse_id = w.id
            LEFT JOIN users u ON t.created_by = u.id
            ORDER BY t.created_at DESC, t.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("
            SELECT
                t.*,
                a.article_number,
                a.name AS article_name,
                a.unit,
                w.name AS warehouse_name,
                w.code AS warehouse_code,
                u.full_name AS created_by_name
            FROM inventory_transactions t
            LEFT JOIN articles a ON t.article_id = a.id
            LEFT JOIN warehouses w ON t.warehouse_id = w.id
            LEFT JOIN users u ON t.created_by = u.id
            WHERE t.id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createTransaction(array $data): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO inventory_transactions
                (article_id, warehouse_id, type, quantity, reference, comment, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['article_id'],
                $data['warehouse_id'],
                $data['type'],
                $data['quantity'],
                $data['reference'] !== '' ? $data['reference'] : null,
                $data['comment'] !== '' ? $data['comment'] : null,
                $data['created_by'],
            ]);

            $transactionId = (int) $pdo->lastInsertId();

            $stockStmt = $pdo->prepare("
                SELECT id, quantity
                FROM inventory_stock
                WHERE article_id = ? AND warehouse_id = ? AND is_deleted = 0
                LIMIT 1
            ");
            $stockStmt->execute([
                $data['article_id'],
                $data['warehouse_id'],
            ]);

            $stock = $stockStmt->fetch(PDO::FETCH_ASSOC);

            $delta = in_array($data['type'], ['in', 'receive', 'adjustment_plus'], true)
                ? $data['quantity']
                : -1 * $data['quantity'];

            if ($stock) {
                $updateStmt = $pdo->prepare("
                    UPDATE inventory_stock
                    SET quantity = quantity + ?
                    WHERE id = ?
                ");
                $updateStmt->execute([$delta, $stock['id']]);
            } else {
                $insertStockStmt = $pdo->prepare("
                    INSERT INTO inventory_stock (article_id, warehouse_id, quantity, created_by)
                    VALUES (?, ?, ?, ?)
                ");
                $insertStockStmt->execute([
                    $data['article_id'],
                    $data['warehouse_id'],
                    $delta,
                    $data['created_by'],
                ]);
            }

            $pdo->commit();

            return $transactionId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
