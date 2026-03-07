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

        $currentIssued = (float) $material['issued_quantity'];
        $delta = round($newIssuedQuantity - $currentIssued, 2);

        if ($delta == 0.0) {
            return;
        }

        $this->db->beginTransaction();

        try {
            if ($delta > 0) {
                $this->handleIssue(
                    tenantId: $tenantId,
                    material: $material,
                    quantity: $delta,
                    userId: $userId
                );
            } else {
                $this->handleReturn(
                    tenantId: $tenantId,
                    material: $material,
                    quantity: abs($delta),
                    userId: $userId
                );
            }

            $this->workOrderRepository->updateMaterial($tenantId, $materialId, [
                'planned_quantity' => (float) $material['planned_quantity'],
                'issued_quantity'  => $newIssuedQuantity,
                'unit_cost'        => (float) $material['unit_cost'],
                'notes'            => (string) ($material['notes'] ?? ''),
            ]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function handleIssue(int $tenantId, array $material, float $quantity, ?int $userId): void
    {
        $articleId = (int) $material['article_id'];
        $workOrderId = (int) $material['work_order_id'];
        $materialId = (int) $material['id'];

        $available = $this->inventoryRepository->getAvailableStock($tenantId, $articleId);

        if ($available < $quantity) {
            throw new RuntimeException('Otillräckligt saldo i lager för detta uttag.');
        }

        $this->inventoryRepository->decreaseStock($tenantId, $articleId, $quantity);

        $transactionId = $this->inventoryRepository->createInventoryTransaction([
            'tenant_id'        => $tenantId,
            'article_id'       => $articleId,
            'transaction_type' => 'maintenance_issue',
            'quantity'         => $quantity,
            'notes'            => 'Uttag till arbetsorder #' . $workOrderId . ', materialrad #' . $materialId,
            'created_by'       => $userId,
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
        $workOrderId = (int) $material['work_order_id'];
        $materialId = (int) $material['id'];

        $this->inventoryRepository->increaseStock($tenantId, $articleId, $quantity);

        $transactionId = $this->inventoryRepository->createInventoryTransaction([
            'tenant_id'        => $tenantId,
            'article_id'       => $articleId,
            'transaction_type' => 'maintenance_return',
            'quantity'         => $quantity,
            'notes'            => 'Retur från arbetsorder #' . $workOrderId . ', materialrad #' . $materialId,
            'created_by'       => $userId,
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
