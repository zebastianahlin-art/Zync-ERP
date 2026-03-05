<?php

declare(strict_types=1);

/**
 * Migration: create emergency_drills table
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS emergency_drills (
            id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by       BIGINT UNSIGNED NULL,
            is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
            drill_number     VARCHAR(50) NOT NULL,
            title            VARCHAR(255) NOT NULL,
            description      TEXT NULL,
            drill_type       ENUM('fire','evacuation','chemical','earthquake','lockdown','medical','other') NOT NULL DEFAULT 'fire',
            template_id      BIGINT UNSIGNED NULL,
            location         VARCHAR(255) NULL,
            department_id    BIGINT UNSIGNED NULL,
            scheduled_date   DATE NOT NULL,
            executed_date    DATE NULL,
            duration_minutes INT NULL,
            participants     INT NULL,
            coordinator_id   BIGINT UNSIGNED NULL,
            status           ENUM('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned',
            evaluation       TEXT NULL,
            score            DECIMAL(5,2) NULL,
            improvements     TEXT NULL,
            next_drill_date  DATE NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_drill_number (drill_number),
            INDEX idx_drills_status (status),
            INDEX idx_drills_date (scheduled_date),
            INDEX idx_drills_type (drill_type),
            INDEX idx_drills_department (department_id),
            CONSTRAINT fk_drills_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
            CONSTRAINT fk_drills_coord FOREIGN KEY (coordinator_id) REFERENCES users(id) ON DELETE SET NULL,
            CONSTRAINT fk_drills_created FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};
