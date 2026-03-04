<?php

declare(strict_types=1);

/**
 * Migration: create machine_spare_parts table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS machine_spare_parts (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by  BIGINT UNSIGNED NULL,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            machine_id  BIGINT UNSIGNED NOT NULL,
            article_id  BIGINT UNSIGNED NOT NULL,
            quantity    DECIMAL(10,2) NOT NULL DEFAULT 1,
            notes       TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_machine_spare_parts_machine (machine_id),
            INDEX idx_machine_spare_parts_article (article_id),
            CONSTRAINT fk_machine_spare_parts_machine FOREIGN KEY (machine_id) REFERENCES machines(id),
            CONSTRAINT fk_machine_spare_parts_article FOREIGN KEY (article_id) REFERENCES articles(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
