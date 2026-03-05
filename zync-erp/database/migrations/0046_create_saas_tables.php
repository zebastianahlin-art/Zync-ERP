<?php

use App\Core\Database;

$pdo = Database::pdo();

$pdo->exec("CREATE TABLE IF NOT EXISTS saas_tenants (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
    company_name    VARCHAR(200) NOT NULL,
    org_number      VARCHAR(30) NULL COMMENT 'Organisationsnummer',
    contact_name    VARCHAR(150) NULL,
    contact_email   VARCHAR(255) NOT NULL,
    contact_phone   VARCHAR(50) NULL,
    address         TEXT NULL,
    subdomain       VARCHAR(60) NULL COMMENT 'tenant.zync-erp.se',
    db_name         VARCHAR(100) NULL COMMENT 'Tenant database name',
    status          ENUM('trial','active','suspended','cancelled') NOT NULL DEFAULT 'trial',
    trial_ends_at   DATE NULL,
    plan            ENUM('starter','professional','enterprise') NOT NULL DEFAULT 'starter',
    max_users       INT NOT NULL DEFAULT 10,
    logo_path       VARCHAR(500) NULL,
    notes           TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tenant_email (contact_email),
    INDEX idx_tenant_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS saas_tenant_modules (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tenant_id   BIGINT UNSIGNED NOT NULL,
    module_slug VARCHAR(60) NOT NULL,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    activated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tenant_module (tenant_id, module_slug),
    INDEX idx_stm_tenant (tenant_id),
    CONSTRAINT fk_stm_tenant FOREIGN KEY (tenant_id) REFERENCES saas_tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS saas_invoices (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
    tenant_id       BIGINT UNSIGNED NOT NULL,
    invoice_number  VARCHAR(50) NOT NULL,
    period_start    DATE NOT NULL,
    period_end      DATE NOT NULL,
    amount          DECIMAL(12,2) NOT NULL DEFAULT 0,
    vat             DECIMAL(12,2) NOT NULL DEFAULT 0,
    total           DECIMAL(12,2) NOT NULL DEFAULT 0,
    status          ENUM('draft','sent','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
    due_date        DATE NOT NULL,
    paid_at         DATE NULL,
    notes           TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_saas_invoice_number (invoice_number),
    INDEX idx_saas_inv_tenant (tenant_id),
    INDEX idx_saas_inv_status (status),
    CONSTRAINT fk_saas_inv_tenant FOREIGN KEY (tenant_id) REFERENCES saas_tenants(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS saas_support_tickets (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
    tenant_id       BIGINT UNSIGNED NOT NULL,
    ticket_number   VARCHAR(50) NOT NULL,
    subject         VARCHAR(255) NOT NULL,
    description     TEXT NOT NULL,
    priority        ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
    status          ENUM('open','in_progress','waiting','resolved','closed') NOT NULL DEFAULT 'open',
    category        ENUM('bug','feature_request','question','billing','other') NOT NULL DEFAULT 'question',
    assigned_to     BIGINT UNSIGNED NULL,
    resolved_at     DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_saas_ticket_number (ticket_number),
    INDEX idx_saas_ticket_tenant (tenant_id),
    INDEX idx_saas_ticket_status (status),
    CONSTRAINT fk_saas_ticket_tenant FOREIGN KEY (tenant_id) REFERENCES saas_tenants(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS saas_support_comments (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    ticket_id   BIGINT UNSIGNED NOT NULL,
    user_id     BIGINT UNSIGNED NULL,
    comment     TEXT NOT NULL,
    is_internal TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_ssc_ticket (ticket_id),
    CONSTRAINT fk_ssc_ticket FOREIGN KEY (ticket_id) REFERENCES saas_support_tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
