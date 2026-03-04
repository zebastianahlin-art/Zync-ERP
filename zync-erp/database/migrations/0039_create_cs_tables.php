<?php

declare(strict_types=1);

/**
 * Migration: create customer service tables (cs_tickets + cs_ticket_comments)
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cs_tickets (
            id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            ticket_number    VARCHAR(50)  NOT NULL,
            title            VARCHAR(255) NOT NULL,
            description      TEXT NULL,
            customer_id      BIGINT UNSIGNED NULL,
            contact_person   VARCHAR(100) NULL,
            contact_email    VARCHAR(255) NULL,
            contact_phone    VARCHAR(50)  NULL,
            category         ENUM('complaint','inquiry','return','warranty','support','other') NOT NULL DEFAULT 'inquiry',
            priority         ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
            status           ENUM('open','in_progress','waiting_customer','waiting_internal','resolved','closed') NOT NULL DEFAULT 'open',
            assigned_to      BIGINT UNSIGNED NULL,
            resolution       TEXT NULL,
            resolved_at      DATETIME NULL,
            closed_at        DATETIME NULL,
            created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by       BIGINT UNSIGNED NULL,
            is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uq_cs_ticket_number (ticket_number),
            INDEX idx_cs_status (status),
            INDEX idx_cs_customer (customer_id),
            INDEX idx_cs_assigned (assigned_to),
            CONSTRAINT fk_cs_tickets_customer    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
            CONSTRAINT fk_cs_tickets_assigned    FOREIGN KEY (assigned_to) REFERENCES users(id)    ON DELETE SET NULL,
            CONSTRAINT fk_cs_tickets_created_by  FOREIGN KEY (created_by)  REFERENCES users(id)    ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cs_ticket_comments (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            ticket_id   BIGINT UNSIGNED NOT NULL,
            user_id     BIGINT UNSIGNED NULL,
            comment     TEXT NOT NULL,
            is_internal TINYINT(1) NOT NULL DEFAULT 0,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX idx_ctc_ticket (ticket_id),
            CONSTRAINT fk_ctc_ticket  FOREIGN KEY (ticket_id) REFERENCES cs_tickets(id) ON DELETE CASCADE,
            CONSTRAINT fk_ctc_user    FOREIGN KEY (user_id)   REFERENCES users(id)      ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
