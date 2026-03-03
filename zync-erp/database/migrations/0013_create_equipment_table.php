<?php

declare(strict_types=1);

/**
 * Migration: create equipment table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS equipment (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            equipment_number VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            category ENUM('production','facility','utility','safety','transport','it','other') NOT NULL DEFAULT 'other',
            location VARCHAR(255) NULL,
            building VARCHAR(100) NULL,
            floor VARCHAR(50) NULL,
            department_id BIGINT UNSIGNED NULL,
            parent_id BIGINT UNSIGNED NULL,
            manufacturer VARCHAR(255) NULL,
            model VARCHAR(255) NULL,
            serial_number VARCHAR(255) NULL,
            year_of_manufacture YEAR NULL,
            installed_date DATE NULL,
            warranty_until DATE NULL,
            status ENUM('operational','maintenance','out_of_service','decommissioned') NOT NULL DEFAULT 'operational',
            criticality ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
            notes TEXT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY idx_equipment_number (equipment_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
