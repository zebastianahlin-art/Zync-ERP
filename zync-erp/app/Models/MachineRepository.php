<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class MachineRepository
{
    public function all(): array
    {
        $sql = "SELECT m.*, d.name AS department_name, e.name AS equipment_name
                FROM machines m
                LEFT JOIN departments d ON m.department_id = d.id
                LEFT JOIN equipment e ON m.equipment_id = e.id
                WHERE m.is_deleted = 0
                ORDER BY m.machine_number";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT m.*, d.name AS department_name, e.name AS equipment_name
             FROM machines m
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN equipment e ON m.equipment_id = e.id
             WHERE m.id = ? AND m.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO machines
             (machine_number, name, description, equipment_id, department_id, location,
              manufacturer, model, serial_number, year_of_manufacture, installed_date,
              warranty_until, status, criticality, last_maintenance_date,
              next_maintenance_date, maintenance_interval_days, notes, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['name'],
            $data['description'] ?? null,
            $data['equipment_id'] ?: null,
            $data['department_id'] ?: null,
            $data['location'] ?? null,
            $data['manufacturer'] ?? null,
            $data['model'] ?? null,
            $data['serial_number'] ?? null,
            $data['year_of_manufacture'] ?: null,
            $data['installed_date'] ?: null,
            $data['warranty_until'] ?: null,
            $data['status'] ?? 'active',
            $data['criticality'] ?? 'medium',
            $data['last_maintenance_date'] ?: null,
            $data['next_maintenance_date'] ?: null,
            $data['maintenance_interval_days'] ?: null,
            $data['notes'] ?? null,
            $data['is_active'] ?? 1,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE machines
             SET name = ?, description = ?, equipment_id = ?, department_id = ?, location = ?,
                 manufacturer = ?, model = ?, serial_number = ?, year_of_manufacture = ?,
                 installed_date = ?, warranty_until = ?, status = ?, criticality = ?,
                 last_maintenance_date = ?, next_maintenance_date = ?,
                 maintenance_interval_days = ?, notes = ?, is_active = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['equipment_id'] ?: null,
            $data['department_id'] ?: null,
            $data['location'] ?? null,
            $data['manufacturer'] ?? null,
            $data['model'] ?? null,
            $data['serial_number'] ?? null,
            $data['year_of_manufacture'] ?: null,
            $data['installed_date'] ?: null,
            $data['warranty_until'] ?: null,
            $data['status'] ?? 'active',
            $data['criticality'] ?? 'medium',
            $data['last_maintenance_date'] ?: null,
            $data['next_maintenance_date'] ?: null,
            $data['maintenance_interval_days'] ?: null,
            $data['notes'] ?? null,
            $data['is_active'] ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE machines SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function generateNumber(): string
    {
        $stmt = Database::pdo()->query(
            "SELECT MAX(CAST(SUBSTRING(machine_number, 3) AS UNSIGNED)) FROM machines"
        );
        $max = (int) $stmt->fetchColumn();
        return 'M-' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    public function getByEquipment(int $eqId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT m.*, d.name AS department_name, e.name AS equipment_name
             FROM machines m
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN equipment e ON m.equipment_id = e.id
             WHERE m.equipment_id = ? AND m.is_deleted = 0
             ORDER BY m.machine_number"
        );
        $stmt->execute([$eqId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getDueForMaintenance(): array
    {
        $sql = "SELECT m.*, d.name AS department_name, e.name AS equipment_name
                FROM machines m
                LEFT JOIN departments d ON m.department_id = d.id
                LEFT JOIN equipment e ON m.equipment_id = e.id
                WHERE m.next_maintenance_date <= CURDATE()
                  AND m.is_deleted = 0
                  AND m.is_active = 1
                ORDER BY m.next_maintenance_date ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = Database::pdo()->prepare("UPDATE machines SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}
