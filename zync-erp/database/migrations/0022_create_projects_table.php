<?php

declare(strict_types=1);

/**
 * Migration: create project tables
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            project_number  VARCHAR(50) NOT NULL,
            name            VARCHAR(150) NOT NULL,
            description     TEXT NULL,
            customer_id     BIGINT UNSIGNED NULL,
            manager_id      BIGINT UNSIGNED NULL,
            start_date      DATE NULL,
            end_date        DATE NULL,
            status          ENUM('planning','active','on_hold','completed','cancelled') NOT NULL DEFAULT 'planning',
            budget          DECIMAL(14,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY idx_projects_number (project_number),
            INDEX idx_projects_customer (customer_id),
            INDEX idx_projects_manager (manager_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_tasks (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            project_id  BIGINT UNSIGNED NOT NULL,
            title       VARCHAR(200) NOT NULL,
            description TEXT NULL,
            assigned_to BIGINT UNSIGNED NULL,
            due_date    DATE NULL,
            status      ENUM('todo','in_progress','done') NOT NULL DEFAULT 'todo',
            priority    ENUM('low','normal','high') NOT NULL DEFAULT 'normal',
            PRIMARY KEY (id),
            INDEX idx_project_tasks_project (project_id),
            CONSTRAINT fk_project_tasks_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_budget_lines (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            project_id  BIGINT UNSIGNED NOT NULL,
            description VARCHAR(255) NOT NULL,
            category    VARCHAR(100) NULL,
            budgeted    DECIMAL(12,2) NOT NULL DEFAULT 0,
            actual      DECIMAL(12,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_project_budget_lines_project (project_id),
            CONSTRAINT fk_project_budget_lines_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
