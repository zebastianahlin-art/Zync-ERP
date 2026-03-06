<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Repositories;

use App\Core\Database;
use PDO;

final class InventoryWarehouseRepository
{
    public function getWarehouses(): array
    {
        $stmt = Database::pdo()->query("
            SELECT w.*, u.full_name AS responsible_name
            FROM warehouses w
            LEFT JOIN users u ON w.responsible_user_id = u.id
            WHERE w.is_deleted = 0
            ORDER BY w.name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWarehouseById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("
            SELECT w.*, u.full_name AS responsible_name
            FROM warehouses w
            LEFT JOIN users u ON w.responsible_user_id = u.id
            WHERE w.id = ? AND w.is_deleted = 0
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createWarehouse(array $data): int
    {
        $stmt = Database::pdo()->prepare("
            INSERT INTO warehouses (name, code, address, responsible_user_id, is_active, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['code'],
            $data['address'],
            $data['responsible_user_id'],
            $data['is_active'],
            $data['created_by'],
        ]);

        return (int) Database::pdo()->lastInsertId();
    }

    public function updateWarehouse(int $id, array $data): void
    {
        Database::pdo()->prepare("
            UPDATE warehouses
            SET name = ?, code = ?, address = ?, responsible_user_id = ?, is_active = ?
            WHERE id = ?
        ")->execute([
            $data['name'],
            $data['code'],
            $data['address'],
            $data['responsible_user_id'],
            $data['is_active'],
            $id,
        ]);
    }

    public function deleteWarehouse(int $id): void
    {
        Database::pdo()->prepare("
            UPDATE warehouses
            SET is_deleted = 1
            WHERE id = ?
        ")->execute([$id]);
    }
}