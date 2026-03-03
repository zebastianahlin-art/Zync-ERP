<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ProjectRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ─── Projekt ───
    public function all(array $filters = []): array
    {
        $where = ['p.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = 'p.category = ?';
            $params[] = $filters['category'];
        }
        if (!empty($filters['manager_id'])) {
            $where[] = 'p.manager_id = ?';
            $params[] = $filters['manager_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.project_number LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT p.*, u.full_name AS manager_name, c.name AS customer_name, d.name AS department_name
                FROM projects p
                LEFT JOIN users u ON u.id = p.manager_id
                LEFT JOIN customers c ON c.id = p.customer_id
                LEFT JOIN departments d ON d.id = p.department_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT p.*, u.full_name AS manager_name, c.name AS customer_name, d.name AS department_name,
                                           cr.full_name AS created_by_name
                                    FROM projects p
                                    LEFT JOIN users u ON u.id = p.manager_id
                                    LEFT JOIN customers c ON c.id = p.customer_id
                                    LEFT JOIN departments d ON d.id = p.department_id
                                    LEFT JOIN users cr ON cr.id = p.created_by
                                    WHERE p.id = ? AND p.is_deleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO projects (project_number, name, description, status, priority, category,
                    customer_id, department_id, manager_id, budget_hours, budget_material, budget_total, hourly_rate,
                    start_date, end_date, created_by)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['project_number'], $data['name'], $data['description'] ?? null,
            $data['status'] ?? 'planning', $data['priority'] ?? 'normal', $data['category'] ?? 'other',
            $data['customer_id'] ?: null, $data['department_id'] ?: null, $data['manager_id'] ?: null,
            $data['budget_hours'] ?? 0, $data['budget_material'] ?? 0, $data['budget_total'] ?? 0,
            $data['hourly_rate'] ?? 850, $data['start_date'] ?: null, $data['end_date'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare("UPDATE projects SET name=?, description=?, status=?, priority=?, category=?,
                    customer_id=?, department_id=?, manager_id=?, budget_hours=?, budget_material=?, budget_total=?,
                    hourly_rate=?, start_date=?, end_date=?, completion_pct=? WHERE id=?");
        $stmt->execute([
            $data['name'], $data['description'] ?? null, $data['status'], $data['priority'], $data['category'],
            $data['customer_id'] ?: null, $data['department_id'] ?: null, $data['manager_id'] ?: null,
            $data['budget_hours'] ?? 0, $data['budget_material'] ?? 0, $data['budget_total'] ?? 0,
            $data['hourly_rate'] ?? 850, $data['start_date'] ?: null, $data['end_date'] ?: null,
            $data['completion_pct'] ?? 0, $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare("UPDATE projects SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function complete(int $id, array $data): void
    {
        $stmt = $this->db->prepare("UPDATE projects SET status='completed', actual_end_date=CURDATE(),
                    completion_pct=100, evaluation_score=?, evaluation_notes=? WHERE id=?");
        $stmt->execute([$data['evaluation_score'] ?? null, $data['evaluation_notes'] ?? null, $id]);
    }

    public function nextProjectNumber(): string
    {
        $prefix = 'P' . date('y');
        $stmt = $this->db->prepare("SELECT project_number FROM projects WHERE project_number LIKE ? ORDER BY project_number DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetchColumn();
        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Faser ───
    public function phases(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM project_phases WHERE project_id = ? ORDER BY sort_order, id");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPhase(int $projectId, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO project_phases (project_id, name, description, sort_order, start_date, end_date, budget_hours)
                VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$projectId, $data['name'], $data['description'] ?? null,
            $data['sort_order'] ?? 0, $data['start_date'] ?: null, $data['end_date'] ?: null, $data['budget_hours'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePhaseStatus(int $phaseId, string $status): void
    {
        $this->db->prepare("UPDATE project_phases SET status = ? WHERE id = ?")->execute([$status, $phaseId]);
    }

    public function deletePhase(int $phaseId): void
    {
        $this->db->prepare("DELETE FROM project_phases WHERE id = ?")->execute([$phaseId]);
    }

    // ─── Milstolpar ───
    public function milestones(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT m.*, ph.name AS phase_name FROM project_milestones m
                LEFT JOIN project_phases ph ON ph.id = m.phase_id
                WHERE m.project_id = ? ORDER BY m.due_date");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createMilestone(int $projectId, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO project_milestones (project_id, phase_id, name, description, due_date, is_invoiceable, invoice_amount)
                VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$projectId, $data['phase_id'] ?: null, $data['name'], $data['description'] ?? null,
            $data['due_date'], $data['is_invoiceable'] ?? 0, $data['invoice_amount'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function completeMilestone(int $milestoneId): void
    {
        $this->db->prepare("UPDATE project_milestones SET completed_at = NOW() WHERE id = ?")->execute([$milestoneId]);
    }

    public function deleteMilestone(int $milestoneId): void
    {
        $this->db->prepare("DELETE FROM project_milestones WHERE id = ?")->execute([$milestoneId]);
    }

    // ─── Uppgifter ───
    public function tasks(int $projectId, ?string $status = null): array
    {
        $sql = "SELECT t.*, u.full_name AS assigned_name, ph.name AS phase_name
                FROM project_tasks t
                LEFT JOIN users u ON u.id = t.assigned_to
                LEFT JOIN project_phases ph ON ph.id = t.phase_id
                WHERE t.project_id = ? AND t.is_deleted = 0";
        $params = [$projectId];
        if ($status) { $sql .= " AND t.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY t.sort_order, t.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask(int $projectId, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO project_tasks (project_id, phase_id, title, description, status, priority,
                    assigned_to, estimated_hours, due_date, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$projectId, $data['phase_id'] ?: null, $data['title'], $data['description'] ?? null,
            $data['status'] ?? 'todo', $data['priority'] ?? 'normal', $data['assigned_to'] ?: null,
            $data['estimated_hours'] ?: null, $data['due_date'] ?: null, $data['sort_order'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function updateTaskStatus(int $taskId, string $status): void
    {
        $completed = $status === 'done' ? ", completed_at = NOW()" : ", completed_at = NULL";
        $this->db->prepare("UPDATE project_tasks SET status = ? $completed WHERE id = ?")->execute([$status, $taskId]);
    }

    public function deleteTask(int $taskId): void
    {
        $this->db->prepare("UPDATE project_tasks SET is_deleted = 1 WHERE id = ?")->execute([$taskId]);
    }

    // ─── Tidrapporter ───
    public function timesheets(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT t.*, u.full_name AS user_name, tk.title AS task_title,
                    ap.full_name AS approved_by_name
                FROM project_timesheets t
                LEFT JOIN users u ON u.id = t.user_id
                LEFT JOIN project_tasks tk ON tk.id = t.task_id
                LEFT JOIN users ap ON ap.id = t.approved_by
                WHERE t.project_id = ? ORDER BY t.work_date DESC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allTimesheets(array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['user_id'])) { $where[] = 't.user_id = ?'; $params[] = $filters['user_id']; }
        if (!empty($filters['from'])) { $where[] = 't.work_date >= ?'; $params[] = $filters['from']; }
        if (!empty($filters['to'])) { $where[] = 't.work_date <= ?'; $params[] = $filters['to']; }
        if (isset($filters['approved'])) { $where[] = 't.approved = ?'; $params[] = $filters['approved']; }

        $sql = "SELECT t.*, u.full_name AS user_name, p.project_number, p.name AS project_name, tk.title AS task_title
                FROM project_timesheets t
                JOIN users u ON u.id = t.user_id
                JOIN projects p ON p.id = t.project_id
                LEFT JOIN project_tasks tk ON tk.id = t.task_id
                WHERE " . implode(' AND ', $where) . " ORDER BY t.work_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTimesheet(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO project_timesheets (project_id, task_id, user_id, work_date, hours, description, billable)
                VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$data['project_id'], $data['task_id'] ?: null, $data['user_id'],
            $data['work_date'], $data['hours'], $data['description'] ?? null, $data['billable'] ?? 1]);
        // Uppdatera actual_hours på task och projekt
        if (!empty($data['task_id'])) {
            $this->db->prepare("UPDATE project_tasks SET actual_hours = actual_hours + ? WHERE id = ?")->execute([$data['hours'], $data['task_id']]);
        }
        $this->db->prepare("UPDATE projects SET actual_hours = actual_hours + ? WHERE id = ?")->execute([$data['hours'], $data['project_id']]);
        return (int) $this->db->lastInsertId();
    }

    public function approveTimesheet(int $id, int $approvedBy): void
    {
        $this->db->prepare("UPDATE project_timesheets SET approved = 1, approved_by = ?, approved_at = NOW() WHERE id = ?")
            ->execute([$approvedBy, $id]);
    }

    // ─── Budget ───
    public function budgetLines(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT b.*, ph.name AS phase_name FROM project_budget_lines b
                LEFT JOIN project_phases ph ON ph.id = b.phase_id
                WHERE b.project_id = ? ORDER BY b.id");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBudgetLine(int $projectId, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO project_budget_lines (project_id, phase_id, category, description, budgeted_amount)
                VALUES (?,?,?,?,?)");
        $stmt->execute([$projectId, $data['phase_id'] ?: null, $data['category'], $data['description'], $data['budgeted_amount'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function deleteBudgetLine(int $id): void
    {
        $this->db->prepare("DELETE FROM project_budget_lines WHERE id = ?")->execute([$id]);
    }

    // ─── Risker ───
    public function risks(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT r.*, u.full_name AS owner_name FROM project_risks r
                LEFT JOIN users u ON u.id = r.owner_id WHERE r.project_id = ? ORDER BY r.id");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRisk(int $projectId, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO project_risks (project_id, title, description, probability, impact, mitigation, owner_id)
                VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$projectId, $data['title'], $data['description'] ?? null,
            $data['probability'] ?? 'medium', $data['impact'] ?? 'medium', $data['mitigation'] ?? null, $data['owner_id'] ?: null]);
        return (int) $this->db->lastInsertId();
    }

    public function updateRiskStatus(int $riskId, string $status): void
    {
        $this->db->prepare("UPDATE project_risks SET status = ? WHERE id = ?")->execute([$status, $riskId]);
    }

    // ─── Logg ───
    public function log(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT l.*, u.full_name AS user_name FROM project_log l
                JOIN users u ON u.id = l.user_id WHERE l.project_id = ? ORDER BY l.created_at DESC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addLog(int $projectId, int $userId, string $action, string $message): void
    {
        $this->db->prepare("INSERT INTO project_log (project_id, user_id, action, message) VALUES (?,?,?,?)")
            ->execute([$projectId, $userId, $action, $message]);
    }

    // ─── Medlemmar ───
    public function members(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT m.*, u.full_name, u.email FROM project_members m
                JOIN users u ON u.id = m.user_id WHERE m.project_id = ? ORDER BY m.role, u.full_name");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMember(int $projectId, int $userId, string $role = 'member'): void
    {
        $this->db->prepare("INSERT IGNORE INTO project_members (project_id, user_id, role) VALUES (?,?,?)")
            ->execute([$projectId, $userId, $role]);
    }

    public function removeMember(int $projectId, int $userId): void
    {
        $this->db->prepare("DELETE FROM project_members WHERE project_id = ? AND user_id = ?")->execute([$projectId, $userId]);
    }

    // ─── Stats ───
    public function stats(): array
    {
        $row = $this->db->query("SELECT
            COUNT(CASE WHEN status = 'active' THEN 1 END) AS active,
            COUNT(CASE WHEN status = 'planning' THEN 1 END) AS planning,
            COUNT(CASE WHEN status = 'on_hold' THEN 1 END) AS on_hold,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed,
            COALESCE(SUM(budget_total), 0) AS total_budget,
            COALESCE(SUM(actual_cost), 0) AS total_actual
            FROM projects WHERE is_deleted = 0")->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
}
