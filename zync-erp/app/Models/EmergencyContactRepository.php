<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class EmergencyContactRepository
{
    public function all(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT c.*, d.name AS department_name
             FROM hs_emergency_contacts c
             LEFT JOIN departments d ON d.id = c.department_id
             WHERE c.is_deleted = 0
             ORDER BY c.sort_order ASC, c.name ASC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT c.*, d.name AS department_name
             FROM hs_emergency_contacts c
             LEFT JOIN departments d ON d.id = c.department_id
             WHERE c.id = ? AND c.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_emergency_contacts
             (name, role, phone, phone_alt, email, department_id, is_external, organization, notes, sort_order, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['role'] ?: null,
            $data['phone'],
            $data['phone_alt'] ?: null,
            $data['email'] ?: null,
            $data['department_id'] ?: null,
            isset($data['is_external']) ? (int) $data['is_external'] : 0,
            $data['organization'] ?: null,
            $data['notes'] ?: null,
            (int) ($data['sort_order'] ?? 0),
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_emergency_contacts
             SET name=?, role=?, phone=?, phone_alt=?, email=?, department_id=?,
                 is_external=?, organization=?, notes=?, sort_order=?
             WHERE id=?'
        );
        $stmt->execute([
            $data['name'],
            $data['role'] ?: null,
            $data['phone'],
            $data['phone_alt'] ?: null,
            $data['email'] ?: null,
            $data['department_id'] ?: null,
            isset($data['is_external']) ? (int) $data['is_external'] : 0,
            $data['organization'] ?: null,
            $data['notes'] ?: null,
            (int) ($data['sort_order'] ?? 0),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_emergency_contacts SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }
}
