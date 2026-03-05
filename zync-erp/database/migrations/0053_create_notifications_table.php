<?php

declare(strict_types=1);

/**
 * Migration 0053: Fas C — Notifications
 *
 * Skapar tabellen notifications för in-app-notifikationer.
 */
return function (\PDO $pdo): void {

    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        user_id      BIGINT UNSIGNED NOT NULL,
        type         VARCHAR(100) NOT NULL,
        title        VARCHAR(255) NOT NULL,
        message      TEXT NULL,
        link         VARCHAR(500) NULL,
        is_read      TINYINT(1) NOT NULL DEFAULT 0,
        read_at      DATETIME NULL,
        PRIMARY KEY (id),
        INDEX idx_notif_user_read (user_id, is_read),
        INDEX idx_notif_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

};
