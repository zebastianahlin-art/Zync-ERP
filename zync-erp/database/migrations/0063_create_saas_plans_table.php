<?php

declare(strict_types=1);

/**
 * Migration 0063 — Create saas_plans table with seed data.
 *
 * Abonnemangsplaner för SaaS-plattformen:
 * Starter (999 SEK/mån), Professional (2 499 SEK/mån), Enterprise (4 999 SEK/mån).
 */

use App\Core\Database;

$pdo = Database::pdo();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS saas_plans (
        id               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        name             VARCHAR(100)    NOT NULL,
        slug             VARCHAR(50)     NOT NULL UNIQUE,
        description      TEXT            NULL,
        price_monthly    DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
        price_yearly     DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
        max_users        INT UNSIGNED    NOT NULL DEFAULT 10,
        max_storage_gb   INT UNSIGNED    NOT NULL DEFAULT 10,
        included_modules JSON            NULL COMMENT 'Array of module slugs included in this plan',
        features         JSON            NULL COMMENT 'Array of feature strings for marketing display',
        is_active        TINYINT(1)      NOT NULL DEFAULT 1,
        sort_order       INT UNSIGNED    NOT NULL DEFAULT 0,
        created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_slug (slug),
        KEY idx_sort (sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Seed de tre standardplanerna
$existing = (int) $pdo->query("SELECT COUNT(*) FROM saas_plans")->fetchColumn();

if ($existing === 0) {
    $stmt = $pdo->prepare("
        INSERT INTO saas_plans
            (name, slug, description, price_monthly, price_yearly, max_users, max_storage_gb, included_modules, features, is_active, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
    ");

    // Starter
    $stmt->execute([
        'Starter',
        'starter',
        'Perfekt för mindre företag som vill komma igång med digitalt underhåll och grundläggande drift.',
        999.00,
        9990.00,
        10,
        10,
        json_encode(['maintenance', 'equipment', 'dashboard']),
        json_encode([
            'Upp till 10 användare',
            '10 GB lagring',
            'Underhållsmodul',
            'Anläggningsregister',
            'E-postsupport',
        ]),
        1,
    ]);

    // Professional
    $stmt->execute([
        'Professional',
        'professional',
        'För medelstora företag med behov av avancerad HR, ekonomi och projekthantering.',
        2499.00,
        24990.00,
        50,
        50,
        json_encode(['maintenance', 'equipment', 'dashboard', 'hr', 'finance', 'projects', 'purchasing', 'sales']),
        json_encode([
            'Upp till 50 användare',
            '50 GB lagring',
            'Alla Starter-moduler',
            'HR & Löner',
            'Ekonomi & Fakturering',
            'Projekthantering',
            'Inköp & Lager',
            'Prioriterad support',
        ]),
        2,
    ]);

    // Enterprise
    $stmt->execute([
        'Enterprise',
        'enterprise',
        'Komplett ERP-plattform utan begränsningar — för stora organisationer med komplex verksamhet.',
        4999.00,
        49990.00,
        999,
        500,
        json_encode(['maintenance', 'equipment', 'dashboard', 'hr', 'finance', 'projects', 'purchasing', 'sales', 'admin', 'integrations', 'risk', 'emergency']),
        json_encode([
            'Obegränsade användare',
            '500 GB lagring',
            'Alla moduler inkluderade',
            'Dedikerad kundansvarig',
            'SLA-garanti 99.9% uptime',
            'On-premise alternativ tillgängligt',
            '24/7 telefonsupport',
        ]),
        3,
    ]);
}
