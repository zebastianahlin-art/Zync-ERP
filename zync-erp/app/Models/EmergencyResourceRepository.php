<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class EmergencyResourceRepository
{
    public function all(?array $filters = null): array
    {
        $where = ['r.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'r.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['resource_type'])) {
            $where[] = 'r.resource_type = ?';
            $params[] = $filters['resource_type'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'r.department_id = ?';
            $params[] = (int) $filters['department_id'];
        }

        $sql = 'SELECT r.*, d.name AS department_name
                FROM hs_emergency_resources r
                LEFT JOIN departments d ON d.id = r.department_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY r.location ASC, r.name ASC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT r.*, d.name AS department_name
             FROM hs_emergency_resources r
             LEFT JOIN departments d ON d.id = r.department_id
             WHERE r.id = ? AND r.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_emergency_resources
             (name, resource_type, location, location_details, department_id, serial_number,
              quantity, status, last_inspection, next_inspection, inspection_interval, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['resource_type'] ?? 'other',
            $data['location'],
            $data['location_details'] ?: null,
            $data['department_id'] ?: null,
            $data['serial_number'] ?: null,
            (int) ($data['quantity'] ?? 1),
            $data['status'] ?? 'ok',
            $data['last_inspection'] ?: null,
            $data['next_inspection'] ?: null,
            $data['inspection_interval'] !== '' ? (int) $data['inspection_interval'] : null,
            $data['notes'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_emergency_resources
             SET name=?, resource_type=?, location=?, location_details=?, department_id=?,
                 serial_number=?, quantity=?, status=?, last_inspection=?, next_inspection=?,
                 inspection_interval=?, notes=?
             WHERE id=?'
        );
        $stmt->execute([
            $data['name'],
            $data['resource_type'],
            $data['location'],
            $data['location_details'] ?: null,
            $data['department_id'] ?: null,
            $data['serial_number'] ?: null,
            (int) ($data['quantity'] ?? 1),
            $data['status'],
            $data['last_inspection'] ?: null,
            $data['next_inspection'] ?: null,
            $data['inspection_interval'] !== '' ? (int) $data['inspection_interval'] : null,
            $data['notes'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_emergency_resources SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function overdue(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT r.*, d.name AS department_name
             FROM hs_emergency_resources r
             LEFT JOIN departments d ON d.id = r.department_id
             WHERE r.is_deleted = 0
               AND r.next_inspection IS NOT NULL
               AND r.next_inspection < CURDATE()
               AND r.status != "out_of_service"
             ORDER BY r.next_inspection ASC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function inspections(int $resourceId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT i.*, u.full_name AS inspector_name
             FROM hs_resource_inspections i
             LEFT JOIN users u ON u.id = i.inspected_by
             WHERE i.resource_id = ?
             ORDER BY i.inspected_at DESC'
        );
        $stmt->execute([$resourceId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addInspection(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_resource_inspections
             (resource_id, inspected_by, inspected_at, status, notes, next_inspection)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['resource_id'],
            $data['inspected_by'] ?? null,
            $data['inspected_at'],
            $data['status'] ?? 'ok',
            $data['notes'] ?: null,
            $data['next_inspection'] ?: null,
        ]);
        $insertId = (int) Database::pdo()->lastInsertId();

        // Update the resource's last/next inspection dates
        $updateStmt = Database::pdo()->prepare(
            'UPDATE hs_emergency_resources
             SET last_inspection = ?, next_inspection = ?, status = ?
             WHERE id = ?'
        );
        $updateStmt->execute([
            $data['inspected_at'],
            $data['next_inspection'] ?: null,
            $data['status'] ?? 'ok',
            (int) $data['resource_id'],
        ]);

        return $insertId;
    }

    public function stats(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT
               COUNT(*) AS total,
               SUM(status = "ok") AS ok,
               SUM(status = "needs_inspection") AS needs_inspection,
               SUM(status = "needs_replacement") AS needs_replacement,
               SUM(status = "out_of_service") AS out_of_service,
               SUM(next_inspection < CURDATE() AND next_inspection IS NOT NULL AND status != "out_of_service") AS overdue
             FROM hs_emergency_resources WHERE is_deleted = 0'
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
