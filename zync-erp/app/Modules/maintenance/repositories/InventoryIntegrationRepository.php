<?php

namespace Modules\Maintenance\Repositories;

use PDO;
use RuntimeException;

class InventoryIntegrationRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function getStockRowForArticle(int $tenantId, int $articleId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM inventory_stock
            WHERE tenant_id = :tenant_id
              AND article_id = :article_id
            LIMIT 1
        ");

        $stmt->execute([
            'tenant_id'  => $tenantId,
            'article_id' => $articleId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function ensureStockRowExists(int $tenantId, int $articleId): void
    {
        $existing = $this->getStockRowForArticle($tenantId, $articleId);

        if ($existing) {
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO inventory_stock (
                tenant_id,
                article_id,
                quantity
            ) VALUES (
                :tenant_id,
                :article_id,
                0
            )
        ");

        $stmt->execute([
            'tenant_id'  => $tenantId,
            'article_id' => $articleId,
        ]);
    }

    public function getAvailableStock(int $tenantId, int $articleId): float
    {
        $row = $this->getStockRowForArticle($tenantId, $articleId);

        if (!$row) {
            return 0.0;
        }

        return (float) ($row['quantity'] ?? 0);
    }

    public function decreaseStock(int $tenantId, int $articleId, float $quantity): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Kvantitet för uttag måste vara större än 0.');
        }

        $available = $this->getAvailableStock($tenantId, $articleId);

        if ($available < $quantity) {
            throw new RuntimeException('Otillräckligt lagersaldo för uttag.');
        }

        $stmt = $this->db->prepare("
            UPDATE inventory_stock
            SET quantity = quantity - :quantity
            WHERE tenant_id = :tenant_id
              AND article_id = :article_id
        ");

        $stmt->execute([
            'tenant_id'  => $tenantId,
            'article_id' => $articleId,
            'quantity'   => $quantity,
        ]);
    }

    public function increaseStock(int $tenantId, int $articleId, float $quantity): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Kvantitet för retur måste vara större än 0.');
        }

        $this->ensureStockRowExists($tenantId, $articleId);

        $stmt = $this->db->prepare("
            UPDATE inventory_stock
            SET quantity = quantity + :quantity
            WHERE tenant_id = :tenant_id
              AND article_id = :article_id
        ");

        $stmt->execute([
            'tenant_id'  => $tenantId,
            'article_id' => $articleId,
            'quantity'   => $quantity,
        ]);
    }

    public function createInventoryTransaction(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO inventory_transactions (
                tenant_id,
                article_id,
                transaction_type,
                quantity,
                notes,
                created_by,
                created_at
            ) VALUES (
                :tenant_id,
                :article_id,
                :transaction_type,
                :quantity,
                :notes,
                :created_by,
                NOW()
            )
        ");

        $stmt->execute([
            'tenant_id'        => $data['tenant_id'],
            'article_id'       => $data['article_id'],
            'transaction_type' => $data['transaction_type'],
            'quantity'         => $data['quantity'],
            'notes'            => $data['notes'],
            'created_by'       => $data['created_by'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createMaintenanceMovement(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_material_inventory_movements (
                tenant_id,
                work_order_id,
                material_id,
                article_id,
                movement_type,
                quantity,
                inventory_transaction_id,
                created_by
            ) VALUES (
                :tenant_id,
                :work_order_id,
                :material_id,
                :article_id,
                :movement_type,
                :quantity,
                :inventory_transaction_id,
                :created_by
            )
        ");

        $stmt->execute([
            'tenant_id'                => $data['tenant_id'],
            'work_order_id'            => $data['work_order_id'],
            'material_id'              => $data['material_id'],
            'article_id'               => $data['article_id'],
            'movement_type'            => $data['movement_type'],
            'quantity'                 => $data['quantity'],
            'inventory_transaction_id' => $data['inventory_transaction_id'],
            'created_by'               => $data['created_by'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function movementsByWorkOrder(int $tenantId, int $workOrderId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                m.*,
                a.article_number,
                a.name AS article_name
            FROM maintenance_material_inventory_movements m
            INNER JOIN articles a
                ON a.id = m.article_id
            WHERE m.tenant_id = :tenant_id
              AND m.work_order_id = :work_order_id
            ORDER BY m.created_at DESC, m.id DESC
        ");

        $stmt->execute([
            'tenant_id'     => $tenantId,
            'work_order_id' => $workOrderId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
