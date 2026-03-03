<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Auth;
use App\Core\Database;

class RiskReportRepository
{
    public function all(?array $filters = null): array
    {
        $where = ['r.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'r.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = 'r.category = ?';
            $params[] = $filters['category'];
        }
        if (!empty($filters['severity'])) {
            $where[] = 'r.severity = ?';
            $params[] = $filters['severity'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'r.department_id = ?';
            $params[] = (int) $filters['department_id'];
        }

        $sql = 'SELECT r.*, d.name AS department_name,
                       u1.full_name AS assigned_name,
                       u2.full_name AS created_by_name
                FROM hs_risk_reports r
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
                    u2.full_name AS created_by_name,
                    u3.full_name AS closed_by_name
             FROM hs_risk_reports r
             LEFT JOIN departments d ON d.id = r.department_id
             LEFT JOIN users u1 ON u1.id = r.assigned_to
             LEFT JOIN users u2 ON u2.id = r.created_by
             LEFT JOIN users u3 ON u3.id = r.closed_by
             WHERE r.id = ? AND r.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_risk_reports
             (title, description, location, department_id, category, severity, status,
              assigned_to, risk_assessment_id, action_taken, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            $data['category'] ?? 'risk',
            $data['severity'] ?? 'medium',
            $data['status'] ?? 'reported',
            $data['assigned_to'] ?: null,
            $data['risk_assessment_id'] ?: null,
            $data['action_taken'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_risk_reports
             SET title=?, description=?, location=?, department_id=?, category=?,
                 severity=?, status=?, assigned_to=?, risk_assessment_id=?, action_taken=?
             WHERE id=?'
        );
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            $data['category'],
            $data['severity'],
            $data['status'],
            $data['assigned_to'] ?: null,
            $data['risk_assessment_id'] ?: null,
            $data['action_taken'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_risk_reports SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $extra = '';
        $params = [$status];
        if ($status === 'closed') {
            $extra = ', closed_at = NOW(), closed_by = ?';
            $params[] = Auth::id();
        }
        $params[] = $id;
        Database::pdo()->prepare("UPDATE hs_risk_reports SET status = ?{$extra} WHERE id = ?")->execute($params);
    }
}
