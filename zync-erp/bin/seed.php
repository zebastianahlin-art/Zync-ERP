#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * ZYNC ERP – Admin User Seeder
 *
 * Creates the initial admin user if they do not already exist.
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
use App\Models\UserRepository;

$email    = (string) Config::env('ADMIN_EMAIL', '');
$password = (string) Config::env('ADMIN_PASSWORD', '');

if ($email === '' || $password === '') {
    fwrite(STDERR, "Error: ADMIN_EMAIL and ADMIN_PASSWORD must be set in .env\n");
    exit(1);
}

$repo = new UserRepository();

if ($repo->findByEmail($email) !== null) {
    echo "Admin user already exists: {$email}\n";
    exit(0);
}

$user = $repo->create($email, $password);
echo "Admin user created: {$user->email} (id={$user->id})\n";
