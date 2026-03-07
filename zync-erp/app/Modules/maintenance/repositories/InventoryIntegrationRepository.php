<?php

namespace Modules\Maintenance\Repositories;

use PDO;
use RuntimeException;

class InventoryIntegrationRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function getWarehouseOptions(): array
    {
        $stmt = $this->db->query("
            SELECT id, name
            FROM warehouses
            ORDER BY name ASC, id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockRow(int $articleId, int $warehouseId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM inventory_stock
            WHERE article_id = :article_id
              AND warehouse_id = :warehouse_id
              AND is_deleted = 0
            LIMIT 1
        ");

        $stmt->execute([
            'article_id'   => $articleId,
            'warehouse_id' => $warehouseId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function ensureStockRowExists(int $articleId, int $warehouseId, ?int $createdBy = null): void
    {
        $existing = $this->getStockRow($articleId, $warehouseId);

        if ($existing) {
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO inventory_stock (
                created_by,
                is_deleted,
                article_id,
                warehouse_id,
                quantity
            ) VALUES (
                :created_by,
                0,
                :article_id,
                :warehouse_id,
                0
            )
        ");

        $stmt->execute([
            'created_by'   => $createdBy,
            'article_id'   => $articleId,
            'warehouse_id' => $warehouseId,
        ]);
    }

    public function getAvailableStock(int $articleId, int $warehouseId): float
    {
        $row = $this->getStockRow($articleId, $warehouseId);

        if (!$row) {
            return 0.0;
        }

        return (float) ($row['quantity'] ?? 0);
    }

    public function decreaseStock(int $articleId, int $warehouseId, float $quantity): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Kvantitet för uttag måste vara större än 0.');
        }

        $available = $this->getAvailableStock($articleId, $warehouseId);

        if ($available < $quantity) {
            throw new RuntimeException('Otillräckligt lagersaldo för uttag.');
        }

        $stmt = $this->db->prepare("
            UPDATE inventory_stock
            SET quantity = quantity - :quantity
            WHERE article_id = :article_id
              AND warehouse_id = :warehouse_id
              AND is_deleted = 0
        ");

        $stmt->execute([
            'article_id'   => $articleId,
            'warehouse_id' => $warehouseId,
            'quantity'     => $quantity,
        ]);
    }

    public function increaseStock(int $articleId, int $warehouseId, float $quantity, ?int $createdBy = null): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Kvantitet för retur måste vara större än 0.');
        }

        $this->ensureStockRowExists($articleId, $warehouseId, $createdBy);

        $stmt = $this->db->prepare("
            UPDATE inventory_stock
            SET quantity = quantity + :quantity
            WHERE article_id = :article_id
              AND warehouse_id = :warehouse_id
              AND is_deleted = 0
        ");

        $stmt->execute([
            'article_id'   => $articleId,
            'warehouse_id' => $warehouseId,
            'quantity'     => $quantity,
        ]);
    }

    public function createInventoryTransaction(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO inventory_transactions (
                created_by,
                is_deleted,
                article_id,
                warehouse_id,
                type,
                quantity,
                reference_type,
                reference_id,
                notes,
                to_warehouse_id
            ) VALUES (
                :created_by,
                0,
                :article_id,
                :warehouse_id,
                :type,
                :quantity,
                :reference_type,
                :reference_id,
                :notes,
                :to_warehouse_id
            )
        ");

        $stmt->execute([
            'created_by'      => $data['created_by'],
            'article_id'      => $data['article_id'],
            'warehouse_id'    => $data['warehouse_id'],
            'type'            => $data['type'],
            'quantity'        => $data['quantity'],
            'reference_type'  => $data['reference_type'],
            'reference_id'    => $data['reference_id'],
            'notes'           => $data['notes'],
            'to_warehouse_id' => $data['to_warehouse_id'] ?? null,
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
