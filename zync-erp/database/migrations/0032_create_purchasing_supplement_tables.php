<?php

declare(strict_types=1);

/**
 * Migration: create supplier_audits and purchase_agreement_templates tables
 */
return function (\PDO $pdo): void
{
    // ─── Supplier Audits ──────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS supplier_audits (
            id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by           BIGINT UNSIGNED NULL,
            is_deleted           TINYINT(1) NOT NULL DEFAULT 0,
            supplier_id          BIGINT UNSIGNED NOT NULL,
            audit_date           DATE NOT NULL,
            auditor_id           BIGINT UNSIGNED NULL,
            status               ENUM('planned','in_progress','completed') NOT NULL DEFAULT 'planned',
            delivery_score       TINYINT NULL,
            quality_score        TINYINT NULL,
            price_score          TINYINT NULL,
            communication_score  TINYINT NULL,
            overall_score        DECIMAL(3,1) NULL,
            notes                TEXT NULL,
            next_audit_date      DATE NULL,
            PRIMARY KEY (id),
            INDEX idx_supplier_id (supplier_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Purchase Agreement Templates ─────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_agreement_templates (
            id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by              BIGINT UNSIGNED NULL,
            is_deleted              TINYINT(1) NOT NULL DEFAULT 0,
            name                    VARCHAR(255) NOT NULL,
            description             TEXT NULL,
            supplier_id             BIGINT UNSIGNED NULL,
            default_terms           TEXT NULL,
            default_payment_terms   VARCHAR(100) NULL,
            default_delivery_terms  VARCHAR(100) NULL,
            is_active               TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            INDEX idx_supplier_id (supplier_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
