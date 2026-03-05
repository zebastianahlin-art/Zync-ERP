<?php

declare(strict_types=1);

/**
 * Migration 0052: Fas C — Job Queue
 *
 * Skapar tabellen job_queue för asynkrona bakgrundsjobb.
 */
return function (\PDO $pdo): void {

    $pdo->exec("CREATE TABLE IF NOT EXISTS job_queue (
        id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        queue        VARCHAR(50) NOT NULL DEFAULT 'default',
        payload      JSON NOT NULL,
        job_class    VARCHAR(255) NOT NULL,
        status       ENUM('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
        attempts     TINYINT UNSIGNED NOT NULL DEFAULT 0,
        max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 3,
        error_msg    TEXT NULL,
        started_at   DATETIME NULL,
        completed_at DATETIME NULL,
        PRIMARY KEY (id),
        INDEX idx_jq_status_queue (status, queue),
        INDEX idx_jq_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

};
