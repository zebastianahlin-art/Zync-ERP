<?php

declare(strict_types=1);

/**
 * Migration: Create account_groups table and add group_id to chart_of_accounts
 */
return function (\PDO $pdo): void
{
    // ─── account_groups ───────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS account_groups (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name        VARCHAR(255) NOT NULL,
            code        VARCHAR(50) NOT NULL,
            parent_id   BIGINT UNSIGNED NULL,
            sort_order  INT NOT NULL DEFAULT 0,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_ag_parent (parent_id),
            INDEX idx_ag_sort (sort_order),
            CONSTRAINT fk_ag_parent FOREIGN KEY (parent_id) REFERENCES account_groups(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── chart_of_accounts — add group_id ────────────────────────────────
    $coaCols = $pdo->query("SHOW COLUMNS FROM chart_of_accounts")->fetchAll(\PDO::FETCH_COLUMN);
    if (!in_array('group_id', $coaCols)) {
        $pdo->exec("ALTER TABLE chart_of_accounts ADD COLUMN group_id BIGINT UNSIGNED NULL AFTER account_class");
    }
};
