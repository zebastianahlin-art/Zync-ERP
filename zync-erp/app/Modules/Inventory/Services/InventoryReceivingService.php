<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Modules\Inventory\Repositories\InventoryReceivingRepository;
use PDO;

final class InventoryReceivingService
{
    public function __construct(
        private ?InventoryReceivingRepository $repository = null
    ) {
        $this->repository ??= new InventoryReceivingRepository();
    }

    public function getReceivablePurchaseOrders(): array
    {
        return $this->repository->getReceivablePurchaseOrders();
    }

    public function getPurchaseOrderForReceiving(int $poId): ?array
    {
        return $this->repository->getPurchaseOrderForReceiving($poId);
    }

    public function getPurchaseOrderLines(int $poId): array
    {
        return $this->repository->getPurchaseOrderLines($poId);
    }

    public function getWarehouses(): array
    {
        /** @var PDO $pdo */
        $pdo = Database::pdo();

        return $pdo->query(
            "SELECT id, name, code
             FROM warehouses
             WHERE is_deleted = 0 AND is_active = 1
             ORDER BY name"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receivePurchaseOrder(int $poId, array $data): array
    {
        $warehouseId = (int) ($data['warehouse_id'] ?? 0);
        $lines = $data['lines'] ?? [];

        if ($poId <= 0) {
            return ['ok' => false, 'message' => 'Ogiltig inköpsorder.'];
        }

        if ($warehouseId <= 0) {
            return ['ok' => false, 'message' => 'Välj lagerställe.'];
        }

        if (!is_array($lines) || $lines === []) {
            return ['ok' => false, 'message' => 'Ingen inleveransrad skickades.'];
        }

        $normalizedLines = [];

        foreach ($lines as $lineId => $row) {
            $qty = (float) ($row['received_quantity'] ?? 0);

            if ($qty <= 0) {
                continue;
            }

            $normalizedLines[] = [
                'line_id' => (int) $lineId,
                'received_quantity' => $qty,
            ];
        }

        if ($normalizedLines === []) {
            return ['ok' => false, 'message' => 'Minst en rad måste ha mottaget antal större än 0.'];
        }

        $this->repository->receivePurchaseOrder(
            $poId,
            $warehouseId,
            $normalizedLines,
            (int) (Auth::id() ?? 0)
        );

        return ['ok' => true];
    }
}
