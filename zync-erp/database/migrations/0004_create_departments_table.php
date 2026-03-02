<?php

declare(strict_types=1);

/**
 * Migration: create departments table (DOC-02 §2.3)
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS departments (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(100) NOT NULL,
            code        VARCHAR(20) NOT NULL,
            manager_id  BIGINT UNSIGNED NULL,
            parent_id   BIGINT UNSIGNED NULL,
            color       VARCHAR(7) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_departments_code (code),
            INDEX idx_departments_manager (manager_id),
            INDEX idx_departments_parent (parent_id),
            CONSTRAINT fk_departments_manager FOREIGN KEY (manager_id) REFERENCES users(id),
            CONSTRAINT fk_departments_parent  FOREIGN KEY (parent_id)  REFERENCES departments(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
