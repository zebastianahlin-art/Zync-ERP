<?php

declare(strict_types=1);

/**
 * Migration: create object_registry table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS object_registry (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by    BIGINT UNSIGNED NULL,
            is_deleted    TINYINT(1) NOT NULL DEFAULT 0,
            object_type   VARCHAR(50) NOT NULL COMMENT 'machine, equipment, article, customer, supplier, employee, work_order, etc.',
            object_id     BIGINT UNSIGNED NOT NULL,
            display_name  VARCHAR(255) NOT NULL,
            search_text   TEXT NULL COMMENT 'Concatenated searchable text',
            parent_type   VARCHAR(50) NULL,
            parent_id     BIGINT UNSIGNED NULL,
            metadata      JSON NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_object_registry_unique (object_type, object_id),
            INDEX idx_object_registry_type (object_type),
            INDEX idx_object_registry_parent (parent_type, parent_id),
            FULLTEXT INDEX idx_object_registry_search (display_name, search_text)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
