<?php

declare(strict_types=1);

/**
 * Migration: create equipment_documents table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS equipment_documents (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            equipment_id    BIGINT UNSIGNED NOT NULL,
            name            VARCHAR(200) NOT NULL,
            file_path       VARCHAR(500) NOT NULL,
            file_type       VARCHAR(100) NULL,
            file_size       INT UNSIGNED NULL,
            description     TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_equipment_documents_equipment (equipment_id),
            CONSTRAINT fk_equipment_documents_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
