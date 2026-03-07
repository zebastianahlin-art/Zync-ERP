<?php

declare(strict_types=1);

class MaintenanceInventoryService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAvailableStock(int $tenantId, int $articleId, int $warehouseId): float
    {
        $stmt = $this->db->prepare("
            SELECT quantity
            FROM inventory_stock
            WHERE tenant_id = :tenant_id
              AND article_id = :article_id
              AND warehouse_id = :warehouse_id
            LIMIT 1
        ");

        $stmt->execute([
            ':tenant_id'    => $tenantId,
            ':article_id'   => $articleId,
            ':warehouse_id' => $warehouseId,
        ]);

        $value = $stmt->fetchColumn();

        return $value !== false ? (float) $value : 0.0;
    }

    public function reserveMaterialLine(int $tenantId, int $materialLineId, int $warehouseId, float $quantity, int $userId): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Reservationsantal måste vara större än 0.');
        }

        $this->db->beginTransaction();

        try {
            $line = $this->getMaterialLineForUpdate($tenantId, $materialLineId);

            if (!$line) {
                throw new RuntimeException('Materialraden hittades inte.');
            }

            $articleId        = (int) $line['article_id'];
            $plannedQuantity  = (float) $line['planned_quantity'];
            $reservedQuantity = (float) ($line['reserved_quantity'] ?? 0);

            if ($articleId <= 0) {
                throw new RuntimeException('Materialraden saknar artikel.');
            }

            $availableStock = $this->getAvailableStock($tenantId, $articleId, $warehouseId);

            if ($availableStock < $quantity) {
                throw new RuntimeException('Otillräckligt lagersaldo för reservation.');
            }

            $newReservedQuantity = $reservedQuantity + $quantity;

            if ($newReservedQuantity > $plannedQuantity) {
                throw new RuntimeException('Reservation kan inte överstiga planerat antal.');
            }

            $reservationStatus = $this->calculateReservationStatus($newReservedQuantity, $plannedQuantity);

            $stmt = $this->db->prepare("
                UPDATE maintenance_work_order_materials
                SET warehouse_id = :warehouse_id,
                    reserved_quantity = :reserved_quantity,
                    reservation_status = :reservation_status,
                    updated_at = NOW()
                WHERE tenant_id = :tenant_id
                  AND id = :id
                LIMIT 1
            ");

            $stmt->execute([
                ':warehouse_id'       => $warehouseId,
                ':reserved_quantity'  => $newReservedQuantity,
                ':reservation_status' => $reservationStatus,
                ':tenant_id'          => $tenantId,
                ':id'                 => $materialLineId,
            ]);

            $this->insertMaterialLog(
                $tenantId,
                (int) $line['work_order_id'],
                $userId,
                'material_reserved',
                sprintf(
                    '[Materialrad #%d] Reserverade %.2f av artikel #%d från lager #%d.',
                    $materialLineId,
                    $quantity,
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

    public function issueMaterialLine(int $tenantId, int $materialLineId, float $quantity, int $userId): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Uttagsantal måste vara större än 0.');
        }

        $this->db->beginTransaction();

        try {
            $line = $this->getMaterialLineForUpdate($tenantId, $materialLineId);

            if (!$line) {
                throw new RuntimeException('Materialraden hittades inte.');
            }

            $articleId         = (int) $line['article_id'];
            $workOrderId       = (int) $line['work_order_id'];
            $warehouseId       = (int) ($line['warehouse_id'] ?? 0);
            $plannedQuantity   = (float) $line['planned_quantity'];
            $issuedQuantity    = (float) ($line['issued_quantity'] ?? 0);
            $returnedQuantity  = (float) ($line['returned_quantity'] ?? 0);

            if ($articleId <= 0) {
                throw new RuntimeException('Materialraden saknar artikel.');
            }

            if ($warehouseId <= 0) {
                throw new RuntimeException('Inget lager är valt på materialraden.');
            }

            $remainingAllowedToIssue = $plannedQuantity - ($issuedQuantity - $returnedQuantity);

            if ($quantity > $remainingAllowedToIssue) {
                throw new RuntimeException('Uttaget överskrider kvarvarande planerat behov.');
            }

            $stockRow = $this->getInventoryStockRowForUpdate($tenantId, $articleId, $warehouseId);
            $availableStock = $stockRow ? (float) $stockRow['quantity'] : 0.0;

            if ($availableStock < $quantity) {
                throw new RuntimeException('Otillräckligt lagersaldo för uttag.');
            }

            $stmt = $this->db->prepare("
                UPDATE inventory_stock
                SET quantity = quantity - :quantity,
                    updated_at = NOW()
                WHERE tenant_id = :tenant_id
                  AND article_id = :article_id
                  AND warehouse_id = :warehouse_id
                LIMIT 1
            ");

            $stmt->execute([
                ':quantity'     => $quantity,
                ':tenant_id'    => $tenantId,
                ':article_id'   => $articleId,
                ':warehouse_id' => $warehouseId,
            ]);

            $this->insertInventoryTransaction(
                $tenantId,
                $userId,
                $articleId,
                $warehouseId,
                'issue',
                $quantity,
                'maintenance_work_order_material',
                $materialLineId,
                'Materialuttag från arbetsorder #' . $workOrderId . ', materialrad #' . $materialLineId
            );

            $newIssuedQuantity = $issuedQuantity + $quantity;
            $stockStatus = $this->calculateStockStatus($newIssuedQuantity, $returnedQuantity, $plannedQuantity);

            $stmt = $this->db->prepare("
                UPDATE maintenance_work_order_materials
                SET issued_quantity = :issued_quantity,
                    stock_status = :stock_status,
                    updated_at = NOW()
                WHERE tenant_id = :tenant_id
                  AND id = :id
                LIMIT 1
            ");

            $stmt->execute([
                ':issued_quantity' => $newIssuedQuantity,
                ':stock_status'    => $stockStatus,
                ':tenant_id'       => $tenantId,
                ':id'              => $materialLineId,
            ]);

            $this->insertMaterialLog(
                $tenantId,
                $workOrderId,
                $userId,
                'material_issued',
                sprintf(
                    '[Materialrad #%d] Tog ut %.2f av artikel #%d från lager #%d.',
                    $materialLineId,
                    $quantity,
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

    public function returnMaterialLine(int $tenantId, int $materialLineId, float $quantity, int $userId): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Returantal måste vara större än 0.');
        }

        $this->db->beginTransaction();

        try {
            $line = $this->getMaterialLineForUpdate($tenantId, $materialLineId);

            if (!$line) {
                throw new RuntimeException('Materialraden hittades inte.');
            }

            $articleId        = (int) $line['article_id'];
            $workOrderId      = (int) $line['work_order_id'];
            $warehouseId      = (int) ($line['warehouse_id'] ?? 0);
            $plannedQuantity  = (float) $line['planned_quantity'];
            $issuedQuantity   = (float) ($line['issued_quantity'] ?? 0);
            $returnedQuantity = (float) ($line['returned_quantity'] ?? 0);

            if ($articleId <= 0) {
                throw new RuntimeException('Materialraden saknar artikel.');
            }

            if ($warehouseId <= 0) {
                throw new RuntimeException('Inget lager är valt på materialraden.');
            }

            $netIssued = $issuedQuantity - $returnedQuantity;

            if ($quantity > $netIssued) {
                throw new RuntimeException('Kan inte returnera mer än nettouttaget antal.');
            }

            $stockRow = $this->getInventoryStockRowForUpdate($tenantId, $articleId, $warehouseId);

            if (!$stockRow) {
                throw new RuntimeException('Lagersaldo saknas för artikel och lager.');
            }

            $stmt = $this->db->prepare("
                UPDATE inventory_stock
                SET quantity = quantity + :quantity,
                    updated_at = NOW()
                WHERE tenant_id = :tenant_id
                  AND article_id = :article_id
                  AND warehouse_id = :warehouse_id
                LIMIT 1
            ");

            $stmt->execute([
                ':quantity'     => $quantity,
                ':tenant_id'    => $tenantId,
                ':article_id'   => $articleId,
                ':warehouse_id' => $warehouseId,
            ]);

            $this->insertInventoryTransaction(
                $tenantId,
                $userId,
                $articleId,
                $warehouseId,
                'receipt',
                $quantity,
                'maintenance_work_order_material_return',
                $materialLineId,
                'Materialretur från arbetsorder #' . $workOrderId . ', materialrad #' . $materialLineId
            );

            $newReturnedQuantity = $returnedQuantity + $quantity;
            $stockStatus = $this->calculateStockStatus($issuedQuantity, $newReturnedQuantity, $plannedQuantity);

            $stmt = $this->db->prepare("
                UPDATE maintenance_work_order_materials
                SET returned_quantity = :returned_quantity,
                    stock_status = :stock_status,
                    updated_at = NOW()
                WHERE tenant_id = :tenant_id
                  AND id = :id
                LIMIT 1
            ");

            $stmt->execute([
                ':returned_quantity' => $newReturnedQuantity,
                ':stock_status'      => $stockStatus,
                ':tenant_id'         => $tenantId,
                ':id'                => $materialLineId,
            ]);

            $this->insertMaterialLog(
                $tenantId,
                $workOrderId,
                $userId,
                'material_returned',
                sprintf(
                    '[Materialrad #%d] Returnerade %.2f av artikel #%d till lager #%d.',
                    $materialLineId,
                    $quantity,
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

    private function getMaterialLineForUpdate(int $tenantId, int $materialLineId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM maintenance_work_order_materials
            WHERE tenant_id = :tenant_id
              AND id = :id
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':id'        => $materialLineId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function getInventoryStockRowForUpdate(int $tenantId, int $articleId, int $warehouseId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM inventory_stock
            WHERE tenant_id = :tenant_id
              AND article_id = :article_id
              AND warehouse_id = :warehouse_id
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            ':tenant_id'    => $tenantId,
            ':article_id'   => $articleId,
            ':warehouse_id' => $warehouseId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function insertInventoryTransaction(
        int $tenantId,
        int $createdBy,
        int $articleId,
        int $warehouseId,
        string $type,
        float $quantity,
        string $referenceType,
        int $referenceId,
        string $notes
    ): void {
        $stmt = $this->db->prepare("
            INSERT INTO inventory_transactions
            (
                tenant_id,
                created_at,
                updated_at,
                created_by,
                is_deleted,
                article_id,
                warehouse_id,
                type,
                quantity,
                reference_type,
                reference_id,
                notes
            )
            VALUES
            (
                :tenant_id,
                NOW(),
                NOW(),
                :created_by,
                0,
                :article_id,
                :warehouse_id,
                :type,
                :quantity,
                :reference_type,
                :reference_id,
                :notes
            )
        ");

        $stmt->execute([
            ':tenant_id'      => $tenantId,
            ':created_by'     => $createdBy,
            ':article_id'     => $articleId,
            ':warehouse_id'   => $warehouseId,
            ':type'           => $type,
            ':quantity'       => $quantity,
            ':reference_type' => $referenceType,
            ':reference_id'   => $referenceId,
            ':notes'          => $notes,
        ]);
    }

    private function insertMaterialLog(
        int $tenantId,
        int $workOrderId,
        int $userId,
        string $logType,
        string $message
    ): void {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_work_order_logs
            (
                tenant_id,
                work_order_id,
                created_by,
                log_type,
                message,
                created_at,
                updated_at
            )
            VALUES
            (
                :tenant_id,
                :work_order_id,
                :created_by,
                :log_type,
                :message,
                NOW(),
                NOW()
            )
        ");

        $stmt->execute([
            ':tenant_id'    => $tenantId,
            ':work_order_id'=> $workOrderId,
            ':created_by'   => $userId,
            ':log_type'     => $logType,
            ':message'      => $message,
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
