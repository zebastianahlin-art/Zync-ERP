<?php

declare(strict_types=1);

/**
 * ZYNC ERP – Application Entry Point
 *
 * Apache document root: /zync-erp/public
 * All requests are routed through this file via .htaccess.
 */

// ── Autoloader ───────────────────────────────────────────────────────────────
require __DIR__ . '/../vendor/autoload.php';

// ── Load .env ────────────────────────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// ── Container ────────────────────────────────────────────────────────────────
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

// ── Slim App ─────────────────────────────────────────────────────────────────
$app = \DI\Bridge\Slim\Bridge::create($container);

// ── Session ──────────────────────────────────────────────────────────────────
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => ($_ENV['APP_ENV'] ?? 'production') === 'production',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Error middleware ──────────────────────────────────────────────────────────
$app->addErrorMiddleware(
    (bool) ($_ENV['APP_DEBUG'] ?? false),
    true,
    true
);

// ── Routes ───────────────────────────────────────────────────────────────────
(require __DIR__ . '/../config/routes.php')($app);

// ── Run ──────────────────────────────────────────────────────────────────────
$app->run();
