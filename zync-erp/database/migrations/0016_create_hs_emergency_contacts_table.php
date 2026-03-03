<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hs_emergency_contacts (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by      BIGINT UNSIGNED NULL,
            is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
            name            VARCHAR(255) NOT NULL,
            role            VARCHAR(255) NULL,
            phone           VARCHAR(50) NOT NULL,
            phone_alt       VARCHAR(50) NULL,
            email           VARCHAR(255) NULL,
            department_id   BIGINT UNSIGNED NULL,
            is_external     TINYINT(1) NOT NULL DEFAULT 0,
            organization    VARCHAR(255) NULL,
            notes           TEXT NULL,
            sort_order      INT NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_hs_ec_department (department_id),
            CONSTRAINT fk_hs_ec_department FOREIGN KEY (department_id) REFERENCES departments(id),
            CONSTRAINT fk_hs_ec_created FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
