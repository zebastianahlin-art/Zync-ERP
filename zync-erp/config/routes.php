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
        $group->get('/purchasing/requisitions/history', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Historiska Anmodan');
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
        $group->get('/purchasing/orders/history', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Historiska Inköpsordrar');
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
        $group->get('/purchasing/agreements/history', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Historiska Avtal');
        $group->get('/purchasing/agreements/templates', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Avtalsmallar');
        $group->get('/purchasing/audits', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Leverantörsaudit');
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
        $group->get('/finance/reports/kpi', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'KPI från Avdelningar');
        $group->get('/finance/reports/stocktaking', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Inventering Ekonomi');

        // ─── Maintenance (Underhåll) ──────────────────────────
        $group->get('/maintenance', [\App\Controllers\MaintenanceController::class, 'dashboard']);

        // Preventive maintenance placeholders
        $group->get('/maintenance/preventive', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Förebyggande Underhåll');
        $group->get('/maintenance/preventive/planner', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'FU Planerare');
        $group->get('/maintenance/preventive/rounds', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'FU Rondering');
        $group->get('/maintenance/ai-engineer', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'AI Ingenjören');

        // Equipment (Utrustning)
        $group->get('/equipment', [\App\Controllers\MaintenanceController::class, 'equipmentIndex']);
        $group->get('/equipment/create', [\App\Controllers\MaintenanceController::class, 'equipmentCreate']);
        $group->post('/equipment', [\App\Controllers\MaintenanceController::class, 'equipmentStore']);
        $group->get('/equipment/{id}', [\App\Controllers\MaintenanceController::class, 'equipmentShow']);
        $group->get('/equipment/{id}/edit', [\App\Controllers\MaintenanceController::class, 'equipmentEdit']);
        $group->post('/equipment/{id}', [\App\Controllers\MaintenanceController::class, 'equipmentUpdate']);
        $group->post('/equipment/{id}/delete', [\App\Controllers\MaintenanceController::class, 'equipmentDelete']);

        // Machines (Maskiner)
        $group->get('/machines', [\App\Controllers\MaintenanceController::class, 'machineIndex']);
        $group->get('/machines/create', [\App\Controllers\MaintenanceController::class, 'machineCreate']);
        $group->post('/machines', [\App\Controllers\MaintenanceController::class, 'machineStore']);
        $group->get('/machines/{id}', [\App\Controllers\MaintenanceController::class, 'machineShow']);
        $group->get('/machines/{id}/edit', [\App\Controllers\MaintenanceController::class, 'machineEdit']);
        $group->post('/machines/{id}', [\App\Controllers\MaintenanceController::class, 'machineUpdate']);
        $group->post('/machines/{id}/delete', [\App\Controllers\MaintenanceController::class, 'machineDelete']);

        // Fault Reports (Felanmälningar)
        $group->get('/maintenance/faults', [\App\Controllers\MaintenanceController::class, 'faultIndex']);
        $group->get('/maintenance/faults/create', [\App\Controllers\MaintenanceController::class, 'faultCreate']);
        $group->post('/maintenance/faults', [\App\Controllers\MaintenanceController::class, 'faultStore']);
        $group->get('/maintenance/faults/{id}', [\App\Controllers\MaintenanceController::class, 'faultShow']);
        $group->get('/maintenance/faults/{id}/edit', [\App\Controllers\MaintenanceController::class, 'faultEdit']);
        $group->post('/maintenance/faults/{id}', [\App\Controllers\MaintenanceController::class, 'faultUpdate']);
        $group->post('/maintenance/faults/{id}/delete', [\App\Controllers\MaintenanceController::class, 'faultDelete']);
        $group->post('/maintenance/faults/{id}/acknowledge', [\App\Controllers\MaintenanceController::class, 'faultAcknowledge']);
        $group->post('/maintenance/faults/{id}/assign', [\App\Controllers\MaintenanceController::class, 'faultAssign']);
        $group->post('/maintenance/faults/{id}/convert', [\App\Controllers\MaintenanceController::class, 'faultConvert']);

        // Work Orders (Arbetsordrar)
        $group->get('/maintenance/work-orders', [\App\Controllers\MaintenanceController::class, 'workOrderIndex']);
        $group->get('/maintenance/work-orders/create', [\App\Controllers\MaintenanceController::class, 'workOrderCreate']);
        $group->post('/maintenance/work-orders', [\App\Controllers\MaintenanceController::class, 'workOrderStore']);
        $group->get('/maintenance/work-orders/archive', [\App\Controllers\MaintenanceController::class, 'archiveIndex']);
        $group->get('/maintenance/work-orders/archive/{id}', [\App\Controllers\MaintenanceController::class, 'archiveShow']);
        $group->get('/maintenance/work-orders/{id}', [\App\Controllers\MaintenanceController::class, 'workOrderShow']);
        $group->get('/maintenance/work-orders/{id}/edit', [\App\Controllers\MaintenanceController::class, 'workOrderEdit']);
        $group->post('/maintenance/work-orders/{id}', [\App\Controllers\MaintenanceController::class, 'workOrderUpdate']);
        $group->post('/maintenance/work-orders/{id}/delete', [\App\Controllers\MaintenanceController::class, 'workOrderDelete']);
        $group->post('/maintenance/work-orders/{id}/start', [\App\Controllers\MaintenanceController::class, 'workOrderStart']);
        $group->post('/maintenance/work-orders/{id}/complete', [\App\Controllers\MaintenanceController::class, 'workOrderComplete']);
        $group->post('/maintenance/work-orders/{id}/time', [\App\Controllers\MaintenanceController::class, 'workOrderAddTime']);
        $group->post('/maintenance/work-orders/{id}/time/{entryId}/delete', [\App\Controllers\MaintenanceController::class, 'workOrderDeleteTime']);
        $group->post('/maintenance/work-orders/{id}/parts', [\App\Controllers\MaintenanceController::class, 'workOrderAddPart']);
        $group->post('/maintenance/work-orders/{id}/parts/{partId}/delete', [\App\Controllers\MaintenanceController::class, 'workOrderDeletePart']);
        $group->post('/maintenance/work-orders/{id}/approve', [\App\Controllers\MaintenanceController::class, 'workOrderApprove']);
        $group->post('/maintenance/work-orders/{id}/reject', [\App\Controllers\MaintenanceController::class, 'workOrderReject']);
        $group->post('/maintenance/work-orders/{id}/close', [\App\Controllers\MaintenanceController::class, 'workOrderClose']);
        $group->post('/maintenance/work-orders/{id}/approve-time/{entryId}', [\App\Controllers\MaintenanceController::class, 'workOrderApproveTime']);
        $group->post('/maintenance/work-orders/{id}/approve-part/{partId}', [\App\Controllers\MaintenanceController::class, 'workOrderApprovePart']);
        $group->post('/maintenance/work-orders/{id}/approve-all', [\App\Controllers\MaintenanceController::class, 'workOrderApproveAll']);

        // Supervisor (Arbetsledare)
        $group->get('/maintenance/supervisor', [\App\Controllers\MaintenanceController::class, 'supervisorDashboard']);
        $group->get('/maintenance/supervisor/unassigned', [\App\Controllers\MaintenanceController::class, 'supervisorUnassigned']);
        $group->get('/maintenance/supervisor/pending-approval', [\App\Controllers\MaintenanceController::class, 'supervisorPendingApproval']);
        $group->get('/maintenance/supervisor/my-team', [\App\Controllers\MaintenanceController::class, 'supervisorMyTeam']);
        $group->get('/maintenance/supervisor/statistics', [\App\Controllers\MaintenanceController::class, 'supervisorStatistics']);

        // Inspections (Besiktningar)
        $group->get('/maintenance/inspections', [\App\Controllers\MaintenanceController::class, 'inspectionIndex']);
        $group->get('/maintenance/inspections/create', [\App\Controllers\MaintenanceController::class, 'inspectionCreate']);
        $group->post('/maintenance/inspections', [\App\Controllers\MaintenanceController::class, 'inspectionStore']);
        $group->get('/maintenance/inspections/overdue', [\App\Controllers\MaintenanceController::class, 'inspectionOverdue']);
        $group->get('/maintenance/inspections/{id}', [\App\Controllers\MaintenanceController::class, 'inspectionShow']);
        $group->get('/maintenance/inspections/{id}/edit', [\App\Controllers\MaintenanceController::class, 'inspectionEdit']);
        $group->post('/maintenance/inspections/{id}', [\App\Controllers\MaintenanceController::class, 'inspectionUpdate']);
        $group->post('/maintenance/inspections/{id}/delete', [\App\Controllers\MaintenanceController::class, 'inspectionDelete']);
        $group->post('/maintenance/inspections/{id}/record', [\App\Controllers\MaintenanceController::class, 'inspectionRecord']);
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
        $group->get('/safety/audits/pending', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Ej Slutförda Åtgärder');
        $group->get('/safety/audits/completed', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Slutförda Åtgärder');
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
        $group->get('/safety/emergency/drills', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Nödlägesövningar Lista');
        $group->get('/safety/emergency/drills/create', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Skapa Nödlägesövning');
        $group->get('/safety/emergency/drills/templates', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Nödlägesövning Mallar');
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

        // ─── Employees (Personal) ────────────────────────────────
        $group->get('/employees', [\App\Controllers\EmployeeController::class, 'index']);
        $group->get('/employees/create', [\App\Controllers\EmployeeController::class, 'create']);
        $group->post('/employees', [\App\Controllers\EmployeeController::class, 'store']);
        $group->get('/employees/{id}/edit', [\App\Controllers\EmployeeController::class, 'edit']);
        $group->post('/employees/{id}', [\App\Controllers\EmployeeController::class, 'update']);
        $group->post('/employees/{id}/delete', [\App\Controllers\EmployeeController::class, 'destroy']);

        // ─── Certificates (Certifikat) ───────────────────────────
        $group->get('/certificates', [\App\Controllers\CertificateController::class, 'index']);
        $group->get('/certificates/create', [\App\Controllers\CertificateController::class, 'create']);
        $group->post('/certificates', [\App\Controllers\CertificateController::class, 'store']);
        $group->get('/certificates/{id}/edit', [\App\Controllers\CertificateController::class, 'edit']);
        $group->post('/certificates/{id}', [\App\Controllers\CertificateController::class, 'update']);
        $group->post('/certificates/{id}/delete', [\App\Controllers\CertificateController::class, 'destroy']);

        // ─── Inventory (Lager) ───────────────────────────────────
        $group->get('/inventory', [\App\Controllers\InventoryController::class, 'index']);
        $group->get('/inventory/transactions', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Lagertransaktionshistorik');
        $group->get('/inventory/order', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Beställ Lagerartiklar');
        $group->get('/inventory/receiving', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Inleverans');
        $group->get('/inventory/withdrawal', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Uttag av Lagerartiklar');
        $group->get('/inventory/stocktaking', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Inventering');
        $group->get('/inventory/{id}', [\App\Controllers\InventoryController::class, 'show']);

        // ─── My Page (Min Sida) ──────────────────────────────────
        $group->get('/my-page', [\App\Controllers\MyPageController::class, 'index']);
        $group->get('/my-page/edit', [\App\Controllers\MyPageController::class, 'edit']);
        $group->post('/my-page', [\App\Controllers\MyPageController::class, 'update']);

        // ─── Production (Produktion) ─────────────────────────────────────────
        $group->get('/production', [\App\Controllers\ProductionController::class, 'index']);
        $group->get('/production/lines', [\App\Controllers\ProductionController::class, 'lines']);
        $group->get('/production/lines/create', [\App\Controllers\ProductionController::class, 'createLine']);
        $group->post('/production/lines', [\App\Controllers\ProductionController::class, 'storeLine']);
        $group->get('/production/products', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Skapa Produkt');
        $group->get('/production/stock', [\App\Controllers\ProductionController::class, 'stock']);
        $group->get('/production/stock/manage', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Hantera Produktionslager');
        $group->get('/production/lines/{id}', [\App\Controllers\ProductionController::class, 'showLine']);
        $group->get('/production/lines/{id}/edit', [\App\Controllers\ProductionController::class, 'editLine']);
        $group->post('/production/lines/{id}', [\App\Controllers\ProductionController::class, 'updateLine']);
        $group->post('/production/lines/{id}/delete', [\App\Controllers\ProductionController::class, 'deleteLine']);
        $group->get('/production/orders', [\App\Controllers\ProductionController::class, 'orders']);
        $group->get('/production/stock', [\App\Controllers\ProductionController::class, 'stock']);

        // ─── Sales ───────────────────────────────────────────────────────────
        $group->get('/sales', [\App\Controllers\SalesController::class, 'index']);
        $group->get('/sales/quotes', [\App\Controllers\SalesController::class, 'quotes']);
        $group->get('/sales/quotes/create', [\App\Controllers\SalesController::class, 'createQuote']);
        $group->post('/sales/quotes', [\App\Controllers\SalesController::class, 'storeQuote']);
        $group->get('/sales/quotes/accepted', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Accepterade Offerter');
        $group->get('/sales/quotes/history', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Historiska Offerter');
        $group->get('/sales/quotes/templates', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Offertmallar');
        $group->get('/sales/quotes/{id}', [\App\Controllers\SalesController::class, 'showQuote']);
        $group->get('/sales/quotes/{id}/edit', [\App\Controllers\SalesController::class, 'editQuote']);
        $group->post('/sales/quotes/{id}', [\App\Controllers\SalesController::class, 'updateQuote']);
        $group->post('/sales/quotes/{id}/delete', [\App\Controllers\SalesController::class, 'deleteQuote']);
        $group->get('/sales/orders', [\App\Controllers\SalesController::class, 'orders']);
        $group->get('/sales/pricing', [\App\Controllers\SalesController::class, 'pricing']);
        $group->get('/sales/pricing/manage', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Hantera Prislistor');

        // ─── Projects (Projekt) ──────────────────────────────────────────────
        $group->get('/projects', [\App\Controllers\ProjectController::class, 'index']);
        $group->get('/projects/create', [\App\Controllers\ProjectController::class, 'create']);
        $group->post('/projects', [\App\Controllers\ProjectController::class, 'store']);
        $group->get('/projects/{id}', [\App\Controllers\ProjectController::class, 'show']);
        $group->get('/projects/{id}/edit', [\App\Controllers\ProjectController::class, 'edit']);
        $group->post('/projects/{id}', [\App\Controllers\ProjectController::class, 'update']);
        $group->post('/projects/{id}/delete', [\App\Controllers\ProjectController::class, 'destroy']);

        // ─── HR: Payroll (Lönehantering) ─────────────────────────────────────
        $group->get('/hr/payroll', [\App\Controllers\PayrollController::class, 'index']);
        $group->get('/hr/payroll/periods/create', [\App\Controllers\PayrollController::class, 'createPeriod']);
        $group->post('/hr/payroll/periods', [\App\Controllers\PayrollController::class, 'storePeriod']);
        $group->get('/hr/payroll/periods/{id}', [\App\Controllers\PayrollController::class, 'showPeriod']);

        // ─── HR: Attendance (Närvaro/Frånvaro) ──────────────────────────────
        $group->get('/hr/attendance', [\App\Controllers\AttendanceController::class, 'index']);
        $group->get('/hr/attendance/create', [\App\Controllers\AttendanceController::class, 'create']);
        $group->post('/hr/attendance', [\App\Controllers\AttendanceController::class, 'store']);
        $group->get('/hr/attendance/balances', [\App\Controllers\AttendanceController::class, 'balances']);

        // ─── HR: Training (Utbildningar) ─────────────────────────────────────
        $group->get('/hr/training', [\App\Controllers\TrainingController::class, 'index']);
        $group->get('/hr/training/courses/create', [\App\Controllers\TrainingController::class, 'createCourse']);
        $group->post('/hr/training/courses', [\App\Controllers\TrainingController::class, 'storeCourse']);
        $group->get('/hr/training/sessions', [\App\Controllers\TrainingController::class, 'sessions']);
        $group->get('/hr/training/participants', [\App\Controllers\TrainingController::class, 'participants']);

        // ─── HR: Recruitment (Rekrytering) ───────────────────────────────────
        $group->get('/hr/recruitment', [\App\Controllers\RecruitmentController::class, 'index']);
        $group->get('/hr/recruitment/positions/create', [\App\Controllers\RecruitmentController::class, 'createPosition']);
        $group->post('/hr/recruitment/positions', [\App\Controllers\RecruitmentController::class, 'storePosition']);
        $group->get('/hr/recruitment/positions/{id}', [\App\Controllers\RecruitmentController::class, 'showPosition']);
        $group->get('/hr/recruitment/applicants', [\App\Controllers\RecruitmentController::class, 'applicants']);

        // ─── HR: Expenses (Reseräkningar) ────────────────────────────────────
        $group->get('/hr/expenses', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Reseräkningar');

        // ─── ObjektNavigator ─────────────────────────────────────────────────
        $group->get('/objects', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'ObjektNavigator');
        $group->get('/objects/dashboard', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Objekt Dashboard');
        $group->get('/objects/manage', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Administrera Objekt');
        $group->get('/objects/inspection-required', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Besiktningspliktig Utrustning');

        // ─── CS & Transport ──────────────────────────────────────────────────
        $group->get('/cs', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Customer Service');
        $group->get('/transport', [\App\Controllers\PlaceholderController::class, 'comingSoon'])->setArgument('module', 'Transport');

        // ─── Reports (Rapporter) ─────────────────────────────────────────────
        $group->get('/reports', [\App\Controllers\ReportController::class, 'index']);
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

        // Role management
        $group->get('/roles', [\App\Controllers\AdminController::class, 'roles']);
        $group->get('/roles/create', [\App\Controllers\AdminController::class, 'createRole']);
        $group->post('/roles', [\App\Controllers\AdminController::class, 'storeRole']);
        $group->get('/roles/{id}/edit', [\App\Controllers\AdminController::class, 'editRole']);
        $group->post('/roles/{id}', [\App\Controllers\AdminController::class, 'updateRole']);
        $group->post('/roles/{id}/delete', [\App\Controllers\AdminController::class, 'deleteRole']);
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

