<?php

declare(strict_types=1);

namespace App\Services\Maintenance;

use PDO;
use Throwable;
use RuntimeException;

class MaintenanceInventoryService
{
    public function __construct(private PDO $db)
    {
    }

    public function getAvailableStock(int $articleId, int $warehouseId): float
    {
        $stmt = $this->db->prepare("
            SELECT quantity
            FROM inventory_stock
            WHERE article_id = :article_id
              AND warehouse_id = :warehouse_id
            LIMIT 1
        ");

        $stmt->execute([
            ':article_id'   => $articleId,
            ':warehouse_id' => $warehouseId,
        ]);

        $value = $stmt->fetchColumn();

        return $value !== false ? (float) $value : 0.0;
    }

    public function reserveMaterialLine(int $materialLineId, int $warehouseId, float $qty, int $userId): void
    {
        if ($qty <= 0) {
            throw new RuntimeException('Reservationsantal måste vara större än 0.');
        }

        $this->db->beginTransaction();

        try {
            $line = $this->getMaterialLineForUpdate($materialLineId);

            if (!$line) {
                throw new RuntimeException('Materialraden hittades inte.');
            }

            $articleId = (int) $line['article_id'];
            $plannedQty = (float) $line['planned_qty'];
            $reservedQty = (float) $line['reserved_qty'];

            if ($articleId <= 0) {
                throw new RuntimeException('Materialraden saknar artikel.');
            }

            $availableStock = $this->getAvailableStock($articleId, $warehouseId);
            if ($availableStock < $qty) {
                throw new RuntimeException('Otillräckligt lagersaldo för reservation.');
            }

            $newReservedQty = $reservedQty + $qty;
            if ($newReservedQty > $plannedQty) {
                throw new RuntimeException('Reservation kan inte överstiga planerat antal.');
            }

            $reservationStatus = $this->calculateReservationStatus($newReservedQty, $plannedQty);

            $stmt = $this->db->prepare("
                UPDATE maintenance_work_order_materials
                SET warehouse_id = :warehouse_id,
                    reserved_qty = :reserved_qty,
                    reservation_status = :reservation_status,
                    updated_at = NOW()
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute([
                ':warehouse_id'        => $warehouseId,
                ':reserved_qty'        => $newReservedQty,
                ':reservation_status'  => $reservationStatus,
                ':id'                  => $materialLineId,
            ]);

            $this->insertMaterialLog(
                workOrderId: (int) $line['work_order_id'],
                materialLineId: $materialLineId,
                userId: $userId,
                actionType: 'reserve',
                message: sprintf(
                    'Reserverade %.4f av artikel #%d från lager #%d.',
                    $qty,
                    $articleId,
                    $warehouseId
                )
            );

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function issueMaterialLine(int $materialLineId, float $qty, int $userId): void
    {
        if ($qty <= 0) {
            throw new RuntimeException('Uttagsantal måste vara större än 0.');
        }

        $this->db->beginTransaction();

        try {
            $line = $this->getMaterialLineForUpdate($materialLineId);

            if (!$line) {
                throw new RuntimeException('Materialraden hittades inte.');
            }

            $articleId = (int) $line['article_id'];
            $workOrderId = (int) $line['work_order_id'];
            $warehouseId = (int) ($line['warehouse_id'] ?? 0);
            $plannedQty = (float) $line['planned_qty'];
            $issuedQty = (float) $line['issued_qty'];
            $returnedQty = (float) $line['returned_qty'];

            if ($articleId <= 0) {
                throw new RuntimeException('Materialraden saknar artikel.');
            }

            if ($warehouseId <= 0) {
                throw new RuntimeException('Inget lager är valt på materialraden.');
            }

            $remainingAllowedToIssue = $plannedQty - ($issuedQty - $returnedQty);
            if ($qty > $remainingAllowedToIssue) {
                throw new RuntimeException('Uttaget överskrider kvarvarande planerat behov.');
            }

            $stockRow = $this->getInventoryStockRowForUpdate($articleId, $warehouseId);
            $availableStock = $stockRow ? (float) $stockRow['quantity'] : 0.0;

            if ($availableStock < $qty) {
                throw new RuntimeException('Otillräckligt lagersaldo för uttag.');
            }

            $stmt = $this->db->prepare("
                UPDATE inventory_stock
                SET quantity = quantity - :qty,
                    updated_at = NOW()
                WHERE article_id = :article_id
                  AND warehouse_id = :warehouse_id
                LIMIT 1
            ");

            $stmt->execute([
                ':qty'          => $qty,
                ':article_id'   => $articleId,
                ':warehouse_id' => $warehouseId,
            ]);

            $this->insertInventoryTransaction(
                createdBy: $userId,
                articleId: $articleId,
                warehouseId: $warehouseId,
                quantity: $qty,
                direction: 'out',
                referenceType: 'maintenance_work_order',
                referenceId: $workOrderId,
                referenceLineId: $materialLineId,
                note: 'Materialuttag från arbetsorder #' . $workOrderId
            );

            $newIssuedQty = $issuedQty + $qty;
            $stockStatus = $this->calculateStockStatus($newIssuedQty, $returnedQty, $plannedQty);

            $stmt = $this->db->prepare("
                UPDATE maintenance_work_order_materials
                SET issued_qty = :issued_qty,
                    stock_status = :stock_status,
                    updated_at = NOW()
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute([
                ':issued_qty'   => $newIssuedQty,
                ':stock_status' => $stockStatus,
                ':id'           => $materialLineId,
            ]);

            $this->insertMaterialLog(
                workOrderId: $workOrderId,
                materialLineId: $materialLineId,
                userId: $userId,
                actionType: 'issue',
                message: sprintf(
                    'Tog ut %.4f av artikel #%d från lager #%d.',
                    $qty,
                    $articleId,
                    $warehouseId
                )
            );

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function returnMaterialLine(int $materialLineId, float $qty, int $userId): void
    {
        if ($qty <= 0) {
            throw new RuntimeException('Returantal måste vara större än 0.');
        }

        $this->db->beginTransaction();

        try {
            $line = $this->getMaterialLineForUpdate($materialLineId);

            if (!$line) {
                throw new RuntimeException('Materialraden hittades inte.');
            }

            $articleId = (int) $line['article_id'];
            $workOrderId = (int) $line['work_order_id'];
            $warehouseId = (int) ($line['warehouse_id'] ?? 0);
            $plannedQty = (float) $line['planned_qty'];
            $issuedQty = (float) $line['issued_qty'];
            $returnedQty = (float) $line['returned_qty'];

            if ($articleId <= 0) {
                throw new RuntimeException('Materialraden saknar artikel.');
            }

            if ($warehouseId <= 0) {
                throw new RuntimeException('Inget lager är valt på materialraden.');
            }

            $netIssued = $issuedQty - $returnedQty;
            if ($qty > $netIssued) {
                throw new RuntimeException('Kan inte returnera mer än nettouttaget antal.');
            }

            $stockRow = $this->getInventoryStockRowForUpdate($articleId, $warehouseId);

            if (!$stockRow) {
                throw new RuntimeException('Lagersaldo saknas för artikel och lager. Skapa stockrad först.');
            }

            $stmt = $this->db->prepare("
                UPDATE inventory_stock
                SET quantity = quantity + :qty,
                    updated_at = NOW()
                WHERE article_id = :article_id
                  AND warehouse_id = :warehouse_id
                LIMIT 1
            ");

            $stmt->execute([
                ':qty'          => $qty,
                ':article_id'   => $articleId,
                ':warehouse_id' => $warehouseId,
            ]);

            $this->insertInventoryTransaction(
                createdBy: $userId,
                articleId: $articleId,
                warehouseId: $warehouseId,
                quantity: $qty,
                direction: 'in',
                referenceType: 'maintenance_work_order',
                referenceId: $workOrderId,
                referenceLineId: $materialLineId,
                note: 'Materialretur från arbetsorder #' . $workOrderId
            );

            $newReturnedQty = $returnedQty + $qty;
            $stockStatus = $this->calculateStockStatus($issuedQty, $newReturnedQty, $plannedQty);

            $stmt = $this->db->prepare("
                UPDATE maintenance_work_order_materials
                SET returned_qty = :returned_qty,
                    stock_status = :stock_status,
                    updated_at = NOW()
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute([
                ':returned_qty' => $newReturnedQty,
                ':stock_status' => $stockStatus,
                ':id'           => $materialLineId,
            ]);

            $this->insertMaterialLog(
                workOrderId: $workOrderId,
                materialLineId: $materialLineId,
                userId: $userId,
                actionType: 'return',
                message: sprintf(
                    'Returnerade %.4f av artikel #%d till lager #%d.',
                    $qty,
                    $articleId,
                    $warehouseId
                )
            );

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    private function getMaterialLineForUpdate(int $materialLineId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM maintenance_work_order_materials
            WHERE id = :id
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            ':id' => $materialLineId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function getInventoryStockRowForUpdate(int $articleId, int $warehouseId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM inventory_stock
            WHERE article_id = :article_id
              AND warehouse_id = :warehouse_id
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            ':article_id'   => $articleId,
            ':warehouse_id' => $warehouseId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function insertInventoryTransaction(
        int $createdBy,
        int $articleId,
        int $warehouseId,
        float $quantity,
        string $direction,
        string $referenceType,
        int $referenceId,
        int $referenceLineId,
        string $note
    ): void {
        $stmt = $this->db->prepare("
            INSERT INTO inventory_transactions
            (
                created_at,
                updated_at,
                created_by,
                is_deleted,
                article_id,
                warehouse_id,
                quantity,
                direction,
                reference_type,
                reference_id,
                reference_line_id,
                note
            )
            VALUES
            (
                NOW(),
                NOW(),
                :created_by,
                0,
                :article_id,
                :warehouse_id,
                :quantity,
                :direction,
                :reference_type,
                :reference_id,
                :reference_line_id,
                :note
            )
        ");

        $stmt->execute([
            ':created_by'        => $createdBy,
            ':article_id'        => $articleId,
            ':warehouse_id'      => $warehouseId,
            ':quantity'          => $quantity,
            ':direction'         => $direction,
            ':reference_type'    => $referenceType,
            ':reference_id'      => $referenceId,
            ':reference_line_id' => $referenceLineId,
            ':note'              => $note,
        ]);
    }

    private function insertMaterialLog(
        int $workOrderId,
        int $materialLineId,
        int $userId,
        string $actionType,
        string $message
    ): void {
        $tableExists = $this->tableExists('maintenance_work_order_logs');
        if (!$tableExists) {
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO maintenance_work_order_logs
            (
                work_order_id,
                created_by,
                log_type,
                message,
                created_at,
                updated_at
            )
            VALUES
            (
                :work_order_id,
                :created_by,
                :log_type,
                :message,
                NOW(),
                NOW()
            )
        ");

        $stmt->execute([
            ':work_order_id' => $workOrderId,
            ':created_by'    => $userId,
            ':log_type'      => 'material_' . $actionType,
            ':message'       => '[Materialrad #' . $materialLineId . '] ' . $message,
        ]);
    }

    private function tableExists(string $tableName): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
              AND table_name = :table_name
        ");

        $stmt->execute([
            ':table_name' => $tableName,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function calculateReservationStatus(float $reservedQty, float $plannedQty): string
    {
        if ($reservedQty <= 0) {
            return 'none';
        }

        if ($reservedQty >= $plannedQty) {
            return 'reserved';
        }

        return 'partial';
    }

    private function calculateStockStatus(float $issuedQty, float $returnedQty, float $plannedQty): string
    {
        $netIssued = $issuedQty - $returnedQty;

        if ($issuedQty <= 0 && $returnedQty <= 0) {
            return 'not_issued';
        }

        if ($netIssued <= 0 && $returnedQty > 0) {
            return 'returned';
        }

        if ($returnedQty > 0 && $netIssued > 0) {
            return 'partially_returned';
        }

        if ($netIssued >= $plannedQty) {
            return 'issued';
        }

        return 'partial';
    }
}
