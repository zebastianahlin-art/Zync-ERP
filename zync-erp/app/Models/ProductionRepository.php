<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ProductionRepository
{
    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'lines'   => (int) $pdo->query('SELECT COUNT(*) FROM production_lines WHERE is_deleted = 0')->fetchColumn(),
            'orders'  => (int) $pdo->query('SELECT COUNT(*) FROM production_orders WHERE is_deleted = 0')->fetchColumn(),
            'stock'   => (int) $pdo->query('SELECT COUNT(*) FROM production_stock WHERE is_deleted = 0')->fetchColumn(),
        ];
    }

    public function recentOrders(int $limit = 5): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT o.*, l.name AS line_name
             FROM production_orders o
             LEFT JOIN production_lines l ON o.line_id = l.id
             WHERE o.is_deleted = 0
             ORDER BY o.created_at DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allLines(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM production_lines WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findLine(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM production_lines WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createLine(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO production_lines (name, code, description, status, created_by)
             VALUES (:name, :code, :description, :status, :created_by)'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?: null,
            'status'      => $data['status'] ?? 'active',
            'created_by'  => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateLine(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE production_lines SET name = :name, code = :code, description = :description,
             status = :status WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?: null,
            'status'      => $data['status'] ?? 'active',
            'id'          => $id,
        ]);
    }

    public function deleteLine(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE production_lines SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function allOrders(): array
    {
        return Database::pdo()->query(
            'SELECT o.*, l.name AS line_name
             FROM production_orders o
             LEFT JOIN production_lines l ON o.line_id = l.id
             WHERE o.is_deleted = 0
             ORDER BY o.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allStock(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM production_stock WHERE is_deleted = 0 ORDER BY location ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
