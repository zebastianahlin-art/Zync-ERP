<?php

declare(strict_types=1);

/**
 * Migration: create work order parts table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS work_order_parts (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            work_order_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NULL,
            quantity DECIMAL(10,3) NOT NULL DEFAULT 1,
            unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_price DECIMAL(12,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
            added_by BIGINT UNSIGNED NULL,
            notes TEXT NULL,
            is_approved TINYINT(1) NOT NULL DEFAULT 0,
            approved_by BIGINT UNSIGNED NULL,
            approved_at TIMESTAMP NULL,
            PRIMARY KEY (id),
            CONSTRAINT fk_wop_work_order FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
