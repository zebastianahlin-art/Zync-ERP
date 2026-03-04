<?php

declare(strict_types=1);

/**
 * Migration: create sales tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_quotes (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            quote_number    VARCHAR(50) NOT NULL,
            customer_id     BIGINT UNSIGNED NULL,
            valid_until     DATE NULL,
            status          ENUM('draft','sent','accepted','rejected','expired') NOT NULL DEFAULT 'draft',
            notes           TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_sales_quotes_number (quote_number),
            INDEX idx_sales_quotes_customer (customer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_quote_lines (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            quote_id    BIGINT UNSIGNED NOT NULL,
            article_id  BIGINT UNSIGNED NULL,
            description VARCHAR(255) NULL,
            quantity    DECIMAL(10,2) NOT NULL DEFAULT 1,
            unit_price  DECIMAL(12,2) NOT NULL DEFAULT 0,
            discount    DECIMAL(5,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_sales_quote_lines_quote (quote_id),
            CONSTRAINT fk_sales_quote_lines_quote FOREIGN KEY (quote_id) REFERENCES sales_quotes(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_orders (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            order_number    VARCHAR(50) NOT NULL,
            customer_id     BIGINT UNSIGNED NULL,
            quote_id        BIGINT UNSIGNED NULL,
            requested_date  DATE NULL,
            status          ENUM('draft','confirmed','in_progress','shipped','completed','cancelled') NOT NULL DEFAULT 'draft',
            notes           TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_sales_orders_number (order_number),
            INDEX idx_sales_orders_customer (customer_id),
            INDEX idx_sales_orders_quote (quote_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_order_lines (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            order_id    BIGINT UNSIGNED NOT NULL,
            article_id  BIGINT UNSIGNED NULL,
            description VARCHAR(255) NULL,
            quantity    DECIMAL(10,2) NOT NULL DEFAULT 1,
            unit_price  DECIMAL(12,2) NOT NULL DEFAULT 0,
            discount    DECIMAL(5,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_sales_order_lines_order (order_id),
            CONSTRAINT fk_sales_order_lines_order FOREIGN KEY (order_id) REFERENCES sales_orders(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_price_lists (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(150) NOT NULL,
            currency    VARCHAR(3) NOT NULL DEFAULT 'SEK',
            valid_from  DATE NULL,
            valid_to    DATE NULL,
            is_default  TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_price_list_items (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by    BIGINT UNSIGNED NULL,
            is_deleted    TINYINT(1) NOT NULL DEFAULT 0,
            price_list_id BIGINT UNSIGNED NOT NULL,
            article_id    BIGINT UNSIGNED NULL,
            unit_price    DECIMAL(12,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_sales_price_list_items_list (price_list_id),
            CONSTRAINT fk_sales_price_list_items_list FOREIGN KEY (price_list_id) REFERENCES sales_price_lists(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
