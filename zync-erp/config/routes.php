<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
    // Public routes
    $app->get('/', [\App\Controllers\HomeController::class, 'index']);
    $app->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
    $app->post('/login', [\App\Controllers\AuthController::class, 'login']);
    $app->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

    // Protected routes (middleware will be added in next PR)
    $app->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);

    // Customer routes (will be migrated to companies module later)
    $app->get('/customers', [\App\Controllers\CustomerController::class, 'index']);
    $app->get('/customers/create', [\App\Controllers\CustomerController::class, 'create']);
    $app->post('/customers', [\App\Controllers\CustomerController::class, 'store']);
    $app->get('/customers/{id}/edit', [\App\Controllers\CustomerController::class, 'edit']);
    $app->post('/customers/{id}', [\App\Controllers\CustomerController::class, 'update']);
    $app->post('/customers/{id}/delete', [\App\Controllers\CustomerController::class, 'destroy']);
};
