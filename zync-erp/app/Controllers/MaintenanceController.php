<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Database;
use App\Models\EquipmentRepository;
use App\Models\MachineRepository;
use App\Models\FaultReportRepository;
use App\Models\WorkOrderRepository;
use App\Models\InspectableEquipmentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MaintenanceController extends Controller
{
    private EquipmentRepository $equipRepo;
    private MachineRepository $machineRepo;
    private FaultReportRepository $faultRepo;
    private WorkOrderRepository $woRepo;
    private InspectableEquipmentRepository $inspRepo;

    public function __construct()
    {
        parent::__construct();
        $this->equipRepo   = new EquipmentRepository();
        $this->machineRepo = new MachineRepository();
        $this->faultRepo   = new FaultReportRepository();
        $this->woRepo      = new WorkOrderRepository();
        $this->inspRepo    = new InspectableEquipmentRepository();
    }

    // ─── MAINTENANCE DASHBOARD ───────────────────────────────

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $faults = $this->faultRepo->all();
        $workOrders = $this->woRepo->all();
        $inspections = $this->inspRepo->all();
        $overdue = $this->inspRepo->getOverdue();

        $openFaults = count(array_filter($faults, fn($f) => !in_array($f['status'], ['resolved', 'closed'])));
        $activeWOs = count(array_filter($workOrders, fn($w) => in_array($w['status'], ['assigned', 'in_progress'])));
        $pendingApproval = count(array_filter($workOrders, fn($w) => $w['status'] === 'completed'));
        $overdueInspections = count($overdue);

        return $this->render($response, 'maintenance/index', [
            'title' => 'Underhåll – ZYNC ERP',
            'openFaults' => $openFaults,
            'activeWOs' => $activeWOs,
            'pendingApproval' => $pendingApproval,
            'overdueInspections' => $overdueInspections,
            'recentFaults' => array_slice($faults, 0, 5),
            'recentWOs' => array_slice($workOrders, 0, 5),
        ]);
    }

    // ─── EQUIPMENT ───────────────────────────────────────────

    public function equipmentIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'equipment/index', [
            'title' => 'Utrustning – ZYNC ERP',
            'equipment' => $this->equipRepo->all(),
        ]);
    }

    public function equipmentCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'equipment/create', [
            'title' => 'Ny utrustning – ZYNC ERP',
            'departments' => $this->getDepartments(),
            'parentEquipment' => $this->equipRepo->all(),
        ]);
    }

    public function equipmentStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, '/equipment/create');
        }

        $id = $this->equipRepo->create($data);
        Flash::set('success', 'Utrustning skapad.');
        return $this->redirect($response, "/equipment/{$id}");
    }

    public function equipmentShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $equipment = $this->equipRepo->find($id);
        if (!$equipment) {
            Flash::set('error', 'Utrustning hittades inte.');
            return $this->redirect($response, '/equipment');
        }
        return $this->render($response, 'equipment/show', [
            'title' => $equipment['name'] . ' – ZYNC ERP',
            'equipment' => $equipment,
        ]);
    }

    public function equipmentEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $equipment = $this->equipRepo->find($id);
        if (!$equipment) {
            Flash::set('error', 'Utrustning hittades inte.');
            return $this->redirect($response, '/equipment');
        }
        return $this->render($response, 'equipment/edit', [
            'title' => 'Redigera utrustning – ZYNC ERP',
            'equipment' => $equipment,
            'departments' => $this->getDepartments(),
            'parentEquipment' => $this->equipRepo->all(),
        ]);
    }

    public function equipmentUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, "/equipment/{$id}/edit");
        }

        $this->equipRepo->update($id, $data);
        Flash::set('success', 'Utrustning uppdaterad.');
        return $this->redirect($response, "/equipment/{$id}");
    }

    public function equipmentDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->equipRepo->delete((int) $args['id']);
        Flash::set('success', 'Utrustning raderad.');
        return $this->redirect($response, '/equipment');
    }

    // ─── MACHINES ────────────────────────────────────────────

    public function machineIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'machines/index', [
            'title' => 'Maskiner – ZYNC ERP',
            'machines' => $this->machineRepo->all(),
        ]);
    }

    public function machineCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'machines/create', [
            'title' => 'Ny maskin – ZYNC ERP',
            'departments' => $this->getDepartments(),
            'equipment' => $this->equipRepo->all(),
        ]);
    }

    public function machineStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, '/machines/create');
        }

        $id = $this->machineRepo->create($data);
        Flash::set('success', 'Maskin skapad.');
        return $this->redirect($response, "/machines/{$id}");
    }

    public function machineShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $machine = $this->machineRepo->find($id);
        if (!$machine) {
            Flash::set('error', 'Maskin hittades inte.');
            return $this->redirect($response, '/machines');
        }
        return $this->render($response, 'machines/show', [
            'title' => $machine['name'] . ' – ZYNC ERP',
            'machine' => $machine,
        ]);
    }

    public function machineEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $machine = $this->machineRepo->find($id);
        if (!$machine) {
            Flash::set('error', 'Maskin hittades inte.');
            return $this->redirect($response, '/machines');
        }
        return $this->render($response, 'machines/edit', [
            'title' => 'Redigera maskin – ZYNC ERP',
            'machine' => $machine,
            'departments' => $this->getDepartments(),
            'equipment' => $this->equipRepo->all(),
        ]);
    }

    public function machineUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, "/machines/{$id}/edit");
        }

        $this->machineRepo->update($id, $data);
        Flash::set('success', 'Maskin uppdaterad.');
        return $this->redirect($response, "/machines/{$id}");
    }

    public function machineDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->machineRepo->delete((int) $args['id']);
        Flash::set('success', 'Maskin raderad.');
        return $this->redirect($response, '/machines');
    }

    // ─── FAULT REPORTS ───────────────────────────────────────

    public function faultIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/faults/index', [
            'title' => 'Felanmälningar – ZYNC ERP',
            'faults' => $this->faultRepo->all(),
        ]);
    }

    public function faultCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/faults/create', [
            'title' => 'Ny felanmälan – ZYNC ERP',
            'machines' => $this->machineRepo->all(),
            'equipment' => $this->equipRepo->all(),
            'departments' => $this->getDepartments(),
        ]);
    }

    public function faultStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $user = Auth::user();
        $data['reported_by'] = $user['id'];
        $data['created_by'] = $user['id'];

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, '/maintenance/faults/create');
        }

        $id = $this->faultRepo->create($data);
        Flash::set('success', 'Felanmälan skapad.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    public function faultShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $fault = $this->faultRepo->find($id);
        if (!$fault) {
            Flash::set('error', 'Felanmälan hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }
        return $this->render($response, 'maintenance/faults/show', [
            'title' => $fault['fault_number'] . ' – ZYNC ERP',
            'fault' => $fault,
            'users' => $this->getUsers(),
        ]);
    }

    public function faultEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $fault = $this->faultRepo->find($id);
        if (!$fault) {
            Flash::set('error', 'Felanmälan hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }
        return $this->render($response, 'maintenance/faults/edit', [
            'title' => 'Redigera felanmälan – ZYNC ERP',
            'fault' => $fault,
            'machines' => $this->machineRepo->all(),
            'equipment' => $this->equipRepo->all(),
            'departments' => $this->getDepartments(),
        ]);
    }

    public function faultUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, "/maintenance/faults/{$id}/edit");
        }

        $this->faultRepo->update($id, $data);
        Flash::set('success', 'Felanmälan uppdaterad.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    public function faultDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->faultRepo->delete((int) $args['id']);
        Flash::set('success', 'Felanmälan raderad.');
        return $this->redirect($response, '/maintenance/faults');
    }

    public function faultAcknowledge(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->faultRepo->acknowledge((int) $args['id'], Auth::user()['id']);
        Flash::set('success', 'Felanmälan bekräftad.');
        return $this->redirect($response, "/maintenance/faults/{$args['id']}");
    }

    public function faultAssign(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $assignedTo = (int) ($data['assigned_to'] ?? 0);
        if (!$assignedTo) {
            Flash::set('error', 'Välj en tekniker.');
            return $this->redirect($response, "/maintenance/faults/{$args['id']}");
        }
        $this->faultRepo->assign((int) $args['id'], $assignedTo, Auth::user()['id']);
        Flash::set('success', 'Felanmälan tilldelad.');
        return $this->redirect($response, "/maintenance/faults/{$args['id']}");
    }

    public function faultConvert(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $faultId = (int) $args['id'];
        $fault = $this->faultRepo->find($faultId);
        if (!$fault) {
            Flash::set('error', 'Felanmälan hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }

        $user = Auth::user();
        $woId = $this->woRepo->create([
            'title' => $fault['title'],
            'description' => $fault['description'],
            'type' => 'corrective',
            'equipment_id' => $fault['equipment_id'],
            'fault_report_id' => $faultId,
            'priority' => $fault['priority'],
            'created_by' => $user['id'],
        ]);

        $this->faultRepo->linkWorkOrder($faultId, $woId);
        Flash::set('success', 'Arbetsorder skapad från felanmälan.');
        return $this->redirect($response, "/maintenance/work-orders/{$woId}");
    }

    // ─── WORK ORDERS ─────────────────────────────────────────

    public function workOrderIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/work-orders/index', [
            'title' => 'Arbetsordrar – ZYNC ERP',
            'workOrders' => $this->woRepo->all(),
        ]);
    }

    public function workOrderCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/work-orders/create', [
            'title' => 'Ny arbetsorder – ZYNC ERP',
            'machines' => $this->machineRepo->all(),
            'equipment' => $this->equipRepo->all(),
            'departments' => $this->getDepartments(),
            'costCenters' => $this->getCostCenters(),
            'faults' => $this->faultRepo->all(),
        ]);
    }

    public function workOrderStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, '/maintenance/work-orders/create');
        }

        $id = $this->woRepo->create($data);
        Flash::set('success', 'Arbetsorder skapad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function workOrderShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $wo = $this->woRepo->find($id);
        if (!$wo) {
            Flash::set('error', 'Arbetsorder hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }
        return $this->render($response, 'maintenance/work-orders/show', [
            'title' => $wo['wo_number'] . ' – ZYNC ERP',
            'wo' => $wo,
            'timeEntries' => $this->woRepo->getTimeEntries($id),
            'parts' => $this->woRepo->getParts($id),
            'users' => $this->getUsers(),
            'articles' => $this->getArticles(),
            'currentUser' => Auth::user(),
        ]);
    }

    public function workOrderEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $wo = $this->woRepo->find($id);
        if (!$wo) {
            Flash::set('error', 'Arbetsorder hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }
        return $this->render($response, 'maintenance/work-orders/edit', [
            'title' => 'Redigera arbetsorder – ZYNC ERP',
            'wo' => $wo,
            'machines' => $this->machineRepo->all(),
            'equipment' => $this->equipRepo->all(),
            'departments' => $this->getDepartments(),
            'costCenters' => $this->getCostCenters(),
        ]);
    }

    public function workOrderUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, "/maintenance/work-orders/{$id}/edit");
        }

        $this->woRepo->update($id, $data);
        Flash::set('success', 'Arbetsorder uppdaterad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function workOrderDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->delete((int) $args['id']);
        Flash::set('success', 'Arbetsorder raderad.');
        return $this->redirect($response, '/maintenance/work-orders');
    }

    public function workOrderStart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->start((int) $args['id']);
        Flash::set('success', 'Arbetsorder startad.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderComplete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->woRepo->complete((int) $args['id'], $data['completion_notes'] ?? '');
        Flash::set('success', 'Arbete markerat som utfört.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderAddTime(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $data['user_id'] = Auth::user()['id'];

        if (empty($data['hours']) || empty($data['work_date'])) {
            Flash::set('error', 'Timmar och datum krävs.');
            return $this->redirect($response, "/maintenance/work-orders/{$id}");
        }

        $this->woRepo->addTimeEntry($id, $data);
        Flash::set('success', 'Tid loggad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function workOrderDeleteTime(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->deleteTimeEntry((int) $args['entryId'], (int) $args['id']);
        Flash::set('success', 'Tidrad borttagen.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderAddPart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $data['added_by'] = Auth::user()['id'];

        if (empty($data['quantity'])) {
            Flash::set('error', 'Antal krävs.');
            return $this->redirect($response, "/maintenance/work-orders/{$id}");
        }

        $this->woRepo->addPart($id, $data);
        Flash::set('success', 'Material tillagt.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function workOrderDeletePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->deletePart((int) $args['partId'], (int) $args['id']);
        Flash::set('success', 'Material borttaget.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderApprove(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->woRepo->approve((int) $args['id'], Auth::user()['id'], $data['notes'] ?? '');
        Flash::set('success', 'Arbetsorder godkänd.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderReject(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->woRepo->reject((int) $args['id'], Auth::user()['id'], $data['notes'] ?? '');
        Flash::set('success', 'Arbetsorder avvisad.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderClose(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->close((int) $args['id'], Auth::user()['id']);
        Flash::set('success', 'Arbetsorder stängd.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderApproveTime(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->approveTimeEntry((int) $args['entryId'], Auth::user()['id']);
        Flash::set('success', 'Tidrad godkänd.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderApprovePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->approvePart((int) $args['partId'], Auth::user()['id']);
        Flash::set('success', 'Material godkänt.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function workOrderApproveAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->woRepo->approveAll((int) $args['id'], Auth::user()['id']);
        Flash::set('success', 'Alla rader godkända.');
        return $this->redirect($response, "/maintenance/work-orders/{$args['id']}");
    }

    public function archiveIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/work-orders/archive/index', [
            'title' => 'Arkiv – ZYNC ERP',
            'workOrders' => $this->woRepo->all(archived: true),
        ]);
    }

    public function archiveShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $wo = $this->woRepo->find($id);
        if (!$wo) {
            Flash::set('error', 'Arbetsorder hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders/archive');
        }
        return $this->render($response, 'maintenance/work-orders/archive/show', [
            'title' => $wo['wo_number'] . ' (Arkiv) – ZYNC ERP',
            'wo' => $wo,
            'timeEntries' => $this->woRepo->getTimeEntries($id),
            'parts' => $this->woRepo->getParts($id),
        ]);
    }

    // ─── SUPERVISOR ──────────────────────────────────────────

    public function supervisorDashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (($this->currentUser()['role_level'] ?? 0) < 5) {
            Flash::set('error', 'Åtkomst nekad.');
            return $this->redirect($response, '/maintenance');
        }

        $faults = $this->faultRepo->all();
        $workOrders = $this->woRepo->all();

        return $this->render($response, 'maintenance/supervisor/index', [
            'title' => 'Arbetsledardashboard – ZYNC ERP',
            'unassignedCount' => count(array_filter($faults, fn($f) => $f['status'] === 'reported')),
            'pendingApprovalCount' => count($this->woRepo->getPendingApproval()),
            'inProgressCount' => count(array_filter($workOrders, fn($w) => $w['status'] === 'in_progress')),
            'closedThisMonth' => count(array_filter($workOrders, fn($w) => $w['status'] === 'closed' && strtotime($w['updated_at'] ?? '') >= strtotime('first day of this month'))),
            'pendingApproval' => $this->woRepo->getPendingApproval(),
            'unassignedWOs' => $this->woRepo->getUnassigned(),
            'teamStats' => $this->woRepo->getTeamStats(),
            'users' => $this->getUsers(),
        ]);
    }

    public function supervisorUnassigned(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (($this->currentUser()['role_level'] ?? 0) < 5) {
            Flash::set('error', 'Åtkomst nekad.');
            return $this->redirect($response, '/maintenance');
        }
        return $this->render($response, 'maintenance/supervisor/unassigned', [
            'title' => 'Otilldelade arbetsordrar – ZYNC ERP',
            'workOrders' => $this->woRepo->getUnassigned(),
            'users' => $this->getUsers(),
        ]);
    }

    public function supervisorPendingApproval(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (($this->currentUser()['role_level'] ?? 0) < 5) {
            Flash::set('error', 'Åtkomst nekad.');
            return $this->redirect($response, '/maintenance');
        }
        return $this->render($response, 'maintenance/supervisor/pending-approval', [
            'title' => 'Väntar attestering – ZYNC ERP',
            'workOrders' => $this->woRepo->getPendingApproval(),
        ]);
    }

    public function supervisorMyTeam(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (($this->currentUser()['role_level'] ?? 0) < 5) {
            Flash::set('error', 'Åtkomst nekad.');
            return $this->redirect($response, '/maintenance');
        }
        return $this->render($response, 'maintenance/supervisor/my-team', [
            'title' => 'Mitt team – ZYNC ERP',
            'teamStats' => $this->woRepo->getTeamStats(),
        ]);
    }

    public function supervisorStatistics(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (($this->currentUser()['role_level'] ?? 0) < 5) {
            Flash::set('error', 'Åtkomst nekad.');
            return $this->redirect($response, '/maintenance');
        }

        $workOrders = $this->woRepo->all();
        $faults = $this->faultRepo->all();

        $byStatus = [];
        foreach ($workOrders as $wo) {
            $byStatus[$wo['status']] = ($byStatus[$wo['status']] ?? 0) + 1;
        }

        $byType = [];
        foreach ($workOrders as $wo) {
            $byType[$wo['type']] = ($byType[$wo['type']] ?? 0) + 1;
        }

        return $this->render($response, 'maintenance/supervisor/statistics', [
            'title' => 'Statistik – ZYNC ERP',
            'totalWOs' => count($workOrders),
            'totalFaults' => count($faults),
            'byStatus' => $byStatus,
            'byType' => $byType,
            'teamStats' => $this->woRepo->getTeamStats(),
        ]);
    }

    // ─── INSPECTIONS ─────────────────────────────────────────

    public function inspectionIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/inspections/index', [
            'title' => 'Besiktningar – ZYNC ERP',
            'inspections' => $this->inspRepo->all(),
            'overdueCount' => count($this->inspRepo->getOverdue()),
        ]);
    }

    public function inspectionCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/inspections/create', [
            'title' => 'Ny besiktningsobjekt – ZYNC ERP',
            'equipment' => $this->equipRepo->all(),
        ]);
    }

    public function inspectionStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, '/maintenance/inspections/create');
        }

        $id = $this->inspRepo->create($data);
        Flash::set('success', 'Besiktningsobjekt skapat.');
        return $this->redirect($response, "/maintenance/inspections/{$id}");
    }

    public function inspectionShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $insp = $this->inspRepo->find($id);
        if (!$insp) {
            Flash::set('error', 'Besiktningsobjekt hittades inte.');
            return $this->redirect($response, '/maintenance/inspections');
        }
        return $this->render($response, 'maintenance/inspections/show', [
            'title' => $insp['name'] . ' – ZYNC ERP',
            'inspection' => $insp,
            'inspections' => $this->inspRepo->getInspections($id),
        ]);
    }

    public function inspectionEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $insp = $this->inspRepo->find($id);
        if (!$insp) {
            Flash::set('error', 'Besiktningsobjekt hittades inte.');
            return $this->redirect($response, '/maintenance/inspections');
        }
        return $this->render($response, 'maintenance/inspections/edit', [
            'title' => 'Redigera besiktningsobjekt – ZYNC ERP',
            'inspection' => $insp,
            'equipment' => $this->equipRepo->all(),
        ]);
    }

    public function inspectionUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, "/maintenance/inspections/{$id}/edit");
        }

        $this->inspRepo->update($id, $data);
        Flash::set('success', 'Besiktningsobjekt uppdaterat.');
        return $this->redirect($response, "/maintenance/inspections/{$id}");
    }

    public function inspectionDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->inspRepo->delete((int) $args['id']);
        Flash::set('success', 'Besiktningsobjekt raderat.');
        return $this->redirect($response, '/maintenance/inspections');
    }

    public function inspectionRecord(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $data['recorded_by'] = Auth::user()['id'];

        $insp = $this->inspRepo->find($id);
        $data['inspection_interval_months'] = $insp['inspection_interval_months'] ?? 12;

        if (empty($data['inspection_date'])) {
            Flash::set('error', 'Besiktningsdatum krävs.');
            return $this->redirect($response, "/maintenance/inspections/{$id}");
        }

        $this->inspRepo->recordInspection($id, $data);
        Flash::set('success', 'Besiktning registrerad.');
        return $this->redirect($response, "/maintenance/inspections/{$id}");
    }

    public function inspectionOverdue(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/inspections/overdue', [
            'title' => 'Förfallna besiktningar – ZYNC ERP',
            'overdue' => $this->inspRepo->getOverdue(),
        ]);
    }

    // ─── HELPERS ─────────────────────────────────────────────

    private function currentUser(): array
    {
        return Auth::user() ?? [];
    }

    private function getDepartments(): array
    {
        return Database::pdo()->query(
            "SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getUsers(): array
    {
        return Database::pdo()->query(
            "SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getArticles(): array
    {
        return Database::pdo()->query(
            "SELECT id, article_number, name, unit, purchase_price FROM articles WHERE is_deleted = 0 AND is_active = 1 ORDER BY name"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCostCenters(): array
    {
        return Database::pdo()->query(
            "SELECT id, code, name FROM cost_centers WHERE is_active = 1 AND is_deleted = 0 ORDER BY code"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** GET /equipment/{id}/documents */
    public function equipmentDocuments(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $equipment = $this->equipRepo->find((int) $args['id']);
        if ($equipment === null) { return $this->notFound($response); }
        $stmt = \App\Core\Database::pdo()->prepare('SELECT * FROM equipment_documents WHERE equipment_id = ? AND is_deleted = 0 ORDER BY uploaded_at DESC');
        $stmt->execute([$args['id']]);
        $documents = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->render($response, 'equipment/documents', ['title' => 'Dokument – ' . $equipment['name'] . ' – ZYNC ERP', 'equipment' => $equipment, 'documents' => $documents]);
    }

    /** POST /equipment/{id}/documents/delete/{docId} */
    public function equipmentDeleteDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stmt = \App\Core\Database::pdo()->prepare('UPDATE equipment_documents SET is_deleted = 1 WHERE id = ? AND equipment_id = ?');
        $stmt->execute([$args['docId'], $args['id']]);
        Flash::set('success', 'Dokumentet har tagits bort.');
        return $this->redirect($response, '/equipment/' . $args['id'] . '/documents');
    }

    /** GET /machines/{id}/documents */
    public function machineDocuments(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $machine = $this->machineRepo->find((int) $args['id']);
        if ($machine === null) { return $this->notFound($response); }
        $stmt = \App\Core\Database::pdo()->prepare('SELECT * FROM equipment_documents WHERE machine_id = ? AND is_deleted = 0 ORDER BY uploaded_at DESC');
        $stmt->execute([$args['id']]);
        $documents = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->render($response, 'machines/documents', ['title' => 'Dokument – ' . $machine['name'] . ' – ZYNC ERP', 'machine' => $machine, 'documents' => $documents]);
    }

    /** POST /machines/{id}/documents/delete/{docId} */
    public function machineDeleteDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stmt = \App\Core\Database::pdo()->prepare('UPDATE equipment_documents SET is_deleted = 1 WHERE id = ? AND machine_id = ?');
        $stmt->execute([$args['docId'], $args['id']]);
        Flash::set('success', 'Dokumentet har tagits bort.');
        return $this->redirect($response, '/machines/' . $args['id'] . '/documents');
    }

    /** POST /machines/{id}/spare-parts */
    public function machineAddSparePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $stmt = \App\Core\Database::pdo()->prepare('INSERT INTO machine_spare_parts (machine_id, article_id, quantity_recommended, notes, is_critical) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$args['id'], $body['article_id'] ?? null, $body['quantity_recommended'] ?? 1, $body['notes'] ?? null, isset($body['is_critical']) ? 1 : 0]);
        Flash::set('success', 'Reservdel tillagd.');
        return $this->redirect($response, '/machines/' . $args['id']);
    }

    /** POST /machines/{id}/spare-parts/{partId}/delete */
    public function machineDeleteSparePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stmt = \App\Core\Database::pdo()->prepare('DELETE FROM machine_spare_parts WHERE id = ? AND machine_id = ?');
        $stmt->execute([$args['partId'], $args['id']]);
        Flash::set('success', 'Reservdel borttagen.');
        return $this->redirect($response, '/machines/' . $args['id']);
    }
}
