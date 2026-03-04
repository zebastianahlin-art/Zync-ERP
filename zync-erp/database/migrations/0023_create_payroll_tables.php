<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payroll_periods (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            name VARCHAR(100) NOT NULL,
            period_start DATE NOT NULL,
            period_end DATE NOT NULL,
            status ENUM('open','processing','closed') NOT NULL DEFAULT 'open',
            closed_at TIMESTAMP NULL,
            closed_by BIGINT UNSIGNED NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payroll_payslips (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            period_id BIGINT UNSIGNED NOT NULL,
            employee_id BIGINT UNSIGNED NOT NULL,
            gross_salary DECIMAL(10,2) NOT NULL DEFAULT 0,
            tax_deduction DECIMAL(10,2) NOT NULL DEFAULT 0,
            net_salary DECIMAL(10,2) NOT NULL DEFAULT 0,
            overtime_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
            overtime_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            other_deductions DECIMAL(10,2) NOT NULL DEFAULT 0,
            other_additions DECIMAL(10,2) NOT NULL DEFAULT 0,
            notes TEXT NULL,
            generated_at TIMESTAMP NULL,
            file_path VARCHAR(500) NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
