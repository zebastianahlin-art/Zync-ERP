<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hs_emergency_resources (
            id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by          BIGINT UNSIGNED NULL,
            is_deleted          TINYINT(1) NOT NULL DEFAULT 0,
            name                VARCHAR(255) NOT NULL,
            resource_type       ENUM('fire_extinguisher','first_aid_kit','aed','eye_wash','emergency_shower','evacuation_chair','fire_blanket','spill_kit','other') NOT NULL DEFAULT 'other',
            location            VARCHAR(255) NOT NULL,
            location_details    TEXT NULL,
            department_id       BIGINT UNSIGNED NULL,
            serial_number       VARCHAR(100) NULL,
            quantity            INT NOT NULL DEFAULT 1,
            status              ENUM('ok','needs_inspection','out_of_service','missing') NOT NULL DEFAULT 'ok',
            last_inspection     DATE NULL,
            next_inspection     DATE NULL,
            inspection_interval INT NULL COMMENT 'Interval in days',
            notes               TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_hs_er_type (resource_type),
            INDEX idx_hs_er_status (status),
            INDEX idx_hs_er_department (department_id),
            INDEX idx_hs_er_next_inspection (next_inspection),
            CONSTRAINT fk_hs_er_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_hs_er_created FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hs_resource_inspections (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            resource_id     BIGINT UNSIGNED NOT NULL,
            inspected_by    BIGINT UNSIGNED NULL,
            inspected_at    DATE NOT NULL,
            status          ENUM('ok','needs_attention','failed') NOT NULL DEFAULT 'ok',
            notes           TEXT NULL,
            next_inspection DATE NULL,
            PRIMARY KEY (id),
            INDEX idx_hs_ri_resource (resource_id),
            INDEX idx_hs_ri_date (inspected_at),
            CONSTRAINT fk_hs_ri_resource FOREIGN KEY (resource_id) REFERENCES hs_emergency_resources(id) ON DELETE CASCADE,
            CONSTRAINT fk_hs_ri_inspector FOREIGN KEY (inspected_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
