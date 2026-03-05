<?php

declare(strict_types=1);

/**
 * Migration: create employees table.
 *
 * Required by HR modules (Payroll, Attendance, Training, Certificates, Expenses).
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS employees (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by        BIGINT UNSIGNED NULL,
            is_deleted        TINYINT(1) NOT NULL DEFAULT 0,
            employee_number   VARCHAR(50) NULL,
            first_name        VARCHAR(100) NOT NULL,
            last_name         VARCHAR(100) NOT NULL,
            department_id     BIGINT UNSIGNED NULL,
            position          VARCHAR(150) NULL,
            phone             VARCHAR(50) NULL,
            email             VARCHAR(255) NULL,
            hire_date         DATE NULL,
            end_date          DATE NULL,
            salary            DECIMAL(12,2) NULL,
            employment_type   ENUM('full_time','part_time','consultant','intern') NOT NULL DEFAULT 'full_time',
            status            ENUM('active','on_leave','terminated') NOT NULL DEFAULT 'active',
            notes             TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_employees_number (employee_number),
            INDEX idx_employees_department (department_id),
            INDEX idx_employees_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
