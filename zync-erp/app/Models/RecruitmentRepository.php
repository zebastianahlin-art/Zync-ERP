<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class RecruitmentRepository
{
    public function allDepartments(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function allPositions(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT p.*, d.name AS department_name,
                 (SELECT COUNT(*) FROM recruitment_applicants a WHERE a.position_id = p.id AND a.is_deleted = 0) AS applicant_count
                 FROM recruitment_positions p
                 LEFT JOIN departments d ON p.department_id = d.id
                 WHERE p.is_deleted = 0
                 ORDER BY p.created_at DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function findPosition(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT p.*, d.name AS department_name
                 FROM recruitment_positions p
                 LEFT JOIN departments d ON p.department_id = d.id
                 WHERE p.id = ? AND p.is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function positionApplicants(int $positionId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT * FROM recruitment_applicants WHERE position_id = ? AND is_deleted = 0 ORDER BY applied_at DESC'
            );
            $stmt->execute([$positionId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function createPosition(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO recruitment_positions (title, department_id, description, requirements,
             num_openings, posted_at, closes_at, status, created_by)
             VALUES (:title, :department_id, :description, :requirements,
             :num_openings, :posted_at, :closes_at, :status, :created_by)'
        );
        $stmt->execute([
            'title'         => $data['title'],
            'department_id' => $data['department_id'] ?: null,
            'description'   => $data['description'] ?: null,
            'requirements'  => $data['requirements'] ?: null,
            'num_openings'  => $data['num_openings'] ?? 1,
            'posted_at'     => $data['posted_at'] ?: null,
            'closes_at'     => $data['closes_at'] ?: null,
            'status'        => $data['status'] ?? 'draft',
            'created_by'    => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function allApplicants(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT a.*, p.title AS position_title
                 FROM recruitment_applicants a
                 LEFT JOIN recruitment_positions p ON a.position_id = p.id
                 WHERE a.is_deleted = 0
                 ORDER BY a.applied_at DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
}
