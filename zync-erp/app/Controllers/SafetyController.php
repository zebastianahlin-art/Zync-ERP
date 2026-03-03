<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AuditRepository;
use App\Models\EmergencyContactRepository;
use App\Models\EmergencyProcedureRepository;
use App\Models\EmergencyResourceRepository;
use App\Models\RiskAssessmentRepository;
use App\Models\RiskReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SafetyController extends Controller
{
    private RiskAssessmentRepository  $risks;
    private RiskReportRepository      $reports;
    private AuditRepository           $audits;
    private EmergencyContactRepository   $contacts;
    private EmergencyProcedureRepository $procedures;
    private EmergencyResourceRepository  $resources;

    public function __construct()
    {
        parent::__construct();
        $this->risks      = new RiskAssessmentRepository();
        $this->reports    = new RiskReportRepository();
        $this->audits     = new AuditRepository();
        $this->contacts   = new EmergencyContactRepository();
        $this->procedures = new EmergencyProcedureRepository();
        $this->resources  = new EmergencyResourceRepository();
    }

    // ── Dashboard ─────────────────────────────────────────────

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }

        return $this->render($response, 'safety/index', [
            'title'          => 'Hälsa & Säkerhet – ZYNC ERP',
            'riskStats'      => $this->risks->stats(),
            'resourceStats'  => $this->resources->stats(),
            'recentReports'  => $this->reports->all(['status' => null]),
            'overdueResources' => $this->resources->overdue(),
        ]);
    }

    // ── Risk Assessments ──────────────────────────────────────

    public function risks(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'safety/risks/index', [
            'title'   => 'Riskbedömningar – ZYNC ERP',
            'risks'   => $this->risks->all($filters),
            'filters' => $filters,
            'success' => Flash::get('success'),
        ]);
    }

    public function createRisk(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/risks/create', [
            'title'  => 'Ny riskbedömning – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storeRisk(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractRiskData($request);
        $errors = $this->validateRisk($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/risks/create', [
                'title'  => 'Ny riskbedömning – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $this->risks->create($data);
        Flash::set('success', 'Riskbedömningen har skapats.');
        return $this->redirect($response, '/safety/risks');
    }

    public function showRisk(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $risk = $this->risks->find((int) $args['id']);
        if ($risk === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/risks/show', [
            'title' => 'Riskbedömning – ZYNC ERP',
            'risk'  => $risk,
        ]);
    }

    public function editRisk(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $risk = $this->risks->find((int) $args['id']);
        if ($risk === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/risks/edit', [
            'title'  => 'Redigera riskbedömning – ZYNC ERP',
            'risk'   => $risk,
            'errors' => [],
        ]);
    }

    public function updateRisk(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id   = (int) $args['id'];
        $risk = $this->risks->find($id);
        if ($risk === null) { return $this->notFound($response); }
        $data   = $this->extractRiskData($request);
        $errors = $this->validateRisk($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/risks/edit', [
                'title'  => 'Redigera riskbedömning – ZYNC ERP',
                'risk'   => array_merge($risk, $data),
                'errors' => $errors,
            ]);
        }
        $this->risks->update($id, $data);
        Flash::set('success', 'Riskbedömningen har uppdaterats.');
        return $this->redirect($response, '/safety/risks/' . $id);
    }

    public function deleteRisk(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->risks->delete((int) $args['id']);
        Flash::set('success', 'Riskbedömningen har tagits bort.');
        return $this->redirect($response, '/safety/risks');
    }

    public function updateRiskStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body   = (array) $request->getParsedBody();
        $status = trim((string) ($body['status'] ?? ''));
        $valid  = ['draft', 'active', 'under_review', 'closed', 'archived'];
        if (in_array($status, $valid, true)) {
            $this->risks->updateStatus((int) $args['id'], $status);
            Flash::set('success', 'Status har uppdaterats.');
        }
        return $this->redirect($response, '/safety/risks/' . $args['id']);
    }

    // ── Risk Reports ──────────────────────────────────────────

    public function reports(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'safety/reports/index', [
            'title'   => 'Riskrapporter – ZYNC ERP',
            'reports' => $this->reports->all($filters),
            'filters' => $filters,
            'success' => Flash::get('success'),
        ]);
    }

    public function createReport(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/reports/create', [
            'title'  => 'Ny riskrapport – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storeReport(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractReportData($request);
        $errors = $this->validateReport($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/reports/create', [
                'title'  => 'Ny riskrapport – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $id = $this->reports->create($data);
        Flash::set('success', 'Riskrapporten har skapats.');
        return $this->redirect($response, '/safety/reports/' . $id);
    }

    public function showReport(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $report = $this->reports->find((int) $args['id']);
        if ($report === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/reports/show', [
            'title'  => 'Riskrapport – ZYNC ERP',
            'report' => $report,
        ]);
    }

    public function editReport(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $report = $this->reports->find((int) $args['id']);
        if ($report === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/reports/edit', [
            'title'  => 'Redigera riskrapport – ZYNC ERP',
            'report' => $report,
            'errors' => [],
        ]);
    }

    public function updateReport(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id     = (int) $args['id'];
        $report = $this->reports->find($id);
        if ($report === null) { return $this->notFound($response); }
        $data   = $this->extractReportData($request);
        $errors = $this->validateReport($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/reports/edit', [
                'title'  => 'Redigera riskrapport – ZYNC ERP',
                'report' => array_merge($report, $data),
                'errors' => $errors,
            ]);
        }
        $this->reports->update($id, $data);
        Flash::set('success', 'Riskrapporten har uppdaterats.');
        return $this->redirect($response, '/safety/reports/' . $id);
    }

    public function deleteReport(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->reports->delete((int) $args['id']);
        Flash::set('success', 'Riskrapporten har tagits bort.');
        return $this->redirect($response, '/safety/reports');
    }

    public function updateReportStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body   = (array) $request->getParsedBody();
        $status = trim((string) ($body['status'] ?? ''));
        $valid  = ['reported', 'acknowledged', 'investigating', 'action_taken', 'closed'];
        if (in_array($status, $valid, true)) {
            $this->reports->updateStatus((int) $args['id'], $status);
            Flash::set('success', 'Status har uppdaterats.');
        }
        return $this->redirect($response, '/safety/reports/' . $args['id']);
    }

    // ── Audits ────────────────────────────────────────────────

    public function audits(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'safety/audits/index', [
            'title'   => 'Audits – ZYNC ERP',
            'audits'  => $this->audits->allAudits($filters),
            'filters' => $filters,
            'success' => Flash::get('success'),
        ]);
    }

    public function createAudit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/audits/create', [
            'title'     => 'Ny audit – ZYNC ERP',
            'templates' => $this->audits->allTemplates(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function storeAudit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractAuditData($request);
        $errors = $this->validateAudit($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/audits/create', [
                'title'     => 'Ny audit – ZYNC ERP',
                'templates' => $this->audits->allTemplates(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $id = $this->audits->createAudit($data);
        Flash::set('success', 'Auditen har skapats.');
        return $this->redirect($response, '/safety/audits/' . $id);
    }

    public function showAudit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $audit = $this->audits->findAudit((int) $args['id']);
        if ($audit === null) { return $this->notFound($response); }
        $items = $audit['template_id'] ? $this->audits->templateItems((int) $audit['template_id']) : [];
        return $this->render($response, 'safety/audits/show', [
            'title'  => 'Audit – ZYNC ERP',
            'audit'  => $audit,
            'items'  => $items,
        ]);
    }

    public function editAudit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $audit = $this->audits->findAudit((int) $args['id']);
        if ($audit === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/audits/edit', [
            'title'     => 'Redigera audit – ZYNC ERP',
            'audit'     => $audit,
            'templates' => $this->audits->allTemplates(),
            'errors'    => [],
        ]);
    }

    public function updateAudit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id    = (int) $args['id'];
        $audit = $this->audits->findAudit($id);
        if ($audit === null) { return $this->notFound($response); }
        $data   = $this->extractAuditData($request);
        $errors = $this->validateAudit($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/audits/edit', [
                'title'     => 'Redigera audit – ZYNC ERP',
                'audit'     => array_merge($audit, $data),
                'templates' => $this->audits->allTemplates(),
                'errors'    => $errors,
            ]);
        }
        $this->audits->updateAudit($id, $data);
        Flash::set('success', 'Auditen har uppdaterats.');
        return $this->redirect($response, '/safety/audits/' . $id);
    }

    public function deleteAudit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->audits->deleteAudit((int) $args['id']);
        Flash::set('success', 'Auditen har tagits bort.');
        return $this->redirect($response, '/safety/audits');
    }

    public function updateAuditStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body   = (array) $request->getParsedBody();
        $status = trim((string) ($body['status'] ?? ''));
        $valid  = ['planned', 'in_progress', 'completed', 'cancelled'];
        if (in_array($status, $valid, true)) {
            $this->audits->updateAuditStatus((int) $args['id'], $status);
            Flash::set('success', 'Status har uppdaterats.');
        }
        return $this->redirect($response, '/safety/audits/' . $args['id']);
    }

    public function saveAuditResponses(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id    = (int) $args['id'];
        $body  = (array) $request->getParsedBody();
        $responses = is_array($body['responses'] ?? null) ? $body['responses'] : [];
        $this->audits->saveResponses($id, $responses);
        Flash::set('success', 'Svar har sparats.');
        return $this->redirect($response, '/safety/audits/' . $id);
    }

    // ── Audit Templates ───────────────────────────────────────

    public function auditTemplates(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/audit-templates/index', [
            'title'     => 'Audit-mallar – ZYNC ERP',
            'templates' => $this->audits->allTemplates(),
            'success'   => Flash::get('success'),
        ]);
    }

    public function createAuditTemplate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/audit-templates/create', [
            'title'  => 'Ny audit-mall – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storeAuditTemplate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractTemplateData($request);
        $errors = $this->validateTemplate($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/audit-templates/create', [
                'title'  => 'Ny audit-mall – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $id = $this->audits->createTemplate($data);
        Flash::set('success', 'Audit-mallen har skapats.');
        return $this->redirect($response, '/safety/audit-templates/' . $id);
    }

    public function showAuditTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $template = $this->audits->findTemplate((int) $args['id']);
        if ($template === null) { return $this->notFound($response); }
        $items = $this->audits->templateItems((int) $args['id']);
        return $this->render($response, 'safety/audit-templates/show', [
            'title'    => 'Audit-mall – ZYNC ERP',
            'template' => $template,
            'items'    => $items,
        ]);
    }

    public function editAuditTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $template = $this->audits->findTemplate((int) $args['id']);
        if ($template === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/audit-templates/edit', [
            'title'    => 'Redigera audit-mall – ZYNC ERP',
            'template' => $template,
            'errors'   => [],
        ]);
    }

    public function updateAuditTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id       = (int) $args['id'];
        $template = $this->audits->findTemplate($id);
        if ($template === null) { return $this->notFound($response); }
        $data   = $this->extractTemplateData($request);
        $errors = $this->validateTemplate($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/audit-templates/edit', [
                'title'    => 'Redigera audit-mall – ZYNC ERP',
                'template' => array_merge($template, $data),
                'errors'   => $errors,
            ]);
        }
        $this->audits->updateTemplate($id, $data);
        Flash::set('success', 'Audit-mallen har uppdaterats.');
        return $this->redirect($response, '/safety/audit-templates/' . $id);
    }

    public function deleteAuditTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->audits->deleteTemplate((int) $args['id']);
        Flash::set('success', 'Audit-mallen har tagits bort.');
        return $this->redirect($response, '/safety/audit-templates');
    }

    public function addTemplateItem(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        if (!empty($body['question'])) {
            $this->audits->addTemplateItem($id, [
                'sort_order'    => trim((string) ($body['sort_order'] ?? '0')),
                'section'       => trim((string) ($body['section'] ?? '')),
                'question'      => trim((string) $body['question']),
                'description'   => trim((string) ($body['description'] ?? '')),
                'response_type' => trim((string) ($body['response_type'] ?? 'yes_no')),
                'is_required'   => isset($body['is_required']) ? 1 : 0,
            ]);
            Flash::set('success', 'Frågan har lagts till.');
        }
        return $this->redirect($response, '/safety/audit-templates/' . $id);
    }

    public function removeTemplateItem(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->audits->removeTemplateItem((int) $args['itemId']);
        Flash::set('success', 'Frågan har tagits bort.');
        return $this->redirect($response, '/safety/audit-templates/' . $args['id']);
    }

    // ── Emergency ─────────────────────────────────────────────

    public function emergency(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/emergency/index', [
            'title'      => 'Krishantering – ZYNC ERP',
            'contacts'   => $this->contacts->all(),
            'procedures' => $this->procedures->all(),
        ]);
    }

    public function emergencyContacts(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/emergency/contacts/index', [
            'title'    => 'Nödkontakter – ZYNC ERP',
            'contacts' => $this->contacts->all(),
            'success'  => Flash::get('success'),
        ]);
    }

    public function createEmergencyContact(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/emergency/contacts/create', [
            'title'  => 'Ny nödkontakt – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storeEmergencyContact(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractContactData($request);
        $errors = $this->validateContact($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/emergency/contacts/create', [
                'title'  => 'Ny nödkontakt – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $this->contacts->create($data);
        Flash::set('success', 'Nödkontakten har skapats.');
        return $this->redirect($response, '/safety/emergency/contacts');
    }

    public function editEmergencyContact(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $contact = $this->contacts->find((int) $args['id']);
        if ($contact === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/emergency/contacts/edit', [
            'title'   => 'Redigera nödkontakt – ZYNC ERP',
            'contact' => $contact,
            'errors'  => [],
        ]);
    }

    public function updateEmergencyContact(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id      = (int) $args['id'];
        $contact = $this->contacts->find($id);
        if ($contact === null) { return $this->notFound($response); }
        $data   = $this->extractContactData($request);
        $errors = $this->validateContact($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/emergency/contacts/edit', [
                'title'   => 'Redigera nödkontakt – ZYNC ERP',
                'contact' => array_merge($contact, $data),
                'errors'  => $errors,
            ]);
        }
        $this->contacts->update($id, $data);
        Flash::set('success', 'Nödkontakten har uppdaterats.');
        return $this->redirect($response, '/safety/emergency/contacts');
    }

    public function deleteEmergencyContact(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->contacts->delete((int) $args['id']);
        Flash::set('success', 'Nödkontakten har tagits bort.');
        return $this->redirect($response, '/safety/emergency/contacts');
    }

    // ── Emergency Procedures ──────────────────────────────────

    public function emergencyProcedures(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $category = (string) ($request->getQueryParams()['category'] ?? '');
        return $this->render($response, 'safety/emergency/procedures/index', [
            'title'      => 'Nödprocedurer – ZYNC ERP',
            'procedures' => $this->procedures->all($category !== '' ? $category : null),
            'category'   => $category,
            'success'    => Flash::get('success'),
        ]);
    }

    public function createEmergencyProcedure(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/emergency/procedures/create', [
            'title'  => 'Ny nödprocedur – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storeEmergencyProcedure(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractProcedureData($request);
        $errors = $this->validateProcedure($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/emergency/procedures/create', [
                'title'  => 'Ny nödprocedur – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $id = $this->procedures->create($data);
        Flash::set('success', 'Nödproceduren har skapats.');
        return $this->redirect($response, '/safety/emergency/procedures/' . $id);
    }

    public function showEmergencyProcedure(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $procedure = $this->procedures->find((int) $args['id']);
        if ($procedure === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/emergency/procedures/show', [
            'title'     => 'Nödprocedur – ZYNC ERP',
            'procedure' => $procedure,
        ]);
    }

    public function editEmergencyProcedure(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $procedure = $this->procedures->find((int) $args['id']);
        if ($procedure === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/emergency/procedures/edit', [
            'title'     => 'Redigera nödprocedur – ZYNC ERP',
            'procedure' => $procedure,
            'errors'    => [],
        ]);
    }

    public function updateEmergencyProcedure(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id        = (int) $args['id'];
        $procedure = $this->procedures->find($id);
        if ($procedure === null) { return $this->notFound($response); }
        $data   = $this->extractProcedureData($request);
        $errors = $this->validateProcedure($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/emergency/procedures/edit', [
                'title'     => 'Redigera nödprocedur – ZYNC ERP',
                'procedure' => array_merge($procedure, $data),
                'errors'    => $errors,
            ]);
        }
        $this->procedures->update($id, $data);
        Flash::set('success', 'Nödproceduren har uppdaterats.');
        return $this->redirect($response, '/safety/emergency/procedures/' . $id);
    }

    public function deleteEmergencyProcedure(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->procedures->delete((int) $args['id']);
        Flash::set('success', 'Nödproceduren har tagits bort.');
        return $this->redirect($response, '/safety/emergency/procedures');
    }

    // ── Emergency Resources ───────────────────────────────────

    public function resources(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'safety/resources/index', [
            'title'     => 'Nödresurser – ZYNC ERP',
            'resources' => $this->resources->all($filters),
            'filters'   => $filters,
            'stats'     => $this->resources->stats(),
            'success'   => Flash::get('success'),
        ]);
    }

    public function resourcesMap(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/resources/map', [
            'title'     => 'Resurskarta – ZYNC ERP',
            'resources' => $this->resources->all(),
        ]);
    }

    public function createResource(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/resources/create', [
            'title'  => 'Ny nödresurs – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storeResource(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $data   = $this->extractResourceData($request);
        $errors = $this->validateResource($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/resources/create', [
                'title'  => 'Ny nödresurs – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $id = $this->resources->create($data);
        Flash::set('success', 'Nödresursen har skapats.');
        return $this->redirect($response, '/safety/resources/' . $id);
    }

    public function showResource(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $resource = $this->resources->find((int) $args['id']);
        if ($resource === null) { return $this->notFound($response); }
        $inspections = $this->resources->inspections((int) $args['id']);
        return $this->render($response, 'safety/resources/show', [
            'title'       => 'Nödresurs – ZYNC ERP',
            'resource'    => $resource,
            'inspections' => $inspections,
        ]);
    }

    public function editResource(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $resource = $this->resources->find((int) $args['id']);
        if ($resource === null) { return $this->notFound($response); }
        return $this->render($response, 'safety/resources/edit', [
            'title'    => 'Redigera nödresurs – ZYNC ERP',
            'resource' => $resource,
            'errors'   => [],
        ]);
    }

    public function updateResource(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id       = (int) $args['id'];
        $resource = $this->resources->find($id);
        if ($resource === null) { return $this->notFound($response); }
        $data   = $this->extractResourceData($request);
        $errors = $this->validateResource($data);
        if (!empty($errors)) {
            return $this->render($response, 'safety/resources/edit', [
                'title'    => 'Redigera nödresurs – ZYNC ERP',
                'resource' => array_merge($resource, $data),
                'errors'   => $errors,
            ]);
        }
        $this->resources->update($id, $data);
        Flash::set('success', 'Nödresursen har uppdaterats.');
        return $this->redirect($response, '/safety/resources/' . $id);
    }

    public function deleteResource(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->resources->delete((int) $args['id']);
        Flash::set('success', 'Nödresursen har tagits bort.');
        return $this->redirect($response, '/safety/resources');
    }

    public function inspectResource(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id      = (int) $args['id'];
        $body    = (array) $request->getParsedBody();
        $this->resources->addInspection([
            'resource_id'    => $id,
            'inspected_by'   => Auth::id(),
            'inspected_at'   => trim((string) ($body['inspected_at'] ?? date('Y-m-d'))),
            'status'         => trim((string) ($body['status'] ?? 'ok')),
            'notes'          => trim((string) ($body['notes'] ?? '')),
            'next_inspection' => trim((string) ($body['next_inspection'] ?? '')),
        ]);
        Flash::set('success', 'Kontrollen har sparats.');
        return $this->redirect($response, '/safety/resources/' . $id);
    }

    public function overdueInspections(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'safety/resources/overdue', [
            'title'     => 'Förfallna kontroller – ZYNC ERP',
            'resources' => $this->resources->overdue(),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────

    private function extractRiskData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'title'         => trim((string) ($body['title'] ?? '')),
            'description'   => trim((string) ($body['description'] ?? '')),
            'location'      => trim((string) ($body['location'] ?? '')),
            'department_id' => trim((string) ($body['department_id'] ?? '')),
            'risk_type'     => trim((string) ($body['risk_type'] ?? 'other')),
            'probability'   => trim((string) ($body['probability'] ?? '1')),
            'consequence'   => trim((string) ($body['consequence'] ?? '1')),
            'status'        => trim((string) ($body['status'] ?? 'draft')),
            'assigned_to'   => trim((string) ($body['assigned_to'] ?? '')),
            'valid_until'   => trim((string) ($body['valid_until'] ?? '')),
            'mitigation'    => trim((string) ($body['mitigation'] ?? '')),
        ];
    }

    private function validateRisk(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        $prob = (int) $data['probability'];
        if ($prob < 1 || $prob > 5) { $errors['probability'] = 'Sannolikhet måste vara 1–5.'; }
        $cons = (int) $data['consequence'];
        if ($cons < 1 || $cons > 5) { $errors['consequence'] = 'Konsekvens måste vara 1–5.'; }
        return $errors;
    }

    private function extractReportData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'title'               => trim((string) ($body['title'] ?? '')),
            'description'         => trim((string) ($body['description'] ?? '')),
            'location'            => trim((string) ($body['location'] ?? '')),
            'department_id'       => trim((string) ($body['department_id'] ?? '')),
            'category'            => trim((string) ($body['category'] ?? 'risk')),
            'severity'            => trim((string) ($body['severity'] ?? 'medium')),
            'status'              => trim((string) ($body['status'] ?? 'reported')),
            'assigned_to'         => trim((string) ($body['assigned_to'] ?? '')),
            'risk_assessment_id'  => trim((string) ($body['risk_assessment_id'] ?? '')),
            'action_taken'        => trim((string) ($body['action_taken'] ?? '')),
        ];
    }

    private function validateReport(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        if ($data['description'] === '') { $errors['description'] = 'Beskrivning är obligatorisk.'; }
        return $errors;
    }

    private function extractAuditData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'template_id'    => trim((string) ($body['template_id'] ?? '')),
            'title'          => trim((string) ($body['title'] ?? '')),
            'description'    => trim((string) ($body['description'] ?? '')),
            'location'       => trim((string) ($body['location'] ?? '')),
            'department_id'  => trim((string) ($body['department_id'] ?? '')),
            'assigned_to'    => trim((string) ($body['assigned_to'] ?? '')),
            'status'         => trim((string) ($body['status'] ?? 'planned')),
            'scheduled_date' => trim((string) ($body['scheduled_date'] ?? '')),
            'completed_date' => trim((string) ($body['completed_date'] ?? '')),
            'score'          => trim((string) ($body['score'] ?? '')),
            'notes'          => trim((string) ($body['notes'] ?? '')),
        ];
    }

    private function validateAudit(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        if ($data['assigned_to'] === '') { $errors['assigned_to'] = 'Ansvarig är obligatorisk.'; }
        if ($data['scheduled_date'] === '') { $errors['scheduled_date'] = 'Planerat datum är obligatoriskt.'; }
        return $errors;
    }

    private function extractTemplateData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'        => trim((string) ($body['name'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'category'    => trim((string) ($body['category'] ?? 'general')),
            'version'     => trim((string) ($body['version'] ?? '1')),
            'is_active'   => isset($body['is_active']) ? 1 : 0,
        ];
    }

    private function validateTemplate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        return $errors;
    }

    private function extractContactData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'          => trim((string) ($body['name'] ?? '')),
            'role'          => trim((string) ($body['role'] ?? '')),
            'phone'         => trim((string) ($body['phone'] ?? '')),
            'phone_alt'     => trim((string) ($body['phone_alt'] ?? '')),
            'email'         => trim((string) ($body['email'] ?? '')),
            'department_id' => trim((string) ($body['department_id'] ?? '')),
            'is_external'   => isset($body['is_external']) ? 1 : 0,
            'organization'  => trim((string) ($body['organization'] ?? '')),
            'notes'         => trim((string) ($body['notes'] ?? '')),
            'sort_order'    => trim((string) ($body['sort_order'] ?? '0')),
        ];
    }

    private function validateContact(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if ($data['phone'] === '') { $errors['phone'] = 'Telefonnummer är obligatoriskt.'; }
        return $errors;
    }

    private function extractProcedureData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'title'           => trim((string) ($body['title'] ?? '')),
            'category'        => trim((string) ($body['category'] ?? 'other')),
            'description'     => trim((string) ($body['description'] ?? '')),
            'steps'           => trim((string) ($body['steps'] ?? '')),
            'responsible'     => trim((string) ($body['responsible'] ?? '')),
            'location'        => trim((string) ($body['location'] ?? '')),
            'last_reviewed'   => trim((string) ($body['last_reviewed'] ?? '')),
            'review_interval' => trim((string) ($body['review_interval'] ?? '')),
            'is_active'       => isset($body['is_active']) ? 1 : 0,
        ];
    }

    private function validateProcedure(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        if ($data['steps'] === '') { $errors['steps'] = 'Åtgärdssteg är obligatoriska.'; }
        return $errors;
    }

    private function extractResourceData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'                => trim((string) ($body['name'] ?? '')),
            'resource_type'       => trim((string) ($body['resource_type'] ?? 'other')),
            'location'            => trim((string) ($body['location'] ?? '')),
            'location_details'    => trim((string) ($body['location_details'] ?? '')),
            'department_id'       => trim((string) ($body['department_id'] ?? '')),
            'serial_number'       => trim((string) ($body['serial_number'] ?? '')),
            'quantity'            => trim((string) ($body['quantity'] ?? '1')),
            'status'              => trim((string) ($body['status'] ?? 'ok')),
            'last_inspection'     => trim((string) ($body['last_inspection'] ?? '')),
            'next_inspection'     => trim((string) ($body['next_inspection'] ?? '')),
            'inspection_interval' => trim((string) ($body['inspection_interval'] ?? '')),
            'notes'               => trim((string) ($body['notes'] ?? '')),
        ];
    }

    private function validateResource(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if ($data['location'] === '') { $errors['location'] = 'Plats är obligatorisk.'; }
        return $errors;
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
