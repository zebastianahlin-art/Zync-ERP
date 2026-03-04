<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class DepartmentRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            'SELECT d.*, p.name AS parent_name
             FROM departments d
             LEFT JOIN departments p ON d.parent_id = p.id
             WHERE d.is_deleted = 0
             ORDER BY d.name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT d.*, p.name AS parent_name
             FROM departments d
             LEFT JOIN departments p ON d.parent_id = p.id
             WHERE d.id = ? AND d.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO departments (name, code, manager_id, parent_id, color, created_by)
             VALUES (:name, :code, :manager_id, :parent_id, :color, :created_by)'
        );
        $stmt->execute([
            'name'       => $data['name'],
            'code'       => $data['code'],
            'manager_id' => $data['manager_id'] ?: null,
            'parent_id'  => $data['parent_id'] ?: null,
            'color'      => $data['color'] ?: null,
            'created_by' => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE departments SET name = :name, code = :code, manager_id = :manager_id,
             parent_id = :parent_id, color = :color WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'       => $data['name'],
            'code'       => $data['code'],
            'manager_id' => $data['manager_id'] ?: null,
            'parent_id'  => $data['parent_id'] ?: null,
            'color'      => $data['color'] ?: null,
            'id'         => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE departments SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
