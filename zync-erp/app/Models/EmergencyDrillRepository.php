<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EmergencyDrillRepository
{
    // ── Drills ────────────────────────────────────────────────

    public function all(array $filters = []): array
    {
        $where  = ['d.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'd.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['drill_type'])) {
            $where[] = 'd.drill_type = ?';
            $params[] = $filters['drill_type'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'd.department_id = ?';
            $params[] = (int) $filters['department_id'];
        }

        $sql = 'SELECT d.*, dep.name AS department_name,
                       u.full_name AS coordinator_name,
                       t.name AS template_name
                FROM emergency_drills d
                LEFT JOIN departments dep ON dep.id = d.department_id
                LEFT JOIN users u ON u.id = d.coordinator_id
                LEFT JOIN drill_templates t ON t.id = d.template_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY d.scheduled_date DESC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT d.*, dep.name AS department_name,
                    u.full_name AS coordinator_name,
                    u2.full_name AS created_by_name,
                    t.name AS template_name
             FROM emergency_drills d
             LEFT JOIN departments dep ON dep.id = d.department_id
             LEFT JOIN users u ON u.id = d.coordinator_id
             LEFT JOIN users u2 ON u2.id = d.created_by
             LEFT JOIN drill_templates t ON t.id = d.template_id
             WHERE d.id = ? AND d.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $drillNumber = $this->generateDrillNumber();
        $stmt = Database::pdo()->prepare(
            'INSERT INTO emergency_drills
             (drill_number, title, description, drill_type, template_id, location,
              department_id, scheduled_date, executed_date, duration_minutes, participants,
              coordinator_id, status, evaluation, score, improvements, next_drill_date, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $drillNumber,
            $data['title'],
            $data['description'] ?: null,
            $data['drill_type'] ?? 'fire',
            $data['template_id'] ?: null,
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            $data['scheduled_date'],
            $data['executed_date'] ?: null,
            !empty($data['duration_minutes']) ? (int) $data['duration_minutes'] : null,
            !empty($data['participants']) ? (int) $data['participants'] : null,
            $data['coordinator_id'] ?: null,
            $data['status'] ?? 'planned',
            $data['evaluation'] ?: null,
            !empty($data['score']) ? (float) $data['score'] : null,
            $data['improvements'] ?: null,
            $data['next_drill_date'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE emergency_drills
             SET title=?, description=?, drill_type=?, template_id=?, location=?,
                 department_id=?, scheduled_date=?, executed_date=?, duration_minutes=?,
                 participants=?, coordinator_id=?, status=?, evaluation=?, score=?,
                 improvements=?, next_drill_date=?
             WHERE id=? AND is_deleted=0'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?: null,
            $data['drill_type'] ?? 'fire',
            $data['template_id'] ?: null,
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            $data['scheduled_date'],
            $data['executed_date'] ?: null,
            !empty($data['duration_minutes']) ? (int) $data['duration_minutes'] : null,
            !empty($data['participants']) ? (int) $data['participants'] : null,
            $data['coordinator_id'] ?: null,
            $data['status'] ?? 'planned',
            $data['evaluation'] ?: null,
            !empty($data['score']) ? (float) $data['score'] : null,
            $data['improvements'] ?: null,
            $data['next_drill_date'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE emergency_drills SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    // ── Templates ─────────────────────────────────────────────

    public function allTemplates(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT * FROM drill_templates WHERE is_deleted = 0 ORDER BY name ASC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findTemplate(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM drill_templates WHERE id = ? AND is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function createTemplate(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO drill_templates
             (name, description, drill_type, checklist, duration_estimate, required_resources, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?: null,
            $data['drill_type'] ?? 'fire',
            $data['checklist'] ?: null,
            !empty($data['duration_estimate']) ? (int) $data['duration_estimate'] : null,
            $data['required_resources'] ?: null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateTemplate(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE drill_templates
             SET name=?, description=?, drill_type=?, checklist=?, duration_estimate=?,
                 required_resources=?, is_active=?
             WHERE id=? AND is_deleted=0'
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?: null,
            $data['drill_type'] ?? 'fire',
            $data['checklist'] ?: null,
            !empty($data['duration_estimate']) ? (int) $data['duration_estimate'] : null,
            $data['required_resources'] ?: null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id,
        ]);
    }

    public function deleteTemplate(int $id): void
    {
        Database::pdo()->prepare('UPDATE drill_templates SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function generateDrillNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM emergency_drills WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $seq = ((int) $stmt->fetchColumn()) + 1;
        return sprintf('DR-%d-%04d', $year, $seq);
    }
}
