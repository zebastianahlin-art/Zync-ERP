<?php

declare(strict_types=1);

/**
 * Migration: create roles table (DOC-02 §2.2)
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roles (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(60) NOT NULL,
            slug        VARCHAR(60) NOT NULL,
            level       TINYINT NOT NULL DEFAULT 1,
            description TEXT NULL,
            is_system   TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY idx_roles_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
