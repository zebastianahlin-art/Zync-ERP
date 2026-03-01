<?php

declare(strict_types=1);

/**
 * Migration: create audit_log table (DOC-02 §2.7)
 *
 * audit_log is immutable: no updated_at, no is_deleted, no created_by.
 */
function up(\PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS audit_log (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id     BIGINT UNSIGNED NULL,
            module      VARCHAR(60) NOT NULL,
            action      VARCHAR(60) NOT NULL,
            table_name  VARCHAR(100) NOT NULL,
            record_id   BIGINT UNSIGNED NULL,
            old_values  JSON NULL,
            new_values  JSON NULL,
            ip_address  VARCHAR(45) NULL,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX idx_audit_user (user_id),
            INDEX idx_audit_module (module),
            INDEX idx_audit_table (table_name, record_id),
            INDEX idx_audit_created (created_at),
            CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
