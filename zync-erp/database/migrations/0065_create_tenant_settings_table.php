<?php

declare(strict_types=1);

/**
 * Migration 0065 — Create saas_tenant_settings table.
 *
 * Per-tenant konfigurationsinställningar (key-value store).
 */

use App\Core\Database;

$pdo = Database::pdo();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS saas_tenant_settings (
        id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        tenant_id   INT UNSIGNED    NOT NULL,
        setting_key VARCHAR(100)    NOT NULL,
        value       TEXT            NULL,
        created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_tenant_key (tenant_id, setting_key),
        KEY idx_tenant (tenant_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
