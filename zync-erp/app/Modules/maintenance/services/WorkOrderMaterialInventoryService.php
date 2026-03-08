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

        $currentIssued = (float) ($material['issued_quantity'] ?? 0);
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

    public function reserveMaterialLine(
        int $tenantId,
        int $materialId,
        int $warehouseId,
        float $quantity,
        ?int $userId = null
    ): void {
        if ($quantity <= 0) {
            throw new RuntimeException('Reservationsantal måste vara större än 0.');
        }

        $material = $this->workOrderRepository->findMaterialById($tenantId, $materialId);

        if (!$material) {
            throw new RuntimeException('Materialrad hittades inte.');
        }

        $plannedQuantity = (float) ($material['planned_quantity'] ?? 0);
        $reservedQuantity = (float) ($material['reserved_quantity'] ?? 0);
        $articleId = (int) ($material['article_id'] ?? 0);

        if ($articleId <= 0) {
            throw new RuntimeException('Materialraden saknar artikel.');
        }

        if ($warehouseId <= 0) {
            throw new RuntimeException('Lager måste anges.');
        }

        $available = $this->inventoryRepository->getAvailableStock($tenantId, $articleId, $warehouseId);

        if ($available < $quantity) {
            throw new RuntimeException('Otillräckligt saldo i valt lager för reservation.');
        }

        $newReservedQuantity = round($reservedQuantity + $quantity, 2);

        if ($newReservedQuantity > $plannedQuantity) {
            throw new RuntimeException('Reservation kan inte överstiga planerat antal.');
        }

        $reservationStatus = $this->calculateReservationStatus($newReservedQuantity, $plannedQuantity);

        $this->workOrderRepository->updateMaterial($tenantId, $materialId, [
            'warehouse_id'        => $warehouseId,
            'planned_quantity'    => (float) $material['planned_quantity'],
            'reserved_quantity'   => $newReservedQuantity,
            'issued_quantity'     => (float) ($material['issued_quantity'] ?? 0),
            'returned_quantity'   => (float) ($material['returned_quantity'] ?? 0),
            'reservation_status'  => $reservationStatus,
            'stock_status'        => (string) ($material['stock_status'] ?? 'not_issued'),
            'unit_cost'           => (float) $material['unit_cost'],
            'notes'               => (string) ($material['notes'] ?? ''),
        ]);

        $this->workOrderRepository->addLog([
            'tenant_id'     => $tenantId,
            'work_order_id' => (int) $material['work_order_id'],
            'log_type'      => 'material_reserved',
            'message'       => 'Material reserverat: ' . number_format($quantity, 2, '.', '') .
                ' st av artikel #' . $articleId . ' från lager #' . $warehouseId . '.',
            'hours_spent'   => 0,
            'created_by'    => $userId,
        ]);
    }

    public function issueMaterialLine(
        int $tenantId,
        int $materialId,
        float $quantity,
        ?int $userId = null
    ): void {
        if ($quantity <= 0) {
            throw new RuntimeException('Uttagsantal måste vara större än 0.');
        }

        $material = $this->workOrderRepository->findMaterialById($tenantId, $materialId);

        if (!$material) {
            throw new RuntimeException('Materialrad hittades inte.');
        }

        $plannedQuantity = (float) ($material['planned_quantity'] ?? 0);
        $issuedQuantity = (float) ($material['issued_quantity'] ?? 0);
        $returnedQuantity = (float) ($material['returned_quantity'] ?? 0);

        $remainingAllowedToIssue = round($plannedQuantity - ($issuedQuantity - $returnedQuantity), 2);

        if ($quantity > $remainingAllowedToIssue) {
            throw new RuntimeException('Uttaget överskrider kvarvarande planerat behov.');
        }

        if ($this->db->inTransaction()) {
            $this->performIssueMaterialLine($tenantId, $material, $quantity, $userId);
            return;
        }

        $this->db->beginTransaction();

        try {
            $this->performIssueMaterialLine($tenantId, $material, $quantity, $userId);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function returnMaterialLine(
        int $tenantId,
        int $materialId,
        float $quantity,
        ?int $userId = null
    ): void {
        if ($quantity <= 0) {
            throw new RuntimeException('Returantal måste vara större än 0.');
        }

        $material = $this->workOrderRepository->findMaterialById($tenantId, $materialId);

        if (!$material) {
            throw new RuntimeException('Materialrad hittades inte.');
        }

        $issuedQuantity = (float) ($material['issued_quantity'] ?? 0);
        $returnedQuantity = (float) ($material['returned_quantity'] ?? 0);
        $netIssued = round($issuedQuantity - $returnedQuantity, 2);

        if ($quantity > $netIssued) {
            throw new RuntimeException('Kan inte returnera mer än nettouttaget antal.');
        }

        if ($this->db->inTransaction()) {
            $this->performReturnMaterialLine($tenantId, $material, $quantity, $userId);
            return;
        }

        $this->db->beginTransaction();

        try {
            $this->performReturnMaterialLine($tenantId, $material, $quantity, $userId);
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

        $plannedQuantity = (float) ($material['planned_quantity'] ?? 0);
        $reservedQuantity = (float) ($material['reserved_quantity'] ?? 0);
        $returnedQuantity = (float) ($material['returned_quantity'] ?? 0);

        $newReturnedQuantity = $returnedQuantity;
        if ($delta < 0) {
            $newReturnedQuantity = round($returnedQuantity + abs($delta), 2);
        }

        $stockStatus = $this->calculateStockStatus($newIssuedQuantity, $newReturnedQuantity, $plannedQuantity);
        $reservationStatus = $this->calculateReservationStatus($reservedQuantity, $plannedQuantity);

        $this->workOrderRepository->updateMaterial($tenantId, (int) $material['id'], [
            'warehouse_id'        => (int) $material['warehouse_id'],
            'planned_quantity'    => $plannedQuantity,
            'reserved_quantity'   => $reservedQuantity,
            'issued_quantity'     => $newIssuedQuantity,
            'returned_quantity'   => $newReturnedQuantity,
            'reservation_status'  => $reservationStatus,
            'stock_status'        => $stockStatus,
            'unit_cost'           => (float) $material['unit_cost'],
            'notes'               => (string) ($material['notes'] ?? ''),
        ]);
    }

    private function performIssueMaterialLine(
        int $tenantId,
        array $material,
        float $quantity,
        ?int $userId
    ): void {
        $this->handleIssue($tenantId, $material, $quantity, $userId);

        $plannedQuantity = (float) ($material['planned_quantity'] ?? 0);
        $reservedQuantity = (float) ($material['reserved_quantity'] ?? 0);
        $issuedQuantity = (float) ($material['issued_quantity'] ?? 0);
        $returnedQuantity = (float) ($material['returned_quantity'] ?? 0);

        $newIssuedQuantity = round($issuedQuantity + $quantity, 2);
        $stockStatus = $this->calculateStockStatus($newIssuedQuantity, $returnedQuantity, $plannedQuantity);
        $reservationStatus = $this->calculateReservationStatus($reservedQuantity, $plannedQuantity);

        $this->workOrderRepository->updateMaterial($tenantId, (int) $material['id'], [
            'warehouse_id'        => (int) $material['warehouse_id'],
            'planned_quantity'    => $plannedQuantity,
            'reserved_quantity'   => $reservedQuantity,
            'issued_quantity'     => $newIssuedQuantity,
            'returned_quantity'   => $returnedQuantity,
            'reservation_status'  => $reservationStatus,
            'stock_status'        => $stockStatus,
            'unit_cost'           => (float) $material['unit_cost'],
            'notes'               => (string) ($material['notes'] ?? ''),
        ]);

        $this->workOrderRepository->addLog([
            'tenant_id'     => $tenantId,
            'work_order_id' => (int) $material['work_order_id'],
            'log_type'      => 'material_issued',
            'message'       => 'Material uttaget: ' . number_format($quantity, 2, '.', '') .
                ' st av artikel #' . (int) $material['article_id'] .
                ' från lager #' . (int) $material['warehouse_id'] . '.',
            'hours_spent'   => 0,
            'created_by'    => $userId,
        ]);
    }

    private function performReturnMaterialLine(
        int $tenantId,
        array $material,
        float $quantity,
        ?int $userId
    ): void {
        $this->handleReturn($tenantId, $material, $quantity, $userId);

        $plannedQuantity = (float) ($material['planned_quantity'] ?? 0);
        $reservedQuantity = (float) ($material['reserved_quantity'] ?? 0);
        $issuedQuantity = (float) ($material['issued_quantity'] ?? 0);
        $returnedQuantity = (float) ($material['returned_quantity'] ?? 0);

        $newReturnedQuantity = round($returnedQuantity + $quantity, 2);
        $stockStatus = $this->calculateStockStatus($issuedQuantity, $newReturnedQuantity, $plannedQuantity);
        $reservationStatus = $this->calculateReservationStatus($reservedQuantity, $plannedQuantity);

        $this->workOrderRepository->updateMaterial($tenantId, (int) $material['id'], [
            'warehouse_id'        => (int) $material['warehouse_id'],
            'planned_quantity'    => $plannedQuantity,
            'reserved_quantity'   => $reservedQuantity,
            'issued_quantity'     => $issuedQuantity,
            'returned_quantity'   => $newReturnedQuantity,
            'reservation_status'  => $reservationStatus,
            'stock_status'        => $stockStatus,
            'unit_cost'           => (float) $material['unit_cost'],
            'notes'               => (string) ($material['notes'] ?? ''),
        ]);

        $this->workOrderRepository->addLog([
            'tenant_id'     => $tenantId,
            'work_order_id' => (int) $material['work_order_id'],
            'log_type'      => 'material_returned',
            'message'       => 'Material returnerat: ' . number_format($quantity, 2, '.', '') .
                ' st av artikel #' . (int) $material['article_id'] .
                ' till lager #' . (int) $material['warehouse_id'] . '.',
            'hours_spent'   => 0,
            'created_by'    => $userId,
        ]);
    }

    private function handleIssue(int $tenantId, array $material, float $quantity, ?int $userId): void
    {
        $articleId = (int) $material['article_id'];
        $warehouseId = (int) $material['warehouse_id'];
        $workOrderId = (int) $material['work_order_id'];
        $materialId = (int) $material['id'];

        if ($warehouseId <= 0) {
            throw new RuntimeException('Materialraden saknar lager.');
        }

        $available = $this->inventoryRepository->getAvailableStock($tenantId, $articleId, $warehouseId);

        if ($available < $quantity) {
            throw new RuntimeException('Otillräckligt saldo i valt lager för detta uttag.');
        }

        $this->inventoryRepository->decreaseStock($tenantId, $articleId, $warehouseId, $quantity);

        $transactionId = $this->inventoryRepository->createInventoryTransaction([
            'tenant_id'      => $tenantId,
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

        if ($warehouseId <= 0) {
            throw new RuntimeException('Materialraden saknar lager.');
        }

        $this->inventoryRepository->increaseStock($tenantId, $articleId, $warehouseId, $quantity, $userId);

        $transactionId = $this->inventoryRepository->createInventoryTransaction([
            'tenant_id'      => $tenantId,
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

    private function calculateReservationStatus(float $reservedQuantity, float $plannedQuantity): string
    {
        if ($reservedQuantity <= 0) {
            return 'none';
        }

        if ($reservedQuantity >= $plannedQuantity) {
            return 'reserved';
        }

        return 'partial';
    }

    private function calculateStockStatus(float $issuedQuantity, float $returnedQuantity, float $plannedQuantity): string
    {
        $netIssued = $issuedQuantity - $returnedQuantity;

        if ($issuedQuantity <= 0 && $returnedQuantity <= 0) {
            return 'not_issued';
        }

        if ($netIssued <= 0 && $returnedQuantity > 0) {
            return 'returned';
        }

        if ($returnedQuantity > 0 && $netIssued > 0) {
            return 'partially_returned';
        }

        if ($netIssued >= $plannedQuantity) {
            return 'issued';
        }

        return 'partial';
    }
}
