<?php

declare(strict_types=1);

/**
 * Migration: create sales supplement tables (quote templates + price list item columns)
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales_quote_templates (
            id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name               VARCHAR(255) NOT NULL,
            description        TEXT NULL,
            default_valid_days INT NOT NULL DEFAULT 30,
            template_lines     JSON NULL,
            is_active          TINYINT(1) NOT NULL DEFAULT 1,
            created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by         BIGINT UNSIGNED NULL,
            is_deleted         TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_sales_quote_templates_active (is_active),
            CONSTRAINT fk_sales_quote_templates_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    /* Add extended columns to the existing sales_price_list_items table */
    $existing = $pdo->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_price_list_items'"
    )->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('product_name', $existing, true)) {
        $pdo->exec("ALTER TABLE sales_price_list_items ADD COLUMN product_name VARCHAR(255) NOT NULL DEFAULT '' AFTER price_list_id");
    }
    if (!in_array('description', $existing, true)) {
        $pdo->exec("ALTER TABLE sales_price_list_items ADD COLUMN description TEXT NULL AFTER product_name");
    }
    if (!in_array('currency', $existing, true)) {
        $pdo->exec("ALTER TABLE sales_price_list_items ADD COLUMN currency VARCHAR(3) NOT NULL DEFAULT 'SEK' AFTER unit_price");
    }
    if (!in_array('unit', $existing, true)) {
        $pdo->exec("ALTER TABLE sales_price_list_items ADD COLUMN unit VARCHAR(50) NULL AFTER currency");
    }
};
