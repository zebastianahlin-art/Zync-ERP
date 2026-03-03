<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class TrainingRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Courses ──────────────────────────────────

    public function allCourses(?string $status = null): array
    {
        $sql = "SELECT tc.*,
                    (SELECT COUNT(DISTINCT ts.id) FROM training_sessions ts WHERE ts.course_id = tc.id) AS session_count,
                    (SELECT COUNT(DISTINCT tp.employee_id) FROM training_participants tp JOIN training_sessions ts2 ON tp.session_id = ts2.id WHERE ts2.course_id = tc.id AND tp.status = 'completed') AS completed_count
                FROM training_courses tc WHERE 1=1";
        $params = [];
        if ($status) {
            $sql .= " AND tc.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY tc.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findCourse(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM training_courses WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createCourse(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO training_courses (name, description, provider, duration_hours, cost, is_mandatory, recurrence_months, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'], $data['description'] ?? null,
            $data['provider'] ?? null, $data['duration_hours'] ?: null,
            $data['cost'] ?: null, $data['is_mandatory'] ?? 0,
            $data['recurrence_months'] ?: null, $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateCourse(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE training_courses SET name = ?, description = ?, provider = ?, duration_hours = ?, cost = ?, is_mandatory = ?, recurrence_months = ?, status = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['name'], $data['description'] ?? null,
            $data['provider'] ?? null, $data['duration_hours'] ?: null,
            $data['cost'] ?: null, $data['is_mandatory'] ?? 0,
            $data['recurrence_months'] ?: null, $data['status'] ?? 'active', $id,
        ]);
    }

    public function deleteCourse(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM training_courses WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ── Sessions ─────────────────────────────────

    public function sessionsForCourse(int $courseId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ts.*,
                    (SELECT COUNT(*) FROM training_participants WHERE session_id = ts.id) AS participant_count
             FROM training_sessions ts
             WHERE ts.course_id = ?
             ORDER BY ts.start_date DESC"
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allSessions(?string $status = null): array
    {
        $sql = "SELECT ts.*, tc.name AS course_name, tc.is_mandatory,
                    (SELECT COUNT(*) FROM training_participants WHERE session_id = ts.id) AS participant_count
                FROM training_sessions ts
                JOIN training_courses tc ON ts.course_id = tc.id
                WHERE 1=1";
        $params = [];
        if ($status) {
            $sql .= " AND ts.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY ts.start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findSession(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT ts.*, tc.name AS course_name
             FROM training_sessions ts
             JOIN training_courses tc ON ts.course_id = tc.id
             WHERE ts.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createSession(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO training_sessions (course_id, instructor, location, start_date, end_date, max_participants, status, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['course_id'], $data['instructor'] ?? null,
            $data['location'] ?? null, $data['start_date'], $data['end_date'],
            $data['max_participants'] ?: null, $data['status'] ?? 'planned',
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateSession(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE training_sessions SET instructor = ?, location = ?, start_date = ?, end_date = ?, max_participants = ?, status = ?, notes = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['instructor'] ?? null, $data['location'] ?? null,
            $data['start_date'], $data['end_date'],
            $data['max_participants'] ?: null, $data['status'] ?? 'planned',
            $data['notes'] ?? null, $id,
        ]);
    }

    public function deleteSession(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM training_sessions WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ── Participants ───────────────���─────────────

    public function participantsForSession(int $sessionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT tp.*, e.first_name, e.last_name, e.employee_number, d.name AS department_name
             FROM training_participants tp
             JOIN employees e ON tp.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE tp.session_id = ?
             ORDER BY e.last_name, e.first_name"
        );
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addParticipant(int $sessionId, int $employeeId): int
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO training_participants (session_id, employee_id) VALUES (?, ?)"
        );
        $stmt->execute([$sessionId, $employeeId]);
        return (int) $this->db->lastInsertId();
    }

    public function updateParticipant(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE training_participants SET status = ?, score = ?, certificate_issued = ?, completed_at = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['status'] ?? 'enrolled', $data['score'] ?? null,
            $data['certificate_issued'] ?? 0,
            ($data['status'] ?? '') === 'completed' ? date('Y-m-d H:i:s') : ($data['completed_at'] ?? null),
            $id,
        ]);
    }

    public function removeParticipant(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM training_participants WHERE id = ?");
        $stmt->execute([$id]);
    }

    /** Training history for a specific employee */
    public function employeeTraining(int $employeeId): array
    {
        $stmt = $this->db->prepare(
            "SELECT tp.*, ts.start_date, ts.end_date, ts.location, tc.name AS course_name, tc.is_mandatory
             FROM training_participants tp
             JOIN training_sessions ts ON tp.session_id = ts.id
             JOIN training_courses tc ON ts.course_id = tc.id
             WHERE tp.employee_id = ?
             ORDER BY ts.start_date DESC"
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Employees who need mandatory training renewal */
    public function overdueTraining(): array
    {
        $stmt = $this->db->query(
            "SELECT e.id AS employee_id, e.first_name, e.last_name, e.employee_number,
                    tc.id AS course_id, tc.name AS course_name, tc.recurrence_months,
                    MAX(ts.end_date) AS last_completed,
                    DATE_ADD(MAX(ts.end_date), INTERVAL tc.recurrence_months MONTH) AS due_date
             FROM employees e
             CROSS JOIN training_courses tc
             LEFT JOIN training_participants tp ON tp.employee_id = e.id AND tp.status = 'completed'
             LEFT JOIN training_sessions ts ON tp.session_id = ts.id AND ts.course_id = tc.id
             WHERE e.status = 'active' AND e.is_deleted = 0
               AND tc.is_mandatory = 1 AND tc.recurrence_months IS NOT NULL AND tc.status = 'active'
             GROUP BY e.id, tc.id
             HAVING last_completed IS NULL OR due_date <= CURDATE()
             ORDER BY due_date ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Stats ────────────────────────────────────

    public function stats(): array
    {
        $s = [];
        $stmt = $this->db->query("SELECT COUNT(*) FROM training_courses WHERE status = 'active'");
        $s['active_courses'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM training_sessions WHERE status = 'planned' AND start_date >= CURDATE()");
        $s['upcoming_sessions'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM training_participants WHERE status = 'completed' AND YEAR(completed_at) = YEAR(CURDATE())");
        $s['completed_this_year'] = (int) $stmt->fetchColumn();

        $s['overdue_count'] = count($this->overdueTraining());

        return $s;
    }
}
