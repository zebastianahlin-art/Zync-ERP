<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class CostCenterRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            "SELECT cc.*, d.name AS department_name, u.full_name AS responsible_name,
                    p.code AS parent_code, p.name AS parent_name
             FROM cost_centers cc
             LEFT JOIN departments d ON cc.department_id = d.id
             LEFT JOIN users u ON cc.responsible_id = u.id
             LEFT JOIN cost_centers p ON cc.parent_id = p.id
             WHERE cc.is_deleted = 0
             ORDER BY cc.code"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT cc.*, d.name AS department_name, u.full_name AS responsible_name
             FROM cost_centers cc
             LEFT JOIN departments d ON cc.department_id = d.id
             LEFT JOIN users u ON cc.responsible_id = u.id
             WHERE cc.id = ? AND cc.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO cost_centers (code, name, description, parent_id, department_id, responsible_id, budget, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['code'],
            $data['name'],
            $data['description'] ?? null,
            $data['parent_id'] ?: null,
            $data['department_id'] ?: null,
            $data['responsible_id'] ?: null,
            $data['budget'] ?? 0,
            $data['is_active'] ?? 1,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE cost_centers SET code = ?, name = ?, description = ?, parent_id = ?, department_id = ?, responsible_id = ?, budget = ?, is_active = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['code'],
            $data['name'],
            $data['description'] ?? null,
            $data['parent_id'] ?: null,
            $data['department_id'] ?: null,
            $data['responsible_id'] ?: null,
            $data['budget'] ?? 0,
            $data['is_active'] ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare("UPDATE cost_centers SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }
}
