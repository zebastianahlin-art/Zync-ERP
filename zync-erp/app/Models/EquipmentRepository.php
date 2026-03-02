<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EquipmentRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(?string $type = null, ?string $status = null, ?int $departmentId = null, ?int $parentId = null): array
    {
        $sql = '
            SELECT e.*, d.name AS department_name,
                   p.name AS parent_name, p.equipment_number AS parent_number
            FROM equipment e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN equipment p ON e.parent_id = p.id
            WHERE e.is_deleted = 0
        ';
        $params = [];

        if ($type) {
            $sql .= ' AND e.type = ?';
            $params[] = $type;
        }
        if ($status) {
            $sql .= ' AND e.status = ?';
            $params[] = $status;
        }
        if ($departmentId) {
            $sql .= ' AND e.department_id = ?';
            $params[] = $departmentId;
        }
        if ($parentId !== null) {
            if ($parentId === 0) {
                $sql .= ' AND e.parent_id IS NULL';
            } else {
                $sql .= ' AND e.parent_id = ?';
                $params[] = $parentId;
            }
        }

        $sql .= ' ORDER BY e.type ASC, e.name ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT e.*, d.name AS department_name,
                   p.name AS parent_name, p.equipment_number AS parent_number
            FROM equipment e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN equipment p ON e.parent_id = p.id
            WHERE e.id = ? AND e.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO equipment
                (parent_id, equipment_number, name, type, description, location,
                 manufacturer, model, serial_number, year_installed, status,
                 criticality, department_id, notes, created_by)
            VALUES
                (:parent_id, :equipment_number, :name, :type, :description, :location,
                 :manufacturer, :model, :serial_number, :year_installed, :status,
                 :criticality, :department_id, :notes, :created_by)
        ');
        $stmt->execute($this->bind($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE equipment SET
                parent_id = :parent_id, equipment_number = :equipment_number,
                name = :name, type = :type, description = :description,
                location = :location, manufacturer = :manufacturer, model = :model,
                serial_number = :serial_number, year_installed = :year_installed,
                status = :status, criticality = :criticality,
                department_id = :department_id, notes = :notes
            WHERE id = :id AND is_deleted = 0
        ');
        $params = $this->bind($data);
        unset($params['created_by']);
        $params['id'] = $id;
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('UPDATE equipment SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    /** Hämta barn till en utrustning. */
    public function children(int $parentId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM equipment WHERE parent_id = ? AND is_deleted = 0 ORDER BY type, name');
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Hierarkiskt träd (rekursivt). */
    public function tree(?int $parentId = null): array
    {
        if ($parentId === null) {
            $stmt = $this->pdo->prepare('SELECT * FROM equipment WHERE parent_id IS NULL AND is_deleted = 0 ORDER BY type, name');
            $stmt->execute();
        } else {
            $stmt = $this->pdo->prepare('SELECT * FROM equipment WHERE parent_id = ? AND is_deleted = 0 ORDER BY type, name');
            $stmt->execute([$parentId]);
        }
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($items as &$item) {
            $item['children'] = $this->tree((int) $item['id']);
        }
        return $items;
    }

    /** Alla möjliga föräldrar (för dropdown, exkludera sig själv). */
    public function allParentOptions(?int $excludeId = null): array
    {
        $sql = "SELECT id, equipment_number, name, type FROM equipment WHERE is_deleted = 0 AND type IN ('facility','line','machine')";
        $params = [];
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY type, name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Generera nästa utrustningsnummer. */
    public function nextNumber(string $type = 'machine'): string
    {
        $prefixes = [
            'facility'  => 'FAC',
            'line'      => 'LIN',
            'machine'   => 'MCH',
            'component' => 'CMP',
            'tool'      => 'TLS',
        ];
        $prefix = $prefixes[$type] ?? 'EQU';
        $stmt = $this->pdo->prepare("SELECT equipment_number FROM equipment WHERE equipment_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetchColumn();
        if ($last) {
            $num = (int) substr($last, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function stats(): array
    {
        return [
            'total'         => (int) $this->pdo->query("SELECT COUNT(*) FROM equipment WHERE is_deleted = 0")->fetchColumn(),
            'operational'   => (int) $this->pdo->query("SELECT COUNT(*) FROM equipment WHERE is_deleted = 0 AND status = 'operational'")->fetchColumn(),
            'maintenance'   => (int) $this->pdo->query("SELECT COUNT(*) FROM equipment WHERE is_deleted = 0 AND status = 'maintenance'")->fetchColumn(),
            'breakdown'     => (int) $this->pdo->query("SELECT COUNT(*) FROM equipment WHERE is_deleted = 0 AND status = 'breakdown'")->fetchColumn(),
            'decommissioned'=> (int) $this->pdo->query("SELECT COUNT(*) FROM equipment WHERE is_deleted = 0 AND status = 'decommissioned'")->fetchColumn(),
        ];
    }

    // --- Dokument ---
    public function documents(int $equipmentId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM equipment_documents WHERE equipment_id = ? AND is_deleted = 0 ORDER BY uploaded_at DESC');
        $stmt->execute([$equipmentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addDocument(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO equipment_documents (equipment_id, name, type, file_path, file_name, file_size, uploaded_by) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$data['equipment_id'], $data['name'], $data['type'], $data['file_path'], $data['file_name'], $data['file_size'], $data['uploaded_by']]);
        return (int) $this->pdo->lastInsertId();
    }

    public function findDocument(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM equipment_documents WHERE id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteDocument(int $id): void
    {
        $this->pdo->prepare('UPDATE equipment_documents SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    // --- Reservdelar ---
    public function spareParts(int $equipmentId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT sp.*, a.article_number, a.name AS article_name, a.unit
            FROM equipment_spare_parts sp
            JOIN articles a ON sp.article_id = a.id
            WHERE sp.equipment_id = ? ORDER BY a.name
        ');
        $stmt->execute([$equipmentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addSparePart(int $equipmentId, int $articleId, float $qty, ?string $notes): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO equipment_spare_parts (equipment_id, article_id, quantity_needed, notes) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE quantity_needed = VALUES(quantity_needed), notes = VALUES(notes)');
        $stmt->execute([$equipmentId, $articleId, $qty, $notes]);
    }

    public function removeSparePart(int $id): void
    {
        $this->pdo->prepare('DELETE FROM equipment_spare_parts WHERE id = ?')->execute([$id]);
    }

    public function allDepartments(): array
    {
        return $this->pdo->query("SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function bind(array $data): array
    {
        return [
            'parent_id'        => $data['parent_id'] ?: null,
            'equipment_number' => $data['equipment_number'],
            'name'             => $data['name'],
            'type'             => $data['type'] ?: 'machine',
            'description'      => $data['description'] ?: null,
            'location'         => $data['location'] ?: null,
            'manufacturer'     => $data['manufacturer'] ?: null,
            'model'            => $data['model'] ?: null,
            'serial_number'    => $data['serial_number'] ?: null,
            'year_installed'   => $data['year_installed'] ?: null,
            'status'           => $data['status'] ?: 'operational',
            'criticality'      => $data['criticality'] ?: 'B',
            'department_id'    => $data['department_id'] ?: null,
            'notes'            => $data['notes'] ?: null,
            'created_by'       => $data['created_by'] ?? null,
        ];
    }
}
