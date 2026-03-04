<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Repository for admin user management operations.
 */
class AdminUserRepository
{
    /** Return all users with role and department info, newest first. */
    public function all(): array
    {
        $stmt = Database::pdo()->query('
            SELECT u.id, u.username, u.email, u.is_active, u.created_at,
                   u.department_id,
                   r.name AS role_name, r.slug AS role_slug, r.level AS role_level,
                   d.name AS department_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE u.is_deleted = 0
            ORDER BY u.id DESC
        ');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single user by ID with role and department data. */
    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('
            SELECT u.*, r.name AS role_name, r.slug AS role_slug, r.level AS role_level,
                   d.name AS department_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE u.id = ? AND u.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Insert a new user. Password is hashed before storage. */
    public function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = Database::pdo()->prepare('
            INSERT INTO users (username, email, password_hash, role_id, department_id, is_active)
            VALUES (:username, :email, :password_hash, :role_id, :department_id, 1)
        ');
        $stmt->execute([
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => $hash,
            'role_id'       => $data['role_id'] ?: null,
            'department_id' => $data['department_id'] ?: null,
        ]);

        return (int) Database::pdo()->lastInsertId();
    }

    /**
     * Update a user. If the password field is empty/absent, the password_hash
     * column is left unchanged.
     */
    public function update(int $id, array $data): void
    {
        $params = [
            'username'      => $data['username'],
            'email'         => $data['email'],
            'role_id'       => $data['role_id'] ?: null,
            'department_id' => $data['department_id'] ?: null,
            'id'            => $id,
        ];

        if (!empty($data['password'])) {
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = Database::pdo()->prepare('
                UPDATE users
                SET username = :username, email = :email, password_hash = :password_hash,
                    role_id = :role_id, department_id = :department_id
                WHERE id = :id AND is_deleted = 0
            ');
        } else {
            $stmt = Database::pdo()->prepare('
                UPDATE users
                SET username = :username, email = :email,
                    role_id = :role_id, department_id = :department_id
                WHERE id = :id AND is_deleted = 0
            ');
        }

        $stmt->execute($params);
    }

    /** Toggle the is_active flag (0 → 1 or 1 → 0). */
    public function toggleActive(int $id): void
    {
        $stmt = Database::pdo()->prepare('
            UPDATE users SET is_active = 1 - is_active WHERE id = ? AND is_deleted = 0
        ');
        $stmt->execute([$id]);
    }

    /** Return all roles ordered by level descending (for dropdown). */
    public function allRoles(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name, slug, level FROM roles WHERE is_deleted = 0 ORDER BY level DESC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Return all departments ordered by name (for dropdown). */
    public function allDepartments(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Count stats for the admin dashboard. */
    public function stats(): array
    {
        $pdo = Database::pdo();

        $total  = (int) $pdo->query('SELECT COUNT(*) FROM users WHERE is_deleted = 0')->fetchColumn();
        $active = (int) $pdo->query('SELECT COUNT(*) FROM users WHERE is_deleted = 0 AND is_active = 1')->fetchColumn();

        $roleStmt = $pdo->query('
            SELECT r.name, COUNT(u.id) AS user_count
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id AND u.is_deleted = 0
            GROUP BY r.id, r.name
            ORDER BY r.level DESC
        ');
        $roles = $roleStmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'total_users'  => $total,
            'active_users' => $active,
            'roles'        => $roles,
        ];
    }

    /** Check if email is already taken (optionally excluding a user ID). */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id != ? AND is_deleted = 0');
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND is_deleted = 0');
            $stmt->execute([$email]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    // ─── Role CRUD ────────────────────────────────────────────────────────────

    /** Find a single role by ID. */
    public function findRole(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM roles WHERE id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /** Create a new role. */
    public function createRole(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO roles (name, slug, level) VALUES (:name, :slug, :level)'
        );
        $stmt->execute([
            'name'  => $data['name'],
            'slug'  => $data['slug'],
            'level' => (int) $data['level'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    /** Update an existing role. */
    public function updateRole(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE roles SET name = :name, slug = :slug, level = :level WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'  => $data['name'],
            'slug'  => $data['slug'],
            'level' => (int) $data['level'],
            'id'    => $id,
        ]);
    }

    /** Soft-delete a role. */
    public function deleteRole(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE roles SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Check if username is already taken (optionally excluding a user ID). */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND id != ? AND is_deleted = 0');
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND is_deleted = 0');
            $stmt->execute([$username]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
