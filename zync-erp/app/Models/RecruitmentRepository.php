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

    public function updatePosition(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_positions SET title=:title, department_id=:department_id,
             description=:description, requirements=:requirements,
             num_openings=:num_openings, posted_at=:posted_at, closes_at=:closes_at, status=:status
             WHERE id=:id AND is_deleted=0'
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
            'id'            => $id,
        ]);
    }

    public function deletePosition(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE recruitment_positions SET is_deleted=1 WHERE id=?');
        $stmt->execute([$id]);
    }

    public function createApplicant(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO recruitment_applicants
             (position_id, first_name, last_name, email, phone, applied_at, status, notes, created_by)
             VALUES (:position_id, :first_name, :last_name, :email, :phone, :applied_at, :status, :notes, :created_by)'
        );
        $stmt->execute([
            'position_id' => $data['position_id'],
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'email'       => $data['email'] ?: null,
            'phone'       => $data['phone'] ?: null,
            'applied_at'  => $data['applied_at'] ?: date('Y-m-d'),
            'status'      => $data['status'] ?? 'new',
            'notes'       => $data['notes'] ?: null,
            'created_by'  => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function findApplicant(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT a.*, p.title AS position_title
                 FROM recruitment_applicants a
                 LEFT JOIN recruitment_positions p ON a.position_id = p.id
                 WHERE a.id = ? AND a.is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateApplicant(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_applicants SET
             first_name=:first_name, last_name=:last_name, email=:email, phone=:phone,
             applied_at=:applied_at, status=:status, notes=:notes,
             cv_url=:cv_url, cover_letter=:cover_letter, salary_expectation=:salary_expectation
             WHERE id=:id AND is_deleted=0'
        );
        $stmt->execute([
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'],
            'email'              => $data['email'] ?: null,
            'phone'              => $data['phone'] ?: null,
            'applied_at'         => $data['applied_at'] ?: null,
            'status'             => $data['status'] ?? 'new',
            'notes'              => $data['notes'] ?: null,
            'cv_url'             => $data['cv_url'] ?: null,
            'cover_letter'       => $data['cover_letter'] ?: null,
            'salary_expectation' => ($data['salary_expectation'] ?? '') !== '' ? $data['salary_expectation'] : null,
            'id'                 => $id,
        ]);
    }

    public function deleteApplicant(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE recruitment_applicants SET is_deleted=1 WHERE id=?');
        $stmt->execute([$id]);
    }

    public function updateApplicantStatus(int $id, string $status): void
    {
        $stmt = Database::pdo()->prepare('UPDATE recruitment_applicants SET status=:status WHERE id=:id AND is_deleted=0');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function positionStats(int $positionId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT status, COUNT(*) AS cnt FROM recruitment_applicants
                 WHERE position_id = ? AND is_deleted = 0 GROUP BY status'
            );
            $stmt->execute([$positionId]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stats = [];
            foreach ($rows as $r) {
                $stats[$r['status']] = (int) $r['cnt'];
            }
            return $stats;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function pipelineStats(int $positionId): array
    {
        return $this->positionStats($positionId);
    }

    public function convertToEmployee(int $applicantId): array
    {
        $applicant = $this->findApplicant($applicantId);
        if ($applicant === null) {
            return [];
        }

        // Return data pre-populated for new employee form
        return [
            'first_name'          => $applicant['first_name'] ?? '',
            'last_name'           => $applicant['last_name'] ?? '',
            'email'               => $applicant['email'] ?? '',
            'phone'               => $applicant['phone'] ?? '',
            'hire_date'           => date('Y-m-d'),
            'status'              => 'active',
            'position'            => $applicant['position_title'] ?? '',
            'notes'               => 'Rekryterad via: ' . ($applicant['position_title'] ?? ''),
        ];
    }

    public function updateApplicantPipeline(int $id, string $step): void
    {
        $validSteps = ['new', 'screening', 'interview', 'offer', 'hired', 'rejected'];
        if (!in_array($step, $validSteps, true)) {
            return;
        }
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_applicants SET pipeline_step = :step, status = :status WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute(['step' => $step, 'status' => $step, 'id' => $id]);
    }

    public function updateApplicantRating(int $id, int $rating): void
    {
        $rating = max(0, min(5, $rating));
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_applicants SET rating = :rating WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute(['rating' => $rating, 'id' => $id]);
    }

    public function markAsConverted(int $applicantId, int $employeeId): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE recruitment_applicants SET converted_employee_id = :eid, status = :status, pipeline_step = :step WHERE id = :id'
        );
        $stmt->execute([
            'eid'    => $employeeId,
            'status' => 'hired',
            'step'   => 'hired',
            'id'     => $applicantId,
        ]);
    }
}
