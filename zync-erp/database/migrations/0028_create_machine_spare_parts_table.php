<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS machine_spare_parts (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            machine_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NOT NULL,
            quantity_recommended DECIMAL(10,2) NOT NULL DEFAULT 1,
            notes TEXT NULL,
            is_critical TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
