<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\JwtAuthMiddleware;
use App\Core\JwtService;
use App\Controllers\Api\AuthApiController;

return function (App $app) {
    // Public routes (no auth required)
    $app->get('/', [\App\Controllers\HomeController::class, 'index']);
    $app->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
    $app->post('/login', [\App\Controllers\AuthController::class, 'login']);
    $app->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

    // 2FA verification — requires session login but not full 2FA completion
    $app->get('/2fa/verify', [\App\Controllers\TwoFactorController::class, 'verify']);
    $app->post('/2fa/verify', [\App\Controllers\TwoFactorController::class, 'verifyPost']);

    // Protected routes — require authentication
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/dashboard', [\App\Controllers\DashboardController::class, 'index']);

        // My Page routes
        $group->get("/my-page", [\App\Controllers\MyPageController::class, "index"]);
        $group->get("/my-page/edit", [\App\Controllers\MyPageController::class, "editProfile"]);
        $group->post("/my-page/update", [\App\Controllers\MyPageController::class, "updateProfile"]);
        $group->post("/my-page/password", [\App\Controllers\MyPageController::class, "changePassword"]);
        $group->post("/my-page/avatar", [\App\Controllers\MyPageController::class, "uploadAvatar"]);

        // Customer routes (will be migrated to companies module later)
        $group->get('/customers', [\App\Controllers\CustomerController::class, 'index']);
        $group->get('/customers/create', [\App\Controllers\CustomerController::class, 'create']);
        $group->post('/customers', [\App\Controllers\CustomerController::class, 'store']);
        $group->get('/customers/{id}/edit', [\App\Controllers\CustomerController::class, 'edit']);
        $group->post('/customers/{id}', [\App\Controllers\CustomerController::class, 'update']);
        $group->post('/customers/{id}/delete', [\App\Controllers\CustomerController::class, 'destroy']);

        // Supplier routes
        $group->get('/suppliers', [\App\Controllers\SupplierController::class, 'index']);
        $group->get('/suppliers/create', [\App\Controllers\SupplierController::class, 'create']);
        $group->post('/suppliers', [\App\Controllers\SupplierController::class, 'store']);
        $group->get('/suppliers/{id}/edit', [\App\Controllers\SupplierController::class, 'edit']);
        $group->post('/suppliers/{id}', [\App\Controllers\SupplierController::class, 'update']);
        $group->post('/suppliers/{id}/delete', [\App\Controllers\SupplierController::class, 'destroy']);

        // Department routes
        $group->get("/departments", [\App\Controllers\DepartmentController::class, "index"]);
        $group->get("/departments/create", [\App\Controllers\DepartmentController::class, "create"]);
        $group->post("/departments", [\App\Controllers\DepartmentController::class, "store"]);
        $group->get("/departments/{id}/edit", [\App\Controllers\DepartmentController::class, "edit"]);
        $group->post("/departments/{id}", [\App\Controllers\DepartmentController::class, "update"]);
        $group->post("/departments/{id}/delete", [\App\Controllers\DepartmentController::class, "destroy"]);

        // Employee routes
        $group->get("/employees", [\App\Controllers\EmployeeController::class, "index"]);
        $group->get("/employees/create", [\App\Controllers\EmployeeController::class, "create"]);
        $group->post("/employees", [\App\Controllers\EmployeeController::class, "store"]);
        $group->get("/employees/{id}/edit", [\App\Controllers\EmployeeController::class, "edit"]);
        $group->post("/employees/{id}", [\App\Controllers\EmployeeController::class, "update"]);
        $group->post("/employees/{id}/delete", [\App\Controllers\EmployeeController::class, "destroy"]);

        // Certificate routes
        $group->get("/certificates", [\App\Controllers\CertificateController::class, "index"]);
        $group->get("/certificates/create", [\App\Controllers\CertificateController::class, "create"]);
        $group->post("/certificates", [\App\Controllers\CertificateController::class, "store"]);
        $group->get("/certificates/{id}/edit", [\App\Controllers\CertificateController::class, "edit"]);
        $group->post("/certificates/{id}", [\App\Controllers\CertificateController::class, "update"]);
        $group->post("/certificates/{id}/delete", [\App\Controllers\CertificateController::class, "destroy"]);
        $group->get("/certificates/{id}/download", [\App\Controllers\CertificateController::class, "download"]);

        // Certificate routes

        // Equipment routes
        $group->get("/equipment", [\App\Controllers\EquipmentController::class, "index"]);
        $group->get("/equipment/tree", [\App\Controllers\EquipmentController::class, "tree"]);
        $group->get("/equipment/create", [\App\Controllers\EquipmentController::class, "create"]);
        $group->post("/equipment", [\App\Controllers\EquipmentController::class, "store"]);
        $group->get("/equipment/{id}", [\App\Controllers\EquipmentController::class, "show"]);
        $group->get("/equipment/{id}/edit", [\App\Controllers\EquipmentController::class, "edit"]);
        $group->post("/equipment/{id}", [\App\Controllers\EquipmentController::class, "update"]);
        $group->post("/equipment/{id}/delete", [\App\Controllers\EquipmentController::class, "destroy"]);
        $group->post("/equipment/{id}/documents", [\App\Controllers\EquipmentController::class, "uploadDocument"]);
        $group->get("/equipment/{id}/documents/{docId}/download", [\App\Controllers\EquipmentController::class, "downloadDocument"]);
        $group->post("/equipment/{id}/documents/{docId}/delete", [\App\Controllers\EquipmentController::class, "deleteDocument"]);
        $group->post("/equipment/{id}/spare-parts", [\App\Controllers\EquipmentController::class, "addSparePart"]);
        $group->post("/equipment/{id}/spare-parts/{spareId}/delete", [\App\Controllers\EquipmentController::class, "removeSparePart"]);

        // Fault report routes
        $group->get("/maintenance/faults", [\App\Controllers\FaultReportController::class, "index"]);
        $group->get("/maintenance/faults/create", [\App\Controllers\FaultReportController::class, "create"]);
        $group->post("/maintenance/faults", [\App\Controllers\FaultReportController::class, "store"]);
        $group->get("/maintenance/faults/{id}", [\App\Controllers\FaultReportController::class, "show"]);
        $group->get("/maintenance/faults/{id}/edit", [\App\Controllers\FaultReportController::class, "edit"]);
        $group->post("/maintenance/faults/{id}", [\App\Controllers\FaultReportController::class, "update"]);
        $group->post("/maintenance/faults/{id}/status", [\App\Controllers\FaultReportController::class, "updateStatus"]);
        $group->post("/maintenance/faults/{id}/delete", [\App\Controllers\FaultReportController::class, "destroy"]);

        // Work order routes
        $group->get("/maintenance/work-orders", [\App\Controllers\WorkOrderController::class, "index"]);
        $group->get("/maintenance/work-orders/create", [\App\Controllers\WorkOrderController::class, "create"]);
        $group->post("/maintenance/work-orders", [\App\Controllers\WorkOrderController::class, "store"]);
        $group->get("/maintenance/work-orders/{id}", [\App\Controllers\WorkOrderController::class, "show"]);
        $group->get("/maintenance/work-orders/{id}/edit", [\App\Controllers\WorkOrderController::class, "edit"]);
        $group->post("/maintenance/work-orders/{id}", [\App\Controllers\WorkOrderController::class, "update"]);
        $group->post("/maintenance/work-orders/{id}/status", [\App\Controllers\WorkOrderController::class, "updateStatus"]);
        $group->post("/maintenance/work-orders/{id}/delete", [\App\Controllers\WorkOrderController::class, "destroy"]);
        $group->post("/maintenance/work-orders/{id}/time", [\App\Controllers\WorkOrderController::class, "addTime"]);
        $group->post("/maintenance/work-orders/{id}/comments", [\App\Controllers\WorkOrderController::class, "addComment"]);
        $group->post("/maintenance/work-orders/{id}/materials", [\App\Controllers\WorkOrderController::class, "withdrawMaterial"]);

        // Employee routes

        // Inventory
        $group->get("/inventory", [\App\Controllers\InventoryController::class, "index"]);
        $group->get("/inventory/detail", [\App\Controllers\InventoryController::class, "detail"]);
        $group->get("/inventory/move", [\App\Controllers\InventoryController::class, "moveForm"]);
        $group->post("/inventory/move", [\App\Controllers\InventoryController::class, "moveStore"]);
        $group->get("/inventory/transactions", [\App\Controllers\InventoryController::class, "transactions"]);
        $group->get("/inventory/warehouses", [\App\Controllers\InventoryController::class, "warehouses"]);
        $group->get("/inventory/warehouses/create", [\App\Controllers\InventoryController::class, "warehouseCreate"]);
        $group->post("/inventory/warehouses", [\App\Controllers\InventoryController::class, "warehouseStore"]);
        $group->get("/inventory/warehouses/{id}/edit", [\App\Controllers\InventoryController::class, "warehouseEdit"]);
        $group->post("/inventory/warehouses/{id}", [\App\Controllers\InventoryController::class, "warehouseUpdate"]);
        $group->post("/inventory/warehouses/{id}/delete", [\App\Controllers\InventoryController::class, "warehouseDestroy"]);

        // Machines
        $group->get("/machines", [\App\Controllers\MachineController::class, "index"]);
        $group->get("/machines/create", [\App\Controllers\MachineController::class, "create"]);
        $group->post("/machines", [\App\Controllers\MachineController::class, "store"]);
        $group->get("/machines/{id}", [\App\Controllers\MachineController::class, "show"]);
        $group->get("/machines/{id}/edit", [\App\Controllers\MachineController::class, "edit"]);
        $group->post("/machines/{id}", [\App\Controllers\MachineController::class, "update"]);
        $group->post("/machines/{id}/delete", [\App\Controllers\MachineController::class, "destroy"]);
        $group->post("/machines/{id}/spare-parts", [\App\Controllers\MachineController::class, "addSparePart"]);
        $group->post("/machines/{id}/spare-parts/{spId}/delete", [\App\Controllers\MachineController::class, "removeSparePart"]);
        $group->post("/machines/{id}/documents", [\App\Controllers\MachineController::class, "uploadDocument"]);
        $group->post("/machines/{id}/documents/{docId}/delete", [\App\Controllers\MachineController::class, "removeDocument"]);
        // Article routes
        $group->get('/articles', [\App\Controllers\ArticleController::class, 'index']);
        $group->get('/articles/create', [\App\Controllers\ArticleController::class, 'create']);
        $group->post('/articles', [\App\Controllers\ArticleController::class, 'store']);
        $group->get('/articles/{id}/edit', [\App\Controllers\ArticleController::class, 'edit']);
        $group->post('/articles/{id}', [\App\Controllers\ArticleController::class, 'update']);
        $group->post('/articles/{id}/delete', [\App\Controllers\ArticleController::class, 'destroy']);

        // Theme preference
        $group->post('/settings/theme', function (
            \Psr\Http\Message\ServerRequestInterface $request,
            \Psr\Http\Message\ResponseInterface      $response
        ): \Psr\Http\Message\ResponseInterface {
            $body  = (array) $request->getParsedBody();
            $theme = in_array($body['theme'] ?? '', ['dark', 'light'], true) ? $body['theme'] : 'light';
            $id    = \App\Core\Auth::id();
            if ($id !== null) {
                \App\Core\Database::pdo()
                    ->prepare('UPDATE users SET theme = ? WHERE id = ?')
                    ->execute([$theme, $id]);
                // Clear cached user so next Auth::user() picks up new theme
                unset($_SESSION['_user_cache']);
            }
            $response->getBody()->write((string) json_encode(['success' => true]));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // 2FA setup and management (requires full authentication)
        $group->get('/2fa/setup', [\App\Controllers\TwoFactorController::class, 'setup']);
        $group->post('/2fa/enable', [\App\Controllers\TwoFactorController::class, 'enable']);
        $group->post('/2fa/disable', [\App\Controllers\TwoFactorController::class, 'disable']);
    })->add(new CsrfMiddleware())->add(new AuthMiddleware());

    // Admin routes — require Chef level (7) or higher
    $app->group('/admin', function (RouteCollectorProxy $group) {
        $group->get('', [\App\Controllers\AdminController::class, 'index']);
        $group->get('/users', [\App\Controllers\AdminController::class, 'users']);
        $group->get('/users/create', [\App\Controllers\AdminController::class, 'createUser']);
        $group->post('/users', [\App\Controllers\AdminController::class, 'storeUser']);
        $group->get('/users/{id}/edit', [\App\Controllers\AdminController::class, 'editUser']);
        $group->post('/users/{id}', [\App\Controllers\AdminController::class, 'updateUser']);
        $group->post('/users/{id}/toggle', [\App\Controllers\AdminController::class, 'toggleUser']);
    })->add(new CsrfMiddleware())->add(new \App\Middleware\RoleMiddleware(minLevel: 7))->add(new AuthMiddleware());

    // Public API routes (no JWT required)
    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->post('/login', [AuthApiController::class, 'login']);
        $group->post('/2fa/verify', [AuthApiController::class, 'verify2fa']);
    });

    // Protected API routes (JWT required)
    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->post('/token/refresh', [AuthApiController::class, 'refresh']);
        $group->get('/me', [AuthApiController::class, 'me']);
    })->add(new JwtAuthMiddleware(new JwtService()));
};

