<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ProjectRepository
{
    public function allCustomers(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allUsers(): array
    {
        return Database::pdo()->query(
            'SELECT id, full_name, username FROM users WHERE is_active = 1 ORDER BY full_name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        return Database::pdo()->query(
            'SELECT p.*, c.name AS customer_name, u.full_name AS manager_name
             FROM projects p
             LEFT JOIN customers c ON p.customer_id = c.id
             LEFT JOIN users u ON p.manager_id = u.id
             WHERE p.is_deleted = 0
             ORDER BY p.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT p.*, c.name AS customer_name, u.full_name AS manager_name
             FROM projects p
             LEFT JOIN customers c ON p.customer_id = c.id
             LEFT JOIN users u ON p.manager_id = u.id
             WHERE p.id = ? AND p.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function tasks(int $projectId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT t.*, u.full_name AS assigned_name
             FROM project_tasks t
             LEFT JOIN users u ON t.assigned_to = u.id
             WHERE t.project_id = ? AND t.is_deleted = 0
             ORDER BY t.due_date ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function budgetLines(int $projectId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM project_budget_lines WHERE project_id = ? AND is_deleted = 0 ORDER BY id ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO projects (project_number, name, description, customer_id, manager_id,
             start_date, end_date, status, budget, created_by)
             VALUES (:project_number, :name, :description, :customer_id, :manager_id,
             :start_date, :end_date, :status, :budget, :created_by)'
        );
        $stmt->execute([
            'project_number' => $data['project_number'],
            'name'           => $data['name'],
            'description'    => $data['description'] ?: null,
            'customer_id'    => $data['customer_id'] ?: null,
            'manager_id'     => $data['manager_id'] ?: null,
            'start_date'     => $data['start_date'] ?: null,
            'end_date'       => $data['end_date'] ?: null,
            'status'         => $data['status'] ?? 'planning',
            'budget'         => $data['budget'] ?: 0,
            'created_by'     => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE projects SET project_number = :project_number, name = :name,
             description = :description, customer_id = :customer_id, manager_id = :manager_id,
             start_date = :start_date, end_date = :end_date, status = :status, budget = :budget
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'project_number' => $data['project_number'],
            'name'           => $data['name'],
            'description'    => $data['description'] ?: null,
            'customer_id'    => $data['customer_id'] ?: null,
            'manager_id'     => $data['manager_id'] ?: null,
            'start_date'     => $data['start_date'] ?: null,
            'end_date'       => $data['end_date'] ?: null,
            'status'         => $data['status'] ?? 'planning',
            'budget'         => $data['budget'] ?: 0,
            'id'             => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE projects SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
