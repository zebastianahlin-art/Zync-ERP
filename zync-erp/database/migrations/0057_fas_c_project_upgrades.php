<?php

declare(strict_types=1);

use App\Core\Database;

/**
 * Migration: Fas C – Projektmodulen professionell nivå
 *
 * C1: project_type kolumn på projects
 * C2: project_stakeholders-tabell
 * C3: project_purchase_orders-tabell
 * C5: start_date/end_date på project_tasks (om ej finns)
 * C6: project_costs-tabell + planned_budget/actual_cost på projects
 */
$pdo = Database::pdo();

// C1 – project_type
$stmt = $pdo->prepare("SHOW COLUMNS FROM projects LIKE 'project_type'");
$stmt->execute();
if (empty($stmt->fetchAll())) {
    $pdo->exec("ALTER TABLE projects ADD COLUMN project_type ENUM('internal','external') NOT NULL DEFAULT 'internal' AFTER status");
}

// C6 – planned_budget on projects
$stmt = $pdo->prepare("SHOW COLUMNS FROM projects LIKE 'planned_budget'");
$stmt->execute();
if (empty($stmt->fetchAll())) {
    $pdo->exec("ALTER TABLE projects ADD COLUMN planned_budget DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER budget");
}

// C6 – actual_cost on projects
$stmt = $pdo->prepare("SHOW COLUMNS FROM projects LIKE 'actual_cost'");
$stmt->execute();
if (empty($stmt->fetchAll())) {
    $pdo->exec("ALTER TABLE projects ADD COLUMN actual_cost DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER planned_budget");
}

// C5 – start_date on project_tasks
$stmt = $pdo->prepare("SHOW COLUMNS FROM project_tasks LIKE 'start_date'");
$stmt->execute();
if (empty($stmt->fetchAll())) {
    $pdo->exec("ALTER TABLE project_tasks ADD COLUMN start_date DATE NULL AFTER due_date");
}

// C2 – project_stakeholders
$pdo->exec("
    CREATE TABLE IF NOT EXISTS project_stakeholders (
        id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
        project_id  BIGINT UNSIGNED NOT NULL,
        name        VARCHAR(150) NOT NULL,
        role        VARCHAR(100) NOT NULL DEFAULT 'Teammedlem',
        email       VARCHAR(200) NULL,
        phone       VARCHAR(50) NULL,
        notes       TEXT NULL,
        PRIMARY KEY (id),
        INDEX idx_project_stakeholders_project (project_id),
        CONSTRAINT fk_project_stakeholders_project FOREIGN KEY (project_id) REFERENCES projects(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// C3 – project_purchase_orders
$pdo->exec("
    CREATE TABLE IF NOT EXISTS project_purchase_orders (
        id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        project_id        BIGINT UNSIGNED NOT NULL,
        purchase_order_id BIGINT UNSIGNED NOT NULL,
        notes             TEXT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uq_project_po (project_id, purchase_order_id),
        INDEX idx_project_po_project (project_id),
        INDEX idx_project_po_po (purchase_order_id),
        CONSTRAINT fk_project_po_project FOREIGN KEY (project_id) REFERENCES projects(id),
        CONSTRAINT fk_project_po_po FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// C6 – project_costs
$pdo->exec("
    CREATE TABLE IF NOT EXISTS project_costs (
        id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
        project_id  BIGINT UNSIGNED NOT NULL,
        description VARCHAR(255) NOT NULL,
        amount      DECIMAL(12,2) NOT NULL DEFAULT 0,
        cost_date   DATE NULL,
        category    VARCHAR(100) NULL,
        created_by  BIGINT UNSIGNED NULL,
        PRIMARY KEY (id),
        INDEX idx_project_costs_project (project_id),
        CONSTRAINT fk_project_costs_project FOREIGN KEY (project_id) REFERENCES projects(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
