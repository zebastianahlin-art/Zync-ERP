<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class AuditRepository
{
    // ── Audits ────────────────────────────────────────────────

    public function allAudits(?array $filters = null): array
    {
        $where = ['a.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'a.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'a.department_id = ?';
            $params[] = (int) $filters['department_id'];
        }

        $sql = 'SELECT a.*, t.name AS template_name, d.name AS department_name,
                       u.full_name AS assigned_name
                FROM hs_audits a
                LEFT JOIN hs_audit_templates t ON t.id = a.template_id
                LEFT JOIN departments d ON d.id = a.department_id
                LEFT JOIN users u ON u.id = a.assigned_to
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY a.scheduled_date DESC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAudit(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT a.*, t.name AS template_name, d.name AS department_name,
                    u.full_name AS assigned_name, u2.full_name AS created_by_name
             FROM hs_audits a
             LEFT JOIN hs_audit_templates t ON t.id = a.template_id
             LEFT JOIN departments d ON d.id = a.department_id
             LEFT JOIN users u ON u.id = a.assigned_to
             LEFT JOIN users u2 ON u2.id = a.created_by
             WHERE a.id = ? AND a.is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function createAudit(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_audits
             (template_id, title, description, location, department_id, assigned_to,
              status, scheduled_date, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['template_id'] ?: null,
            $data['title'],
            $data['description'] ?: null,
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            (int) $data['assigned_to'],
            $data['status'] ?? 'planned',
            $data['scheduled_date'],
            $data['notes'] ?: null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateAudit(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_audits
             SET template_id=?, title=?, description=?, location=?, department_id=?,
                 assigned_to=?, status=?, scheduled_date=?, completed_date=?, score=?, notes=?
             WHERE id=?'
        );
        $stmt->execute([
            $data['template_id'] ?: null,
            $data['title'],
            $data['description'] ?: null,
            $data['location'] ?: null,
            $data['department_id'] ?: null,
            (int) $data['assigned_to'],
            $data['status'],
            $data['scheduled_date'],
            $data['completed_date'] ?: null,
            !empty($data['score']) ? (float) $data['score'] : null,
            $data['notes'] ?: null,
            $id,
        ]);
    }

    public function deleteAudit(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_audits SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function updateAuditStatus(int $id, string $status): void
    {
        $extra = '';
        if ($status === 'completed') {
            $extra = ', completed_date = CURDATE()';
        }
        Database::pdo()->prepare("UPDATE hs_audits SET status = ?{$extra} WHERE id = ?")->execute([$status, $id]);
    }

    // ── Templates ─────────────────────────────────────────────

    public function allTemplates(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT * FROM hs_audit_templates WHERE is_deleted = 0 ORDER BY name ASC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findTemplate(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM hs_audit_templates WHERE id = ? AND is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function createTemplate(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_audit_templates (name, description, category, version, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?: null,
            $data['category'] ?? 'general',
            (int) ($data['version'] ?? 1),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateTemplate(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE hs_audit_templates SET name=?, description=?, category=?, version=?, is_active=? WHERE id=?'
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?: null,
            $data['category'],
            (int) ($data['version'] ?? 1),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id,
        ]);
    }

    public function deleteTemplate(int $id): void
    {
        Database::pdo()->prepare('UPDATE hs_audit_templates SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    public function templateItems(int $templateId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM hs_audit_template_items WHERE template_id = ? ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute([$templateId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addTemplateItem(int $templateId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO hs_audit_template_items
             (template_id, sort_order, section, question, description, response_type, is_required)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $templateId,
            (int) ($data['sort_order'] ?? 0),
            $data['section'] ?: null,
            $data['question'],
            $data['description'] ?: null,
            $data['response_type'] ?? 'yes_no',
            isset($data['is_required']) ? (int) $data['is_required'] : 1,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function removeTemplateItem(int $itemId): void
    {
        Database::pdo()->prepare('DELETE FROM hs_audit_template_items WHERE id = ?')->execute([$itemId]);
    }

    // ── Responses ─────────────────────────────────────────────

    public function saveResponses(int $auditId, array $responses): void
    {
        $pdo = Database::pdo();
        $pdo->prepare('DELETE FROM hs_audit_responses WHERE audit_id = ?')->execute([$auditId]);
        $stmt = $pdo->prepare(
            'INSERT INTO hs_audit_responses (audit_id, template_item_id, response, comment)
             VALUES (?, ?, ?, ?)'
        );
        foreach ($responses as $itemId => $resp) {
            $stmt->execute([
                $auditId,
                (int) $itemId,
                $resp['response'] ?? null,
                $resp['comment'] ?? null,
            ]);
        }
    }
}
