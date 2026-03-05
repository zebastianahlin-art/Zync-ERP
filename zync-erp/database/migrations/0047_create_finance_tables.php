<?php

declare(strict_types=1);

/**
 * Migration: create cost_centers, chart_of_accounts, journal_entries,
 *            and journal_entry_lines tables.
 *
 * These tables are required by the Finance, Purchasing, and Reporting modules.
 */
return function (\PDO $pdo): void
{
    // ─── Cost Centers ─────────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cost_centers (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            code            VARCHAR(50) NOT NULL,
            name            VARCHAR(200) NOT NULL,
            description     TEXT NULL,
            parent_id       BIGINT UNSIGNED NULL,
            department_id   BIGINT UNSIGNED NULL,
            responsible_id  BIGINT UNSIGNED NULL,
            budget          DECIMAL(14,2) NOT NULL DEFAULT 0,
            is_active       TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY uq_cost_centers_code (code),
            INDEX idx_cost_centers_parent (parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Chart of Accounts ────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS chart_of_accounts (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            account_number  VARCHAR(20) NOT NULL,
            name            VARCHAR(255) NOT NULL,
            account_class   VARCHAR(10) NOT NULL,
            account_group   VARCHAR(50) NULL,
            group_id        BIGINT UNSIGNED NULL,
            vat_code        VARCHAR(20) NULL,
            description     TEXT NULL,
            is_active       TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY uq_chart_account_number (account_number),
            INDEX idx_chart_class (account_class),
            INDEX idx_chart_group_id (group_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Journal Entries ──────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS journal_entries (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            voucher_number  VARCHAR(50) NOT NULL,
            voucher_series  VARCHAR(10) NOT NULL DEFAULT 'A',
            entry_date      DATE NOT NULL,
            description     VARCHAR(255) NOT NULL,
            source_type     VARCHAR(50) NULL,
            source_id       BIGINT UNSIGNED NULL,
            fiscal_year     SMALLINT UNSIGNED NOT NULL,
            fiscal_period   TINYINT UNSIGNED NOT NULL,
            is_locked       TINYINT(1) NOT NULL DEFAULT 0,
            subtotal        DECIMAL(14,2) NOT NULL DEFAULT 0,
            vat_amount      DECIMAL(14,2) NOT NULL DEFAULT 0,
            total_amount    DECIMAL(14,2) NOT NULL DEFAULT 0,
            notes           TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_journal_voucher (voucher_series, voucher_number),
            INDEX idx_journal_entry_date (entry_date),
            INDEX idx_journal_fiscal (fiscal_year, fiscal_period)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Journal Entry Lines ──────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS journal_entry_lines (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            entry_id        BIGINT UNSIGNED NOT NULL,
            account_id      BIGINT UNSIGNED NOT NULL,
            cost_center_id  BIGINT UNSIGNED NULL,
            description     VARCHAR(255) NULL,
            debit           DECIMAL(14,2) NOT NULL DEFAULT 0,
            credit          DECIMAL(14,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_jel_entry_id (entry_id),
            INDEX idx_jel_account_id (account_id),
            INDEX idx_jel_cost_center_id (cost_center_id),
            CONSTRAINT fk_jel_entry FOREIGN KEY (entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
