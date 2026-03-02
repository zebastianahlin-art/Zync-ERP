#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * ZYNC ERP – Database Migration Runner
 *
 * Usage (from the zync-erp/ directory):
 *   php bin/migrate.php
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

use App\Core\Database;

$pdo = Database::pdo();

// Ensure the migrations tracking table exists.
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        migration  VARCHAR(255) NOT NULL,
        ran_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY migrations_migration_unique (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Fetch already-ran migrations.
$ran = $pdo->query('SELECT migration FROM migrations')->fetchAll(\PDO::FETCH_COLUMN);

// Discover migration files.
$migrationsDir = BASE_PATH . '/database/migrations';
$files = glob($migrationsDir . '/*.php');
sort($files);

$pending = array_filter($files, fn (string $f) => !in_array(basename($f), $ran, true));

if (empty($pending)) {
    echo "Nothing to migrate.\n";
    exit(0);
}

foreach ($pending as $file) {
    $name = basename($file);
    echo "Running migration: {$name} … ";

    $migration = require $file;
    if (is_callable($migration)) {
        $migration($pdo);
    }

    $stmt = $pdo->prepare('INSERT INTO migrations (migration) VALUES (?)');
    $stmt->execute([$name]);

    echo "done.\n";
}

echo "All migrations ran successfully.\n";
