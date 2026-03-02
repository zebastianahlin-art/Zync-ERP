<?php

declare(strict_types=1);

/**
 * Migration: create user_sessions table (DOC-02 §2.6)
 */
return function (\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_sessions (
            id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by   BIGINT UNSIGNED NULL,
            is_deleted   TINYINT(1) NOT NULL DEFAULT 0,
            user_id      BIGINT UNSIGNED NOT NULL,
            token_hash   VARCHAR(255) NOT NULL,
            ip_address   VARCHAR(45) NULL,
            user_agent   TEXT NULL,
            expires_at   TIMESTAMP NOT NULL,
            last_seen_at TIMESTAMP NULL,
            PRIMARY KEY (id),
            INDEX idx_sessions_user (user_id),
            INDEX idx_sessions_token (token_hash),
            INDEX idx_sessions_expires (expires_at),
            CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
