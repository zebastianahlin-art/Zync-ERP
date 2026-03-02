<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class MachineRepository
{
    /* ── Machines ── */

    public function all(): array
    {
        return Database::pdo()->query('
            SELECT m.*, p.name AS parent_name, p.code AS parent_code
            FROM machines m
            LEFT JOIN machines p ON m.parent_id = p.id
            WHERE m.is_deleted = 0
            ORDER BY m.type, m.name
        ')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('
            SELECT m.*, p.name AS parent_name, p.code AS parent_code
            FROM machines m
            LEFT JOIN machines p ON m.parent_id = p.id
            WHERE m.id = ? AND m.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function children(int $parentId): array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM machines WHERE parent_id = ? AND is_deleted = 0 ORDER BY name');
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Build full tree structure */
    public function tree(): array
    {
        $all = $this->all();
        $map = [];
        foreach ($all as &$row) {
            $row['children'] = [];
            $map[$row['id']] = &$row;
        }
        unset($row);

        $tree = [];
        foreach ($map as &$node) {
            if ($node['parent_id'] && isset($map[$node['parent_id']])) {
                $map[$node['parent_id']]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        return $tree;
    }

    /** Flat list for parent dropdown (exclude self and descendants) */
    public function parentOptions(?int $excludeId = null): array
    {
        $sql = 'SELECT id, name, code, type, parent_id FROM machines WHERE is_deleted = 0';
        $params = [];
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY type, name';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        $all = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!$excludeId) return $all;

        // Also exclude descendants
        $excludeIds = $this->descendantIds($excludeId);
        return array_filter($all, fn($r) => !in_array((int)$r['id'], $excludeIds));
    }

    private function descendantIds(int $parentId): array
    {
        $stmt = Database::pdo()->prepare('SELECT id FROM machines WHERE parent_id = ? AND is_deleted = 0');
        $stmt->execute([$parentId]);
        $ids = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $childId) {
            $ids[] = (int)$childId;
            $ids = array_merge($ids, $this->descendantIds((int)$childId));
        }
        return $ids;
    }

    public function create(array $d): int
    {
        $stmt = Database::pdo()->prepare('
            INSERT INTO machines (parent_id, name, code, type, description, manufacturer, model, serial_number, year_installed, location, status, criticality, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ');
        $stmt->execute([
            $d['parent_id'] ?: null, $d['name'], strtoupper($d['code']), $d['type'],
            $d['description'] ?: null, $d['manufacturer'] ?: null, $d['model'] ?: null,
            $d['serial_number'] ?: null, $d['year_installed'] ?: null, $d['location'] ?: null,
            $d['status'], $d['criticality'], $d['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare('
            UPDATE machines SET parent_id=?, name=?, code=?, type=?, description=?, manufacturer=?, model=?,
            serial_number=?, year_installed=?, location=?, status=?, criticality=?, is_active=?
            WHERE id=? AND is_deleted=0
        ');
        $stmt->execute([
            $d['parent_id'] ?: null, $d['name'], strtoupper($d['code']), $d['type'],
            $d['description'] ?: null, $d['manufacturer'] ?: null, $d['model'] ?: null,
            $d['serial_number'] ?: null, $d['year_installed'] ?: null, $d['location'] ?: null,
            $d['status'], $d['criticality'], (int)($d['is_active'] ?? 1), $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE machines SET is_deleted=1 WHERE id=?')->execute([$id]);
    }

    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM machines WHERE code=? AND id!=? AND is_deleted=0');
            $stmt->execute([strtoupper($code), $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM machines WHERE code=? AND is_deleted=0');
            $stmt->execute([strtoupper($code)]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /* ── Spare parts ── */

    public function spareParts(int $machineId): array
    {
        $stmt = Database::pdo()->prepare('
            SELECT sp.*, a.article_number, a.name AS article_name, a.unit
            FROM machine_spare_parts sp
            JOIN articles a ON sp.article_id = a.id
            WHERE sp.machine_id = ?
            ORDER BY a.name
        ');
        $stmt->execute([$machineId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addSparePart(int $machineId, int $articleId, float $qty, ?string $note): void
    {
        $stmt = Database::pdo()->prepare('
            INSERT INTO machine_spare_parts (machine_id, article_id, quantity, note)
            VALUES (?,?,?,?)
            ON DUPLICATE KEY UPDATE quantity=VALUES(quantity), note=VALUES(note)
        ');
        $stmt->execute([$machineId, $articleId, $qty, $note ?: null]);
    }

    public function removeSparePart(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM machine_spare_parts WHERE id=?')->execute([$id]);
    }

    /* ── Documents ── */

    public function documents(int $machineId): array
    {
        $stmt = Database::pdo()->prepare('
            SELECT d.*, u.full_name AS uploader_name
            FROM machine_documents d
            LEFT JOIN users u ON d.uploaded_by = u.id
            WHERE d.machine_id = ?
            ORDER BY d.created_at DESC
        ');
        $stmt->execute([$machineId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addDocument(int $machineId, array $d): int
    {
        $stmt = Database::pdo()->prepare('
            INSERT INTO machine_documents (machine_id, title, type, file_path, file_size, mime_type, uploaded_by)
            VALUES (?,?,?,?,?,?,?)
        ');
        $stmt->execute([$machineId, $d['title'], $d['type'], $d['file_path'], $d['file_size'] ?? null, $d['mime_type'] ?? null, $d['uploaded_by'] ?? null]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function removeDocument(int $id): ?string
    {
        $stmt = Database::pdo()->prepare('SELECT file_path FROM machine_documents WHERE id=?');
        $stmt->execute([$id]);
        $path = $stmt->fetchColumn();
        Database::pdo()->prepare('DELETE FROM machine_documents WHERE id=?')->execute([$id]);
        return $path ?: null;
    }

    /* ── Stats ── */

    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'total'          => (int) $pdo->query("SELECT COUNT(*) FROM machines WHERE is_deleted=0")->fetchColumn(),
            'operational'    => (int) $pdo->query("SELECT COUNT(*) FROM machines WHERE is_deleted=0 AND status='operational'")->fetchColumn(),
            'degraded'       => (int) $pdo->query("SELECT COUNT(*) FROM machines WHERE is_deleted=0 AND status='degraded'")->fetchColumn(),
            'down'           => (int) $pdo->query("SELECT COUNT(*) FROM machines WHERE is_deleted=0 AND status='down'")->fetchColumn(),
            'decommissioned' => (int) $pdo->query("SELECT COUNT(*) FROM machines WHERE is_deleted=0 AND status='decommissioned'")->fetchColumn(),
        ];
    }
}
