<?php

declare(strict_types=1);

/**
 * Migration: create drill_templates table
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS drill_templates (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by        BIGINT UNSIGNED NULL,
            is_deleted        TINYINT(1) NOT NULL DEFAULT 0,
            name              VARCHAR(255) NOT NULL,
            description       TEXT NULL,
            drill_type        ENUM('fire','evacuation','chemical','earthquake','lockdown','medical','other') NOT NULL DEFAULT 'fire',
            checklist         TEXT NULL,
            duration_estimate INT NULL,
            required_resources TEXT NULL,
            is_active         TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            INDEX idx_dt_type (drill_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};
