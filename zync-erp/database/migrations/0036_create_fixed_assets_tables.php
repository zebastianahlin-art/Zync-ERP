<?php

declare(strict_types=1);

/**
 * Migration: Create fixed_assets and asset_depreciations tables
 */
return function (\PDO $pdo): void
{
    // ─── fixed_assets ─────────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS fixed_assets (
            id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name                 VARCHAR(255) NOT NULL,
            description          TEXT NULL,
            asset_number         VARCHAR(100) NOT NULL,
            purchase_date        DATE NOT NULL,
            purchase_price       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            current_value        DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            depreciation_method  ENUM('linear','declining') NOT NULL DEFAULT 'linear',
            depreciation_years   INT NOT NULL DEFAULT 5,
            department_id        BIGINT UNSIGNED NULL,
            account_id           BIGINT UNSIGNED NULL,
            status               ENUM('active','disposed','written_off') NOT NULL DEFAULT 'active',
            created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by           BIGINT UNSIGNED NULL,
            is_deleted           TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uk_fa_number (asset_number),
            INDEX idx_fa_status (status),
            INDEX idx_fa_department (department_id),
            CONSTRAINT fk_fa_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
            CONSTRAINT fk_fa_account FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
            CONSTRAINT fk_fa_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── asset_depreciations ──────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS asset_depreciations (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            asset_id          BIGINT UNSIGNED NOT NULL,
            depreciation_date DATE NOT NULL,
            amount            DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            accumulated       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX idx_ad_asset (asset_id),
            INDEX idx_ad_date (depreciation_date),
            CONSTRAINT fk_ad_asset FOREIGN KEY (asset_id) REFERENCES fixed_assets(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
