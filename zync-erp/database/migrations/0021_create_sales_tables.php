<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_quotes (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            quote_number VARCHAR(50) NOT NULL,
            customer_id BIGINT UNSIGNED NULL,
            contact_person VARCHAR(255) NULL,
            valid_until DATE NULL,
            status ENUM('draft','sent','accepted','rejected','expired') NOT NULL DEFAULT 'draft',
            total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            currency VARCHAR(10) NOT NULL DEFAULT 'SEK',
            notes TEXT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_quote_lines (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            quote_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NULL,
            description VARCHAR(255) NOT NULL DEFAULT '',
            quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
            unit VARCHAR(50) NOT NULL DEFAULT 'st',
            unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
            line_total DECIMAL(12,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_orders (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            order_number VARCHAR(50) NOT NULL,
            quote_id BIGINT UNSIGNED NULL,
            customer_id BIGINT UNSIGNED NULL,
            order_date DATE NULL,
            delivery_date DATE NULL,
            status ENUM('confirmed','in_production','shipped','delivered','cancelled') NOT NULL DEFAULT 'confirmed',
            total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            notes TEXT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_order_lines (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NULL,
            description VARCHAR(255) NOT NULL DEFAULT '',
            quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
            unit VARCHAR(50) NOT NULL DEFAULT 'st',
            unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            line_total DECIMAL(12,2) NOT NULL DEFAULT 0,
            production_order_id BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_price_lists (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            name VARCHAR(255) NOT NULL,
            customer_id BIGINT UNSIGNED NULL,
            valid_from DATE NULL,
            valid_until DATE NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_price_list_items (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            price_list_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NULL,
            unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            min_quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
            discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
