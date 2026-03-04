<?php

declare(strict_types=1);

/**
 * Migration: Create products table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_number     VARCHAR(50) NOT NULL,
            name               VARCHAR(255) NOT NULL,
            description        TEXT NULL,
            category           VARCHAR(100) NULL,
            datasheet_url      VARCHAR(500) NULL,
            composition        TEXT NULL,
            weight             DECIMAL(10,3) NULL,
            weight_unit        ENUM('kg','g','ton') NOT NULL DEFAULT 'kg',
            dimensions         VARCHAR(100) NULL,
            sku                VARCHAR(50) NULL,
            barcode            VARCHAR(50) NULL,
            unit_price         DECIMAL(12,2) NULL,
            currency           VARCHAR(3) NOT NULL DEFAULT 'SEK',
            production_line_id BIGINT UNSIGNED NULL,
            min_stock_level    INT NULL,
            lead_time_days     INT NULL,
            status             ENUM('active','inactive','discontinued') NOT NULL DEFAULT 'active',
            created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by         BIGINT UNSIGNED NULL,
            is_deleted         TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uk_product_number (product_number),
            INDEX idx_products_status (status),
            INDEX idx_products_line (production_line_id),
            CONSTRAINT fk_products_line FOREIGN KEY (production_line_id) REFERENCES production_lines(id) ON DELETE SET NULL,
            CONSTRAINT fk_products_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
