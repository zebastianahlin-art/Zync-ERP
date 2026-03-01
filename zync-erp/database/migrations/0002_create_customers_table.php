<?php

declare(strict_types=1);

/**
 * Migration: create customers table
 */
function up(\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS customers (
            id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
            name       VARCHAR(255)  NOT NULL,
            org_number VARCHAR(50)   NOT NULL,
            email      VARCHAR(255)  NOT NULL,
            phone      VARCHAR(50)   NULL,
            address    TEXT          NULL,
            created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY customers_org_number_unique (org_number),
            UNIQUE KEY customers_email_unique (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
