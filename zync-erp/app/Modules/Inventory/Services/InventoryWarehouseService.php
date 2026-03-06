<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Core\Database;
use App\Modules\Inventory\Repositories\InventoryWarehouseRepository;
use PDO;

final class InventoryWarehouseService
{
    public function __construct(
        private ?InventoryWarehouseRepository $repository = null
    ) {
        $this->repository ??= new InventoryWarehouseRepository();
    }

    public function getWarehouses(): array
    {
        return $this->repository->getWarehouses();
    }

    public function getWarehouseById(int $id): ?array
    {
        return $this->repository->getWarehouseById($id);
    }

    public function createWarehouse(array $data): array
    {
        $payload = $this->normalize($data);

        if (!$this->isValid($payload)) {
            return ['ok' => false, 'message' => 'Namn och kod krävs.'];
        }

        $this->repository->createWarehouse($payload);

        return ['ok' => true];
    }

    public function updateWarehouse(int $id, array $data): array
    {
        $payload = $this->normalize($data);

        if (!$this->isValid($payload)) {
            return ['ok' => false, 'message' => 'Namn och kod krävs.'];
        }

        if (!$this->repository->getWarehouseById($id)) {
            return ['ok' => false, 'message' => 'Lagerställe hittades inte.'];
        }

        $this->repository->updateWarehouse($id, $payload);

        return ['ok' => true];
    }

    public function deleteWarehouse(int $id): void
    {
        $this->repository->deleteWarehouse($id);
    }

    public function getAssignableUsers(): array
    {
        /** @var PDO $pdo */
        $pdo = Database::pdo();

        return $pdo->query(
            "SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'code' => trim((string) ($data['code'] ?? '')),
            'address' => isset($data['address']) ? trim((string) $data['address']) : null,
            'responsible_user_id' => !empty($data['responsible_user_id']) ? (int) $data['responsible_user_id'] : null,
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'created_by' => !empty($data['created_by']) ? (int) $data['created_by'] : null,
        ];
    }

    private function isValid(array $data): bool
    {
        return $data['name'] !== '' && $data['code'] !== '';
    }
}