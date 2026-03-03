<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hs_risk_assessments (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            title           VARCHAR(255) NOT NULL,
            description     TEXT NULL,
            location        VARCHAR(255) NULL,
            department_id   BIGINT UNSIGNED NULL,
            risk_type       ENUM('physical','chemical','biological','ergonomic','psychosocial','electrical','fire','fall','other') NOT NULL DEFAULT 'other',
            probability     TINYINT NOT NULL DEFAULT 1,
            consequence     TINYINT NOT NULL DEFAULT 1,
            risk_level      TINYINT GENERATED ALWAYS AS (probability * consequence) STORED,
            status          ENUM('draft','active','under_review','closed','archived') NOT NULL DEFAULT 'draft',
            assigned_to     BIGINT UNSIGNED NULL,
            reviewed_by     BIGINT UNSIGNED NULL,
            reviewed_at     TIMESTAMP NULL,
            valid_until     DATE NULL,
            mitigation      TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_hs_ra_department (department_id),
            INDEX idx_hs_ra_status (status),
            INDEX idx_hs_ra_assigned (assigned_to),
            INDEX idx_hs_ra_risk_level (risk_level),
            CONSTRAINT fk_hs_ra_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_hs_ra_assigned FOREIGN KEY (assigned_to) REFERENCES users(id),
            CONSTRAINT fk_hs_ra_reviewed FOREIGN KEY (reviewed_by) REFERENCES users(id),
            CONSTRAINT fk_hs_ra_created FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
