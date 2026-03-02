<?php

declare(strict_types=1);

/**
 * Migration: create permissions table (DOC-02 §2.4)
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS permissions (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            module      VARCHAR(60) NOT NULL,
            action      VARCHAR(60) NOT NULL,
            resource    VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_permissions_unique (module, action, resource)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
