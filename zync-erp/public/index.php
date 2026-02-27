<?php

declare(strict_types=1);

/**
 * ZYNC ERP – Application Entry Point
 *
 * Apache document root: /zync-erp/public
 * All requests are routed through this file via .htaccess.
 */

// ── Paths ────────────────────────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

// ── Autoloader ───────────────────────────────────────────────────────────────
require BASE_PATH . '/vendor/autoload.php';

// ── Bootstrap ────────────────────────────────────────────────────────────────
$app = new App\Core\App(BASE_PATH);

// ── Routes ───────────────────────────────────────────────────────────────────
$router = $app->router();

$router->get('/', 'HomeController@index');

// ── Run ──────────────────────────────────────────────────────────────────────
$app->run();
