<?php
use App\Core\Database;

$pdo = Database::pdo();

$pdo->exec("CREATE TABLE IF NOT EXISTS dashboard_widgets (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
    slug             VARCHAR(60) NOT NULL,
    name             VARCHAR(150) NOT NULL,
    description      TEXT NULL,
    category         ENUM('kpi','shortcut','chart','list','mandatory') NOT NULL DEFAULT 'kpi',
    module           VARCHAR(60) NULL,
    min_role_level   TINYINT NOT NULL DEFAULT 1,
    is_mandatory     TINYINT(1) NOT NULL DEFAULT 0,
    is_active        TINYINT(1) NOT NULL DEFAULT 1,
    default_width    TINYINT NOT NULL DEFAULT 1,
    sort_order       INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_widget_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS user_dashboard_widgets (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     BIGINT UNSIGNED NOT NULL,
    widget_id   BIGINT UNSIGNED NOT NULL,
    sort_order  INT NOT NULL DEFAULT 0,
    width       TINYINT NOT NULL DEFAULT 1,
    is_visible  TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_widget (user_id, widget_id),
    INDEX idx_udw_user (user_id),
    CONSTRAINT fk_udw_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_udw_widget FOREIGN KEY (widget_id) REFERENCES dashboard_widgets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed default widgets - use INSERT IGNORE to be idempotent
$pdo->exec("INSERT IGNORE INTO dashboard_widgets (slug, name, description, category, module, min_role_level, is_mandatory, default_width, sort_order) VALUES
('risk_report', 'Rapportera risk/fara', 'Snabbknapp för att rapportera en risk eller fara', 'mandatory', 'safety', 1, 1, 1, 1),
('crisis_plan', 'Krishanteringsplan', 'Visa aktiv krishanteringsplan', 'mandatory', 'safety', 1, 1, 2, 2),
('kpi_maintenance', 'Underhåll KPI', 'Öppna/stängda arbetsordrar, MTBF, MTTR', 'kpi', 'maintenance', 3, 0, 2, 10),
('kpi_inventory', 'Lager KPI', 'Totalt lagervärde, artiklar under minimum', 'kpi', 'inventory', 3, 0, 1, 11),
('kpi_purchasing', 'Inköp KPI', 'Aktiva ordrar, väntande anmodan', 'kpi', 'purchasing', 3, 0, 1, 12),
('kpi_finance', 'Ekonomi KPI', 'Obetalda fakturor, månadens intäkter/kostnader', 'kpi', 'finance', 5, 0, 2, 13),
('kpi_safety', 'H&S KPI', 'Öppna riskrapporter, kommande audits', 'kpi', 'safety', 3, 0, 1, 14),
('kpi_production', 'Produktion KPI', 'Aktiva ordrar, produkter, lagerplatser', 'kpi', 'production', 3, 0, 1, 15),
('kpi_sales', 'Försäljning KPI', 'Aktiva offerter, kundordrar, prislistor', 'kpi', 'sales', 3, 0, 1, 16),
('kpi_hr', 'HR KPI', 'Anställda, frånvaro idag, väntande reseräkningar', 'kpi', 'hr', 5, 0, 1, 17),
('kpi_projects', 'Projekt KPI', 'Aktiva projekt, öppna tasks', 'kpi', 'projects', 3, 0, 1, 18),
('kpi_cs', 'CS KPI', 'Öppna tickets, genomsnittlig svarstid', 'kpi', 'cs', 3, 0, 1, 19),
('shortcut_workorder', 'Snabbknapp: Ny arbetsorder', 'Skapa ny arbetsorder snabbt', 'shortcut', 'maintenance', 1, 0, 1, 30),
('shortcut_fault', 'Snabbknapp: Felanmälan', 'Rapportera fel snabbt', 'shortcut', 'maintenance', 1, 0, 1, 31),
('shortcut_invoice', 'Snabbknapp: Ny faktura', 'Skapa ny kundfaktura', 'shortcut', 'finance', 3, 0, 1, 32),
('shortcut_requisition', 'Snabbknapp: Ny anmodan', 'Skapa ny inköpsanmodan', 'shortcut', 'purchasing', 1, 0, 1, 33),
('recent_workorders', 'Senaste arbetsordrar', 'Lista senaste 5 arbetsordrar', 'list', 'maintenance', 1, 0, 2, 40),
('recent_invoices', 'Senaste fakturor', 'Lista senaste 5 kundfakturor', 'list', 'finance', 3, 0, 2, 41),
('overdue_resources', 'Förfallna nödresurser', 'Brandsläckare/hjärtstartare som behöver besiktigas', 'list', 'safety', 3, 0, 2, 42)");
