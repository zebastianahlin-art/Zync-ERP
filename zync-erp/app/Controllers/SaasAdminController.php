<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\SaasRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * SaasAdminController — SaaS platform administration.
 * Requires role level >= 9 (enforced via RoleMiddleware on the route group).
 */
class SaasAdminController extends Controller
{
    private SaasRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new SaasRepository();
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    /** GET /saas-admin */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'saas/index', [
            'title' => 'SaaS Admin – ZYNC ERP',
            'stats' => $this->repo->dashboardStats(),
            'plans' => $this->repo->allPlans(true),
        ]);
    }

    // ── Plans ─────────────────────────────────────────────────────────────────

    /** GET /saas-admin/plans */
    public function plans(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'saas/plans/index', [
            'title'        => 'Abonnemangsplaner – SaaS Admin – ZYNC ERP',
            'plans'        => $this->repo->allPlans(),
            'countByPlan'  => $this->repo->tenantCountByPlan(),
        ]);
    }

    /** GET /saas-admin/plans/create */
    public function createPlan(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'saas/plans/create', [
            'title'  => 'Ny plan – SaaS Admin – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /saas-admin/plans */
    public function storePlan(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = (array) $request->getParsedBody();
        $errors = $this->validatePlan($data);

        if (!empty($errors)) {
            return $this->render($response, 'saas/plans/create', [
                'title'  => 'Ny plan – SaaS Admin – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        try {
            $this->repo->createPlan($data);
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när planen skapades.');
            return $this->render($response, 'saas/plans/create', [
                'title'  => 'Ny plan – SaaS Admin – ZYNC ERP',
                'errors' => ['db' => $e->getMessage()],
                'old'    => $data,
            ]);
        }
        Flash::set('success', 'Planen har skapats.');
        return $this->redirect($response, '/saas-admin/plans');
    }

    /** GET /saas-admin/plans/{id}/edit */
    public function editPlan(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $plan = $this->repo->findPlan((int) $args['id']);
        if ($plan === null) {
            return $this->notFound($response, 'Planen hittades inte.');
        }
        return $this->render($response, 'saas/plans/edit', [
            'title'  => 'Redigera plan – SaaS Admin – ZYNC ERP',
            'plan'   => $plan,
            'errors' => [],
        ]);
    }

    /** POST /saas-admin/plans/{id} */
    public function updatePlan(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $plan = $this->repo->findPlan($id);
        if ($plan === null) {
            return $this->notFound($response, 'Planen hittades inte.');
        }

        $data   = (array) $request->getParsedBody();
        $errors = $this->validatePlan($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'saas/plans/edit', [
                'title'  => 'Redigera plan – SaaS Admin – ZYNC ERP',
                'plan'   => array_merge($plan, $data),
                'errors' => $errors,
            ]);
        }

        try {
            $this->repo->updatePlan($id, $data);
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när planen uppdaterades.');
            return $this->render($response, 'saas/plans/edit', [
                'title'  => 'Redigera plan – SaaS Admin – ZYNC ERP',
                'plan'   => array_merge($plan, $data),
                'errors' => ['db' => $e->getMessage()],
            ]);
        }
        Flash::set('success', 'Planen har uppdaterats.');
        return $this->redirect($response, '/saas-admin/plans');
    }

    /** POST /saas-admin/plans/{id}/delete */
    public function deletePlan(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        try {
            $this->repo->deletePlan($id);
            Flash::set('success', 'Planen har tagits bort.');
        } catch (\Throwable) {
            Flash::set('error', 'Ett fel uppstod när planen togs bort.');
        }
        return $this->redirect($response, '/saas-admin/plans');
    }

    // ── Tenant Provisioning ───────────────────────────────────────────────────

    /** GET /saas-admin/tenants/provision */
    public function provision(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'saas/tenants/provision', [
            'title'  => 'Skapa ny kund (Wizard) – SaaS Admin – ZYNC ERP',
            'plans'  => $this->repo->allPlans(true),
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /saas-admin/tenants/provision */
    public function storeProvision(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = (array) $request->getParsedBody();
        $errors = $this->validateTenant($data);

        // Auto-generate subdomain from company name if empty
        if (empty($data['subdomain']) && !empty($data['company_name'])) {
            $data['subdomain'] = $this->generateSubdomain((string) $data['company_name']);
        }

        // Auto-set trial_ends_at if status = trial
        if (($data['status'] ?? 'trial') === 'trial' && empty($data['trial_ends_at'])) {
            $data['trial_ends_at'] = date('Y-m-d', strtotime('+30 days'));
        }

        if (!empty($errors)) {
            return $this->render($response, 'saas/tenants/provision', [
                'title'  => 'Skapa ny kund (Wizard) – SaaS Admin – ZYNC ERP',
                'plans'  => $this->repo->allPlans(true),
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        try {
            $id = $this->repo->createTenant($data);

            // Activate default modules based on plan
            $plan = $this->repo->findPlanBySlug($data['plan'] ?? 'starter');
            if ($plan !== null && !empty($plan['included_modules'])) {
                $modules = is_string($plan['included_modules'])
                    ? json_decode($plan['included_modules'], true)
                    : $plan['included_modules'];
                foreach ((array) $modules as $slug) {
                    $this->repo->activateModule($id, (string) $slug);
                }
            }

            // Log history
            $this->repo->addTenantHistory($id, 'created', null, $data['status'] ?? 'trial', Auth::id(), 'Kund skapad via provisioning-wizard.');

        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när kunden skapades.');
            return $this->render($response, 'saas/tenants/provision', [
                'title'  => 'Skapa ny kund (Wizard) – SaaS Admin – ZYNC ERP',
                'plans'  => $this->repo->allPlans(true),
                'errors' => ['db' => $e->getMessage()],
                'old'    => $data,
            ]);
        }

        Flash::set('success', 'Kunden har skapats och moduler har aktiverats. Välkomstmeddelande (placeholder) skickat.');
        return $this->redirect($response, "/saas-admin/tenants/{$id}");
    }

    // ── Tenants ──────────────────────────────────────────────────────────────

    /** GET /saas-admin/tenants */
    public function tenants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $filters = [
            'status' => $query['status'] ?? '',
            'plan'   => $query['plan']   ?? '',
            'search' => $query['search'] ?? '',
        ];
        return $this->render($response, 'saas/tenants/index', [
            'title'   => 'Kunder – SaaS Admin – ZYNC ERP',
            'tenants' => $this->repo->allTenants($filters),
            'filters' => $filters,
        ]);
    }

    /** GET /saas-admin/tenants/create */
    public function createTenant(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'saas/tenants/create', [
            'title'  => 'Ny kund – SaaS Admin – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /saas-admin/tenants */
    public function storeTenant(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = (array) $request->getParsedBody();
        $errors = $this->validateTenant($data);

        if (!empty($errors)) {
            return $this->render($response, 'saas/tenants/create', [
                'title'  => 'Ny kund – SaaS Admin – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        try {
            $id = $this->repo->createTenant($data);
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när kunden skapades.');
            return $this->render($response, 'saas/tenants/create', [
                'title'  => 'Ny kund – SaaS Admin – ZYNC ERP',
                'errors' => ['db' => $e->getMessage()],
                'old'    => $data,
            ]);
        }
        Flash::set('success', 'Kunden har skapats.');
        return $this->redirect($response, "/saas-admin/tenants/{$id}");
    }

    /** GET /saas-admin/tenants/{id} */
    public function showTenant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tenant = $this->repo->findTenant((int) $args['id']);
        if ($tenant === null) {
            return $this->notFound($response, 'Kunden hittades inte.');
        }

        // All available modules from erp_modules table
        try {
            $allModules = \App\Core\Database::pdo()
                ->query('SELECT * FROM erp_modules ORDER BY sort_order ASC')
                ->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            $allModules = [];
        }

        $activeModuleSlugs = array_column(array_filter($tenant['modules'] ?? [], fn($m) => (int)($m['is_active'] ?? 0) === 1), 'module_slug');

        return $this->render($response, 'saas/tenants/show', [
            'title'               => htmlspecialchars($tenant['company_name'], ENT_QUOTES, 'UTF-8') . ' – SaaS Admin – ZYNC ERP',
            'tenant'              => $tenant,
            'all_modules'         => $allModules,
            'active_module_slugs' => $activeModuleSlugs,
        ]);
    }

    /** GET /saas-admin/tenants/{id}/edit */
    public function editTenant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tenant = $this->repo->findTenant((int) $args['id']);
        if ($tenant === null) {
            return $this->notFound($response, 'Kunden hittades inte.');
        }
        return $this->render($response, 'saas/tenants/edit', [
            'title'  => 'Redigera kund – SaaS Admin – ZYNC ERP',
            'tenant' => $tenant,
            'errors' => [],
        ]);
    }

    /** POST /saas-admin/tenants/{id} */
    public function updateTenant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $tenant = $this->repo->findTenant($id);
        if ($tenant === null) {
            return $this->notFound($response, 'Kunden hittades inte.');
        }

        $data      = (array) $request->getParsedBody();
        $oldStatus = $tenant['status'];
        $errors    = $this->validateTenant($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'saas/tenants/edit', [
                'title'  => 'Redigera kund – SaaS Admin – ZYNC ERP',
                'tenant' => array_merge($tenant, $data),
                'errors' => $errors,
            ]);
        }

        try {
            $this->repo->updateTenant($id, $data);

            // Log status change if status changed
            $newStatus = $data['status'] ?? $oldStatus;
            if ($newStatus !== $oldStatus) {
                $this->repo->addTenantHistory($id, 'status_change', $oldStatus, $newStatus, Auth::id());
            }
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när kunden uppdaterades.');
            return $this->render($response, 'saas/tenants/edit', [
                'title'  => 'Redigera kund – SaaS Admin – ZYNC ERP',
                'tenant' => array_merge($tenant, $data),
                'errors' => ['db' => $e->getMessage()],
            ]);
        }
        Flash::set('success', 'Kunden har uppdaterats.');
        return $this->redirect($response, "/saas-admin/tenants/{$id}");
    }

    /** POST /saas-admin/tenants/{id}/delete */
    public function deleteTenant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findTenant($id) !== null) {
            try {
                $this->repo->deleteTenant($id);
            } catch (\Throwable) {
                Flash::set('error', 'Ett fel uppstod när kunden togs bort.');
                return $this->redirect($response, '/saas-admin/tenants');
            }
            Flash::set('success', 'Kunden har tagits bort.');
        }
        return $this->redirect($response, '/saas-admin/tenants');
    }

    /** POST /saas-admin/tenants/{id}/modules/activate */
    public function activateModule(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $slug = trim((string) ($body['module_slug'] ?? ''));

        if ($slug !== '') {
            $this->repo->activateModule($id, $slug);
            $this->repo->addTenantHistory($id, 'module_activated', null, $slug, Auth::id());
            Flash::set('success', 'Modulen har aktiverats.');
        }
        return $this->redirect($response, "/saas-admin/tenants/{$id}");
    }

    /** POST /saas-admin/tenants/{id}/modules/deactivate */
    public function deactivateModule(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $slug = trim((string) ($body['module_slug'] ?? ''));

        if ($slug !== '') {
            $this->repo->deactivateModule($id, $slug);
            $this->repo->addTenantHistory($id, 'module_deactivated', $slug, null, Auth::id());
            Flash::set('success', 'Modulen har inaktiverats.');
        }
        return $this->redirect($response, "/saas-admin/tenants/{$id}");
    }

    /** GET /saas-admin/tenants/{id}/history */
    public function tenantHistory(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tenant = $this->repo->findTenant((int) $args['id']);
        if ($tenant === null) {
            return $this->notFound($response, 'Kunden hittades inte.');
        }
        return $this->render($response, 'saas/tenants/history', [
            'title'   => 'Historik – ' . htmlspecialchars($tenant['company_name'], ENT_QUOTES, 'UTF-8') . ' – SaaS Admin',
            'tenant'  => $tenant,
            'history' => $this->repo->tenantHistory((int) $args['id']),
        ]);
    }

    // ── Billing / Invoices ────────────────────────────────────────────────────

    /** GET /saas-admin/invoices */
    public function invoices(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $filters = ['status' => $query['status'] ?? ''];
        return $this->render($response, 'saas/invoices/index', [
            'title'    => 'Fakturering – SaaS Admin – ZYNC ERP',
            'invoices' => $this->repo->allInvoices($filters),
            'filters'  => $filters,
            'stats'    => $this->repo->invoiceStats(),
        ]);
    }

    /** GET /saas-admin/invoices/create */
    public function createInvoice(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query = $request->getQueryParams();
        return $this->render($response, 'saas/invoices/create', [
            'title'   => 'Ny faktura – SaaS Admin – ZYNC ERP',
            'tenants' => $this->repo->allTenants(),
            'errors'  => [],
            'old'     => ['tenant_id' => $query['tenant_id'] ?? ''],
        ]);
    }

    /** POST /saas-admin/invoices */
    public function storeInvoice(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = (array) $request->getParsedBody();
        $errors = $this->validateInvoice($data);

        if (!empty($errors)) {
            return $this->render($response, 'saas/invoices/create', [
                'title'   => 'Ny faktura – SaaS Admin – ZYNC ERP',
                'tenants' => $this->repo->allTenants(),
                'errors'  => $errors,
                'old'     => $data,
            ]);
        }

        try {
            $data['invoice_number'] = $this->repo->generateInvoiceNumber();
            $id = $this->repo->createInvoice($data);
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när fakturan skapades.');
            return $this->render($response, 'saas/invoices/create', [
                'title'   => 'Ny faktura – SaaS Admin – ZYNC ERP',
                'tenants' => $this->repo->allTenants(),
                'errors'  => ['db' => $e->getMessage()],
                'old'     => $data,
            ]);
        }
        Flash::set('success', 'Fakturan har skapats.');
        return $this->redirect($response, "/saas-admin/invoices/{$id}");
    }

    /** GET /saas-admin/invoices/{id} */
    public function showInvoice(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $invoice = $this->repo->findInvoice((int) $args['id']);
        if ($invoice === null) {
            return $this->notFound($response, 'Fakturan hittades inte.');
        }
        return $this->render($response, 'saas/invoices/show', [
            'title'   => 'Faktura ' . htmlspecialchars($invoice['invoice_number'], ENT_QUOTES, 'UTF-8') . ' – SaaS Admin – ZYNC ERP',
            'invoice' => $invoice,
        ]);
    }

    /** GET /saas-admin/invoices/{id}/edit */
    public function editInvoice(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $invoice = $this->repo->findInvoice((int) $args['id']);
        if ($invoice === null) {
            return $this->notFound($response, 'Fakturan hittades inte.');
        }
        return $this->render($response, 'saas/invoices/edit', [
            'title'   => 'Redigera faktura – SaaS Admin – ZYNC ERP',
            'invoice' => $invoice,
            'tenants' => $this->repo->allTenants(),
            'errors'  => [],
        ]);
    }

    /** POST /saas-admin/invoices/{id} */
    public function updateInvoice(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $invoice = $this->repo->findInvoice($id);
        if ($invoice === null) {
            return $this->notFound($response, 'Fakturan hittades inte.');
        }

        $data   = (array) $request->getParsedBody();
        $errors = $this->validateInvoice($data);

        if (!empty($errors)) {
            return $this->render($response, 'saas/invoices/edit', [
                'title'   => 'Redigera faktura – SaaS Admin – ZYNC ERP',
                'invoice' => array_merge($invoice, $data),
                'tenants' => $this->repo->allTenants(),
                'errors'  => $errors,
            ]);
        }

        try {
            $this->repo->updateInvoice($id, $data);
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod när fakturan uppdaterades.');
            return $this->render($response, 'saas/invoices/edit', [
                'title'   => 'Redigera faktura – SaaS Admin – ZYNC ERP',
                'invoice' => array_merge($invoice, $data),
                'tenants' => $this->repo->allTenants(),
                'errors'  => ['db' => $e->getMessage()],
            ]);
        }
        Flash::set('success', 'Fakturan har uppdaterats.');
        return $this->redirect($response, "/saas-admin/invoices/{$id}");
    }

    /** POST /saas-admin/invoices/{id}/status */
    public function updateInvoiceStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $body   = (array) $request->getParsedBody();
        $status = trim((string) ($body['status'] ?? ''));
        $allowed = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];
        if (in_array($status, $allowed, true)) {
            $this->repo->updateInvoiceStatus($id, $status);
            Flash::set('success', 'Fakturastatusen har uppdaterats.');
        }
        return $this->redirect($response, "/saas-admin/invoices/{$id}");
    }

    /** GET /saas-admin/invoices/generate — Batch invoice generation */
    public function generateInvoices(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query = $request->getQueryParams();
        $month = (int) ($query['month'] ?? date('n'));
        $year  = (int) ($query['year']  ?? date('Y'));

        return $this->render($response, 'saas/invoices/generate', [
            'title'  => 'Batch-fakturering – SaaS Admin – ZYNC ERP',
            'month'  => $month,
            'year'   => $year,
            'months' => $this->monthNames(),
        ]);
    }

    /** POST /saas-admin/invoices/generate */
    public function storeGeneratedInvoices(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data  = (array) $request->getParsedBody();
        $month = (int) ($data['month'] ?? date('n'));
        $year  = (int) ($data['year']  ?? date('Y'));

        try {
            $count = $this->repo->generateMonthlyInvoices($month, $year);
            Flash::set('success', "{$count} faktura(or) genererades för " . $this->monthNames()[$month] . " {$year}.");
        } catch (\Throwable $e) {
            Flash::set('error', 'Ett fel uppstod vid batch-fakturering: ' . $e->getMessage());
        }
        return $this->redirect($response, '/saas-admin/invoices');
    }

    // ── Support Tickets ───────────────────────────────────────────────────────

    /** GET /saas-admin/support */
    public function tickets(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $filters = [
            'status'    => $query['status']    ?? '',
            'priority'  => $query['priority']  ?? '',
            'tenant_id' => $query['tenant_id'] ?? '',
        ];
        return $this->render($response, 'saas/support/index', [
            'title'   => 'Support – SaaS Admin – ZYNC ERP',
            'tickets' => $this->repo->allTickets($filters),
            'filters' => $filters,
            'tenants' => $this->repo->allTenants(),
        ]);
    }

    /** GET /saas-admin/support/{id} */
    public function showTicket(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $ticket = $this->repo->findTicket((int) $args['id']);
        if ($ticket === null) {
            return $this->notFound($response, 'Ärendet hittades inte.');
        }
        return $this->render($response, 'saas/support/show', [
            'title'  => 'Ärende ' . htmlspecialchars($ticket['ticket_number'], ENT_QUOTES, 'UTF-8') . ' – SaaS Admin – ZYNC ERP',
            'ticket' => $ticket,
            'user'   => Auth::user(),
        ]);
    }

    /** POST /saas-admin/support/{id}/status */
    public function updateTicketStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $body    = (array) $request->getParsedBody();
        $status  = trim((string) ($body['status'] ?? ''));
        $allowed = ['open', 'in_progress', 'waiting', 'resolved', 'closed'];
        if (in_array($status, $allowed, true)) {
            $this->repo->updateTicketStatus($id, $status);
            Flash::set('success', 'Ärendestatusen har uppdaterats.');
        }
        return $this->redirect($response, "/saas-admin/support/{$id}");
    }

    /** POST /saas-admin/support/{id}/comment */
    public function addComment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $comment = trim((string) ($body['comment'] ?? ''));

        if ($comment !== '') {
            $isInternal = isset($body['is_internal']) ? 1 : 0;
            $this->repo->addComment($id, [
                'comment'     => $comment,
                'user_id'     => Auth::id(),
                'is_internal' => $isInternal,
            ]);
            $label = $isInternal ? 'Intern anteckning har lagts till.' : 'Kommentaren har lagts till.';
            Flash::set('success', $label);
        }
        return $this->redirect($response, "/saas-admin/support/{$id}");
    }

    // ── API ───────────────────────────────────────────────────────────────────

    /** GET /api/tenant-info — Returns tenant name, plan and active modules as JSON. */
    public function tenantInfo(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $tenantId = null;

        // From X-Tenant-ID header
        $header = $request->getHeader('X-Tenant-ID');
        if (!empty($header)) {
            $tenantId = (int) $header[0];
        }

        // From query param (fallback)
        if ($tenantId === null) {
            $params   = $request->getQueryParams();
            $tenantId = isset($params['tenant_id']) ? (int) $params['tenant_id'] : null;
        }

        if ($tenantId === null) {
            $data = json_encode(['error' => 'No tenant specified'], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write((string) $data);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $tenant = $this->repo->findTenant($tenantId);
        if ($tenant === null) {
            $data = json_encode(['error' => 'Tenant not found'], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write((string) $data);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $activeModules = array_values(array_column(
            array_filter($tenant['modules'] ?? [], fn($m) => (int)($m['is_active'] ?? 0) === 1),
            'module_slug'
        ));

        $payload = [
            'id'             => (int) $tenant['id'],
            'company_name'   => $tenant['company_name'],
            'plan'           => $tenant['plan'],
            'status'         => $tenant['status'],
            'active_modules' => $activeModules,
        ];

        $response->getBody()->write((string) json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** @return array<string, string> */
    private function validateTenant(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        if (empty(trim((string) ($data['company_name'] ?? '')))) {
            $errors['company_name'] = 'Företagsnamn är obligatoriskt.';
        }
        if (empty(trim((string) ($data['contact_email'] ?? '')))) {
            $errors['contact_email'] = 'E-post är obligatoriskt.';
        } elseif (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_email'] = 'Ogiltig e-postadress.';
        }
        return $errors;
    }

    /** @return array<string, string> */
    private function validateInvoice(array $data): array
    {
        $errors = [];
        if (empty($data['tenant_id'])) {
            $errors['tenant_id'] = 'Kund är obligatoriskt.';
        }
        if (empty($data['period_start'])) {
            $errors['period_start'] = 'Periodens start är obligatorisk.';
        }
        if (empty($data['period_end'])) {
            $errors['period_end'] = 'Periodens slut är obligatorisk.';
        }
        if (empty($data['due_date'])) {
            $errors['due_date'] = 'Förfallodatum är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, string> */
    private function validatePlan(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        if (empty(trim((string) ($data['name'] ?? '')))) {
            $errors['name'] = 'Plannamn är obligatoriskt.';
        }
        if (empty(trim((string) ($data['slug'] ?? '')))) {
            $errors['slug'] = 'Slug är obligatoriskt.';
        } elseif (!preg_match('/^[a-z0-9\-]+$/', (string) $data['slug'])) {
            $errors['slug'] = 'Slug får bara innehålla gemener, siffror och bindestreck.';
        }
        if (!is_numeric($data['price_monthly'] ?? '')) {
            $errors['price_monthly'] = 'Månadspris måste vara ett tal.';
        }
        return $errors;
    }

    /** Generate a URL-friendly subdomain from a company name. */
    private function generateSubdomain(string $companyName): string
    {
        $slug = mb_strtolower($companyName, 'UTF-8');
        $slug = preg_replace('/[åäÅÄ]/u', 'a', $slug) ?? $slug;
        $slug = preg_replace('/[öÖ]/u', 'o', $slug) ?? $slug;
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? $slug;
        $slug = trim($slug, '-');
        return substr($slug, 0, 63);
    }

    /** @return array<int, string> */
    private function monthNames(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Mars', 4 => 'April',
            5 => 'Maj', 6 => 'Juni', 7 => 'Juli', 8 => 'Augusti',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'December',
        ];
    }

}
