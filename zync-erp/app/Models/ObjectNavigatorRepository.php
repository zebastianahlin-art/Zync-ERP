<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class ObjectNavigatorRepository
{
    /**
     * Search across the object_registry with optional type filter.
     */
    public function search(string $query, string $type = ''): array
    {
        $params = [];
        $sql = "SELECT * FROM object_registry WHERE is_deleted = 0";

        if ($type !== '') {
            $sql .= " AND object_type = ?";
            $params[] = $type;
        }

        if ($query !== '') {
            $sql .= " AND (display_name LIKE ? OR search_text LIKE ?)";
            $like = '%' . $query . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY object_type ASC, display_name ASC LIMIT 200";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return all top-level objects (no parent) for the tree view.
     */
    public function tree(): array
    {
        $sql = "SELECT * FROM object_registry WHERE is_deleted = 0 AND parent_id IS NULL
                ORDER BY object_type ASC, display_name ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return children for a given parent object.
     */
    public function children(string $parentType, int $parentId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT * FROM object_registry WHERE is_deleted = 0
             AND parent_type = ? AND parent_id = ?
             ORDER BY display_name ASC"
        );
        $stmt->execute([$parentType, $parentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find a single registry entry by type + object_id.
     */
    public function findByTypeAndId(string $type, int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT * FROM object_registry WHERE object_type = ? AND object_id = ? AND is_deleted = 0"
        );
        $stmt->execute([$type, $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Count objects per type.
     */
    public function countByType(): array
    {
        $sql = "SELECT object_type, COUNT(*) AS cnt
                FROM object_registry WHERE is_deleted = 0
                GROUP BY object_type ORDER BY cnt DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Sync live data into the object_registry table.
     * This rebuilds/upserts entries from the primary tables.
     * Each source table is synced independently; failures in one do not abort the others.
     */
    public function sync(): void
    {
        $pdo = Database::pdo();

        // Machines
        try {
            $machines = $pdo->query("SELECT id, name, location, status FROM machines WHERE is_deleted = 0")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($machines as $m) {
                $this->upsert($pdo, 'machine', $m['id'], $m['name'], implode(' ', array_filter([$m['name'], $m['location'], $m['status']])), null, null, json_encode(['status' => $m['status'], 'location' => $m['location']]));
            }
        } catch (\Exception $e) {
            // machines table unavailable – skip
        }

        // Equipment
        try {
            $equip = $pdo->query("SELECT e.id, e.name, e.location, e.status, m.id AS parent_mid FROM equipment e LEFT JOIN machines m ON e.parent_id = m.id WHERE e.is_deleted = 0")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($equip as $e) {
                $this->upsert($pdo, 'equipment', $e['id'], $e['name'], implode(' ', array_filter([$e['name'], $e['location'], $e['status']])), $e['parent_mid'] ? 'machine' : null, $e['parent_mid'] ?: null, json_encode(['status' => $e['status']]));
            }
        } catch (\Exception $e) {
            // equipment table unavailable – skip
        }

        // Articles
        try {
            $articles = $pdo->query("SELECT id, article_number, name, unit FROM articles WHERE is_deleted = 0")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($articles as $a) {
                $this->upsert($pdo, 'article', $a['id'], $a['name'] . ' (' . $a['article_number'] . ')', implode(' ', array_filter([$a['name'], $a['article_number'], $a['unit']])), null, null, json_encode(['article_number' => $a['article_number']]));
            }
        } catch (\Exception $e) {
            // articles table unavailable – skip
        }

        // Customers
        try {
            $customers = $pdo->query("SELECT id, name, email FROM customers WHERE is_deleted = 0")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($customers as $c) {
                $this->upsert($pdo, 'customer', $c['id'], $c['name'], implode(' ', array_filter([$c['name'], $c['email']])), null, null, null);
            }
        } catch (\Exception $e) {
            // customers table unavailable – skip
        }

        // Suppliers
        try {
            $suppliers = $pdo->query("SELECT id, name, email FROM suppliers WHERE is_deleted = 0")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($suppliers as $s) {
                $this->upsert($pdo, 'supplier', $s['id'], $s['name'], implode(' ', array_filter([$s['name'], $s['email']])), null, null, null);
            }
        } catch (\Exception $e) {
            // suppliers table unavailable – skip
        }

        // Employees (users)
        try {
            $users = $pdo->query("SELECT id, full_name, email FROM users WHERE is_active = 1")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($users as $u) {
                $this->upsert($pdo, 'employee', $u['id'], $u['full_name'], implode(' ', array_filter([$u['full_name'], $u['email']])), null, null, null);
            }
        } catch (\Exception $e) {
            // users table unavailable – skip
        }

        // Work orders
        try {
            $wos = $pdo->query("SELECT id, wo_number, title, status FROM work_orders WHERE is_deleted = 0")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($wos as $w) {
                $this->upsert($pdo, 'work_order', $w['id'], $w['wo_number'] . ' – ' . $w['title'], implode(' ', array_filter([$w['wo_number'], $w['title'], $w['status']])), null, null, json_encode(['status' => $w['status']]));
            }
        } catch (\Exception $e) {
            // work_orders table unavailable – skip
        }
    }

    private function upsert(\PDO $pdo, string $type, int $objectId, string $displayName, string $searchText, ?string $parentType, ?int $parentId, ?string $metadata): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO object_registry (object_type, object_id, display_name, search_text, parent_type, parent_id, metadata)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                display_name = VALUES(display_name),
                search_text  = VALUES(search_text),
                parent_type  = VALUES(parent_type),
                parent_id    = VALUES(parent_id),
                metadata     = VALUES(metadata),
                updated_at   = CURRENT_TIMESTAMP"
        );
        $stmt->execute([$type, $objectId, $displayName, $searchText, $parentType, $parentId, $metadata]);
    }
}
