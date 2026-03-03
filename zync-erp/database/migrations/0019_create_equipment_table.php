<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS equipment (
            id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by           BIGINT UNSIGNED NULL,
            is_deleted           TINYINT(1) NOT NULL DEFAULT 0,
            equipment_number     VARCHAR(20) NOT NULL,
            name                 VARCHAR(255) NOT NULL,
            description          TEXT NULL,
            category             ENUM('production','facility','utility','safety','transport','it','other') NOT NULL DEFAULT 'other',
            location             VARCHAR(255) NULL,
            building             VARCHAR(100) NULL,
            floor                VARCHAR(50) NULL,
            department_id        BIGINT UNSIGNED NULL,
            parent_id            BIGINT UNSIGNED NULL,
            manufacturer         VARCHAR(255) NULL,
            model                VARCHAR(255) NULL,
            serial_number        VARCHAR(100) NULL,
            year_of_manufacture  SMALLINT UNSIGNED NULL,
            installed_date       DATE NULL,
            warranty_until       DATE NULL,
            status               ENUM('operational','maintenance','out_of_service','decommissioned') NOT NULL DEFAULT 'operational',
            criticality          ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
            notes                TEXT NULL,
            is_active            TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY uq_equipment_number (equipment_number),
            INDEX idx_equipment_status (status),
            INDEX idx_equipment_category (category),
            INDEX idx_equipment_department (department_id),
            CONSTRAINT fk_equipment_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_equipment_parent FOREIGN KEY (parent_id) REFERENCES equipment(id),
            CONSTRAINT fk_equipment_created_by FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
