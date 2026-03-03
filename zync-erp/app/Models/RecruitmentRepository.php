<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class RecruitmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Positions ────────────────────────────────

    public function allPositions(?string $status = null): array
    {
        $sql = "SELECT rp.*, d.name AS department_name,
                    (SELECT COUNT(*) FROM recruitment_candidates WHERE position_id = rp.id) AS candidate_count
                FROM recruitment_positions rp
                LEFT JOIN departments d ON rp.department_id = d.id
                WHERE 1=1";
        $params = [];
        if ($status) {
            $sql .= " AND rp.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY rp.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPosition(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT rp.*, d.name AS department_name
             FROM recruitment_positions rp
             LEFT JOIN departments d ON rp.department_id = d.id
             WHERE rp.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createPosition(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO recruitment_positions (title, department_id, description, requirements, employment_type, salary_range_min, salary_range_max, status, opening_date, closing_date, positions_count, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['title'], $data['department_id'] ?: null,
            $data['description'] ?? null, $data['requirements'] ?? null,
            $data['employment_type'] ?? 'full_time',
            $data['salary_range_min'] ?: null, $data['salary_range_max'] ?: null,
            $data['status'] ?? 'draft',
            $data['opening_date'] ?: null, $data['closing_date'] ?: null,
            $data['positions_count'] ?? 1, $data['created_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePosition(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE recruitment_positions SET title = ?, department_id = ?, description = ?, requirements = ?, employment_type = ?, salary_range_min = ?, salary_range_max = ?, status = ?, opening_date = ?, closing_date = ?, positions_count = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['title'], $data['department_id'] ?: null,
            $data['description'] ?? null, $data['requirements'] ?? null,
            $data['employment_type'] ?? 'full_time',
            $data['salary_range_min'] ?: null, $data['salary_range_max'] ?: null,
            $data['status'] ?? 'draft',
            $data['opening_date'] ?: null, $data['closing_date'] ?: null,
            $data['positions_count'] ?? 1, $id,
        ]);
    }

    public function deletePosition(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM recruitment_positions WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ── Candidates ───────────────────────────────

    public function candidatesForPosition(int $positionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT rc.*, 
                    (SELECT COUNT(*) FROM recruitment_interviews WHERE candidate_id = rc.id) AS interview_count
             FROM recruitment_candidates rc
             WHERE rc.position_id = ?
             ORDER BY rc.applied_at DESC"
        );
        $stmt->execute([$positionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findCandidate(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT rc.*, rp.title AS position_title
             FROM recruitment_candidates rc
             JOIN recruitment_positions rp ON rc.position_id = rp.id
             WHERE rc.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createCandidate(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO recruitment_candidates (position_id, first_name, last_name, email, phone, cv_path, cover_letter, status, rating, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['position_id'], $data['first_name'], $data['last_name'],
            $data['email'], $data['phone'] ?? null,
            $data['cv_path'] ?? null, $data['cover_letter'] ?? null,
            $data['status'] ?? 'new', $data['rating'] ?? null, $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateCandidate(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE recruitment_candidates SET first_name = ?, last_name = ?, email = ?, phone = ?, cv_path = ?, cover_letter = ?, status = ?, rating = ?, notes = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['first_name'], $data['last_name'], $data['email'],
            $data['phone'] ?? null, $data['cv_path'] ?? null,
            $data['cover_letter'] ?? null, $data['status'] ?? 'new',
            $data['rating'] ?? null, $data['notes'] ?? null, $id,
        ]);
    }

    public function updateCandidateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare("UPDATE recruitment_candidates SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public function deleteCandidate(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM recruitment_candidates WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ── Interviews ───────────────────────────────

    public function interviewsForCandidate(int $candidateId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ri.*, e.first_name AS interviewer_first, e.last_name AS interviewer_last
             FROM recruitment_interviews ri
             LEFT JOIN employees e ON ri.interviewer_id = e.id
             WHERE ri.candidate_id = ?
             ORDER BY ri.scheduled_at DESC"
        );
        $stmt->execute([$candidateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createInterview(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO recruitment_interviews (candidate_id, interviewer_id, scheduled_at, duration_minutes, location, type, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['candidate_id'], $data['interviewer_id'] ?: null,
            $data['scheduled_at'], $data['duration_minutes'] ?? 60,
            $data['location'] ?? null, $data['type'] ?? 'onsite',
            $data['status'] ?? 'scheduled',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateInterview(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE recruitment_interviews SET interviewer_id = ?, scheduled_at = ?, duration_minutes = ?, location = ?, type = ?, status = ?, feedback = ?, rating = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['interviewer_id'] ?: null, $data['scheduled_at'],
            $data['duration_minutes'] ?? 60, $data['location'] ?? null,
            $data['type'] ?? 'onsite', $data['status'] ?? 'scheduled',
            $data['feedback'] ?? null, $data['rating'] ?? null, $id,
        ]);
    }

    // ── Stats ────────────────────────────────────

    public function stats(): array
    {
        $s = [];
        $stmt = $this->db->query("SELECT COUNT(*) FROM recruitment_positions WHERE status = 'open'");
        $s['open_positions'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM recruitment_candidates WHERE status = 'new'");
        $s['new_candidates'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM recruitment_interviews WHERE status = 'scheduled' AND scheduled_at >= NOW()");
        $s['upcoming_interviews'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM recruitment_candidates WHERE status = 'hired' AND YEAR(updated_at) = YEAR(CURDATE())");
        $s['hired_this_year'] = (int) $stmt->fetchColumn();

        return $s;
    }
}
