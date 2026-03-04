<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class RecruitmentRepository
{
    // ─── Positions ────────────────────────────────────────────

    public function allPositions(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT rp.*, d.name AS department_name,
             (SELECT COUNT(*) FROM recruitment_applicants ra WHERE ra.position_id = rp.id AND ra.is_deleted = 0) AS applicant_count
             FROM recruitment_positions rp
             LEFT JOIN departments d ON d.id = rp.department_id
             WHERE rp.is_deleted = 0
             ORDER BY rp.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findPosition(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT rp.*, d.name AS department_name
             FROM recruitment_positions rp
             LEFT JOIN departments d ON d.id = rp.department_id
             WHERE rp.id = ? AND rp.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createPosition(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO recruitment_positions (title, department_id, description, requirements, status, deadline)
             VALUES (:title, :department_id, :description, :requirements, :status, :deadline)'
        );
        $stmt->execute([
            'title'         => $data['title'],
            'department_id' => $data['department_id'] ?: null,
            'description'   => $data['description'] ?? null,
            'requirements'  => $data['requirements'] ?? null,
            'status'        => $data['status'] ?? 'draft',
            'deadline'      => $data['deadline'] ?: null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updatePosition(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_positions SET title = :title, department_id = :department_id,
             description = :description, requirements = :requirements, status = :status, deadline = :deadline
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'title'         => $data['title'],
            'department_id' => $data['department_id'] ?: null,
            'description'   => $data['description'] ?? null,
            'requirements'  => $data['requirements'] ?? null,
            'status'        => $data['status'] ?? 'draft',
            'deadline'      => $data['deadline'] ?: null,
            'id'            => $id,
        ]);
    }

    public function deletePosition(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE recruitment_positions SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Applicants ───────────────────────────────────────────

    public function allApplicants(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT ra.*, rp.title AS position_title
             FROM recruitment_applicants ra
             JOIN recruitment_positions rp ON rp.id = ra.position_id
             WHERE ra.is_deleted = 0
             ORDER BY ra.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function applicantsForPosition(int $positionId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM recruitment_applicants WHERE position_id = ? AND is_deleted = 0 ORDER BY id DESC'
        );
        $stmt->execute([$positionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findApplicant(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT ra.*, rp.title AS position_title
             FROM recruitment_applicants ra
             JOIN recruitment_positions rp ON rp.id = ra.position_id
             WHERE ra.id = ? AND ra.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createApplicant(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO recruitment_applicants (position_id, name, email, phone, cover_letter, status, notes)
             VALUES (:position_id, :name, :email, :phone, :cover_letter, :status, :notes)'
        );
        $stmt->execute([
            'position_id'  => $data['position_id'],
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? null,
            'cover_letter' => $data['cover_letter'] ?? null,
            'status'       => $data['status'] ?? 'applied',
            'notes'        => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateApplicantStatus(int $id, string $status, string $notes = ''): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_applicants SET status = :status, notes = :notes WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute(['status' => $status, 'notes' => $notes, 'id' => $id]);
    }

    public function allDepartments(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
