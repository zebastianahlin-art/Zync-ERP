<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class DepartmentRepository
{
    /** All departments with manager name and parent name. */
    public function all(): array
    {
        return Database::pdo()->query('
            SELECT d.*, 
                   u.username AS manager_name,
                   u.full_name AS manager_full_name,
                   p.name AS parent_name,
                   (SELECT COUNT(*) FROM users WHERE department_id = d.id AND is_deleted = 0 AND is_active = 1) AS user_count
            FROM departments d
            LEFT JOIN users u ON d.manager_id = u.id AND u.is_deleted = 0
            LEFT JOIN departments p ON d.parent_id = p.id AND p.is_deleted = 0
            WHERE d.is_deleted = 0
            ORDER BY d.name ASC
        ')->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single department by ID. */
    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('
            SELECT d.*,
                   u.username AS manager_name,
                   u.full_name AS manager_full_name,
                   p.name AS parent_name
            FROM departments d
            LEFT JOIN users u ON d.manager_id = u.id AND u.is_deleted = 0
            LEFT JOIN departments p ON d.parent_id = p.id AND p.is_deleted = 0
            WHERE d.id = ? AND d.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Create a new department. */
    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare('
            INSERT INTO departments (name, code, manager_id, parent_id, color, created_by)
            VALUES (:name, :code, :manager_id, :parent_id, :color, :created_by)
        ');
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

    /** Update a department. */
    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare('
            UPDATE departments
            SET name = :name, code = :code, manager_id = :manager_id,
                parent_id = :parent_id, color = :color
            WHERE id = :id AND is_deleted = 0
        ');
        $stmt->execute([
            'name'       => $data['name'],
            'code'       => $data['code'],
            'manager_id' => $data['manager_id'] ?: null,
            'parent_id'  => $data['parent_id'] ?: null,
            'color'      => $data['color'] ?: null,
            'id'         => $id,
        ]);
    }

    /** Soft-delete a department. */
    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE departments SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Check if code is taken (optionally excluding an ID). */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM departments WHERE code = ? AND id != ? AND is_deleted = 0');
            $stmt->execute([$code, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM departments WHERE code = ? AND is_deleted = 0');
            $stmt->execute([$code]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /** All departments for dropdown (id + name), optionally excluding one. */
    public function allForDropdown(?int $excludeId = null): array
    {
        $sql = 'SELECT id, name FROM departments WHERE is_deleted = 0';
        $params = [];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY name ASC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** All active users for manager dropdown. */
    public function allManagers(): array
    {
        return Database::pdo()->query('
            SELECT id, username, full_name 
            FROM users 
            WHERE is_deleted = 0 AND is_active = 1 
            ORDER BY username ASC
        ')->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Get users belonging to a department. */
    public function members(int $departmentId): array
    {
        $stmt = Database::pdo()->prepare('
            SELECT u.id, u.username, u.full_name, u.email, r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.department_id = ? AND u.is_deleted = 0 AND u.is_active = 1
            ORDER BY u.username ASC
        ');
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
