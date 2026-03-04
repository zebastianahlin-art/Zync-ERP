<?php

declare(strict_types=1);

/**
 * Migration: create warehouses, inventory_stock, inventory_transactions,
 *            stocktakings, and stocktaking_lines tables
 */
return function (\PDO $pdo): void
{
    // ─── Warehouses ───────────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS warehouses (
            id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by          BIGINT UNSIGNED NULL,
            is_deleted          TINYINT(1) NOT NULL DEFAULT 0,
            name                VARCHAR(255) NOT NULL,
            code                VARCHAR(50) NOT NULL,
            address             TEXT NULL,
            responsible_user_id BIGINT UNSIGNED NULL,
            is_active           TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Inventory Stock ──────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inventory_stock (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by    BIGINT UNSIGNED NULL,
            is_deleted    TINYINT(1) NOT NULL DEFAULT 0,
            article_id    BIGINT UNSIGNED NOT NULL,
            warehouse_id  BIGINT UNSIGNED NOT NULL,
            quantity      DECIMAL(12,2) NOT NULL DEFAULT 0,
            min_quantity  DECIMAL(12,2) NULL,
            max_quantity  DECIMAL(12,2) NULL,
            location_code VARCHAR(50) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_article_warehouse (article_id, warehouse_id),
            INDEX idx_warehouse_id (warehouse_id),
            INDEX idx_article_id (article_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Inventory Transactions ───────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inventory_transactions (
            id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by       BIGINT UNSIGNED NULL,
            is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
            article_id       BIGINT UNSIGNED NOT NULL,
            warehouse_id     BIGINT UNSIGNED NOT NULL,
            type             ENUM('receipt','issue','adjustment','transfer') NOT NULL,
            quantity         DECIMAL(12,2) NOT NULL,
            reference_type   VARCHAR(50) NULL,
            reference_id     BIGINT UNSIGNED NULL,
            notes            TEXT NULL,
            to_warehouse_id  BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            INDEX idx_article_id (article_id),
            INDEX idx_warehouse_id (warehouse_id),
            INDEX idx_type (type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Stocktakings ─────────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS stocktakings (
            id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by   BIGINT UNSIGNED NULL,
            is_deleted   TINYINT(1) NOT NULL DEFAULT 0,
            warehouse_id BIGINT UNSIGNED NOT NULL,
            name         VARCHAR(255) NOT NULL,
            status       ENUM('draft','in_progress','completed','approved') NOT NULL DEFAULT 'draft',
            started_at   TIMESTAMP NULL,
            completed_at TIMESTAMP NULL,
            approved_by  BIGINT UNSIGNED NULL,
            approved_at  TIMESTAMP NULL,
            PRIMARY KEY (id),
            INDEX idx_warehouse_id (warehouse_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Stocktaking Lines ────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS stocktaking_lines (
            id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            stocktaking_id   BIGINT UNSIGNED NOT NULL,
            article_id       BIGINT UNSIGNED NOT NULL,
            system_quantity  DECIMAL(12,2) NOT NULL DEFAULT 0,
            counted_quantity DECIMAL(12,2) NULL,
            notes            TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_stocktaking_article (stocktaking_id, article_id),
            INDEX idx_stocktaking_id (stocktaking_id),
            INDEX idx_article_id (article_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
