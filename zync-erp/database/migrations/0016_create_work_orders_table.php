<?php

declare(strict_types=1);

/**
 * Migration: create work orders table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS work_orders (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            order_number VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            work_type ENUM('corrective','preventive','predictive','emergency','improvement','inspection') NOT NULL DEFAULT 'corrective',
            machine_id BIGINT UNSIGNED NULL,
            equipment_id BIGINT UNSIGNED NULL,
            fault_report_id BIGINT UNSIGNED NULL,
            location VARCHAR(255) NULL,
            department_id BIGINT UNSIGNED NULL,
            priority ENUM('low','normal','high','urgent','critical') NOT NULL DEFAULT 'normal',
            status ENUM('reported','assigned','in_progress','work_completed','pending_approval','approved','rejected','closed','archived') NOT NULL DEFAULT 'reported',
            assigned_to BIGINT UNSIGNED NULL,
            assigned_by BIGINT UNSIGNED NULL,
            assigned_at TIMESTAMP NULL,
            started_at TIMESTAMP NULL,
            completed_at TIMESTAMP NULL,
            completion_notes TEXT NULL,
            approved_by BIGINT UNSIGNED NULL,
            approved_at TIMESTAMP NULL,
            approval_notes TEXT NULL,
            rejected_reason TEXT NULL,
            closed_at TIMESTAMP NULL,
            closed_by BIGINT UNSIGNED NULL,
            archived_at TIMESTAMP NULL,
            planned_start DATETIME NULL,
            planned_end DATETIME NULL,
            estimated_hours DECIMAL(8,2) NULL,
            total_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            total_material_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
            downtime_hours DECIMAL(8,2) NULL,
            cost_center_id BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idx_order_number (order_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
