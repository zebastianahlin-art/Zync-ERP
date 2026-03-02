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

        // ─── Purchasing (Inköp) ──────────────────────────────────
        $group->get('/purchasing', [\App\Controllers\PurchaseController::class, 'index']);

        // Requisitions (Anmodan)
        $group->get('/purchasing/requisitions', [\App\Controllers\PurchaseController::class, 'requisitions']);
        $group->get('/purchasing/requisitions/create', [\App\Controllers\PurchaseController::class, 'createRequisition']);
        $group->post('/purchasing/requisitions', [\App\Controllers\PurchaseController::class, 'storeRequisition']);
        $group->get('/purchasing/requisitions/{id}', [\App\Controllers\PurchaseController::class, 'showRequisition']);
        $group->get('/purchasing/requisitions/{id}/edit', [\App\Controllers\PurchaseController::class, 'editRequisition']);
        $group->post('/purchasing/requisitions/{id}', [\App\Controllers\PurchaseController::class, 'updateRequisition']);
        $group->post('/purchasing/requisitions/{id}/delete', [\App\Controllers\PurchaseController::class, 'deleteRequisition']);
        $group->post('/purchasing/requisitions/{id}/submit', [\App\Controllers\PurchaseController::class, 'submitRequisition']);
        $group->post('/purchasing/requisitions/{id}/approve', [\App\Controllers\PurchaseController::class, 'approveRequisition']);
        $group->post('/purchasing/requisitions/{id}/reject', [\App\Controllers\PurchaseController::class, 'rejectRequisition']);
        $group->post('/purchasing/requisitions/{id}/convert', [\App\Controllers\PurchaseController::class, 'convertToOrder']);
        $group->post('/purchasing/requisitions/{id}/lines', [\App\Controllers\PurchaseController::class, 'addRequisitionLine']);
        $group->post('/purchasing/requisitions/{id}/lines/{lineId}/delete', [\App\Controllers\PurchaseController::class, 'removeRequisitionLine']);

        // Purchase Orders (Inköpsordrar)
        $group->get('/purchasing/orders', [\App\Controllers\PurchaseController::class, 'orders']);
        $group->get('/purchasing/orders/create', [\App\Controllers\PurchaseController::class, 'createOrder']);
        $group->post('/purchasing/orders', [\App\Controllers\PurchaseController::class, 'storeOrder']);
        $group->get('/purchasing/orders/{id}', [\App\Controllers\PurchaseController::class, 'showOrder']);
        $group->get('/purchasing/orders/{id}/edit', [\App\Controllers\PurchaseController::class, 'editOrder']);
        $group->post('/purchasing/orders/{id}', [\App\Controllers\PurchaseController::class, 'updateOrder']);
        $group->post('/purchasing/orders/{id}/delete', [\App\Controllers\PurchaseController::class, 'deleteOrder']);
        $group->post('/purchasing/orders/{id}/status', [\App\Controllers\PurchaseController::class, 'updateOrderStatus']);
        $group->post('/purchasing/orders/{id}/lines', [\App\Controllers\PurchaseController::class, 'addOrderLine']);
        $group->post('/purchasing/orders/{id}/lines/{lineId}/delete', [\App\Controllers\PurchaseController::class, 'removeOrderLine']);

        // Agreements (Avtal)
        $group->get('/purchasing/agreements', [\App\Controllers\PurchaseController::class, 'agreements']);
        $group->get('/purchasing/agreements/create', [\App\Controllers\PurchaseController::class, 'createAgreement']);
        $group->post('/purchasing/agreements', [\App\Controllers\PurchaseController::class, 'storeAgreement']);
        $group->get('/purchasing/agreements/{id}', [\App\Controllers\PurchaseController::class, 'showAgreement']);
        $group->get('/purchasing/agreements/{id}/edit', [\App\Controllers\PurchaseController::class, 'editAgreement']);
        $group->post('/purchasing/agreements/{id}', [\App\Controllers\PurchaseController::class, 'updateAgreement']);
        $group->post('/purchasing/agreements/{id}/delete', [\App\Controllers\PurchaseController::class, 'deleteAgreement']);

        // ─── Finance (Ekonomi) ───────────────────────────────────
        $group->get('/finance', [\App\Controllers\FinanceController::class, 'index']);

        // Utgående fakturor
        $group->get('/finance/invoices-out', [\App\Controllers\FinanceController::class, 'invoicesOut']);
        $group->get('/finance/invoices-out/create', [\App\Controllers\FinanceController::class, 'createInvoiceOut']);
        $group->post('/finance/invoices-out', [\App\Controllers\FinanceController::class, 'storeInvoiceOut']);
        $group->get('/finance/invoices-out/{id}', [\App\Controllers\FinanceController::class, 'showInvoiceOut']);
        $group->get('/finance/invoices-out/{id}/edit', [\App\Controllers\FinanceController::class, 'editInvoiceOut']);
        $group->post('/finance/invoices-out/{id}', [\App\Controllers\FinanceController::class, 'updateInvoiceOut']);
        $group->post('/finance/invoices-out/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteInvoiceOut']);
        $group->post('/finance/invoices-out/{id}/status', [\App\Controllers\FinanceController::class, 'statusInvoiceOut']);
        $group->post('/finance/invoices-out/{id}/lines', [\App\Controllers\FinanceController::class, 'addLineOut']);
        $group->post('/finance/invoices-out/{id}/lines/{lineId}/delete', [\App\Controllers\FinanceController::class, 'removeLineOut']);
        $group->post('/finance/invoices-out/{id}/payment', [\App\Controllers\FinanceController::class, 'paymentOut']);

        // Inkommande fakturor
        $group->get('/finance/invoices-in', [\App\Controllers\FinanceController::class, 'invoicesIn']);
        $group->get('/finance/invoices-in/create', [\App\Controllers\FinanceController::class, 'createInvoiceIn']);
        $group->post('/finance/invoices-in', [\App\Controllers\FinanceController::class, 'storeInvoiceIn']);
        $group->get('/finance/invoices-in/{id}', [\App\Controllers\FinanceController::class, 'showInvoiceIn']);
        $group->get('/finance/invoices-in/{id}/edit', [\App\Controllers\FinanceController::class, 'editInvoiceIn']);
        $group->post('/finance/invoices-in/{id}', [\App\Controllers\FinanceController::class, 'updateInvoiceIn']);
        $group->post('/finance/invoices-in/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteInvoiceIn']);
        $group->post('/finance/invoices-in/{id}/status', [\App\Controllers\FinanceController::class, 'statusInvoiceIn']);
        $group->post('/finance/invoices-in/{id}/lines', [\App\Controllers\FinanceController::class, 'addLineIn']);
        $group->post('/finance/invoices-in/{id}/lines/{lineId}/delete', [\App\Controllers\FinanceController::class, 'removeLineIn']);
        $group->post('/finance/invoices-in/{id}/payment', [\App\Controllers\FinanceController::class, 'paymentIn']);

        // Bokföring / Verifikationer
        $group->get('/finance/journal', [\App\Controllers\FinanceController::class, 'journal']);
        $group->get('/finance/journal/create', [\App\Controllers\FinanceController::class, 'createJournal']);
        $group->post('/finance/journal', [\App\Controllers\FinanceController::class, 'storeJournal']);
        $group->get('/finance/journal/{id}', [\App\Controllers\FinanceController::class, 'showJournal']);
        $group->post('/finance/journal/{id}/lines', [\App\Controllers\FinanceController::class, 'addJournalLine']);
        $group->post('/finance/journal/{id}/lines/{lineId}/delete', [\App\Controllers\FinanceController::class, 'removeJournalLine']);
        $group->post('/finance/journal/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteJournal']);

        // Kontoplan
        $group->get('/finance/accounts', [\App\Controllers\FinanceController::class, 'chartOfAccounts']);
        $group->get('/finance/accounts/create', [\App\Controllers\FinanceController::class, 'createAccount']);
        $group->post('/finance/accounts', [\App\Controllers\FinanceController::class, 'storeAccount']);
        $group->get('/finance/accounts/{id}/edit', [\App\Controllers\FinanceController::class, 'editAccount']);
        $group->post('/finance/accounts/{id}', [\App\Controllers\FinanceController::class, 'updateAccount']);

        // Kostnadsställen
        $group->get('/finance/cost-centers', [\App\Controllers\FinanceController::class, 'costCentersIndex']);
        $group->get('/finance/cost-centers/create', [\App\Controllers\FinanceController::class, 'createCostCenter']);
        $group->post('/finance/cost-centers', [\App\Controllers\FinanceController::class, 'storeCostCenter']);
        $group->get('/finance/cost-centers/{id}/edit', [\App\Controllers\FinanceController::class, 'editCostCenter']);
        $group->post('/finance/cost-centers/{id}', [\App\Controllers\FinanceController::class, 'updateCostCenter']);
        $group->post('/finance/cost-centers/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteCostCenter']);

        // Rapporter
        $group->get('/finance/reports/ledger', [\App\Controllers\FinanceController::class, 'ledger']);
        $group->get('/finance/reports/trial-balance', [\App\Controllers\FinanceController::class, 'trialBalance']);
        $group->get('/finance/reports/cost-centers', [\App\Controllers\FinanceController::class, 'costCenterReport']);
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

