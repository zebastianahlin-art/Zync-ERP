<?php

declare(strict_types=1);

/**
 * Migration: create inspectable equipment table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inspectable_equipment (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT UNSIGNED NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            equipment_id BIGINT UNSIGNED NULL,
            name VARCHAR(255) NOT NULL,
            type ENUM('crane','hoist','lifting_gear','fall_protection','pressure_vessel','forklift','elevator','fire_equipment','electrical','other') NOT NULL DEFAULT 'other',
            serial_number VARCHAR(255) NULL,
            manufacturer VARCHAR(255) NULL,
            location VARCHAR(255) NULL,
            certification_body VARCHAR(255) NULL,
            inspection_interval_months INT NOT NULL DEFAULT 12,
            last_inspection_date DATE NULL,
            next_inspection_date DATE NULL,
            last_inspection_result ENUM('passed','failed','conditional') NULL,
            certificate_number VARCHAR(100) NULL,
            max_load_kg DECIMAL(10,2) NULL,
            notes TEXT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inspectable_equipment_inspections (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            inspectable_id BIGINT UNSIGNED NOT NULL,
            inspection_date DATE NOT NULL,
            inspector VARCHAR(255) NULL,
            inspection_company VARCHAR(255) NULL,
            result ENUM('passed','failed','conditional') NOT NULL DEFAULT 'passed',
            certificate_number VARCHAR(100) NULL,
            valid_until DATE NULL,
            notes TEXT NULL,
            recorded_by BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            CONSTRAINT fk_inspection_inspectable FOREIGN KEY (inspectable_id) REFERENCES inspectable_equipment(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
