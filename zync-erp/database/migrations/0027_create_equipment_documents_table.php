<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS equipment_documents (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            equipment_id BIGINT UNSIGNED NULL,
            machine_id BIGINT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_type VARCHAR(100) NULL,
            file_size BIGINT NULL,
            uploaded_by BIGINT UNSIGNED NULL,
            uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
