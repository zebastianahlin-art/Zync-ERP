<?php

use App\Core\Database;

$pdo = Database::pdo();

$pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    setting_key  VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,
    category     VARCHAR(60) NOT NULL DEFAULT 'general',
    description  TEXT NULL,
    data_type    ENUM('string','integer','boolean','json','text') NOT NULL DEFAULT 'string',
    is_public    TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Can non-admin users see this setting',
    PRIMARY KEY (id),
    UNIQUE KEY uq_setting_key (setting_key),
    INDEX idx_setting_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS erp_modules (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    slug         VARCHAR(60) NOT NULL,
    name         VARCHAR(150) NOT NULL,
    description  TEXT NULL,
    icon         VARCHAR(60) NULL COMMENT 'Icon class or emoji',
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    sort_order   INT NOT NULL DEFAULT 0,
    version      VARCHAR(20) NOT NULL DEFAULT '1.0.0',
    PRIMARY KEY (id),
    UNIQUE KEY uq_module_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    company_name    VARCHAR(200) NOT NULL DEFAULT 'ZYNC ERP',
    company_logo    VARCHAR(500) NULL,
    primary_color   VARCHAR(20) NOT NULL DEFAULT '#4f46e5',
    timezone        VARCHAR(50) NOT NULL DEFAULT 'Europe/Stockholm',
    date_format     VARCHAR(20) NOT NULL DEFAULT 'Y-m-d',
    currency        VARCHAR(10) NOT NULL DEFAULT 'SEK',
    language        VARCHAR(5) NOT NULL DEFAULT 'sv',
    smtp_host       VARCHAR(200) NULL,
    smtp_port       INT NULL,
    smtp_user       VARCHAR(200) NULL,
    smtp_password   VARCHAR(200) NULL,
    smtp_encryption ENUM('none','tls','ssl') NOT NULL DEFAULT 'tls',
    footer_text     TEXT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed default system settings
$pdo->exec("INSERT IGNORE INTO system_settings (setting_key, setting_value, category, description, data_type) VALUES
('company_name', 'ZYNC ERP', 'general', 'Företagsnamn som visas i systemet', 'string'),
('default_language', 'sv', 'general', 'Standardspråk för nya användare', 'string'),
('default_theme', 'dark', 'general', 'Standardtema för nya användare', 'string'),
('session_timeout', '3600', 'security', 'Sessionstimeout i sekunder', 'integer'),
('max_login_attempts', '5', 'security', 'Max antal inloggningsförsök före lockout', 'integer'),
('lockout_duration', '900', 'security', 'Lockout-tid i sekunder', 'integer'),
('require_2fa', '0', 'security', 'Kräv tvåfaktorsautentisering för alla', 'boolean'),
('password_min_length', '8', 'security', 'Minsta lösenordslängd', 'integer'),
('maintenance_mode', '0', 'system', 'Aktivera underhållsläge', 'boolean'),
('backup_enabled', '0', 'system', 'Automatisk backup aktiverad', 'boolean'),
('backup_interval', 'daily', 'system', 'Backup-intervall', 'string'),
('audit_log_retention', '365', 'system', 'Dagar att behålla audit-logg', 'integer'),
('email_notifications', '1', 'notifications', 'Aktivera e-postnotifieringar', 'boolean'),
('low_stock_threshold', '10', 'inventory', 'Låglagernivå för varningar', 'integer'),
('default_vat_rate', '25', 'finance', 'Standard-momssats (%)', 'integer'),
('fiscal_year_start', '01-01', 'finance', 'Räkenskapsårets start (MM-DD)', 'string')");

// Seed default ERP modules
$pdo->exec("INSERT IGNORE INTO erp_modules (slug, name, description, is_active, sort_order, version) VALUES
('dashboard', 'Dashboard', 'Konfigurerbar dashboard med widgets och KPI', 1, 1, '1.0.0'),
('maintenance', 'Underhåll', 'Arbetsordrar, felanmälningar, förebyggande underhåll', 1, 2, '1.0.0'),
('objects', 'ObjektNavigator', 'Objektträd med maskiner och utrustning', 1, 3, '1.0.0'),
('inventory', 'Lager', 'Lagerhantering, artiklar, lagerställen', 1, 4, '1.0.0'),
('purchasing', 'Inköp', 'Inköpsanmodan, inköpsordrar, avtal, leverantörer', 1, 5, '1.0.0'),
('finance', 'Ekonomi', 'Fakturor, kontoplan, budgetar, anläggningstillgångar', 1, 6, '1.0.0'),
('safety', 'Hälsa & Säkerhet', 'Riskhantering, audits, krishantering, nödresurser', 1, 7, '1.0.0'),
('production', 'Produktion', 'Produktionslinjer, produkter, ordrar, lager', 1, 8, '1.0.0'),
('sales', 'Försäljning', 'Offerter, kundordrar, prislistor', 1, 9, '1.0.0'),
('cs', 'Customer Service', 'Ticketsystem för kundärenden', 1, 10, '1.0.0'),
('transport', 'Transport', 'Transportordrar och åkerihantering', 1, 11, '1.0.0'),
('projects', 'Projekt', 'Projekthantering med tasks och budget', 1, 12, '1.0.0'),
('hr', 'HR', 'Personal, löner, närvaro, utbildning, rekrytering, reseräkningar', 1, 13, '1.0.0'),
('reports', 'Rapporter', 'Rapportgenerator per modul', 1, 14, '1.0.0'),
('admin', 'Admin', 'Systeminställningar och administration', 1, 15, '1.0.0')");

// Seed default site_settings row
$pdo->exec("INSERT IGNORE INTO site_settings (id, company_name, timezone, currency, language) VALUES (1, 'ZYNC ERP', 'Europe/Stockholm', 'SEK', 'sv')");
