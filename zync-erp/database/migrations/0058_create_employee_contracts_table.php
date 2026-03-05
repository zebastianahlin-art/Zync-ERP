<?php

declare(strict_types=1);

/**
 * Migration 0058: create employee_contracts table (FAS E – E5)
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS employee_contracts (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by        BIGINT UNSIGNED NULL,
            is_deleted        TINYINT(1) NOT NULL DEFAULT 0,
            employee_id       BIGINT UNSIGNED NOT NULL,
            contract_type     ENUM('permanent','fixed_term','part_time','probationary','consultant','intern') NOT NULL DEFAULT 'permanent',
            start_date        DATE NOT NULL,
            end_date          DATE NULL,
            salary_type       ENUM('monthly','hourly','commission') NOT NULL DEFAULT 'monthly',
            salary            DECIMAL(12,2) NULL,
            weekly_hours      DECIMAL(5,2) NULL,
            workplace         VARCHAR(255) NULL,
            notice_period     VARCHAR(100) NULL,
            notes             TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_employee_contracts_employee (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
