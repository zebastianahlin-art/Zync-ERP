<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class EmergencyProcedureRepository
{
    public function all(?string $category = null): array
    {
        $where = ['is_deleted = 0'];
        $params = [];
        if ($category !== null) {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        $sql = 'SELECT * FROM hs_emergency_procedures WHERE ' . implode(' AND ', $where) . ' ORDER BY category ASC, title ASC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM hs_emergency_procedures WHERE id = ? AND is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_emergency_procedures
             (title, category, description, steps, responsible, location, last_reviewed, review_interval, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['category'] ?? 'other',
            $data['description'] ?: null,
            $data['steps'],
            $data['responsible'] ?: null,
            $data['location'] ?: null,
            $data['last_reviewed'] ?: null,
            $data['review_interval'] !== '' ? (int) $data['review_interval'] : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_emergency_procedures
             SET title=?, category=?, description=?, steps=?, responsible=?, location=?,
                 last_reviewed=?, review_interval=?, is_active=?
             WHERE id=?'
        );
        $stmt->execute([
            $data['title'],
            $data['category'],
            $data['description'] ?: null,
            $data['steps'],
            $data['responsible'] ?: null,
            $data['location'] ?: null,
            $data['last_reviewed'] ?: null,
            $data['review_interval'] !== '' ? (int) $data['review_interval'] : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_emergency_procedures SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }
}
