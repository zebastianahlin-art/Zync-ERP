<?php

declare(strict_types=1);

/**
 * Migration: create expense_reports and expense_report_lines tables
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS expense_reports (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            report_number   VARCHAR(50) NOT NULL,
            employee_id     BIGINT UNSIGNED NOT NULL,
            title           VARCHAR(255) NOT NULL,
            description     TEXT NULL,
            trip_start      DATE NULL,
            trip_end        DATE NULL,
            destination     VARCHAR(255) NULL,
            purpose         VARCHAR(255) NULL,
            total_amount    DECIMAL(12,2) NOT NULL DEFAULT 0,
            currency        VARCHAR(3) NOT NULL DEFAULT 'SEK',
            status          ENUM('draft','submitted','approved','rejected','paid') NOT NULL DEFAULT 'draft',
            approved_by     BIGINT UNSIGNED NULL,
            approved_at     DATETIME NULL,
            paid_at         DATETIME NULL,
            notes           TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_expense_report_number (report_number),
            INDEX idx_er_employee (employee_id),
            INDEX idx_er_status (status),
            CONSTRAINT fk_er_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            CONSTRAINT fk_er_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
            CONSTRAINT fk_er_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS expense_report_lines (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            report_id       BIGINT UNSIGNED NOT NULL,
            expense_date    DATE NOT NULL,
            category        ENUM('travel','accommodation','meals','fuel','parking','taxi','other') NOT NULL DEFAULT 'other',
            description     VARCHAR(255) NOT NULL,
            amount          DECIMAL(12,2) NOT NULL,
            currency        VARCHAR(3) NOT NULL DEFAULT 'SEK',
            receipt_ref     VARCHAR(255) NULL,
            notes           TEXT NULL,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX idx_erl_report (report_id),
            CONSTRAINT fk_erl_report FOREIGN KEY (report_id) REFERENCES expense_reports(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};
