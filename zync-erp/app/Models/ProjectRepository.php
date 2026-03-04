<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class ProjectRepository
{
    public function all(): array
    {
        $stmt = Database::pdo()->query(
            "SELECT p.*, c.name AS customer_name, d.name AS department_name,
             u.full_name AS manager_name
             FROM projects p
             LEFT JOIN customers c ON c.id = p.customer_id
             LEFT JOIN departments d ON d.id = p.department_id
             LEFT JOIN users u ON u.id = p.project_manager_id
             WHERE p.is_deleted = 0 AND p.status NOT IN ('completed','cancelled')
             ORDER BY p.id DESC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allArchived(): array
    {
        $stmt = Database::pdo()->query(
            "SELECT p.*, c.name AS customer_name
             FROM projects p
             LEFT JOIN customers c ON c.id = p.customer_id
             WHERE p.is_deleted = 0 AND p.status IN ('completed','cancelled')
             ORDER BY p.id DESC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT p.*, c.name AS customer_name, d.name AS department_name, u.full_name AS manager_name
             FROM projects p
             LEFT JOIN customers c ON c.id = p.customer_id
             LEFT JOIN departments d ON d.id = p.department_id
             LEFT JOIN users u ON u.id = p.project_manager_id
             WHERE p.id = ? AND p.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO projects (project_number, name, description, customer_id, department_id,
             project_manager_id, status, start_date, end_date, budget_amount, notes)
             VALUES (:project_number, :name, :description, :customer_id, :department_id,
             :project_manager_id, :status, :start_date, :end_date, :budget_amount, :notes)'
        );
        $stmt->execute([
            'project_number'     => $data['project_number'],
            'name'               => $data['name'],
            'description'        => $data['description'] ?? null,
            'customer_id'        => $data['customer_id'] ?: null,
            'department_id'      => $data['department_id'] ?: null,
            'project_manager_id' => $data['project_manager_id'] ?: null,
            'status'             => $data['status'] ?? 'planning',
            'start_date'         => $data['start_date'] ?: null,
            'end_date'           => $data['end_date'] ?: null,
            'budget_amount'      => $data['budget_amount'] ?: null,
            'notes'              => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE projects SET project_number = :project_number, name = :name, description = :description,
             customer_id = :customer_id, department_id = :department_id, project_manager_id = :project_manager_id,
             status = :status, start_date = :start_date, end_date = :end_date,
             budget_amount = :budget_amount, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'project_number'     => $data['project_number'],
            'name'               => $data['name'],
            'description'        => $data['description'] ?? null,
            'customer_id'        => $data['customer_id'] ?: null,
            'department_id'      => $data['department_id'] ?: null,
            'project_manager_id' => $data['project_manager_id'] ?: null,
            'status'             => $data['status'] ?? 'planning',
            'start_date'         => $data['start_date'] ?: null,
            'end_date'           => $data['end_date'] ?: null,
            'budget_amount'      => $data['budget_amount'] ?: null,
            'notes'              => $data['notes'] ?? null,
            'id'                 => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE projects SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function allTasks(int $projectId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT pt.*, u.full_name AS assigned_name
             FROM project_tasks pt
             LEFT JOIN users u ON u.id = pt.assigned_to
             WHERE pt.project_id = ? AND pt.is_deleted = 0
             ORDER BY pt.sort_order ASC, pt.id ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createTask(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO project_tasks (project_id, title, description, assigned_to, status, planned_start, planned_end, estimated_hours, sort_order)
             VALUES (:project_id, :title, :description, :assigned_to, :status, :planned_start, :planned_end, :estimated_hours, :sort_order)'
        );
        $stmt->execute([
            'project_id'      => $data['project_id'],
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'assigned_to'     => $data['assigned_to'] ?: null,
            'status'          => $data['status'] ?? 'todo',
            'planned_start'   => $data['planned_start'] ?: null,
            'planned_end'     => $data['planned_end'] ?: null,
            'estimated_hours' => $data['estimated_hours'] ?: null,
            'sort_order'      => (int) ($data['sort_order'] ?? 0),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function deleteTask(int $taskId): void
    {
        $stmt = Database::pdo()->prepare('UPDATE project_tasks SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$taskId]);
    }

    public function allCustomers(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allDepartments(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allUsers(): array
    {
        $stmt = Database::pdo()->query('SELECT id, full_name FROM users WHERE is_deleted = 0 AND is_active = 1 ORDER BY full_name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
