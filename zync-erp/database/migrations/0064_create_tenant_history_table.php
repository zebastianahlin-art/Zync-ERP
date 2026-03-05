<?php

declare(strict_types=1);

/**
 * Migration 0064 — Create saas_tenant_history table.
 *
 * Logg över alla statusändringar och viktiga händelser per tenant.
 */

use App\Core\Database;

$pdo = Database::pdo();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS saas_tenant_history (
        id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        tenant_id   INT UNSIGNED    NOT NULL,
        action      VARCHAR(100)    NOT NULL COMMENT 'E.g. status_change, plan_change, module_activated',
        old_value   VARCHAR(255)    NULL,
        new_value   VARCHAR(255)    NULL,
        changed_by  INT UNSIGNED    NULL COMMENT 'User ID who made the change',
        notes       TEXT            NULL,
        created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_tenant (tenant_id),
        KEY idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
