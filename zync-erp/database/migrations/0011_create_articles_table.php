<?php

declare(strict_types=1);

/**
 * Migration: create articles table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS articles (
            id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by     BIGINT UNSIGNED NULL,
            is_deleted     TINYINT(1) NOT NULL DEFAULT 0,
            article_number VARCHAR(50) NOT NULL,
            name           VARCHAR(255) NOT NULL,
            description    TEXT NULL,
            unit           VARCHAR(20) NOT NULL DEFAULT 'st',
            purchase_price DECIMAL(12,2) NULL DEFAULT 0.00,
            selling_price  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            vat_rate       DECIMAL(5,2) NOT NULL DEFAULT 25.00,
            category       VARCHAR(100) NULL,
            supplier_id    BIGINT UNSIGNED NULL,
            is_active      TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY idx_articles_number (article_number),
            INDEX idx_articles_category (category),
            INDEX idx_articles_supplier (supplier_id),
            CONSTRAINT fk_articles_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
