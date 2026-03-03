<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hs_risk_reports (
            id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by          BIGINT UNSIGNED NULL,
            is_deleted          TINYINT(1) NOT NULL DEFAULT 0,
            title               VARCHAR(255) NOT NULL,
            description         TEXT NOT NULL,
            location            VARCHAR(255) NULL,
            department_id       BIGINT UNSIGNED NULL,
            category            ENUM('risk','hazard','near_miss','incident','improvement') NOT NULL DEFAULT 'risk',
            severity            ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
            status              ENUM('reported','acknowledged','investigating','action_taken','closed') NOT NULL DEFAULT 'reported',
            assigned_to         BIGINT UNSIGNED NULL,
            risk_assessment_id  BIGINT UNSIGNED NULL,
            action_taken        TEXT NULL,
            closed_at           TIMESTAMP NULL,
            closed_by           BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            INDEX idx_hs_rr_status (status),
            INDEX idx_hs_rr_category (category),
            INDEX idx_hs_rr_severity (severity),
            INDEX idx_hs_rr_department (department_id),
            INDEX idx_hs_rr_assigned (assigned_to),
            INDEX idx_hs_rr_assessment (risk_assessment_id),
            CONSTRAINT fk_hs_rr_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_hs_rr_assigned FOREIGN KEY (assigned_to) REFERENCES users(id),
            CONSTRAINT fk_hs_rr_assessment FOREIGN KEY (risk_assessment_id) REFERENCES hs_risk_assessments(id),
            CONSTRAINT fk_hs_rr_created FOREIGN KEY (created_by) REFERENCES users(id),
            CONSTRAINT fk_hs_rr_closed FOREIGN KEY (closed_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
