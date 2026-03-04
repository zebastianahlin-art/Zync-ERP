<?php

declare(strict_types=1);

/**
 * Migration: create recruitment tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS recruitment_positions (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            title           VARCHAR(200) NOT NULL,
            department_id   BIGINT UNSIGNED NULL,
            description     TEXT NULL,
            requirements    TEXT NULL,
            num_openings    SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            posted_at       DATE NULL,
            closes_at       DATE NULL,
            status          ENUM('draft','open','on_hold','closed','filled') NOT NULL DEFAULT 'draft',
            PRIMARY KEY (id),
            INDEX idx_recruitment_positions_dept (department_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS recruitment_applicants (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            position_id     BIGINT UNSIGNED NOT NULL,
            first_name      VARCHAR(100) NOT NULL,
            last_name       VARCHAR(100) NOT NULL,
            email           VARCHAR(255) NOT NULL,
            phone           VARCHAR(50) NULL,
            cv_path         VARCHAR(500) NULL,
            applied_at      DATE NOT NULL,
            status          ENUM('new','screening','interview','offer','hired','rejected') NOT NULL DEFAULT 'new',
            notes           TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_recruitment_applicants_position (position_id),
            CONSTRAINT fk_recruitment_applicants_position FOREIGN KEY (position_id) REFERENCES recruitment_positions(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
