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
                ORDER BY m.machine_number ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT m.*, d.name AS department_name, e.name AS equipment_name,
                    u.full_name AS created_by_name
             FROM machines m
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN equipment e ON m.equipment_id = e.id
             LEFT JOIN users u ON m.created_by = u.id
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
              manufacturer, model, serial_number, year_of_manufacture, power_kw,
              status, criticality, maintenance_interval_days, last_maintenance_date,
              next_maintenance_date, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
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
            $data['power_kw'] ?: null,
            $data['status'] ?? 'running',
            $data['criticality'] ?? 'medium',
            $data['maintenance_interval_days'] ?: null,
            $data['last_maintenance_date'] ?: null,
            $data['next_maintenance_date'] ?: null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE machines SET name=?, description=?, equipment_id=?, department_id=?, location=?,
             manufacturer=?, model=?, serial_number=?, year_of_manufacture=?, power_kw=?,
             status=?, criticality=?, maintenance_interval_days=?, last_maintenance_date=?,
             next_maintenance_date=?, notes=?
             WHERE id=?"
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
            $data['power_kw'] ?: null,
            $data['status'] ?? 'running',
            $data['criticality'] ?? 'medium',
            $data['maintenance_interval_days'] ?: null,
            $data['last_maintenance_date'] ?: null,
            $data['next_maintenance_date'] ?: null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE machines SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function generateNumber(): string
    {
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM machines WHERE is_deleted = 0");
        $count = (int) $stmt->fetchColumn() + 1;
        return 'M-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}
