<?php

declare(strict_types=1);

/**
 * Migration: create preventive_maintenance_schedules and preventive_maintenance_logs tables
 *            + ALTER fault_reports and work_orders for DEL 4
 */
return function (\PDO $pdo): void
{
    // ─── Preventive Maintenance Schedules ────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS preventive_maintenance_schedules (
            id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by       BIGINT UNSIGNED NULL,
            is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
            title            VARCHAR(255) NOT NULL,
            description      TEXT NULL,
            equipment_id     BIGINT UNSIGNED NULL,
            machine_id       BIGINT UNSIGNED NULL,
            interval_type    ENUM('daily','weekly','monthly','yearly','hours') NOT NULL DEFAULT 'monthly',
            interval_value   INT UNSIGNED NOT NULL DEFAULT 1,
            last_performed_at DATETIME NULL,
            next_due_at      DATETIME NULL,
            priority         ENUM('low','normal','high','critical') NOT NULL DEFAULT 'normal',
            assigned_to      BIGINT UNSIGNED NULL,
            checklist        JSON NULL,
            status           ENUM('active','paused','completed') NOT NULL DEFAULT 'active',
            PRIMARY KEY (id),
            INDEX idx_pms_equipment (equipment_id),
            INDEX idx_pms_machine (machine_id),
            INDEX idx_pms_next_due (next_due_at),
            INDEX idx_pms_status (status),
            CONSTRAINT fk_pms_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL,
            CONSTRAINT fk_pms_machine FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE SET NULL,
            CONSTRAINT fk_pms_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
            CONSTRAINT fk_pms_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Preventive Maintenance Logs ─────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS preventive_maintenance_logs (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by    BIGINT UNSIGNED NULL,
            is_deleted    TINYINT(1) NOT NULL DEFAULT 0,
            schedule_id   BIGINT UNSIGNED NOT NULL,
            performed_at  DATETIME NOT NULL,
            performed_by  BIGINT UNSIGNED NULL,
            notes         TEXT NULL,
            work_order_id BIGINT UNSIGNED NULL COMMENT 'Kopplad arbetsorder om genererad',
            PRIMARY KEY (id),
            INDEX idx_pml_schedule (schedule_id),
            INDEX idx_pml_performed_at (performed_at),
            CONSTRAINT fk_pml_schedule FOREIGN KEY (schedule_id) REFERENCES preventive_maintenance_schedules(id) ON DELETE CASCADE,
            CONSTRAINT fk_pml_performed_by FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
            CONSTRAINT fk_pml_work_order FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── DEL 4: ALTER fault_reports ──────────────────────────────────────
    // Add urgency, estimated_downtime, photos if columns don't exist
    $cols = $pdo->query("SHOW COLUMNS FROM fault_reports")->fetchAll(\PDO::FETCH_COLUMN);
    if (!in_array('urgency', $cols)) {
        $pdo->exec("ALTER TABLE fault_reports ADD COLUMN urgency ENUM('normal','high','critical') NOT NULL DEFAULT 'normal' AFTER priority");
    }
    if (!in_array('estimated_downtime', $cols)) {
        $pdo->exec("ALTER TABLE fault_reports ADD COLUMN estimated_downtime SMALLINT UNSIGNED NULL COMMENT 'Estimated downtime in minutes' AFTER urgency");
    }
    if (!in_array('photos', $cols)) {
        $pdo->exec("ALTER TABLE fault_reports ADD COLUMN photos JSON NULL COMMENT 'Array of uploaded photo filenames' AFTER estimated_downtime");
    }

    // ─── DEL 4: ALTER work_orders ────────────────────────────────────────
    $wcols = $pdo->query("SHOW COLUMNS FROM work_orders")->fetchAll(\PDO::FETCH_COLUMN);
    if (!in_array('total_labor_cost', $wcols)) {
        $pdo->exec("ALTER TABLE work_orders ADD COLUMN total_labor_cost DECIMAL(10,2) NULL DEFAULT 0 AFTER actual_hours");
    }
    if (!in_array('total_parts_cost', $wcols)) {
        $pdo->exec("ALTER TABLE work_orders ADD COLUMN total_parts_cost DECIMAL(10,2) NULL DEFAULT 0 AFTER total_labor_cost");
    }
    if (!in_array('total_cost', $wcols)) {
        $pdo->exec("ALTER TABLE work_orders ADD COLUMN total_cost DECIMAL(10,2) NULL DEFAULT 0 AFTER total_parts_cost");
    }
    if (!in_array('waiting_reason', $wcols)) {
        $pdo->exec("ALTER TABLE work_orders ADD COLUMN waiting_reason VARCHAR(255) NULL AFTER total_cost");
    }
};
