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

// ── Session ──────────────────────────────────────────────────────────────────
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ── Bootstrap ────────────────────────────────────────────────────────────────
$app = new App\Core\App(BASE_PATH);

// ── Routes ───────────────────────────────────────────────────────────────────
$router = $app->router();

$router->get('/', 'HomeController@index');

$router->get('/login',  'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/dashboard', 'DashboardController@index');

$router->get('/customers',              'CustomerController@index');
$router->get('/customers/create',       'CustomerController@create');
$router->post('/customers',             'CustomerController@store');
$router->get('/customers/{id}/edit',    'CustomerController@edit');
$router->post('/customers/{id}',        'CustomerController@update');
$router->post('/customers/{id}/delete', 'CustomerController@destroy');

// ── Run ──────────────────────────────────────────────────────────────────────
$app->run();
