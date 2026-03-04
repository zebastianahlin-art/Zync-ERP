<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class AccountGroupRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            "SELECT ag.*, p.name AS parent_name
             FROM account_groups ag
             LEFT JOIN account_groups p ON ag.parent_id = p.id
             WHERE ag.is_deleted = 0
             ORDER BY ag.sort_order, ag.code"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT ag.*, p.name AS parent_name
             FROM account_groups ag
             LEFT JOIN account_groups p ON ag.parent_id = p.id
             WHERE ag.id = ? AND ag.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO account_groups (name, code, parent_id, sort_order)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['code'],
            !empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            (int) ($data['sort_order'] ?? 0),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE account_groups SET name = ?, code = ?, parent_id = ?, sort_order = ?
             WHERE id = ? AND is_deleted = 0"
        );
        $stmt->execute([
            $data['name'],
            $data['code'],
            !empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            (int) ($data['sort_order'] ?? 0),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE account_groups SET is_deleted = 1 WHERE id = ?"
        )->execute([$id]);
    }
}
