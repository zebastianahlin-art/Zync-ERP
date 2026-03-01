#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * ZYNC ERP – Admin User Seeder
 *
 * Seeds the 7 default roles and creates the initial admin user (VD role)
 * if they do not already exist.
 * Credentials are read from the environment – never hardcoded.
 *
 * Usage (from the zync-erp/ directory):
 *   php bin/seed.php
 *
 * Required .env variables:
 *   ADMIN_EMAIL
 *   ADMIN_PASSWORD
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/app/Core/EnvLoader.php';

loadEnv(BASE_PATH . '/.env');

use App\Core\Config;
use App\Core\Database;

$pdo = Database::pdo();

// ── Seed roles (DOC-03 §2) ───────────────────────────────────────────────────

$roles = [
    ['name' => 'VD',            'slug' => 'vd',        'level' => 10, 'is_system' => 1, 'description' => 'Fullständig åtkomst till alla moduler och systemkonfiguration'],
    ['name' => 'CEO',           'slug' => 'ceo',       'level' => 9,  'is_system' => 1, 'description' => 'Identisk med VD. Separat roll för multi-ledarskap'],
    ['name' => 'Chef',          'slug' => 'chef',      'level' => 8,  'is_system' => 1, 'description' => 'Full åtkomst till sin avdelning'],
    ['name' => 'Teamchef',      'slug' => 'team_lead', 'level' => 6,  'is_system' => 1, 'description' => 'Hanterar sitt team inom avdelning'],
    ['name' => 'Arbetsledare',  'slug' => 'foreman',   'level' => 5,  'is_system' => 1, 'description' => 'Skapar och tilldelar arbetsordrar'],
    ['name' => 'Arbetare',      'slug' => 'worker',    'level' => 3,  'is_system' => 1, 'description' => 'Utför tilldelade uppgifter'],
    ['name' => 'Läsbehörighet', 'slug' => 'readonly',  'level' => 1,  'is_system' => 1, 'description' => 'Kan läsa rapporter och dashboards'],
];

$insertRole = $pdo->prepare(
    'INSERT INTO roles (name, slug, level, is_system, description)
     VALUES (:name, :slug, :level, :is_system, :description)'
);

foreach ($roles as $role) {
    $exists = $pdo->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
    $exists->execute([$role['slug']]);

    if ($exists->fetchColumn() === false) {
        $insertRole->execute($role);
        echo "Role created: {$role['name']} (slug={$role['slug']})\n";
    } else {
        echo "Role already exists: {$role['name']} (slug={$role['slug']})\n";
    }
}

// ── Resolve the VD role id ───────────────────────────────────────────────────

$vdStmt = $pdo->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
$vdStmt->execute(['vd']);
$vdRoleId = $vdStmt->fetchColumn();

if ($vdRoleId === false) {
    fwrite(STDERR, "Error: VD role not found after seeding.\n");
    exit(1);
}

// ── Seed admin user ──────────────────────────────────────────────────────────

$email    = (string) Config::env('ADMIN_EMAIL', '');
$password = (string) Config::env('ADMIN_PASSWORD', '');

if ($email === '' || $password === '') {
    fwrite(STDERR, "Error: ADMIN_EMAIL and ADMIN_PASSWORD must be set in .env\n");
    exit(1);
}

$existing = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$existing->execute([$email]);

if ($existing->fetchColumn() !== false) {
    echo "Admin user already exists: {$email}\n";
    exit(0);
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare(
    'INSERT INTO users (email, username, password_hash, role_id, is_active)
     VALUES (:email, :username, :password_hash, :role_id, 1)'
);
$stmt->execute([
    'email'         => $email,
    'username'      => 'admin',
    'password_hash' => $hash,
    'role_id'       => $vdRoleId,
]);

$userId = $pdo->lastInsertId();
echo "Admin user created: {$email} (id={$userId}, role=VD)\n";
