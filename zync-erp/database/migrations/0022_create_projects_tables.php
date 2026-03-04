<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            project_number VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            customer_id BIGINT UNSIGNED NULL,
            department_id BIGINT UNSIGNED NULL,
            project_manager_id BIGINT UNSIGNED NULL,
            status ENUM('planning','active','on_hold','completed','cancelled') NOT NULL DEFAULT 'planning',
            start_date DATE NULL,
            end_date DATE NULL,
            budget_amount DECIMAL(12,2) NULL,
            actual_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
            notes TEXT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_tasks (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            project_id BIGINT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            assigned_to BIGINT UNSIGNED NULL,
            status ENUM('todo','in_progress','done') NOT NULL DEFAULT 'todo',
            planned_start DATE NULL,
            planned_end DATE NULL,
            estimated_hours DECIMAL(6,2) NULL,
            actual_hours DECIMAL(6,2) NOT NULL DEFAULT 0,
            sort_order INT NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_budget_lines (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            project_id BIGINT UNSIGNED NOT NULL,
            category VARCHAR(100) NOT NULL,
            description VARCHAR(255) NOT NULL,
            planned_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            actual_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
