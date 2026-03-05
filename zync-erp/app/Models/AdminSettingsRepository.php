<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Repository for admin system settings, ERP modules, and site settings.
 */
class AdminSettingsRepository
{
    // ── System Settings ────────────────────────────────────────────────────────

    /** Return all system settings, optionally filtered by category. */
    public function allSettings(?string $category = null): array
    {
        $pdo = Database::pdo();

        if ($category !== null) {
            $stmt = $pdo->prepare('SELECT * FROM system_settings WHERE category = ? ORDER BY category, setting_key');
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query('SELECT * FROM system_settings ORDER BY category, setting_key');
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Return all settings grouped by category. */
    public function allSettingsGrouped(): array
    {
        $rows = $this->allSettings();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row;
        }
        return $grouped;
    }

    /** Retrieve a single setting value by key. */
    public function getSetting(string $key): ?string
    {
        $stmt = Database::pdo()->prepare('SELECT setting_value FROM system_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (string) $val : null;
    }

    /** Update a single setting. */
    public function setSetting(string $key, string $value): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE system_settings SET setting_value = ? WHERE setting_key = ?'
        );
        $stmt->execute([$value, $key]);
    }

    /** Batch-update multiple settings. */
    public function setSettings(array $keyValues): void
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare('UPDATE system_settings SET setting_value = ? WHERE setting_key = ?');
        foreach ($keyValues as $key => $value) {
            $stmt->execute([(string) $value, (string) $key]);
        }
    }

    // ── ERP Modules ────────────────────────────────────────────────────────────

    /** Return all ERP modules ordered by sort_order. */
    public function allModules(): array
    {
        $stmt = Database::pdo()->query('SELECT * FROM erp_modules ORDER BY sort_order ASC, id ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single ERP module by ID. */
    public function findModule(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM erp_modules WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Update an ERP module. */
    public function updateModule(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE erp_modules SET name = ?, description = ?, is_active = ?, sort_order = ?, version = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            (int) ($data['sort_order'] ?? 0),
            $data['version'] ?? '1.0.0',
            $id,
        ]);
    }

    /** Toggle a module's is_active flag. */
    public function toggleModule(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE erp_modules SET is_active = 1 - is_active WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ── Site Settings ──────────────────────────────────────────────────────────

    /** Return the site settings row (always row with id=1). */
    public function getSiteSettings(): array
    {
        $stmt = Database::pdo()->query('SELECT * FROM site_settings LIMIT 1');
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: [];
    }

    /** Update site settings (upsert on row id=1). */
    public function updateSiteSettings(array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE site_settings SET
                company_name    = ?,
                company_logo    = ?,
                primary_color   = ?,
                timezone        = ?,
                date_format     = ?,
                currency        = ?,
                language        = ?,
                smtp_host       = ?,
                smtp_port       = ?,
                smtp_user       = ?,
                smtp_password   = ?,
                smtp_encryption = ?,
                footer_text     = ?
            WHERE id = 1'
        );
        $stmt->execute([
            $data['company_name']    ?? 'ZYNC ERP',
            $data['company_logo']    ?: null,
            $data['primary_color']   ?? '#4f46e5',
            $data['timezone']        ?? 'Europe/Stockholm',
            $data['date_format']     ?? 'Y-m-d',
            $data['currency']        ?? 'SEK',
            $data['language']        ?? 'sv',
            $data['smtp_host']       ?: null,
            !empty($data['smtp_port']) ? (int) $data['smtp_port'] : null,
            $data['smtp_user']       ?: null,
            $data['smtp_password']   ?: null,
            $data['smtp_encryption'] ?? 'tls',
            $data['footer_text']     ?: null,
        ]);
    }

    // ── System Info ────────────────────────────────────────────────────────────

    /** Return general system information. */
    public function systemInfo(): array
    {
        $pdo = Database::pdo();

        try {
            $dbVersion = $pdo->query('SELECT VERSION()')->fetchColumn();
        } catch (\Throwable) {
            $dbVersion = 'N/A';
        }

        try {
            $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users WHERE is_deleted = 0')->fetchColumn();
        } catch (\Throwable) {
            $userCount = 0;
        }

        try {
            $moduleCount = (int) $pdo->query('SELECT COUNT(*) FROM erp_modules WHERE is_active = 1')->fetchColumn();
        } catch (\Throwable) {
            $moduleCount = 0;
        }

        try {
            $auditCount = (int) $pdo->query('SELECT COUNT(*) FROM audit_log')->fetchColumn();
        } catch (\Throwable) {
            $auditCount = 0;
        }

        return [
            'php_version'   => PHP_VERSION,
            'db_version'    => (string) $dbVersion,
            'user_count'    => $userCount,
            'module_count'  => $moduleCount,
            'audit_count'   => $auditCount,
            'memory_limit'  => ini_get('memory_limit'),
            'memory_usage'  => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'disk_free'     => function_exists('disk_free_space') ? round((float) disk_free_space('/') / 1024 / 1024 / 1024, 2) . ' GB' : 'N/A',
            'server_time'   => date('Y-m-d H:i:s'),
        ];
    }

    // ── Audit Log ──────────────────────────────────────────────────────────────

    /** Return audit log stats grouped by module. */
    public function auditLogStats(): array
    {
        try {
            $stmt = Database::pdo()->query(
                'SELECT module, COUNT(*) AS cnt FROM audit_log GROUP BY module ORDER BY cnt DESC LIMIT 10'
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Return paginated audit log entries with optional filters.
     * @return array{rows: array, total: int}
     */
    public function auditLog(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $pdo    = Database::pdo();
        $where  = [];
        $params = [];

        if (!empty($filters['module'])) {
            $where[]  = 'al.module = ?';
            $params[] = $filters['module'];
        }
        if (!empty($filters['action'])) {
            $where[]  = 'al.action = ?';
            $params[] = $filters['action'];
        }
        if (!empty($filters['user_id'])) {
            $where[]  = 'al.user_id = ?';
            $params[] = (int) $filters['user_id'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'al.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'al.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM audit_log al $whereClause");
            $countStmt->execute($params);
            $total = (int) $countStmt->fetchColumn();

            $offset = ($page - 1) * $perPage;
            $stmt   = $pdo->prepare(
                "SELECT al.*, u.username, u.email
                 FROM audit_log al
                 LEFT JOIN users u ON u.id = al.user_id
                 $whereClause
                 ORDER BY al.created_at DESC
                 LIMIT $perPage OFFSET $offset"
            );
            $stmt->execute($params);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return ['rows' => [], 'total' => 0];
        }

        return ['rows' => $rows, 'total' => $total];
    }

    /** Delete audit log entries older than the given number of days. Returns count deleted. */
    public function clearAuditLog(int $olderThanDays): int
    {
        try {
            $stmt = Database::pdo()->prepare(
                'DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)'
            );
            $stmt->execute([$olderThanDays]);
            return $stmt->rowCount();
        } catch (\Throwable) {
            return 0;
        }
    }
}
