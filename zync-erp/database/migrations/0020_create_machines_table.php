<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS machines (
            id                        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by                BIGINT UNSIGNED NULL,
            is_deleted                TINYINT(1) NOT NULL DEFAULT 0,
            machine_number            VARCHAR(20) NOT NULL,
            name                      VARCHAR(255) NOT NULL,
            description               TEXT NULL,
            equipment_id              BIGINT UNSIGNED NULL,
            department_id             BIGINT UNSIGNED NULL,
            location                  VARCHAR(255) NULL,
            manufacturer              VARCHAR(255) NULL,
            model                     VARCHAR(255) NULL,
            serial_number             VARCHAR(100) NULL,
            year_of_manufacture       SMALLINT UNSIGNED NULL,
            power_kw                  DECIMAL(8,2) NULL,
            status                    ENUM('running','idle','maintenance','breakdown','decommissioned') NOT NULL DEFAULT 'running',
            criticality               ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
            maintenance_interval_days INT NULL,
            last_maintenance_date     DATE NULL,
            next_maintenance_date     DATE NULL,
            notes                     TEXT NULL,
            is_active                 TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY uq_machine_number (machine_number),
            INDEX idx_machines_status (status),
            INDEX idx_machines_department (department_id),
            INDEX idx_machines_next_maintenance (next_maintenance_date),
            CONSTRAINT fk_machines_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id),
            CONSTRAINT fk_machines_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_machines_created_by FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
