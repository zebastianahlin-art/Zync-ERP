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
        $group->get('/dashboard/configure', [\App\Controllers\DashboardController::class, 'configure']);
        $group->post('/dashboard/widgets/add', [\App\Controllers\DashboardController::class, 'addWidget']);
        $group->post('/dashboard/widgets/remove', [\App\Controllers\DashboardController::class, 'removeWidget']);
        $group->post('/dashboard/widgets/reorder', [\App\Controllers\DashboardController::class, 'reorderWidgets']);

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
            \Psr\Http\Message\ResponseInterface $response
        ): \Psr\Http\Message\ResponseInterface {
            $body  = (array) $request->getParsedBody();
            $theme = in_array($body['theme'] ?? '', ['dark', 'light'], true) ? $body['theme'] : 'light';
            $id    = \App\Core\Auth::id();
            if ($id !== null) {
                \App\Core\Database::pdo()
                    ->prepare('UPDATE users SET theme = ? WHERE id = ?')
                    ->execute([$theme, $id]);
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
        $group->get('/purchasing/requisitions/history', [\App\Controllers\PurchaseController::class, 'requisitionHistory']);
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
        $group->get('/purchasing/orders/history', [\App\Controllers\PurchaseController::class, 'orderHistory']);
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
        $group->get('/purchasing/agreements/history', [\App\Controllers\PurchaseController::class, 'agreementHistory']);
        // Supplier Audits
        $group->get('/purchasing/supplier-audits', [\App\Controllers\PurchaseController::class, 'supplierAuditIndex']);
        $group->get('/purchasing/supplier-audits/create', [\App\Controllers\PurchaseController::class, 'createSupplierAudit']);
        $group->post('/purchasing/supplier-audits', [\App\Controllers\PurchaseController::class, 'storeSupplierAudit']);
        $group->get('/purchasing/supplier-audits/{id}', [\App\Controllers\PurchaseController::class, 'showSupplierAudit']);
        $group->get('/purchasing/supplier-audits/{id}/edit', [\App\Controllers\PurchaseController::class, 'editSupplierAudit']);
        $group->post('/purchasing/supplier-audits/{id}', [\App\Controllers\PurchaseController::class, 'updateSupplierAudit']);
        $group->post('/purchasing/supplier-audits/{id}/delete', [\App\Controllers\PurchaseController::class, 'deleteSupplierAudit']);
        // Agreement Templates
        $group->get('/purchasing/agreement-templates', [\App\Controllers\PurchaseController::class, 'agreementTemplateIndex']);
        $group->get('/purchasing/agreement-templates/create', [\App\Controllers\PurchaseController::class, 'createAgreementTemplate']);
        $group->post('/purchasing/agreement-templates', [\App\Controllers\PurchaseController::class, 'storeAgreementTemplate']);
        $group->get('/purchasing/agreement-templates/{id}', [\App\Controllers\PurchaseController::class, 'showAgreementTemplate']);
        $group->get('/purchasing/agreement-templates/{id}/edit', [\App\Controllers\PurchaseController::class, 'editAgreementTemplate']);
        $group->post('/purchasing/agreement-templates/{id}', [\App\Controllers\PurchaseController::class, 'updateAgreementTemplate']);
        $group->post('/purchasing/agreement-templates/{id}/delete', [\App\Controllers\PurchaseController::class, 'deleteAgreementTemplate']);
        // Keep redirects for old URLs
        $group->get('/purchasing/agreements/templates', [\App\Controllers\PurchaseController::class, 'agreementTemplateIndex']);
        $group->get('/purchasing/audits', [\App\Controllers\PurchaseController::class, 'supplierAuditIndex']);
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
        $group->get('/finance/invoices-out/{id}/credit-note', [\App\Controllers\FinanceController::class, 'creditNoteForm']);
        $group->post('/finance/invoices-out/{id}/credit-note', [\App\Controllers\FinanceController::class, 'createCreditNote']);
        $group->get('/finance/invoices-out/{id}/pdf', [\App\Controllers\FinanceController::class, 'pdfInvoiceOut']);
        $group->post('/finance/invoices-out/{id}/reminder', [\App\Controllers\FinanceController::class, 'sendReminder']);
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
        $group->post('/finance/invoices-in/{id}/approve', [\App\Controllers\FinanceController::class, 'approveInvoiceIn']);
        $group->post('/finance/invoices-in/{id}/reject', [\App\Controllers\FinanceController::class, 'rejectInvoiceIn']);
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
        $group->get('/finance/accounts/export', [\App\Controllers\FinanceController::class, 'exportAccounts']);
        $group->get('/finance/accounts/import', [\App\Controllers\FinanceController::class, 'importAccountsForm']);
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
        $group->get('/finance/reports/ledger/{accountId}', [\App\Controllers\FinanceController::class, 'accountLedger']);
        $group->get('/finance/reports/trial-balance', [\App\Controllers\FinanceController::class, 'trialBalance']);
        $group->get('/finance/reports/balance-sheet', [\App\Controllers\FinanceController::class, 'balanceSheet']);
        $group->get('/finance/reports/cost-centers', [\App\Controllers\FinanceController::class, 'costCenterReport']);
        $group->get('/finance/reports/kpi', [\App\Controllers\FinanceController::class, 'reportKpi']);
        $group->get('/finance/reports/stocktaking', [\App\Controllers\FinanceController::class, 'reportStocktaking']);

        // Kontoplansgrupper
        $group->get('/finance/account-groups', [\App\Controllers\FinanceController::class, 'accountGroups']);
        $group->get('/finance/account-groups/create', [\App\Controllers\FinanceController::class, 'createAccountGroup']);
        $group->post('/finance/account-groups', [\App\Controllers\FinanceController::class, 'storeAccountGroup']);
        $group->get('/finance/account-groups/{id}/edit', [\App\Controllers\FinanceController::class, 'editAccountGroup']);
        $group->post('/finance/account-groups/{id}', [\App\Controllers\FinanceController::class, 'updateAccountGroup']);
        $group->post('/finance/account-groups/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteAccountGroup']);

        // Budgetar
        $group->get('/finance/budgets', [\App\Controllers\FinanceController::class, 'budgetsIndex']);
        $group->get('/finance/budgets/create', [\App\Controllers\FinanceController::class, 'createBudget']);
        $group->post('/finance/budgets', [\App\Controllers\FinanceController::class, 'storeBudget']);
        $group->get('/finance/budgets/{id}/edit', [\App\Controllers\FinanceController::class, 'editBudget']);
        $group->post('/finance/budgets/{id}', [\App\Controllers\FinanceController::class, 'updateBudget']);
        $group->post('/finance/budgets/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteBudget']);

        // Anläggningstillgångar
        $group->get('/finance/assets', [\App\Controllers\FinanceController::class, 'assetsIndex']);
        $group->get('/finance/assets/create', [\App\Controllers\FinanceController::class, 'createAsset']);
        $group->post('/finance/assets', [\App\Controllers\FinanceController::class, 'storeAsset']);
        $group->get('/finance/assets/{id}', [\App\Controllers\FinanceController::class, 'showAsset']);
        $group->get('/finance/assets/{id}/edit', [\App\Controllers\FinanceController::class, 'editAsset']);
        $group->post('/finance/assets/{id}', [\App\Controllers\FinanceController::class, 'updateAsset']);
        $group->post('/finance/assets/{id}/delete', [\App\Controllers\FinanceController::class, 'deleteAsset']);
        $group->post('/finance/assets/{id}/depreciate', [\App\Controllers\FinanceController::class, 'depreciateAsset']);


        // ─── Health & Safety (Hälsa & Säkerhet) ─────────────
        $group->get('/safety', [\App\Controllers\SafetyController::class, 'index']);

        $group->get('/safety/risks', [\App\Controllers\SafetyController::class, 'risks']);
        $group->get('/safety/risks/create', [\App\Controllers\SafetyController::class, 'createRisk']);
        $group->post('/safety/risks', [\App\Controllers\SafetyController::class, 'storeRisk']);
        $group->get('/safety/risks/{id}', [\App\Controllers\SafetyController::class, 'showRisk']);
        $group->get('/safety/risks/{id}/edit', [\App\Controllers\SafetyController::class, 'editRisk']);
        $group->post('/safety/risks/{id}', [\App\Controllers\SafetyController::class, 'updateRisk']);
        $group->post('/safety/risks/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteRisk']);
        $group->post('/safety/risks/{id}/status', [\App\Controllers\SafetyController::class, 'updateRiskStatus']);

        $group->get('/safety/reports', [\App\Controllers\SafetyController::class, 'reports']);
        $group->get('/safety/reports/create', [\App\Controllers\SafetyController::class, 'createReport']);
        $group->post('/safety/reports', [\App\Controllers\SafetyController::class, 'storeReport']);
        $group->get('/safety/reports/{id}', [\App\Controllers\SafetyController::class, 'showReport']);
        $group->get('/safety/reports/{id}/edit', [\App\Controllers\SafetyController::class, 'editReport']);
        $group->post('/safety/reports/{id}', [\App\Controllers\SafetyController::class, 'updateReport']);
        $group->post('/safety/reports/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteReport']);
        $group->post('/safety/reports/{id}/status', [\App\Controllers\SafetyController::class, 'updateReportStatus']);

        $group->get('/safety/audits', [\App\Controllers\SafetyController::class, 'audits']);
        $group->get('/safety/audits/create', [\App\Controllers\SafetyController::class, 'createAudit']);
        $group->post('/safety/audits', [\App\Controllers\SafetyController::class, 'storeAudit']);
        $group->get('/safety/audits/pending', [\App\Controllers\SafetyController::class, 'pendingAudits']);
        $group->get('/safety/audits/completed', [\App\Controllers\SafetyController::class, 'completedAudits']);
        $group->get('/safety/audits/{id}', [\App\Controllers\SafetyController::class, 'showAudit']);
        $group->get('/safety/audits/{id}/edit', [\App\Controllers\SafetyController::class, 'editAudit']);
        $group->post('/safety/audits/{id}', [\App\Controllers\SafetyController::class, 'updateAudit']);
        $group->post('/safety/audits/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteAudit']);
        $group->post('/safety/audits/{id}/status', [\App\Controllers\SafetyController::class, 'updateAuditStatus']);
        $group->post('/safety/audits/{id}/responses', [\App\Controllers\SafetyController::class, 'saveAuditResponses']);

        $group->get('/safety/audit-templates', [\App\Controllers\SafetyController::class, 'auditTemplates']);
        $group->get('/safety/audit-templates/create', [\App\Controllers\SafetyController::class, 'createAuditTemplate']);
        $group->post('/safety/audit-templates', [\App\Controllers\SafetyController::class, 'storeAuditTemplate']);
        $group->get('/safety/audit-templates/{id}', [\App\Controllers\SafetyController::class, 'showAuditTemplate']);
        $group->get('/safety/audit-templates/{id}/edit', [\App\Controllers\SafetyController::class, 'editAuditTemplate']);
        $group->post('/safety/audit-templates/{id}', [\App\Controllers\SafetyController::class, 'updateAuditTemplate']);
        $group->post('/safety/audit-templates/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteAuditTemplate']);
        $group->post('/safety/audit-templates/{id}/items', [\App\Controllers\SafetyController::class, 'addTemplateItem']);
        $group->post('/safety/audit-templates/{id}/items/{itemId}/delete', [\App\Controllers\SafetyController::class, 'removeTemplateItem']);

        $group->get('/safety/emergency', [\App\Controllers\SafetyController::class, 'emergency']);
        $group->get('/safety/emergency/contacts', [\App\Controllers\SafetyController::class, 'emergencyContacts']);
        $group->get('/safety/emergency/contacts/create', [\App\Controllers\SafetyController::class, 'createEmergencyContact']);
        $group->post('/safety/emergency/contacts', [\App\Controllers\SafetyController::class, 'storeEmergencyContact']);
        $group->get('/safety/emergency/contacts/{id}/edit', [\App\Controllers\SafetyController::class, 'editEmergencyContact']);
        $group->post('/safety/emergency/contacts/{id}', [\App\Controllers\SafetyController::class, 'updateEmergencyContact']);
        $group->post('/safety/emergency/contacts/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteEmergencyContact']);

        $group->get('/safety/emergency/procedures', [\App\Controllers\SafetyController::class, 'emergencyProcedures']);
        $group->get('/safety/emergency/procedures/create', [\App\Controllers\SafetyController::class, 'createEmergencyProcedure']);
        $group->post('/safety/emergency/procedures', [\App\Controllers\SafetyController::class, 'storeEmergencyProcedure']);
        $group->get('/safety/emergency/drills', [\App\Controllers\SafetyController::class, 'drills']);
        $group->get('/safety/emergency/drills/create', [\App\Controllers\SafetyController::class, 'createDrill']);
        $group->post('/safety/emergency/drills', [\App\Controllers\SafetyController::class, 'storeDrill']);
        $group->get('/safety/emergency/drills/templates', [\App\Controllers\SafetyController::class, 'drillTemplates']);
        $group->get('/safety/emergency/drills/templates/create', [\App\Controllers\SafetyController::class, 'createDrillTemplate']);
        $group->post('/safety/emergency/drills/templates', [\App\Controllers\SafetyController::class, 'storeDrillTemplate']);
        $group->get('/safety/emergency/drills/templates/{id}/edit', [\App\Controllers\SafetyController::class, 'editDrillTemplate']);
        $group->post('/safety/emergency/drills/templates/{id}', [\App\Controllers\SafetyController::class, 'updateDrillTemplate']);
        $group->post('/safety/emergency/drills/templates/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteDrillTemplate']);
        $group->get('/safety/emergency/drills/{id}', [\App\Controllers\SafetyController::class, 'showDrill']);
        $group->get('/safety/emergency/drills/{id}/edit', [\App\Controllers\SafetyController::class, 'editDrill']);
        $group->post('/safety/emergency/drills/{id}', [\App\Controllers\SafetyController::class, 'updateDrill']);
        $group->post('/safety/emergency/drills/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteDrill']);
        $group->get('/safety/emergency/procedures/{id}', [\App\Controllers\SafetyController::class, 'showEmergencyProcedure']);
        $group->get('/safety/emergency/procedures/{id}/edit', [\App\Controllers\SafetyController::class, 'editEmergencyProcedure']);
        $group->post('/safety/emergency/procedures/{id}', [\App\Controllers\SafetyController::class, 'updateEmergencyProcedure']);
        $group->post('/safety/emergency/procedures/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteEmergencyProcedure']);

        $group->get('/safety/resources', [\App\Controllers\SafetyController::class, 'resources']);
        $group->get('/safety/resources/map', [\App\Controllers\SafetyController::class, 'resourcesMap']);
        $group->get('/safety/resources/overdue', [\App\Controllers\SafetyController::class, 'overdueInspections']);
        $group->get('/safety/resources/create', [\App\Controllers\SafetyController::class, 'createResource']);
        $group->post('/safety/resources', [\App\Controllers\SafetyController::class, 'storeResource']);
        $group->get('/safety/resources/{id}', [\App\Controllers\SafetyController::class, 'showResource']);
        $group->get('/safety/resources/{id}/edit', [\App\Controllers\SafetyController::class, 'editResource']);
        $group->post('/safety/resources/{id}', [\App\Controllers\SafetyController::class, 'updateResource']);
        $group->post('/safety/resources/{id}/delete', [\App\Controllers\SafetyController::class, 'deleteResource']);
        $group->post('/safety/resources/{id}/inspect', [\App\Controllers\SafetyController::class, 'inspectResource']);

        // ─── Departments (Avdelningar) ────────────────────────────
        $group->get('/departments', [\App\Controllers\DepartmentController::class, 'index']);
        $group->get('/departments/create', [\App\Controllers\DepartmentController::class, 'create']);
        $group->post('/departments', [\App\Controllers\DepartmentController::class, 'store']);
        $group->get('/departments/{id}/edit', [\App\Controllers\DepartmentController::class, 'edit']);
        $group->post('/departments/{id}', [\App\Controllers\DepartmentController::class, 'update']);
        $group->post('/departments/{id}/delete', [\App\Controllers\DepartmentController::class, 'destroy']);

        // ─── HR Dashboard ────────────────────────────────────────
        $group->get('/hr', [\App\Controllers\HrController::class, 'dashboard']);

        // ─── Employees (Personal) ────────────────────────────────
        $group->get('/employees', [\App\Controllers\EmployeeController::class, 'index']);
        $group->get('/employees/create', [\App\Controllers\EmployeeController::class, 'create']);
        $group->post('/employees', [\App\Controllers\EmployeeController::class, 'store']);
        $group->get('/employees/{id}', [\App\Controllers\EmployeeController::class, 'show']);
        $group->get('/employees/{id}/edit', [\App\Controllers\EmployeeController::class, 'edit']);
        $group->post('/employees/{id}', [\App\Controllers\EmployeeController::class, 'update']);
        $group->post('/employees/{id}/delete', [\App\Controllers\EmployeeController::class, 'destroy']);

        // ─── Certificates (Certifikat) ───────────────────────────
        $group->get('/certificates', [\App\Controllers\CertificateController::class, 'index']);
        $group->get('/certificates/create', [\App\Controllers\CertificateController::class, 'create']);
        $group->get('/certificates/expiring', [\App\Controllers\CertificateController::class, 'expiring']);
        $group->get('/certificates/expired', [\App\Controllers\CertificateController::class, 'expired']);
        $group->post('/certificates', [\App\Controllers\CertificateController::class, 'store']);
        $group->get('/certificates/{id}/edit', [\App\Controllers\CertificateController::class, 'edit']);
        $group->get('/certificates/{id}', [\App\Controllers\CertificateController::class, 'show']);
        $group->post('/certificates/{id}', [\App\Controllers\CertificateController::class, 'update']);
        $group->post('/certificates/{id}/delete', [\App\Controllers\CertificateController::class, 'destroy']);
        $group->post('/certificates/{id}/renew', [\App\Controllers\CertificateController::class, 'renew']);

        // ─── Inventory (Lager) ───────────────────────────────────
        $group->get('/inventory', [\App\Controllers\InventoryController::class, 'index']);

        // Warehouses
        $group->get('/inventory/warehouses', [\App\Modules\Inventory\Controllers\InventoryWarehouseController::class, 'index']);
        $group->get('/inventory/warehouses/create', [\App\Modules\Inventory\Controllers\InventoryWarehouseController::class, 'create']);
        $group->post('/inventory/warehouses', [\App\Modules\Inventory\Controllers\InventoryWarehouseController::class, 'store']);
        $group->get('/inventory/warehouses/{id}/edit', [\App\Modules\Inventory\Controllers\InventoryWarehouseController::class, 'edit']);
        $group->post('/inventory/warehouses/{id}', [\App\Modules\Inventory\Controllers\InventoryWarehouseController::class, 'update']);
        $group->post('/inventory/warehouses/{id}/delete', [\App\Modules\Inventory\Controllers\InventoryWarehouseController::class, 'delete']);

        // Transactions
        $group->get('/inventory/transactions', [\App\Modules\Inventory\Controllers\InventoryTransactionController::class, 'index']);
        $group->get('/inventory/transactions/create', [\App\Modules\Inventory\Controllers\InventoryTransactionController::class, 'create']);
        $group->post('/inventory/transactions', [\App\Modules\Inventory\Controllers\InventoryTransactionController::class, 'store']);
        $group->get('/inventory/transactions/{id}', [\App\Modules\Inventory\Controllers\InventoryTransactionController::class, 'show']);

        // Receiving
        $group->get('/inventory/receiving', [\App\Modules\Inventory\Controllers\InventoryReceivingController::class, 'index']);
        $group->get('/inventory/receiving/{poId}', [\App\Modules\Inventory\Controllers\InventoryReceivingController::class, 'show']);
        $group->post('/inventory/receiving/{poId}', [\App\Modules\Inventory\Controllers\InventoryReceivingController::class, 'store']);

        // Issues
        $group->get('/inventory/issues', [\App\Controllers\InventoryController::class, 'issueIndex']);
        $group->get('/inventory/issues/create', [\App\Controllers\InventoryController::class, 'createIssue']);
        $group->post('/inventory/issues', [\App\Controllers\InventoryController::class, 'storeIssue']);

        // Stocktaking
        $group->get('/inventory/stocktaking', [\App\Controllers\InventoryController::class, 'stocktakingIndex']);
        $group->get('/inventory/stocktaking/create', [\App\Controllers\InventoryController::class, 'createStocktaking']);
        $group->post('/inventory/stocktaking', [\App\Controllers\InventoryController::class, 'storeStocktaking']);
        $group->get('/inventory/stocktaking/{id}', [\App\Controllers\InventoryController::class, 'showStocktaking']);
        $group->post('/inventory/stocktaking/{id}/count', [\App\Controllers\InventoryController::class, 'storeCount']);
        $group->post('/inventory/stocktaking/{id}/approve', [\App\Controllers\InventoryController::class, 'approveStocktaking']);

        $group->get('/inventory/{id}', [\App\Controllers\InventoryController::class, 'show']);

        // ─── My Page (Min Sida) ──────────────────────────────────
        $group->get('/my-page', [\App\Controllers\MyPageController::class, 'index']);
        $group->get('/my-page/edit', [\App\Controllers\MyPageController::class, 'edit']);
        $group->post('/my-page', [\App\Controllers\MyPageController::class, 'update']);
        $group->get('/my-page/calendar-events', [\App\Controllers\MyPageController::class, 'calendarEvents']);
        $group->get('/my-page/payslips', [\App\Controllers\MyPageController::class, 'payslips']);
        $group->get('/my-page/payslips/{id}', [\App\Controllers\MyPageController::class, 'showPayslip']);
        $group->get('/my-page/contract', [\App\Controllers\MyPageController::class, 'contract']);
        $group->get('/my-page/tickets', [\App\Controllers\MyPageController::class, 'tickets']);

        // ─── Production (Produktion) ─────────────────────────────────────────
        $group->get('/production', [\App\Controllers\ProductionController::class, 'index']);
        $group->get('/production/lines', [\App\Controllers\ProductionController::class, 'lines']);
        $group->get('/production/lines/create', [\App\Controllers\ProductionController::class, 'createLine']);
        $group->post('/production/lines', [\App\Controllers\ProductionController::class, 'storeLine']);
        $group->get('/production/lines/{id}', [\App\Controllers\ProductionController::class, 'showLine']);
        $group->get('/production/lines/{id}/edit', [\App\Controllers\ProductionController::class, 'editLine']);
        $group->post('/production/lines/{id}', [\App\Controllers\ProductionController::class, 'updateLine']);
        $group->post('/production/lines/{id}/delete', [\App\Controllers\ProductionController::class, 'deleteLine']);
        $group->get('/production/products', [\App\Controllers\ProductionController::class, 'products']);
        $group->get('/production/products/create', [\App\Controllers\ProductionController::class, 'createProduct']);
        $group->post('/production/products', [\App\Controllers\ProductionController::class, 'storeProduct']);
        $group->get('/production/products/{id}', [\App\Controllers\ProductionController::class, 'showProduct']);
        $group->get('/production/products/{id}/edit', [\App\Controllers\ProductionController::class, 'editProduct']);
        $group->post('/production/products/{id}', [\App\Controllers\ProductionController::class, 'updateProduct']);
        $group->post('/production/products/{id}/delete', [\App\Controllers\ProductionController::class, 'deleteProduct']);
        $group->get('/production/orders', [\App\Controllers\ProductionController::class, 'orders']);
        $group->get('/production/orders/create', [\App\Controllers\ProductionController::class, 'createOrder']);
        $group->post('/production/orders', [\App\Controllers\ProductionController::class, 'storeOrder']);
        $group->get('/production/orders/{id}', [\App\Controllers\ProductionController::class, 'showOrder']);
        $group->get('/production/orders/{id}/edit', [\App\Controllers\ProductionController::class, 'editOrder']);
        $group->post('/production/orders/{id}', [\App\Controllers\ProductionController::class, 'updateOrder']);
        $group->post('/production/orders/{id}/status', [\App\Controllers\ProductionController::class, 'updateOrderStatus']);
        $group->post('/production/orders/{id}/delete', [\App\Controllers\ProductionController::class, 'deleteOrder']);
        $group->get('/production/stock', [\App\Controllers\ProductionController::class, 'stock']);
        $group->get('/production/stock/manage', [\App\Controllers\ProductionController::class, 'manageStock']);
        $group->get('/production/stock/create', [\App\Controllers\ProductionController::class, 'createStockEntry']);
        $group->post('/production/stock', [\App\Controllers\ProductionController::class, 'storeStockEntry']);
        $group->get('/production/stock/move', [\App\Controllers\ProductionController::class, 'moveStock']);
        $group->post('/production/stock/move', [\App\Controllers\ProductionController::class, 'storeMoveStock']);
        $group->post('/production/stock/{id}/delete', [\App\Controllers\ProductionController::class, 'deleteStockEntry']);

        // ─── Sales ───────────────────────────────────────────────────────────
        $group->get('/sales', [\App\Controllers\SalesController::class, 'index']);
        $group->get('/sales/quotes', [\App\Controllers\SalesController::class, 'quotes']);
        $group->get('/sales/quotes/create', [\App\Controllers\SalesController::class, 'createQuote']);
        $group->post('/sales/quotes', [\App\Controllers\SalesController::class, 'storeQuote']);
        $group->get('/sales/quotes/accepted', [\App\Controllers\SalesController::class, 'acceptedQuotes']);
        $group->get('/sales/quotes/history', [\App\Controllers\SalesController::class, 'historyQuotes']);
        $group->get('/sales/quotes/templates', [\App\Controllers\SalesController::class, 'quoteTemplates']);
        $group->get('/sales/quotes/templates/create', [\App\Controllers\SalesController::class, 'createQuoteTemplate']);
        $group->post('/sales/quotes/templates', [\App\Controllers\SalesController::class, 'storeQuoteTemplate']);
        $group->get('/sales/quotes/templates/{id}/edit', [\App\Controllers\SalesController::class, 'editQuoteTemplate']);
        $group->post('/sales/quotes/templates/{id}', [\App\Controllers\SalesController::class, 'updateQuoteTemplate']);
        $group->post('/sales/quotes/templates/{id}/delete', [\App\Controllers\SalesController::class, 'deleteQuoteTemplate']);
        $group->get('/sales/quotes/templates/{id}/use', [\App\Controllers\SalesController::class, 'useTemplate']);
        $group->get('/sales/quotes/{id}', [\App\Controllers\SalesController::class, 'showQuote']);
        $group->get('/sales/quotes/{id}/edit', [\App\Controllers\SalesController::class, 'editQuote']);
        $group->post('/sales/quotes/{id}', [\App\Controllers\SalesController::class, 'updateQuote']);
        $group->post('/sales/quotes/{id}/delete', [\App\Controllers\SalesController::class, 'deleteQuote']);
        $group->post('/sales/quotes/{id}/convert', [\App\Controllers\SalesController::class, 'convertQuote']);
        $group->get('/sales/quotes/{id}/pdf', [\App\Controllers\SalesController::class, 'quotePreviewPdf']);
        $group->post('/sales/quotes/{id}/send', [\App\Controllers\SalesController::class, 'sendQuoteEmail']);
        $group->post('/sales/quotes/{id}/lines', [\App\Controllers\SalesController::class, 'addQuoteLine']);
        $group->post('/sales/quotes/{id}/lines/{lineId}/delete', [\App\Controllers\SalesController::class, 'removeQuoteLine']);
        $group->get('/sales/orders', [\App\Controllers\SalesController::class, 'orders']);
        $group->get('/sales/orders/create', [\App\Controllers\SalesController::class, 'createOrder']);
        $group->post('/sales/orders', [\App\Controllers\SalesController::class, 'storeOrder']);
        $group->get('/sales/orders/{id}', [\App\Controllers\SalesController::class, 'showOrder']);
        $group->get('/sales/orders/{id}/edit', [\App\Controllers\SalesController::class, 'editOrder']);
        $group->post('/sales/orders/{id}', [\App\Controllers\SalesController::class, 'updateOrder']);
        $group->post('/sales/orders/{id}/status', [\App\Controllers\SalesController::class, 'updateOrderStatus']);
        $group->post('/sales/orders/{id}/delete', [\App\Controllers\SalesController::class, 'deleteOrder']);
        $group->get('/sales/pricing', [\App\Controllers\SalesController::class, 'pricing']);
        $group->get('/sales/pricing/manage', [\App\Controllers\SalesController::class, 'managePricing']);
        $group->get('/sales/pricing/create', [\App\Controllers\SalesController::class, 'createPriceList']);
        $group->post('/sales/pricing', [\App\Controllers\SalesController::class, 'storePriceList']);
        $group->get('/sales/pricing/{id}', [\App\Controllers\SalesController::class, 'showPriceList']);
        $group->get('/sales/pricing/{id}/edit', [\App\Controllers\SalesController::class, 'editPriceList']);
        $group->post('/sales/pricing/{id}', [\App\Controllers\SalesController::class, 'updatePriceList']);
        $group->post('/sales/pricing/{id}/delete', [\App\Controllers\SalesController::class, 'deletePriceList']);
        $group->post('/sales/pricing/{id}/items', [\App\Controllers\SalesController::class, 'addPriceListItem']);
        $group->post('/sales/pricing/{id}/items/{itemId}/delete', [\App\Controllers\SalesController::class, 'removePriceListItem']);

        // ─── Projects (Projekt) ──────────────────────────────────────────────
        $group->get('/projects', [\App\Controllers\ProjectController::class, 'index']);
        $group->get('/projects/create', [\App\Controllers\ProjectController::class, 'create']);
        $group->post('/projects', [\App\Controllers\ProjectController::class, 'store']);
        $group->get('/projects/{id}', [\App\Controllers\ProjectController::class, 'show']);
        $group->get('/projects/{id}/edit', [\App\Controllers\ProjectController::class, 'edit']);
        $group->post('/projects/{id}', [\App\Controllers\ProjectController::class, 'update']);
        $group->post('/projects/{id}/delete', [\App\Controllers\ProjectController::class, 'destroy']);
        $group->post('/projects/{id}/tasks', [\App\Controllers\ProjectController::class, 'addTask']);
        $group->post('/projects/{id}/tasks/{taskId}', [\App\Controllers\ProjectController::class, 'updateTask']);
        $group->post('/projects/{id}/tasks/{taskId}/delete', [\App\Controllers\ProjectController::class, 'deleteTask']);
        $group->post('/projects/{id}/budget', [\App\Controllers\ProjectController::class, 'addBudgetLine']);
        $group->post('/projects/{id}/budget/{lineId}/delete', [\App\Controllers\ProjectController::class, 'deleteBudgetLine']);
        $group->post('/projects/{id}/stakeholders', [\App\Controllers\ProjectController::class, 'addStakeholder']);
        $group->post('/projects/{id}/stakeholders/{stakeholderId}/delete', [\App\Controllers\ProjectController::class, 'deleteStakeholder']);
        $group->post('/projects/{id}/purchase-orders', [\App\Controllers\ProjectController::class, 'linkPurchaseOrder']);
        $group->post('/projects/{id}/purchase-orders/{linkId}/delete', [\App\Controllers\ProjectController::class, 'unlinkPurchaseOrder']);
        $group->get('/projects/{id}/report', [\App\Controllers\ProjectController::class, 'report']);
        $group->get('/projects/{id}/kanban', [\App\Controllers\ProjectController::class, 'kanban']);
        $group->post('/projects/{id}/tasks/{taskId}/status', [\App\Controllers\ProjectController::class, 'updateTaskStatus']);
        $group->post('/projects/{id}/costs', [\App\Controllers\ProjectController::class, 'addCost']);
        $group->post('/projects/{id}/costs/{costId}/delete', [\App\Controllers\ProjectController::class, 'deleteCost']);

        // ─── HR: Payroll (Lönehantering) ─────────────────────────────────────
        $group->get('/hr/payroll', [\App\Controllers\PayrollController::class, 'index']);
        $group->get('/hr/payroll/periods/create', [\App\Controllers\PayrollController::class, 'createPeriod']);
        $group->post('/hr/payroll/periods', [\App\Controllers\PayrollController::class, 'storePeriod']);
        $group->get('/hr/payroll/periods/{id}/edit', [\App\Controllers\PayrollController::class, 'editPeriod']);
        $group->post('/hr/payroll/periods/{id}', [\App\Controllers\PayrollController::class, 'updatePeriod']);
        $group->post('/hr/payroll/periods/{id}/delete', [\App\Controllers\PayrollController::class, 'deletePeriod']);
        $group->post('/hr/payroll/periods/{id}/close', [\App\Controllers\PayrollController::class, 'closePeriod']);
        $group->post('/hr/payroll/periods/{id}/generate', [\App\Controllers\PayrollController::class, 'generatePayslips']);
        $group->get('/hr/payroll/periods/{id}/payslips/create', [\App\Controllers\PayrollController::class, 'createPayslip']);
        $group->post('/hr/payroll/periods/{id}/payslips', [\App\Controllers\PayrollController::class, 'storePayslip']);
        $group->get('/hr/payroll/periods/{id}', [\App\Controllers\PayrollController::class, 'showPeriod']);
        $group->get('/hr/payroll/payslips/{slipId}/edit', [\App\Controllers\PayrollController::class, 'editPayslip']);
        $group->post('/hr/payroll/payslips/{slipId}', [\App\Controllers\PayrollController::class, 'updatePayslip']);
        $group->post('/hr/payroll/payslips/{slipId}/delete', [\App\Controllers\PayrollController::class, 'deletePayslip']);
        $group->get('/hr/payroll/payslips/{slipId}/print', [\App\Controllers\PayrollController::class, 'printPayslip']);
        $group->get('/hr/payroll/payslips/{slipId}', [\App\Controllers\PayrollController::class, 'showPayslip']);

        // ─── HR: Attendance (Närvaro/Frånvaro) ──────────────────────────────
        $group->get('/hr/attendance', [\App\Controllers\AttendanceController::class, 'index']);
        $group->get('/hr/attendance/create', [\App\Controllers\AttendanceController::class, 'create']);
        $group->post('/hr/attendance', [\App\Controllers\AttendanceController::class, 'store']);
        $group->get('/hr/attendance/balances', [\App\Controllers\AttendanceController::class, 'balances']);

        // ─── HR: Training (Utbildningar) ─────────────────────────────────────
        $group->get('/hr/training', [\App\Controllers\TrainingController::class, 'index']);
        $group->get('/hr/training/courses/create', [\App\Controllers\TrainingController::class, 'createCourse']);
        $group->post('/hr/training/courses', [\App\Controllers\TrainingController::class, 'storeCourse']);
        $group->get('/hr/training/courses/{id}/edit', [\App\Controllers\TrainingController::class, 'editCourse']);
        $group->post('/hr/training/courses/{id}', [\App\Controllers\TrainingController::class, 'updateCourse']);
        $group->post('/hr/training/courses/{id}/delete', [\App\Controllers\TrainingController::class, 'deleteCourse']);
        $group->get('/hr/training/courses/{id}', [\App\Controllers\TrainingController::class, 'showCourse']);
        $group->get('/hr/training/sessions/create', [\App\Controllers\TrainingController::class, 'createSession']);
        $group->post('/hr/training/sessions', [\App\Controllers\TrainingController::class, 'storeSession']);
        $group->get('/hr/training/sessions/{id}/edit', [\App\Controllers\TrainingController::class, 'editSession']);
        $group->post('/hr/training/sessions/{id}', [\App\Controllers\TrainingController::class, 'updateSession']);
        $group->post('/hr/training/sessions/{id}/delete', [\App\Controllers\TrainingController::class, 'deleteSession']);
        $group->post('/hr/training/sessions/{id}/participants', [\App\Controllers\TrainingController::class, 'addParticipant']);
        $group->post('/hr/training/sessions/{id}/participants/{participantId}/remove', [\App\Controllers\TrainingController::class, 'removeParticipant']);
        $group->post('/hr/training/sessions/{id}/participants/{participantId}/complete', [\App\Controllers\TrainingController::class, 'completeParticipant']);
        $group->get('/hr/training/sessions/{id}', [\App\Controllers\TrainingController::class, 'showSession']);
        $group->get('/hr/training/sessions', [\App\Controllers\TrainingController::class, 'sessions']);
        $group->get('/hr/training/participants', [\App\Controllers\TrainingController::class, 'participants']);

        // ─── HR: Recruitment (Rekrytering) ───────────────────────────────────
        $group->get('/hr/recruitment', [\App\Controllers\RecruitmentController::class, 'index']);
        $group->get('/hr/recruitment/positions/create', [\App\Controllers\RecruitmentController::class, 'createPosition']);
        $group->post('/hr/recruitment/positions', [\App\Controllers\RecruitmentController::class, 'storePosition']);
        $group->get('/hr/recruitment/positions/{id}/edit', [\App\Controllers\RecruitmentController::class, 'editPosition']);
        $group->post('/hr/recruitment/positions/{id}', [\App\Controllers\RecruitmentController::class, 'updatePosition']);
        $group->post('/hr/recruitment/positions/{id}/delete', [\App\Controllers\RecruitmentController::class, 'deletePosition']);
        $group->get('/hr/recruitment/positions/{id}/applicants/create', [\App\Controllers\RecruitmentController::class, 'createApplicant']);
        $group->post('/hr/recruitment/positions/{id}/applicants', [\App\Controllers\RecruitmentController::class, 'storeApplicant']);
        $group->get('/hr/recruitment/positions/{id}', [\App\Controllers\RecruitmentController::class, 'showPosition']);
        $group->get('/hr/recruitment/applicants', [\App\Controllers\RecruitmentController::class, 'applicants']);
        $group->get('/hr/recruitment/applicants/{applicantId}/edit', [\App\Controllers\RecruitmentController::class, 'editApplicant']);
        $group->post('/hr/recruitment/applicants/{applicantId}', [\App\Controllers\RecruitmentController::class, 'updateApplicant']);
        $group->post('/hr/recruitment/applicants/{applicantId}/delete', [\App\Controllers\RecruitmentController::class, 'deleteApplicant']);
        $group->post('/hr/recruitment/applicants/{applicantId}/status', [\App\Controllers\RecruitmentController::class, 'updateApplicantStatus']);
        $group->get('/hr/recruitment/applicants/{applicantId}/convert', [\App\Controllers\RecruitmentController::class, 'convertApplicant']);
        $group->get('/hr/recruitment/applicants/{applicantId}', [\App\Controllers\RecruitmentController::class, 'showApplicant']);

        // ─── HR: Expenses (Reseräkningar) ────────────────────────────────────
        $group->get('/hr/expenses', [\App\Controllers\ExpenseController::class, 'index']);
        $group->get('/hr/expenses/create', [\App\Controllers\ExpenseController::class, 'create']);
        $group->post('/hr/expenses', [\App\Controllers\ExpenseController::class, 'store']);
        $group->get('/hr/expenses/{id}', [\App\Controllers\ExpenseController::class, 'show']);
        $group->get('/hr/expenses/{id}/edit', [\App\Controllers\ExpenseController::class, 'edit']);
        $group->post('/hr/expenses/{id}', [\App\Controllers\ExpenseController::class, 'update']);
        $group->post('/hr/expenses/{id}/delete', [\App\Controllers\ExpenseController::class, 'delete']);
        $group->post('/hr/expenses/{id}/submit', [\App\Controllers\ExpenseController::class, 'submit']);
        $group->post('/hr/expenses/{id}/approve', [\App\Controllers\ExpenseController::class, 'approve']);
        $group->post('/hr/expenses/{id}/reject', [\App\Controllers\ExpenseController::class, 'reject']);
        $group->post('/hr/expenses/{id}/lines', [\App\Controllers\ExpenseController::class, 'addLine']);
        $group->post('/hr/expenses/{id}/lines/{lineId}/delete', [\App\Controllers\ExpenseController::class, 'removeLine']);

        // ─── ObjektNavigator ─────────────────────────────────────────────────
        $group->get('/objects/tree', [\App\Controllers\ObjectNavigatorController::class, 'tree']);
        $group->get('/objects/search', [\App\Controllers\ObjectNavigatorController::class, 'search']);
        $group->get('/objects', [\App\Controllers\ObjectNavigatorController::class, 'index']);
        $group->post('/objects/sync', [\App\Controllers\ObjectNavigatorController::class, 'sync']);
        $group->get('/objects/{type}/{id}/children', [\App\Controllers\ObjectNavigatorController::class, 'children']);
        $group->get('/objects/{type}/{id}', [\App\Controllers\ObjectNavigatorController::class, 'show']);

        // ─── CS (Customer Service) ───────────────────────────────────────────
        $group->get('/cs', [\App\Controllers\CustomerServiceController::class, 'index']);
        $group->get('/cs/tickets', [\App\Controllers\CustomerServiceController::class, 'tickets']);
        $group->get('/cs/tickets/create', [\App\Controllers\CustomerServiceController::class, 'createTicket']);
        $group->post('/cs/tickets', [\App\Controllers\CustomerServiceController::class, 'storeTicket']);
        $group->get('/cs/tickets/my', [\App\Controllers\CustomerServiceController::class, 'myTickets']);
        $group->get('/cs/tickets/{id}', [\App\Controllers\CustomerServiceController::class, 'showTicket']);
        $group->get('/cs/tickets/{id}/edit', [\App\Controllers\CustomerServiceController::class, 'editTicket']);
        $group->post('/cs/tickets/{id}', [\App\Controllers\CustomerServiceController::class, 'updateTicket']);
        $group->post('/cs/tickets/{id}/status', [\App\Controllers\CustomerServiceController::class, 'updateTicketStatus']);
        $group->post('/cs/tickets/{id}/comments', [\App\Controllers\CustomerServiceController::class, 'addComment']);
        $group->post('/cs/tickets/{id}/delete', [\App\Controllers\CustomerServiceController::class, 'deleteTicket']);

        // ─── Transport ───────────────────────────────────────────────────────
        $group->get('/transport', [\App\Controllers\TransportController::class, 'index']);
        $group->get('/transport/orders', [\App\Controllers\TransportController::class, 'orders']);
        $group->get('/transport/orders/create', [\App\Controllers\TransportController::class, 'createOrder']);
        $group->post('/transport/orders', [\App\Controllers\TransportController::class, 'storeOrder']);
        $group->get('/transport/orders/{id}', [\App\Controllers\TransportController::class, 'showOrder']);
        $group->get('/transport/orders/{id}/edit', [\App\Controllers\TransportController::class, 'editOrder']);
        $group->post('/transport/orders/{id}', [\App\Controllers\TransportController::class, 'updateOrder']);
        $group->post('/transport/orders/{id}/status', [\App\Controllers\TransportController::class, 'updateOrderStatus']);
        $group->post('/transport/orders/{id}/delete', [\App\Controllers\TransportController::class, 'deleteOrder']);
        $group->get('/transport/carriers', [\App\Controllers\TransportController::class, 'carriers']);
        $group->get('/transport/carriers/create', [\App\Controllers\TransportController::class, 'createCarrier']);
        $group->post('/transport/carriers/sync-supplier', [\App\Controllers\TransportController::class, 'syncFromSupplier']);
        $group->post('/transport/carriers', [\App\Controllers\TransportController::class, 'storeCarrier']);
        $group->get('/transport/carriers/{id}/edit', [\App\Controllers\TransportController::class, 'editCarrier']);
        $group->post('/transport/carriers/{id}', [\App\Controllers\TransportController::class, 'updateCarrier']);
        $group->post('/transport/carriers/{id}/delete', [\App\Controllers\TransportController::class, 'deleteCarrier']);

        // ─── Reports (Rapporter) ─────────────────────────────────────────────
        $group->get('/reports', [\App\Controllers\ReportController::class, 'index']);
        $group->get('/reports/maintenance', [\App\Controllers\ReportController::class, 'maintenance']);
        $group->get('/reports/inventory', [\App\Controllers\ReportController::class, 'inventory']);
        $group->get('/reports/purchasing', [\App\Controllers\ReportController::class, 'purchasing']);
        $group->get('/reports/finance', [\App\Controllers\ReportController::class, 'finance']);
        $group->get('/reports/safety', [\App\Controllers\ReportController::class, 'safety']);
        $group->get('/reports/production', [\App\Controllers\ReportController::class, 'production']);
        $group->get('/reports/sales', [\App\Controllers\ReportController::class, 'sales']);
        $group->get('/reports/hr', [\App\Controllers\ReportController::class, 'hr']);
        $group->get('/reports/projects', [\App\Controllers\ReportController::class, 'projects']);
        $group->get('/reports/cs', [\App\Controllers\ReportController::class, 'cs']);
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

        $group->get('/roles', [\App\Controllers\AdminController::class, 'roles']);
        $group->get('/roles/create', [\App\Controllers\AdminController::class, 'createRole']);
        $group->post('/roles', [\App\Controllers\AdminController::class, 'storeRole']);
        $group->get('/roles/{id}/edit', [\App\Controllers\AdminController::class, 'editRole']);
        $group->post('/roles/{id}', [\App\Controllers\AdminController::class, 'updateRole']);
        $group->post('/roles/{id}/delete', [\App\Controllers\AdminController::class, 'deleteRole']);

        $group->get('/settings', [\App\Controllers\AdminController::class, 'settings']);
        $group->post('/settings', [\App\Controllers\AdminController::class, 'updateSettings']);
        $group->get('/modules', [\App\Controllers\AdminController::class, 'modules']);
        $group->post('/modules/{id}/toggle', [\App\Controllers\AdminController::class, 'toggleModule']);
        $group->get('/site', [\App\Controllers\AdminController::class, 'siteSettings']);
        $group->post('/site', [\App\Controllers\AdminController::class, 'updateSiteSettings']);
        $group->get('/audit-log', [\App\Controllers\AdminController::class, 'auditLog']);
        $group->post('/audit-log/clear', [\App\Controllers\AdminController::class, 'clearAuditLog']);
    })->add(new CsrfMiddleware())->add(new \App\Middleware\RoleMiddleware(minLevel: 7))->add(new AuthMiddleware());

    // SaaS Admin routes — require role_level >= 9
    $app->group('/saas-admin', function (RouteCollectorProxy $group) {
        $group->get('', [\App\Controllers\SaasAdminController::class, 'index']);

        $group->get('/plans', [\App\Controllers\SaasAdminController::class, 'plans']);
        $group->get('/plans/create', [\App\Controllers\SaasAdminController::class, 'createPlan']);
        $group->post('/plans', [\App\Controllers\SaasAdminController::class, 'storePlan']);
        $group->get('/plans/{id}/edit', [\App\Controllers\SaasAdminController::class, 'editPlan']);
        $group->post('/plans/{id}', [\App\Controllers\SaasAdminController::class, 'updatePlan']);
        $group->post('/plans/{id}/delete', [\App\Controllers\SaasAdminController::class, 'deletePlan']);

        $group->get('/tenants/provision', [\App\Controllers\SaasAdminController::class, 'provision']);
        $group->post('/tenants/provision', [\App\Controllers\SaasAdminController::class, 'storeProvision']);

        $group->get('/tenants', [\App\Controllers\SaasAdminController::class, 'tenants']);
        $group->get('/tenants/create', [\App\Controllers\SaasAdminController::class, 'createTenant']);
        $group->post('/tenants', [\App\Controllers\SaasAdminController::class, 'storeTenant']);
        $group->get('/tenants/{id}', [\App\Controllers\SaasAdminController::class, 'showTenant']);
        $group->get('/tenants/{id}/edit', [\App\Controllers\SaasAdminController::class, 'editTenant']);
        $group->post('/tenants/{id}', [\App\Controllers\SaasAdminController::class, 'updateTenant']);
        $group->post('/tenants/{id}/delete', [\App\Controllers\SaasAdminController::class, 'deleteTenant']);
        $group->post('/tenants/{id}/modules/activate', [\App\Controllers\SaasAdminController::class, 'activateModule']);
        $group->post('/tenants/{id}/modules/deactivate', [\App\Controllers\SaasAdminController::class, 'deactivateModule']);
        $group->get('/tenants/{id}/history', [\App\Controllers\SaasAdminController::class, 'tenantHistory']);

        $group->get('/invoices/generate', [\App\Controllers\SaasAdminController::class, 'generateInvoices']);
        $group->post('/invoices/generate', [\App\Controllers\SaasAdminController::class, 'storeGeneratedInvoices']);
        $group->get('/invoices', [\App\Controllers\SaasAdminController::class, 'invoices']);
        $group->get('/invoices/create', [\App\Controllers\SaasAdminController::class, 'createInvoice']);
        $group->post('/invoices', [\App\Controllers\SaasAdminController::class, 'storeInvoice']);
        $group->get('/invoices/{id}', [\App\Controllers\SaasAdminController::class, 'showInvoice']);
        $group->get('/invoices/{id}/edit', [\App\Controllers\SaasAdminController::class, 'editInvoice']);
        $group->post('/invoices/{id}', [\App\Controllers\SaasAdminController::class, 'updateInvoice']);
        $group->post('/invoices/{id}/status', [\App\Controllers\SaasAdminController::class, 'updateInvoiceStatus']);

        $group->get('/support', [\App\Controllers\SaasAdminController::class, 'tickets']);
        $group->get('/support/{id}', [\App\Controllers\SaasAdminController::class, 'showTicket']);
        $group->post('/support/{id}/status', [\App\Controllers\SaasAdminController::class, 'updateTicketStatus']);
        $group->post('/support/{id}/comment', [\App\Controllers\SaasAdminController::class, 'addComment']);
    })->add(new CsrfMiddleware())->add(new \App\Middleware\RoleMiddleware(minLevel: 9))->add(new AuthMiddleware());

    $app->get('/api/tenant-info', [\App\Controllers\SaasAdminController::class, 'tenantInfo']);

    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->post('/login', [AuthApiController::class, 'login']);
        $group->post('/2fa/verify', [AuthApiController::class, 'verify2fa']);
    });

    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->post('/token/refresh', [AuthApiController::class, 'refresh']);
        $group->get('/me', [AuthApiController::class, 'me']);
    })->add(new JwtAuthMiddleware(new JwtService()));

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/notifications', [\App\Controllers\NotificationController::class, 'index']);
        $group->post('/notifications/{id}/read', [\App\Controllers\NotificationController::class, 'markRead']);
        $group->post('/notifications/read-all', [\App\Controllers\NotificationController::class, 'markAllRead']);
    })->add(new CsrfMiddleware())->add(new AuthMiddleware());

    $app->group('/api/notifications', function (RouteCollectorProxy $group) {
        $group->get('/unread-count', [\App\Controllers\NotificationController::class, 'unreadCount']);
        $group->get('/recent', [\App\Controllers\NotificationController::class, 'recent']);
    })->add(new AuthMiddleware());

    $app->group('/admin/integrations', function (RouteCollectorProxy $group) {
        $group->get('', [\App\Controllers\IntegrationController::class, 'index']);
        $group->post('/{slug}/test', [\App\Controllers\IntegrationController::class, 'test']);
    })->add(new CsrfMiddleware())->add(new \App\Middleware\RoleMiddleware(minLevel: 8))->add(new AuthMiddleware());
};
