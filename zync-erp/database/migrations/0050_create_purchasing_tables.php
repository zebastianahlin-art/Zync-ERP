<?php

declare(strict_types=1);

/**
 * Migration: create purchasing tables.
 *
 * Creates: purchase_requisitions, purchase_requisition_lines,
 *          purchase_orders, purchase_order_lines, purchase_agreements.
 *
 * Required by PurchaseController, InventoryController (receiving), and
 * related repository classes.
 */
return function (\PDO $pdo): void
{
    // ─── Purchase Requisitions ────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_requisitions (
            id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by          BIGINT UNSIGNED NULL,
            is_deleted          TINYINT(1) NOT NULL DEFAULT 0,
            requisition_number  VARCHAR(50) NOT NULL,
            title               VARCHAR(255) NOT NULL,
            description         TEXT NULL,
            priority            ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
            status              ENUM('draft','pending_approval','approved','rejected','cancelled','completed')
                                NOT NULL DEFAULT 'draft',
            requested_by        BIGINT UNSIGNED NULL,
            department_id       BIGINT UNSIGNED NULL,
            approved_by         BIGINT UNSIGNED NULL,
            approved_at         DATETIME NULL,
            rejected_reason     TEXT NULL,
            needed_by           DATE NULL,
            total_amount        DECIMAL(14,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uq_pr_number (requisition_number),
            INDEX idx_pr_status (status),
            INDEX idx_pr_requested_by (requested_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Purchase Requisition Lines ───────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_requisition_lines (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            requisition_id    BIGINT UNSIGNED NOT NULL,
            article_id        BIGINT UNSIGNED NULL,
            description       VARCHAR(255) NOT NULL,
            quantity          DECIMAL(12,2) NOT NULL DEFAULT 1,
            unit              VARCHAR(50) NOT NULL DEFAULT 'st',
            estimated_price   DECIMAL(12,2) NOT NULL DEFAULT 0,
            supplier_id       BIGINT UNSIGNED NULL,
            notes             TEXT NULL,
            account_id        BIGINT UNSIGNED NULL,
            cost_center_id    BIGINT UNSIGNED NULL,
            PRIMARY KEY (id),
            INDEX idx_prl_requisition (requisition_id),
            CONSTRAINT fk_prl_requisition FOREIGN KEY (requisition_id) REFERENCES purchase_requisitions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Purchase Orders ──────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_orders (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by        BIGINT UNSIGNED NULL,
            is_deleted        TINYINT(1) NOT NULL DEFAULT 0,
            order_number      VARCHAR(50) NOT NULL,
            requisition_id    BIGINT UNSIGNED NULL,
            supplier_id       BIGINT UNSIGNED NOT NULL,
            buyer_id          BIGINT UNSIGNED NULL,
            status            ENUM('draft','sent','confirmed','partially_received','received','cancelled')
                              NOT NULL DEFAULT 'draft',
            reference         VARCHAR(100) NULL,
            delivery_address  TEXT NULL,
            delivery_date     DATE NULL,
            payment_terms     VARCHAR(100) NULL,
            currency          CHAR(3) NOT NULL DEFAULT 'SEK',
            notes             TEXT NULL,
            subtotal          DECIMAL(14,2) NOT NULL DEFAULT 0,
            vat_amount        DECIMAL(14,2) NOT NULL DEFAULT 0,
            total_amount      DECIMAL(14,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uq_po_number (order_number),
            INDEX idx_po_supplier (supplier_id),
            INDEX idx_po_status (status),
            INDEX idx_po_requisition (requisition_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Purchase Order Lines ─────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_order_lines (
            id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id            BIGINT UNSIGNED NOT NULL,
            article_id          BIGINT UNSIGNED NULL,
            description         VARCHAR(255) NOT NULL,
            quantity            DECIMAL(12,2) NOT NULL DEFAULT 1,
            unit                VARCHAR(50) NOT NULL DEFAULT 'st',
            unit_price          DECIMAL(12,2) NOT NULL DEFAULT 0,
            vat_rate            DECIMAL(5,2) NOT NULL DEFAULT 0,
            line_total          DECIMAL(14,2) NOT NULL DEFAULT 0,
            received_quantity   DECIMAL(12,2) NOT NULL DEFAULT 0,
            notes               TEXT NULL,
            account_id          BIGINT UNSIGNED NULL,
            cost_center_id      BIGINT UNSIGNED NULL,
            is_deleted          TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX idx_pol_order (order_id),
            INDEX idx_pol_article (article_id),
            CONSTRAINT fk_pol_order FOREIGN KEY (order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Purchase Agreements ──────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_agreements (
            id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by        BIGINT UNSIGNED NULL,
            is_deleted        TINYINT(1) NOT NULL DEFAULT 0,
            agreement_number  VARCHAR(50) NOT NULL,
            title             VARCHAR(255) NOT NULL,
            supplier_id       BIGINT UNSIGNED NOT NULL,
            agreement_type    ENUM('standard','framework','spot') NOT NULL DEFAULT 'standard',
            status            ENUM('draft','active','expired','terminated','cancelled') NOT NULL DEFAULT 'draft',
            start_date        DATE NOT NULL,
            end_date          DATE NULL,
            value             DECIMAL(14,2) NULL,
            currency          CHAR(3) NOT NULL DEFAULT 'SEK',
            responsible_id    BIGINT UNSIGNED NULL,
            description       TEXT NULL,
            terms             TEXT NULL,
            file_path         VARCHAR(500) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_pa_number (agreement_number),
            INDEX idx_pa_supplier (supplier_id),
            INDEX idx_pa_status (status),
            INDEX idx_pa_end_date (end_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
