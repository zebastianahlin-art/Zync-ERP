<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class InspectableEquipmentRepository
{
    public function all(): array
    {
        $sql = "SELECT ie.*, e.name AS equipment_name
                FROM inspectable_equipment ie
                LEFT JOIN equipment e ON ie.equipment_id = e.id
                WHERE ie.is_deleted = 0
                ORDER BY ie.name ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT ie.*, e.name AS equipment_name
             FROM inspectable_equipment ie
             LEFT JOIN equipment e ON ie.equipment_id = e.id
             WHERE ie.id = ? AND ie.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getInspections(int $inspectableId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT i.*, u.full_name AS recorded_by_name
             FROM inspectable_equipment_inspections i
             LEFT JOIN users u ON i.recorded_by = u.id
             WHERE i.inspectable_id = ?
             ORDER BY i.inspection_date DESC"
        );
        $stmt->execute([$inspectableId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOverdue(): array
    {
        $sql = "SELECT ie.*, e.name AS equipment_name
                FROM inspectable_equipment ie
                LEFT JOIN equipment e ON ie.equipment_id = e.id
                WHERE ie.is_deleted = 0 AND ie.is_active = 1
                  AND (ie.next_inspection_date IS NULL OR ie.next_inspection_date <= CURDATE())
                ORDER BY ie.next_inspection_date ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO inspectable_equipment
             (equipment_id, name, type, serial_number, manufacturer, location,
              certification_body, inspection_interval_months, last_inspection_date,
              next_inspection_date, last_inspection_result, certificate_number, max_load_kg, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['equipment_id'] ?: null,
            $data['name'],
            $data['type'] ?? 'other',
            $data['serial_number'] ?? null,
            $data['manufacturer'] ?? null,
            $data['location'] ?? null,
            $data['certification_body'] ?? null,
            $data['inspection_interval_months'] ?? 12,
            $data['last_inspection_date'] ?: null,
            $data['next_inspection_date'] ?: null,
            $data['last_inspection_result'] ?: null,
            $data['certificate_number'] ?? null,
            $data['max_load_kg'] ?: null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE inspectable_equipment SET equipment_id=?, name=?, type=?, serial_number=?,
             manufacturer=?, location=?, certification_body=?, inspection_interval_months=?,
             last_inspection_date=?, next_inspection_date=?, last_inspection_result=?,
             certificate_number=?, max_load_kg=?, notes=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['equipment_id'] ?: null,
            $data['name'],
            $data['type'] ?? 'other',
            $data['serial_number'] ?? null,
            $data['manufacturer'] ?? null,
            $data['location'] ?? null,
            $data['certification_body'] ?? null,
            $data['inspection_interval_months'] ?? 12,
            $data['last_inspection_date'] ?: null,
            $data['next_inspection_date'] ?: null,
            $data['last_inspection_result'] ?: null,
            $data['certificate_number'] ?? null,
            $data['max_load_kg'] ?: null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function recordInspection(int $inspectableId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO inspectable_equipment_inspections
             (inspectable_id, inspection_date, inspector, inspection_company, result,
              certificate_number, valid_until, notes, recorded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $inspectableId,
            $data['inspection_date'],
            $data['inspector'] ?? null,
            $data['inspection_company'] ?? null,
            $data['result'] ?? 'passed',
            $data['certificate_number'] ?? null,
            $data['valid_until'] ?: null,
            $data['notes'] ?? null,
            $data['recorded_by'],
        ]);
        $inspId = (int) Database::pdo()->lastInsertId();

        // Update the parent record
        $nextDate = null;
        if (!empty($data['valid_until'])) {
            $nextDate = $data['valid_until'];
        } elseif (!empty($data['inspection_date'])) {
            $interval = (int) ($data['inspection_interval_months'] ?? 12);
            $nextDate = date('Y-m-d', strtotime($data['inspection_date'] . " +{$interval} months"));
        }

        $stmt2 = Database::pdo()->prepare(
            "UPDATE inspectable_equipment SET last_inspection_date=?, next_inspection_date=?, last_inspection_result=?, certificate_number=? WHERE id=?"
        );
        $stmt2->execute([
            $data['inspection_date'],
            $nextDate,
            $data['result'] ?? 'passed',
            $data['certificate_number'] ?? null,
            $inspectableId,
        ]);

        return $inspId;
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE inspectable_equipment SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
}
