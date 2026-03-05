<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ProjectRepository
{
    public function allCustomers(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function allUsers(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, full_name, username FROM users WHERE is_active = 1 ORDER BY full_name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function all(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT p.*, c.name AS customer_name, u.full_name AS manager_name
                 FROM projects p
                 LEFT JOIN customers c ON p.customer_id = c.id
                 LEFT JOIN users u ON p.manager_id = u.id
                 WHERE p.is_deleted = 0
                 ORDER BY p.created_at DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT p.*, c.name AS customer_name, u.full_name AS manager_name
                 FROM projects p
                 LEFT JOIN customers c ON p.customer_id = c.id
                 LEFT JOIN users u ON p.manager_id = u.id
                 WHERE p.id = ? AND p.is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function tasks(int $projectId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT t.*, u.full_name AS assigned_name
                 FROM project_tasks t
                 LEFT JOIN users u ON t.assigned_to = u.id
                 WHERE t.project_id = ? AND t.is_deleted = 0
                 ORDER BY t.due_date ASC'
            );
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function budgetLines(int $projectId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT * FROM project_budget_lines WHERE project_id = ? AND is_deleted = 0 ORDER BY id ASC'
            );
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO projects (project_number, name, description, customer_id, manager_id,
             start_date, end_date, status, project_type, budget, planned_budget, created_by)
             VALUES (:project_number, :name, :description, :customer_id, :manager_id,
             :start_date, :end_date, :status, :project_type, :budget, :planned_budget, :created_by)'
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
            'project_type'   => in_array($data['project_type'] ?? '', ['internal','external'], true) ? $data['project_type'] : 'internal',
            'budget'         => $data['budget'] ?: 0,
            'planned_budget' => $data['planned_budget'] ?: $data['budget'] ?: 0,
            'created_by'     => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE projects SET project_number = :project_number, name = :name,
             description = :description, customer_id = :customer_id, manager_id = :manager_id,
             start_date = :start_date, end_date = :end_date, status = :status,
             project_type = :project_type, budget = :budget, planned_budget = :planned_budget
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
            'project_type'   => in_array($data['project_type'] ?? '', ['internal','external'], true) ? $data['project_type'] : 'internal',
            'budget'         => $data['budget'] ?: 0,
            'planned_budget' => $data['planned_budget'] ?: $data['budget'] ?: 0,
            'id'             => $id,
        ]);
    }

    /** Recalculate actual_cost from project_costs + linked POs and store it */
    public function recalcActualCost(int $projectId): void
    {
        try {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare(
                'SELECT COALESCE(SUM(amount),0) FROM project_costs WHERE project_id = ? AND is_deleted = 0'
            );
            $stmt->execute([$projectId]);
            $costsTotal = (float) $stmt->fetchColumn();

            $stmt = $pdo->prepare(
                'SELECT COALESCE(SUM(po.total_amount),0)
                 FROM project_purchase_orders ppo
                 JOIN purchase_orders po ON po.id = ppo.purchase_order_id
                 WHERE ppo.project_id = ?'
            );
            $stmt->execute([$projectId]);
            $poTotal = (float) $stmt->fetchColumn();

            $pdo->prepare('UPDATE projects SET actual_cost = ? WHERE id = ?')
                ->execute([$costsTotal + $poTotal, $projectId]);
        } catch (\Exception $e) {
            // silently fail – actual_cost is a cache column
        }
    }

    /** Return the current actual_cost for a project without a full re-fetch */
    public function getActualCost(int $projectId): float
    {
        try {
            $stmt = Database::pdo()->prepare('SELECT actual_cost FROM projects WHERE id = ?');
            $stmt->execute([$projectId]);
            return (float) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE projects SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function addTask(int $projectId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO project_tasks (project_id, title, assigned_to, due_date, priority, status)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $projectId,
            $data['title'],
            $data['assigned_to'] ?: null,
            $data['due_date'] ?: null,
            $data['priority'] ?? 'normal',
            $data['status'] ?? 'todo',
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function findTask(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT t.*, u.full_name AS assigned_name
                 FROM project_tasks t
                 LEFT JOIN users u ON t.assigned_to = u.id
                 WHERE t.id = ? AND t.is_deleted = 0 LIMIT 1'
            );
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row !== false ? $row : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateTask(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE project_tasks SET title=?, assigned_to=?, due_date=?, priority=?, status=?
             WHERE id=? AND is_deleted=0'
        );
        $stmt->execute([
            $data['title'],
            $data['assigned_to'] ?: null,
            $data['due_date'] ?: null,
            $data['priority'] ?? 'normal',
            $data['status'] ?? 'todo',
            $id,
        ]);
    }

    public function deleteTask(int $id): void
    {
        Database::pdo()->prepare('UPDATE project_tasks SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function addBudgetLine(int $projectId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO project_budget_lines (project_id, description, budgeted, actual)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $projectId,
            $data['description'],
            (float) ($data['budgeted_amount'] ?? 0),
            (float) ($data['actual_amount'] ?? 0),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function deleteBudgetLine(int $id): void
    {
        Database::pdo()->prepare('UPDATE project_budget_lines SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    // ─── C2: Stakeholders ────────────────────────────────────────────────────

    public function stakeholders(int $projectId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT * FROM project_stakeholders WHERE project_id = ? AND is_deleted = 0 ORDER BY name ASC'
            );
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function addStakeholder(int $projectId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO project_stakeholders (project_id, name, role, email, phone, notes)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $projectId,
            $data['name'],
            $data['role'] ?: 'Teammedlem',
            $data['email'] ?: null,
            $data['phone'] ?: null,
            $data['notes'] ?: null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function deleteStakeholder(int $id): void
    {
        Database::pdo()->prepare('UPDATE project_stakeholders SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    // ─── C3: Koppling till Inköp ─────────────────────────────────────────────

    public function linkedPurchaseOrders(int $projectId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT ppo.id AS link_id, ppo.notes AS link_notes,
                        po.id, po.order_number, po.status, po.total_amount, po.order_date,
                        s.name AS supplier_name
                 FROM project_purchase_orders ppo
                 JOIN purchase_orders po ON po.id = ppo.purchase_order_id
                 LEFT JOIN suppliers s ON po.supplier_id = s.id
                 WHERE ppo.project_id = ?
                 ORDER BY po.order_date DESC'
            );
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function linkPurchaseOrder(int $projectId, int $poId, string $notes = ''): void
    {
        try {
            $stmt = Database::pdo()->prepare(
                'INSERT IGNORE INTO project_purchase_orders (project_id, purchase_order_id, notes) VALUES (?, ?, ?)'
            );
            $stmt->execute([$projectId, $poId, $notes ?: null]);
        } catch (\Exception $e) {
            // duplicate — ignore
        }
    }

    public function unlinkPurchaseOrder(int $linkId): void
    {
        Database::pdo()->prepare('DELETE FROM project_purchase_orders WHERE id = ?')->execute([$linkId]);
    }

    public function allPurchaseOrders(): array
    {
        try {
            return Database::pdo()->query(
                "SELECT po.id, po.order_number, po.total_amount, po.status, s.name AS supplier_name
                 FROM purchase_orders po
                 LEFT JOIN suppliers s ON po.supplier_id = s.id
                 WHERE po.is_deleted = 0
                 ORDER BY po.order_date DESC"
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    // ─── C6: Kostnader ───────────────────────────────────────────────────────

    public function costs(int $projectId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT pc.*, u.full_name AS created_by_name
                 FROM project_costs pc
                 LEFT JOIN users u ON pc.created_by = u.id
                 WHERE pc.project_id = ? AND pc.is_deleted = 0
                 ORDER BY pc.cost_date DESC, pc.id DESC'
            );
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function addCost(int $projectId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO project_costs (project_id, description, amount, cost_date, category, created_by)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $projectId,
            $data['description'],
            (float) ($data['amount'] ?? 0),
            $data['cost_date'] ?: null,
            $data['category'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function deleteCost(int $id): void
    {
        Database::pdo()->prepare('UPDATE project_costs SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }
}
