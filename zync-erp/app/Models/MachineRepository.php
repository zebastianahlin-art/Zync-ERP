<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class MachineRepository
{
    public function all(): array
    {
        $sql = "SELECT m.*
                FROM machines m
                WHERE m.is_deleted = 0
                ORDER BY m.code ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT m.*, u.full_name AS created_by_name
             FROM machines m
             LEFT JOIN users u ON m.created_by = u.id
             WHERE m.id = ? AND m.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $code = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO machines
             (code, name, description, location,
              manufacturer, model, serial_number, year_installed,
              status, criticality, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $code,
            $data['name'],
            $data['description'] ?? null,
            $data['location'] ?? null,
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
            "UPDATE machines SET name=?, description=?, location=?,
             manufacturer=?, model=?, serial_number=?, year_installed=?,
             status=?, criticality=?, notes=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['location'] ?? null,
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
        $stmt = Database::pdo()->prepare("UPDATE machines SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function generateNumber(): string
    {
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM machines");
        $count = (int) $stmt->fetchColumn() + 1;
        return 'M-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}
