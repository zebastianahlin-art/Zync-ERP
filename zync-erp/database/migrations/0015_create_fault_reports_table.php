<?php

declare(strict_types=1);

/**
 * Migration: create fault reports table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS fault_reports (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            fault_number VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            machine_id BIGINT UNSIGNED NULL,
            equipment_id BIGINT UNSIGNED NULL,
            location VARCHAR(255) NULL,
            department_id BIGINT UNSIGNED NULL,
            fault_type ENUM('mechanical','electrical','hydraulic','pneumatic','software','structural','safety','other') NOT NULL DEFAULT 'other',
            priority ENUM('low','normal','high','urgent','critical') NOT NULL DEFAULT 'normal',
            status ENUM('reported','acknowledged','assigned','in_progress','resolved','closed') NOT NULL DEFAULT 'reported',
            reported_by BIGINT UNSIGNED NULL,
            acknowledged_by BIGINT UNSIGNED NULL,
            acknowledged_at TIMESTAMP NULL,
            assigned_to BIGINT UNSIGNED NULL,
            assigned_by BIGINT UNSIGNED NULL,
            assigned_at TIMESTAMP NULL,
            resolved_at TIMESTAMP NULL,
            work_order_id BIGINT UNSIGNED NULL,
            resolution TEXT NULL,
            downtime_hours DECIMAL(8,2) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_fault_number (fault_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
