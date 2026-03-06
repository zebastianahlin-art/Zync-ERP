<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Core\Database;
use App\Modules\Inventory\Repositories\InventoryTransactionRepository;
use PDO;

final class InventoryTransactionService
{
    public function __construct(
        private ?InventoryTransactionRepository $repository = null
    ) {
        $this->repository ??= new InventoryTransactionRepository();
    }

    public function getTransactions(): array
    {
        return $this->repository->getTransactions();
    }

    public function getTransactionById(int $id): ?array
    {
        return $this->repository->getTransactionById($id);
    }

    public function createTransaction(array $data): array
    {
        $payload = $this->normalize($data);

        if (
            $payload['article_id'] <= 0 ||
            $payload['warehouse_id'] <= 0 ||
            $payload['type'] === '' ||
            $payload['quantity'] <= 0
        ) {
            return ['ok' => false, 'message' => 'Artikel, lagerställe, typ och antal krävs.'];
        }

        $id = $this->repository->createTransaction($payload);

        return ['ok' => true, 'id' => $id];
    }

    public function getArticles(): array
    {
        /** @var PDO $pdo */
        $pdo = Database::pdo();

        return $pdo->query(
            "SELECT id, article_number, name, unit, purchase_price
             FROM articles
             WHERE is_deleted = 0 AND is_active = 1
             ORDER BY name"
        )->fetchAll(PDO::FETCH_ASSOC);
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

    public function getUsers(): array
    {
        /** @var PDO $pdo */
        $pdo = Database::pdo();

        return $pdo->query(
            "SELECT id, full_name
             FROM users
             WHERE is_active = 1
             ORDER BY full_name"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function normalize(array $data): array
    {
        return [
            'article_id' => (int) ($data['article_id'] ?? 0),
            'warehouse_id' => (int) ($data['warehouse_id'] ?? 0),
            'type' => trim((string) ($data['type'] ?? '')),
            'quantity' => (float) ($data['quantity'] ?? 0),
            'reference' => trim((string) ($data['reference'] ?? '')),
            'comment' => trim((string) ($data['comment'] ?? '')),
            'created_by' => !empty($data['created_by']) ? (int) $data['created_by'] : null,
        ];
    }
}
