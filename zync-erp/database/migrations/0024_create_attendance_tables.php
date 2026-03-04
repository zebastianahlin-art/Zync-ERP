<?php

declare(strict_types=1);

/**
 * Migration: create attendance tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS attendance_records (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            employee_id BIGINT UNSIGNED NOT NULL,
            date        DATE NOT NULL,
            type        ENUM('presence','absence','vacation','sick','other') NOT NULL DEFAULT 'presence',
            time_in     TIME NULL,
            time_out    TIME NULL,
            approved    TINYINT(1) NOT NULL DEFAULT 0,
            notes       TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_attendance_records_employee (employee_id),
            INDEX idx_attendance_records_date (date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS attendance_balances (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            employee_id     BIGINT UNSIGNED NOT NULL,
            year            SMALLINT UNSIGNED NOT NULL,
            vacation_days   DECIMAL(5,1) NOT NULL DEFAULT 0,
            used_days       DECIMAL(5,1) NOT NULL DEFAULT 0,
            sick_days       DECIMAL(5,1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY idx_attendance_balances_emp_year (employee_id, year),
            INDEX idx_attendance_balances_employee (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
