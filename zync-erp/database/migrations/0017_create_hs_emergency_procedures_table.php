<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hs_emergency_procedures (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            title           VARCHAR(255) NOT NULL,
            category        ENUM('fire','evacuation','first_aid','chemical_spill','electrical','natural_disaster','bomb_threat','medical','other') NOT NULL DEFAULT 'other',
            description     TEXT NULL,
            steps           TEXT NOT NULL,
            responsible     VARCHAR(255) NULL,
            location        VARCHAR(255) NULL,
            last_reviewed   DATE NULL,
            review_interval INT NULL COMMENT 'Review interval in days',
            is_active       TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            INDEX idx_hs_ep_category (category),
            CONSTRAINT fk_hs_ep_created FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
