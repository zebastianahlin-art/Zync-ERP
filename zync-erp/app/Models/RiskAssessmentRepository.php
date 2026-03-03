<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class RiskAssessmentRepository
{
    public function all(?array $filters = null): array
    {
        $where = ['r.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'r.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['risk_type'])) {
            $where[] = 'r.risk_type = ?';
            $params[] = $filters['risk_type'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'r.department_id = ?';
            $params[] = (int) $filters['department_id'];
        }

        $sql = 'SELECT r.*, d.name AS department_name,
                       u1.full_name AS assigned_name,
                       u2.full_name AS created_by_name
                FROM hs_risk_assessments r
                LEFT JOIN departments d ON d.id = r.department_id
                LEFT JOIN users u1 ON u1.id = r.assigned_to
                LEFT JOIN users u2 ON u2.id = r.created_by
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY r.created_at DESC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT r.*, d.name AS department_name,
                    u1.full_name AS assigned_name,
                    u2.full_name AS reviewed_by_name,
                    u3.full_name AS created_by_name
             FROM hs_risk_assessments r
             LEFT JOIN departments d ON d.id = r.department_id
             LEFT JOIN users u1 ON u1.id = r.assigned_to
             LEFT JOIN users u2 ON u2.id = r.reviewed_by
             LEFT JOIN users u3 ON u3.id = r.created_by
             WHERE r.id = ? AND r.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_risk_assessments
             (title, description, location, department_id, risk_type, probability, consequence,
              status, assigned_to, valid_until, mitigation, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?: null,
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            $data['risk_type'],
            (int) ($data['probability'] ?? 1),
            (int) ($data['consequence'] ?? 1),
            $data['status'] ?? 'draft',
            $data['assigned_to'] ?: null,
            $data['valid_until'] ?: null,
            $data['mitigation'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_risk_assessments
             SET title=?, description=?, location=?, department_id=?, risk_type=?,
                 probability=?, consequence=?, status=?, assigned_to=?, valid_until=?, mitigation=?
             WHERE id=?'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?: null,
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            $data['risk_type'],
            (int) ($data['probability'] ?? 1),
            (int) ($data['consequence'] ?? 1),
            $data['status'],
            $data['assigned_to'] ?: null,
            $data['valid_until'] ?: null,
            $data['mitigation'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_risk_assessments SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function updateStatus(int $id, string $status): void
    {
        Database::pdo()->prepare('UPDATE hs_risk_assessments SET status = ? WHERE id = ?')->execute([$status, $id]);
    }

    public function stats(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT
               COUNT(*) AS total,
               SUM(status = "active") AS active,
               SUM(status = "draft") AS draft,
               SUM(status = "under_review") AS under_review,
               SUM(status = "closed") AS closed,
               SUM(risk_level >= 15) AS high_risk,
               SUM(risk_level BETWEEN 8 AND 14) AS medium_risk,
               SUM(risk_level < 8) AS low_risk
             FROM hs_risk_assessments WHERE is_deleted = 0'
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
