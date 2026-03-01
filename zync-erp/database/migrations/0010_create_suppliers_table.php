<?php

declare(strict_types=1);

/**
 * Migration: create suppliers table
 */
function up_0010(\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS suppliers (
            id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by     BIGINT UNSIGNED NULL,
            is_deleted     TINYINT(1) NOT NULL DEFAULT 0,
            name           VARCHAR(255) NOT NULL,
            org_number     VARCHAR(50) NOT NULL,
            email          VARCHAR(255) NOT NULL,
            phone          VARCHAR(50) NULL,
            address        TEXT NULL,
            city           VARCHAR(100) NULL,
            postal_code    VARCHAR(20) NULL,
            country        VARCHAR(100) NOT NULL DEFAULT 'Sverige',
            contact_person VARCHAR(255) NULL,
            website        VARCHAR(500) NULL,
            notes          TEXT NULL,
            is_active      TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY idx_suppliers_org_number (org_number),
            UNIQUE KEY idx_suppliers_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
