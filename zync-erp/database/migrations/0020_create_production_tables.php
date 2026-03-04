<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_lines (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            department_id BIGINT UNSIGNED NULL,
            equipment_id BIGINT UNSIGNED NULL,
            capacity VARCHAR(100) NULL,
            status ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active',
            sort_order INT NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_orders (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            order_number VARCHAR(50) NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            production_line_id BIGINT UNSIGNED NULL,
            quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
            unit VARCHAR(50) NOT NULL DEFAULT 'st',
            planned_start DATE NULL,
            planned_end DATE NULL,
            actual_start DATE NULL,
            actual_end DATE NULL,
            status ENUM('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned',
            priority ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
            notes TEXT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_stock (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            article_id BIGINT UNSIGNED NULL,
            warehouse_type ENUM('raw_material','finished_goods') NOT NULL DEFAULT 'raw_material',
            quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
            unit VARCHAR(50) NOT NULL DEFAULT 'st',
            location VARCHAR(255) NULL,
            min_stock_level DECIMAL(10,2) NULL,
            notes TEXT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
