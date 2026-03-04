<?php

declare(strict_types=1);

/**
 * Migration: create training tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS training_courses (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(200) NOT NULL,
            description TEXT NULL,
            duration_h  DECIMAL(5,1) NULL,
            provider    VARCHAR(150) NULL,
            category    VARCHAR(100) NULL,
            is_mandatory TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS training_sessions (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            course_id   BIGINT UNSIGNED NOT NULL,
            start_date  DATE NOT NULL,
            end_date    DATE NULL,
            location    VARCHAR(200) NULL,
            trainer     VARCHAR(150) NULL,
            max_participants SMALLINT UNSIGNED NULL,
            status      ENUM('planned','ongoing','completed','cancelled') NOT NULL DEFAULT 'planned',
            PRIMARY KEY (id),
            INDEX idx_training_sessions_course (course_id),
            CONSTRAINT fk_training_sessions_course FOREIGN KEY (course_id) REFERENCES training_courses(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS training_participants (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            session_id  BIGINT UNSIGNED NOT NULL,
            employee_id BIGINT UNSIGNED NOT NULL,
            status      ENUM('registered','attended','passed','failed','absent') NOT NULL DEFAULT 'registered',
            score       DECIMAL(5,2) NULL,
            notes       TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_training_participants_session (session_id),
            INDEX idx_training_participants_employee (employee_id),
            CONSTRAINT fk_training_participants_session FOREIGN KEY (session_id) REFERENCES training_sessions(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
