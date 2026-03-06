<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Repositories;

use App\Core\Database;
use PDO;
use RuntimeException;

final class InventoryReceivingRepository
{
    public function getReceivablePurchaseOrders(): array
    {
        $stmt = Database::pdo()->query("
            SELECT
                po.*,
                s.name AS supplier_name
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.id
            WHERE po.is_deleted = 0
              AND po.status IN ('approved', 'ordered', 'partially_received')
            ORDER BY po.created_at DESC, po.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPurchaseOrderForReceiving(int $poId): ?array
    {
        $stmt = Database::pdo()->prepare("
            SELECT
                po.*,
                s.name AS supplier_name
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.id
            WHERE po.id = ?
              AND po.is_deleted = 0
            LIMIT 1
        ");

        $stmt->execute([$poId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getPurchaseOrderLines(int $poId): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT *
            FROM purchase_order_lines
            WHERE order_id = ?
            ORDER BY id ASC
        ");

        $stmt->execute([$poId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receivePurchaseOrder(int $poId, int $warehouseId, array $lines, int $userId): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            foreach ($lines as $row) {
                $lineId = (int) $row['line_id'];
                $receivedQty = (float) $row['received_quantity'];

                $lineStmt = $pdo->prepare("
                    SELECT *
                    FROM purchase_order_lines
                    WHERE id = ? AND order_id = ?
                    LIMIT 1
                ");
                $lineStmt->execute([$lineId, $poId]);
                $line = $lineStmt->fetch(PDO::FETCH_ASSOC);

                if (!$line) {
                    throw new RuntimeException("Inköpsorderrad saknas: {$lineId}");
                }

                $articleId = (int) ($line['article_id'] ?? 0);

                if ($articleId <= 0) {
                    throw new RuntimeException("Artikel saknas på inköpsorderrad: {$lineId}");
                }

                $insertTransaction = $pdo->prepare("
                    INSERT INTO inventory_transactions
                    (article_id, warehouse_id, type, quantity, reference, comment, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $insertTransaction->execute([
                    $articleId,
                    $warehouseId,
                    'receive',
                    $receivedQty,
                    'PO-' . $poId,
                    'Inleverans från inköpsorder',
                    $userId > 0 ? $userId : null,
                ]);

                $stockStmt = $pdo->prepare("
                    SELECT id, quantity
                    FROM inventory_stock
                    WHERE article_id = ? AND warehouse_id = ? AND is_deleted = 0
                    LIMIT 1
                ");
                $stockStmt->execute([$articleId, $warehouseId]);
                $stock = $stockStmt->fetch(PDO::FETCH_ASSOC);

                if ($stock) {
                    $updateStock = $pdo->prepare("
                        UPDATE inventory_stock
                        SET quantity = quantity + ?
                        WHERE id = ?
                    ");
                    $updateStock->execute([$receivedQty, $stock['id']]);
                } else {
                    $insertStock = $pdo->prepare("
                        INSERT INTO inventory_stock (article_id, warehouse_id, quantity, created_by)
                        VALUES (?, ?, ?, ?)
                    ");
                    $insertStock->execute([
                        $articleId,
                        $warehouseId,
                        $receivedQty,
                        $userId > 0 ? $userId : null,
                    ]);
                }
            }

            $statusStmt = $pdo->prepare("
                UPDATE purchase_orders
                SET status = 'partially_received'
                WHERE id = ?
            ");
            $statusStmt->execute([$poId]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
