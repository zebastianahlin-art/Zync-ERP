<?php

declare(strict_types=1);

/**
 * Migration 0066 — Extend saas_invoices with additional fields.
 *
 * Lägger till plan_slug (för batch-fakturering) och reminder_sent_at.
 */

use App\Core\Database;

$pdo = Database::pdo();

// Lägg till plan_slug om det inte finns
$columns = $pdo->query("SHOW COLUMNS FROM saas_invoices")->fetchAll(\PDO::FETCH_COLUMN);

if (!in_array('plan_slug', $columns, true)) {
    $pdo->exec("ALTER TABLE saas_invoices ADD COLUMN plan_slug VARCHAR(50) NULL AFTER tenant_id");
}

if (!in_array('reminder_sent_at', $columns, true)) {
    $pdo->exec("ALTER TABLE saas_invoices ADD COLUMN reminder_sent_at TIMESTAMP NULL AFTER paid_at");
}
