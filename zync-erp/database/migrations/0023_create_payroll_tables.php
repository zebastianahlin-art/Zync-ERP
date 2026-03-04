<?php

declare(strict_types=1);

/**
 * Migration: create payroll tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payroll_periods (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(100) NOT NULL,
            period_from DATE NOT NULL,
            period_to   DATE NOT NULL,
            status      ENUM('open','locked','paid') NOT NULL DEFAULT 'open',
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payroll_payslips (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            period_id   BIGINT UNSIGNED NOT NULL,
            employee_id BIGINT UNSIGNED NOT NULL,
            gross_pay   DECIMAL(12,2) NOT NULL DEFAULT 0,
            deductions  DECIMAL(12,2) NOT NULL DEFAULT 0,
            net_pay     DECIMAL(12,2) NOT NULL DEFAULT 0,
            status      ENUM('draft','approved','paid') NOT NULL DEFAULT 'draft',
            notes       TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_payroll_payslips_period (period_id),
            INDEX idx_payroll_payslips_employee (employee_id),
            CONSTRAINT fk_payroll_payslips_period FOREIGN KEY (period_id) REFERENCES payroll_periods(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
