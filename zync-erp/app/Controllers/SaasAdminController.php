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
        ]);
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

        $activeModuleSlugs = array_column($tenant['modules'], 'module_slug');

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

        $data   = (array) $request->getParsedBody();
        $errors = $this->validateTenant($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'saas/tenants/edit', [
                'title'  => 'Redigera kund – SaaS Admin – ZYNC ERP',
                'tenant' => array_merge($tenant, $data),
                'errors' => $errors,
            ]);
        }

        try {
            $this->repo->updateTenant($id, $data);
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
            Flash::set('success', 'Modulen har inaktiverats.');
        }
        return $this->redirect($response, "/saas-admin/tenants/{$id}");
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
        ]);
    }

    /** GET /saas-admin/invoices/create */
    public function createInvoice(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'saas/invoices/create', [
            'title'   => 'Ny faktura – SaaS Admin – ZYNC ERP',
            'tenants' => $this->repo->allTenants(),
            'errors'  => [],
            'old'     => [],
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
            $this->repo->addComment($id, [
                'comment'     => $comment,
                'user_id'     => Auth::id(),
                'is_internal' => isset($body['is_internal']) ? 1 : 0,
            ]);
            Flash::set('success', 'Kommentaren har lagts till.');
        }
        return $this->redirect($response, "/saas-admin/support/{$id}");
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

}
