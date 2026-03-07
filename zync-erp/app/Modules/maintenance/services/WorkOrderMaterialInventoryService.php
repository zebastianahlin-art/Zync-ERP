<?php

namespace Modules\Maintenance\Services;

use Modules\Maintenance\Repositories\InventoryIntegrationRepository;
use Modules\Maintenance\Repositories\WorkOrderRepository;
use PDO;
use RuntimeException;

class WorkOrderMaterialInventoryService
{
    public function __construct(
        private PDO $db,
        private WorkOrderRepository $workOrderRepository,
        private InventoryIntegrationRepository $inventoryRepository
    ) {
    }

    public function syncIssuedQuantity(
        int $tenantId,
        int $materialId,
        float $newIssuedQuantity,
        ?int $userId = null
    ): void {
        $material = $this->workOrderRepository->findMaterialById($tenantId, $materialId);

        if (!$material) {
            throw new RuntimeException('Materialrad hittades inte.');
        }

        $warehouseId = (int) ($material['warehouse_id'] ?? 0);
        if ($warehouseId <= 0) {
            throw new RuntimeException('Materialraden saknar lager.');
        }

        $currentIssued = (float) $material['issued_quantity'];
        $delta = round($newIssuedQuantity - $currentIssued, 2);

        if ($delta == 0.0) {
            return;
        }

        if ($this->db->inTransaction()) {
            $this->performSync($tenantId, $material, $delta, $newIssuedQuantity, $userId);
            return;
        }

        $this->db->beginTransaction();

        try {
            $this->performSync($tenantId, $material, $delta, $newIssuedQuantity, $userId);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function performSync(
        int $tenantId,
        array $material,
        float $delta,
        float $newIssuedQuantity,
        ?int $userId
    ): void {
        if ($delta > 0) {
            $this->handleIssue($tenantId, $material, $delta, $userId);
        } else {
            $this->handleReturn($tenantId, $material, abs($delta), $userId);
        }

        $this->workOrderRepository->updateMaterial($tenantId, (int) $material['id'], [
            'warehouse_id'     => (int) $material['warehouse_id'],
            'planned_quantity' => (float) $material['planned_quantity'],
            'issued_quantity'  => $newIssuedQuantity,
            'unit_cost'        => (float) $material['unit_cost'],
            'notes'            => (string) ($material['notes'] ?? ''),
        ]);
    }

    private function handleIssue(int $tenantId, array $material, float $quantity, ?int $userId): void
    {
        $articleId = (int) $material['article_id'];
        $warehouseId = (int) $material['warehouse_id'];
        $workOrderId = (int) $material['work_order_id'];
        $materialId = (int) $material['id'];

        $available = $this->inventoryRepository->getAvailableStock($articleId, $warehouseId);

        if ($available < $quantity) {
            throw new RuntimeException('Otillräckligt saldo i valt lager för detta uttag.');
        }

        $this->inventoryRepository->decreaseStock($articleId, $warehouseId, $quantity);

        $transactionId = $this->inventoryRepository->createInventoryTransaction([
            'created_by'     => $userId,
            'article_id'     => $articleId,
            'warehouse_id'   => $warehouseId,
            'type'           => 'issue',
            'quantity'       => $quantity,
            'reference_type' => 'maintenance_work_order',
            'reference_id'   => $workOrderId,
            'notes'          => 'Uttag till arbetsorder #' . $workOrderId . ', materialrad #' . $materialId,
        ]);

        $this->inventoryRepository->createMaintenanceMovement([
            'tenant_id'                => $tenantId,
            'work_order_id'            => $workOrderId,
            'material_id'              => $materialId,
            'article_id'               => $articleId,
            'movement_type'            => 'issue',
            'quantity'                 => $quantity,
            'inventory_transaction_id' => $transactionId,
            'created_by'               => $userId,
        ]);
    }

    private function handleReturn(int $tenantId, array $material, float $quantity, ?int $userId): void
    {
        $articleId = (int) $material['article_id'];
        $warehouseId = (int) $material['warehouse_id'];
        $workOrderId = (int) $material['work_order_id'];
        $materialId = (int) $material['id'];

        $this->inventoryRepository->increaseStock($articleId, $warehouseId, $quantity, $userId);

        $transactionId = $this->inventoryRepository->createInventoryTransaction([
            'created_by'     => $userId,
            'article_id'     => $articleId,
            'warehouse_id'   => $warehouseId,
            'type'           => 'receipt',
            'quantity'       => $quantity,
            'reference_type' => 'maintenance_work_order_return',
            'reference_id'   => $workOrderId,
            'notes'          => 'Retur från arbetsorder #' . $workOrderId . ', materialrad #' . $materialId,
        ]);

        $this->inventoryRepository->createMaintenanceMovement([
            'tenant_id'                => $tenantId,
            'work_order_id'            => $workOrderId,
            'material_id'              => $materialId,
            'article_id'               => $articleId,
            'movement_type'            => 'return',
            'quantity'                 => $quantity,
            'inventory_transaction_id' => $transactionId,
            'created_by'               => $userId,
        ]);
    }
}
