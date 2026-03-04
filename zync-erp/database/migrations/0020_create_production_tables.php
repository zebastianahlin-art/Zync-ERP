<?php

declare(strict_types=1);

/**
 * Migration: create production tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_lines (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(150) NOT NULL,
            code        VARCHAR(30) NOT NULL,
            description TEXT NULL,
            status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
            PRIMARY KEY (id),
            UNIQUE KEY idx_production_lines_code (code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_orders (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            order_number    VARCHAR(50) NOT NULL,
            line_id         BIGINT UNSIGNED NULL,
            article_id      BIGINT UNSIGNED NULL,
            quantity        DECIMAL(10,2) NOT NULL DEFAULT 0,
            planned_start   DATE NULL,
            planned_end     DATE NULL,
            status          ENUM('draft','planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'draft',
            notes           TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_production_orders_number (order_number),
            INDEX idx_production_orders_line (line_id),
            CONSTRAINT fk_production_orders_line FOREIGN KEY (line_id) REFERENCES production_lines(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_stock (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            article_id  BIGINT UNSIGNED NULL,
            location    VARCHAR(100) NULL,
            quantity    DECIMAL(10,2) NOT NULL DEFAULT 0,
            unit        VARCHAR(20) NOT NULL DEFAULT 'st',
            PRIMARY KEY (id),
            INDEX idx_production_stock_article (article_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
