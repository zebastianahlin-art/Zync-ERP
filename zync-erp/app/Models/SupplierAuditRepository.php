<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class SupplierAuditRepository
{
    public function all(array $filters = []): array
    {
        $params = [];
        $where  = ['sa.is_deleted = 0'];

        if (!empty($filters['supplier_id'])) {
            $where[]  = 'sa.supplier_id = ?';
            $params[] = (int) $filters['supplier_id'];
        }
        if (!empty($filters['status'])) {
            $where[]  = 'sa.status = ?';
            $params[] = $filters['status'];
        }

        $sql = "SELECT sa.*, s.name AS supplier_name, u.full_name AS auditor_name
                FROM supplier_audits sa
                LEFT JOIN suppliers s ON sa.supplier_id = s.id
                LEFT JOIN users u ON sa.auditor_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY sa.audit_date DESC";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT sa.*, s.name AS supplier_name, u.full_name AS auditor_name
             FROM supplier_audits sa
             LEFT JOIN suppliers s ON sa.supplier_id = s.id
             LEFT JOIN users u ON sa.auditor_id = u.id
             WHERE sa.id = ? AND sa.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $overallScore = $this->calculateOverallScore($data);

        $stmt = Database::pdo()->prepare(
            "INSERT INTO supplier_audits
                (supplier_id, audit_date, auditor_id, status,
                 delivery_score, quality_score, price_score, communication_score,
                 overall_score, notes, next_audit_date, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['supplier_id'],
            $data['audit_date'],
            $data['auditor_id']           ?? null,
            $data['status']               ?? 'planned',
            $data['delivery_score']       ?? null,
            $data['quality_score']        ?? null,
            $data['price_score']          ?? null,
            $data['communication_score']  ?? null,
            $overallScore,
            $data['notes']                ?? null,
            $data['next_audit_date']      ?? null,
            $data['created_by']           ?? null,
        ]);

        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $overallScore = $this->calculateOverallScore($data);

        Database::pdo()->prepare(
            "UPDATE supplier_audits
             SET supplier_id         = ?,
                 audit_date          = ?,
                 auditor_id          = ?,
                 status              = ?,
                 delivery_score      = ?,
                 quality_score       = ?,
                 price_score         = ?,
                 communication_score = ?,
                 overall_score       = ?,
                 notes               = ?,
                 next_audit_date     = ?
             WHERE id = ?"
        )->execute([
            $data['supplier_id'],
            $data['audit_date'],
            $data['auditor_id']          ?? null,
            $data['status']              ?? 'planned',
            $data['delivery_score']      ?? null,
            $data['quality_score']       ?? null,
            $data['price_score']         ?? null,
            $data['communication_score'] ?? null,
            $overallScore,
            $data['notes']               ?? null,
            $data['next_audit_date']     ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE supplier_audits SET is_deleted = 1 WHERE id = ?"
        )->execute([$id]);
    }

    private function calculateOverallScore(array $data): ?float
    {
        $scoreFields = ['delivery_score', 'quality_score', 'price_score', 'communication_score'];
        $values = [];

        foreach ($scoreFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $values[] = (float) $data[$field];
            }
        }

        if (empty($values)) {
            return null;
        }

        return round(array_sum($values) / count($values), 1);
    }
}
