<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS equipment_inspections (
            id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by           BIGINT UNSIGNED NULL,
            is_deleted           TINYINT(1) NOT NULL DEFAULT 0,
            inspection_number    VARCHAR(20) NOT NULL,
            equipment_id         BIGINT UNSIGNED NULL,
            machine_id           BIGINT UNSIGNED NULL,
            inspection_type      ENUM('safety','regulatory','routine','preventive') NOT NULL DEFAULT 'routine',
            scheduled_date       DATE NOT NULL,
            completed_date       DATE NULL,
            status               ENUM('scheduled','in_progress','completed','overdue','cancelled') NOT NULL DEFAULT 'scheduled',
            inspector_id         BIGINT UNSIGNED NULL,
            result               ENUM('pass','fail','conditional','na') NULL,
            notes                TEXT NULL,
            next_inspection_date DATE NULL,
            department_id        BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_inspection_number (inspection_number),
            INDEX idx_inspections_status (status),
            INDEX idx_inspections_scheduled_date (scheduled_date),
            INDEX idx_inspections_equipment (equipment_id),
            CONSTRAINT fk_inspections_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id),
            CONSTRAINT fk_inspections_machine FOREIGN KEY (machine_id) REFERENCES machines(id),
            CONSTRAINT fk_inspections_inspector FOREIGN KEY (inspector_id) REFERENCES users(id),
            CONSTRAINT fk_inspections_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_inspections_created_by FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
