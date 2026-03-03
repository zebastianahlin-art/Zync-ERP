<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EquipmentRepository
{
    public function all(): array
    {
        $sql = "SELECT e.*, d.name AS department_name, p.name AS parent_name
                FROM equipment e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN equipment p ON e.parent_id = p.id
                WHERE e.is_deleted = 0
                ORDER BY e.equipment_number ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT e.*, d.name AS department_name, p.name AS parent_name,
                    u.full_name AS created_by_name
             FROM equipment e
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN equipment p ON e.parent_id = p.id
             LEFT JOIN users u ON e.created_by = u.id
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
             (equipment_number, name, description, type, location,
              department_id, parent_id, manufacturer, model, serial_number,
              year_installed, status, criticality, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['name'],
            $data['description'] ?? null,
            $data['type'] ?? 'machine',
            $data['location'] ?? null,
            $data['department_id'] ?: null,
            $data['parent_id'] ?: null,
            $data['manufacturer'] ?? null,
            $data['model'] ?? null,
            $data['serial_number'] ?? null,
            $data['year_installed'] ?: null,
            $data['status'] ?? 'operational',
            $data['criticality'] ?? 'B',
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE equipment SET name=?, description=?, type=?, location=?,
             department_id=?, parent_id=?, manufacturer=?, model=?, serial_number=?,
             year_installed=?, status=?, criticality=?, notes=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['type'] ?? 'machine',
            $data['location'] ?? null,
            $data['department_id'] ?: null,
            $data['parent_id'] ?: null,
            $data['manufacturer'] ?? null,
            $data['model'] ?? null,
            $data['serial_number'] ?? null,
            $data['year_installed'] ?: null,
            $data['status'] ?? 'operational',
            $data['criticality'] ?? 'B',
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE equipment SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function generateNumber(): string
    {
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM equipment");
        $count = (int) $stmt->fetchColumn() + 1;
        return 'EQ-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}
