<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EquipmentRepository
{
    public function all(): array
    {
        $sql = "SELECT e.*, d.name AS department_name
                FROM equipment e
                LEFT JOIN departments d ON e.department_id = d.id
                WHERE e.is_deleted = 0
                ORDER BY e.equipment_number";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT e.*, d.name AS department_name
             FROM equipment e
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE e.id = ? AND e.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO equipment
             (equipment_number, name, description, category, location, building, floor,
              department_id, parent_id, manufacturer, model, serial_number,
              year_of_manufacture, installed_date, warranty_until, status, criticality,
              notes, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['name'],
            $data['description'] ?? null,
            $data['category'] ?? null,
            $data['location'] ?? null,
            $data['building'] ?? null,
            $data['floor'] ?? null,
            $data['department_id'] ?: null,
            $data['parent_id'] ?: null,
            $data['manufacturer'] ?? null,
            $data['model'] ?? null,
            $data['serial_number'] ?? null,
            $data['year_of_manufacture'] ?: null,
            $data['installed_date'] ?: null,
            $data['warranty_until'] ?: null,
            $data['status'] ?? 'active',
            $data['criticality'] ?? 'medium',
            $data['notes'] ?? null,
            $data['is_active'] ?? 1,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE equipment
             SET name = ?, description = ?, category = ?, location = ?, building = ?, floor = ?,
                 department_id = ?, parent_id = ?, manufacturer = ?, model = ?, serial_number = ?,
                 year_of_manufacture = ?, installed_date = ?, warranty_until = ?, status = ?,
                 criticality = ?, notes = ?, is_active = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['category'] ?? null,
            $data['location'] ?? null,
            $data['building'] ?? null,
            $data['floor'] ?? null,
            $data['department_id'] ?: null,
            $data['parent_id'] ?: null,
            $data['manufacturer'] ?? null,
            $data['model'] ?? null,
            $data['serial_number'] ?? null,
            $data['year_of_manufacture'] ?: null,
            $data['installed_date'] ?: null,
            $data['warranty_until'] ?: null,
            $data['status'] ?? 'active',
            $data['criticality'] ?? 'medium',
            $data['notes'] ?? null,
            $data['is_active'] ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE equipment SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function generateNumber(): string
    {
        $stmt = Database::pdo()->query(
            "SELECT MAX(CAST(SUBSTRING(equipment_number, 4) AS UNSIGNED)) FROM equipment"
        );
        $max = (int) $stmt->fetchColumn();
        return 'EQ-' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    public function getByDepartment(int $deptId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT e.*, d.name AS department_name
             FROM equipment e
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE e.department_id = ? AND e.is_deleted = 0
             ORDER BY e.equipment_number"
        );
        $stmt->execute([$deptId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getByStatus(string $status): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT e.*, d.name AS department_name
             FROM equipment e
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE e.status = ? AND e.is_deleted = 0
             ORDER BY e.equipment_number"
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
