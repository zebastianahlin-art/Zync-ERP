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
use App\Models\InspectionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MaintenanceController extends Controller
{
    private EquipmentRepository   $equipmentRepo;
    private MachineRepository     $machineRepo;
    private FaultReportRepository $faultRepo;
    private WorkOrderRepository   $workOrderRepo;
    private InspectionRepository  $inspectionRepo;

    public function __construct()
    {
        parent::__construct();
        $this->equipmentRepo   = new EquipmentRepository();
        $this->machineRepo     = new MachineRepository();
        $this->faultRepo       = new FaultReportRepository();
        $this->workOrderRepo   = new WorkOrderRepository();
        $this->inspectionRepo  = new InspectionRepository();
    }

    // ─── UTRUSTNING (EQUIPMENT) ───────────────────────────────

    public function equipmentIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'equipment/index', [
            'title'     => 'Utrustning – ZYNC ERP',
            'equipment' => $this->equipmentRepo->all(),
            'success'   => Flash::get('success'),
            'error'     => Flash::get('error'),
        ]);
    }

    public function createEquipment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'equipment/create', [
            'title'       => 'Ny utrustning – ZYNC ERP',
            'departments' => $this->getDepartments(),
            'users'       => $this->getUsers(),
        ]);
    }

    public function storeEquipment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        if (empty($data['title']) && empty($data['name'])) {
            Flash::set('error', 'Titel/namn krävs.');
            return $this->redirect($response, '/equipment/create');
        }

        $data['created_by'] = Auth::user()['id'];
        $id = $this->equipmentRepo->create($data);
        Flash::set('success', 'Utrustning skapad.');
        return $this->redirect($response, "/equipment/{$id}");
    }

    public function showEquipment(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $equipment = $this->equipmentRepo->find($id);
        if (!$equipment) {
            Flash::set('error', 'Utrustningen hittades inte.');
            return $this->redirect($response, '/equipment');
        }

        return $this->render($response, 'equipment/show', [
            'title'     => ($equipment['title'] ?? $equipment['name'] ?? 'Utrustning') . ' – ZYNC ERP',
            'equipment' => $equipment,
            'machines'  => $this->machineRepo->getByEquipment($id),
            'success'   => Flash::get('success'),
            'error'     => Flash::get('error'),
        ]);
    }

    public function editEquipment(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $equipment = $this->equipmentRepo->find($id);
        if (!$equipment) {
            Flash::set('error', 'Utrustningen hittades inte.');
            return $this->redirect($response, '/equipment');
        }

        return $this->render($response, 'equipment/edit', [
            'title'     => 'Redigera utrustning – ZYNC ERP',
            'equipment' => $equipment,
            'departments' => $this->getDepartments(),
            'parentList'  => $this->equipmentRepo->all(),
        ]);
    }

    public function updateEquipment(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->equipmentRepo->update($id, $data);
        Flash::set('success', 'Utrustning uppdaterad.');
        return $this->redirect($response, "/equipment/{$id}");
    }

    public function deleteEquipment(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->equipmentRepo->delete($id);
        Flash::set('success', 'Utrustning raderad.');
        return $this->redirect($response, '/equipment');
    }

    // ─── MASKINER (MACHINES) ──────────────────────────────────

    public function machineIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'machines/index', [
            'title'    => 'Maskiner – ZYNC ERP',
            'machines' => $this->machineRepo->all(),
            'success'  => Flash::get('success'),
            'error'    => Flash::get('error'),
        ]);
    }

    public function createMachine(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'machines/create', [
            'title'       => 'Ny maskin – ZYNC ERP',
            'departments' => $this->getDepartments(),
            'equipment'   => $this->equipmentRepo->all(),
        ]);
    }

    public function storeMachine(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        if (empty($data['name'])) {
            Flash::set('error', 'Namn krävs.');
            return $this->redirect($response, '/machines/create');
        }

        $id = $this->machineRepo->create($data);
        Flash::set('success', 'Maskin skapad.');
        return $this->redirect($response, "/machines/{$id}");
    }

    public function showMachine(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $machine = $this->machineRepo->find($id);
        if (!$machine) {
            Flash::set('error', 'Maskinen hittades inte.');
            return $this->redirect($response, '/machines');
        }

        return $this->render($response, 'machines/show', [
            'title'        => ($machine['name'] ?? 'Maskin') . ' – ZYNC ERP',
            'machine'      => $machine,
            'faultReports' => $this->faultRepo->getByMachine($id),
            'success'      => Flash::get('success'),
            'error'        => Flash::get('error'),
        ]);
    }

    public function editMachine(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $machine = $this->machineRepo->find($id);
        if (!$machine) {
            Flash::set('error', 'Maskinen hittades inte.');
            return $this->redirect($response, '/machines');
        }

        return $this->render($response, 'machines/edit', [
            'title'       => 'Redigera maskin – ZYNC ERP',
            'machine'     => $machine,
            'departments' => $this->getDepartments(),
            'equipment'   => $this->equipmentRepo->all(),
        ]);
    }

    public function updateMachine(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->machineRepo->update($id, $data);
        Flash::set('success', 'Maskin uppdaterad.');
        return $this->redirect($response, "/machines/{$id}");
    }

    public function deleteMachine(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->machineRepo->delete($id);
        Flash::set('success', 'Maskin raderad.');
        return $this->redirect($response, '/machines');
    }

    // ─── UNDERHÅLLSDASHBOARD ──────────────────────────────────

    public function maintenanceDashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $allFaults = $this->faultRepo->all();
        $allOrders = $this->workOrderRepo->all();

        $openFaults = count(array_filter($allFaults, fn($f) => !in_array($f['status'] ?? '', ['resolved', 'closed'])));
        $activeWorkOrders = count(array_filter($allOrders, fn($o) => in_array($o['status'] ?? '', ['assigned', 'in_progress', 'work_completed'])));
        $dueMachines = count($this->machineRepo->getDueForMaintenance());
        $recentFaults = array_slice($allFaults, 0, 10);

        $activeOrders = array_merge(
            $this->workOrderRepo->getByStatus('in_progress'),
            $this->workOrderRepo->getByStatus('assigned')
        );

        return $this->render($response, 'maintenance/index', [
            'title'            => 'Underhåll – ZYNC ERP',
            'openFaults'       => $openFaults,
            'activeWorkOrders' => $activeWorkOrders,
            'dueMachines'      => $dueMachines,
            'recentFaults'     => $recentFaults,
            'activeOrders'     => $activeOrders,
            'stats'            => $this->workOrderRepo->getStatistics(),
            'success'          => Flash::get('success'),
            'error'            => Flash::get('error'),
        ]);
    }

    // ─── FELRAPPORTER (FAULT REPORTS) ─────────────────────────

    public function faultIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/faults/index', [
            'title'   => 'Felrapporter – ZYNC ERP',
            'faults'  => $this->faultRepo->all(),
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    public function createFault(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/faults/create', [
            'title'       => 'Ny felrapport – ZYNC ERP',
            'machines'    => $this->machineRepo->all(),
            'equipment'   => $this->equipmentRepo->all(),
            'departments' => $this->getDepartments(),
            'users'       => $this->getUsers(),
        ]);
    }

    public function storeFault(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, '/maintenance/faults/create');
        }

        $user = Auth::user();
        $data['reported_by'] = $user['id'];
        $data['created_by']  = $user['id'];
        $data['fault_number'] = $this->faultRepo->generateFaultNumber();

        $id = $this->faultRepo->create($data);
        Flash::set('success', 'Felrapport skapad.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    public function showFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $fault = $this->faultRepo->find($id);
        if (!$fault) {
            Flash::set('error', 'Felrapporten hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }

        return $this->render($response, 'maintenance/faults/show', [
            'title'   => ($fault['fault_number'] ?? 'Felrapport') . ' – ZYNC ERP',
            'fault'   => $fault,
            'users'   => $this->getUsers(),
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    public function editFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $fault = $this->faultRepo->find($id);
        if (!$fault) {
            Flash::set('error', 'Felrapporten hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }

        return $this->render($response, 'maintenance/faults/edit', [
            'title'       => 'Redigera felrapport – ZYNC ERP',
            'fault'       => $fault,
            'machines'    => $this->machineRepo->all(),
            'equipment'   => $this->equipmentRepo->all(),
            'departments' => $this->getDepartments(),
            'users'       => $this->getUsers(),
        ]);
    }

    public function updateFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->faultRepo->update($id, $data);
        Flash::set('success', 'Felrapport uppdaterad.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    public function deleteFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->faultRepo->delete($id);
        Flash::set('success', 'Felrapport raderad.');
        return $this->redirect($response, '/maintenance/faults');
    }

    public function acknowledgeFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->faultRepo->updateStatus($id, 'acknowledged', Auth::user()['id']);
        Flash::set('success', 'Felrapport bekräftad.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    public function assignFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $assignedTo = $data['assigned_to'] ?? null;
        $this->faultRepo->updateStatus($id, 'assigned', $assignedTo);
        Flash::set('success', 'Felrapport tilldelad.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    public function resolveFault(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $resolution = $data['resolution'] ?? null;

        $this->faultRepo->updateStatus($id, 'resolved', Auth::user()['id']);
        if ($resolution !== null) {
            $this->faultRepo->update($id, ['resolution' => $resolution]);
        }

        Flash::set('success', 'Felrapport löst.');
        return $this->redirect($response, "/maintenance/faults/{$id}");
    }

    // ─── ARBETSORDER (WORK ORDERS) ────────────────────────────

    public function workOrderIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array) $request->getQueryParams();
        $status = $params['status'] ?? null;

        $orders = $status
            ? $this->workOrderRepo->getByStatus($status)
            : $this->workOrderRepo->all();

        return $this->render($response, 'maintenance/work-orders/index', [
            'title'   => 'Arbetsorder – ZYNC ERP',
            'orders'  => $orders,
            'status'  => $status,
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    public function createWorkOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $openFaults = array_filter(
            $this->faultRepo->all(),
            fn($f) => !in_array($f['status'] ?? '', ['resolved', 'closed'])
        );

        return $this->render($response, 'maintenance/work-orders/create', [
            'title'        => 'Ny arbetsorder – ZYNC ERP',
            'machines'     => $this->machineRepo->all(),
            'equipment'    => $this->equipmentRepo->all(),
            'faultReports' => array_values($openFaults),
            'departments'  => $this->getDepartments(),
            'users'        => $this->getUsers(),
            'costCenters'  => $this->getCostCenters(),
        ]);
    }

    public function storeWorkOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, '/maintenance/work-orders/create');
        }

        $data['created_by'] = Auth::user()['id'];
        $id = $this->workOrderRepo->create($data);
        Flash::set('success', 'Arbetsorder skapad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function showWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $order = $this->workOrderRepo->find($id);
        if (!$order) {
            Flash::set('error', 'Arbetsordern hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }

        return $this->render($response, 'maintenance/work-orders/show', [
            'title'       => ($order['work_order_number'] ?? 'Arbetsorder') . ' – ZYNC ERP',
            'order'       => $order,
            'timeEntries' => $this->workOrderRepo->getTimeEntries($id),
            'parts'       => $this->workOrderRepo->getParts($id),
            'users'       => $this->getUsers(),
            'articles'    => $this->getArticles(),
            'success'     => Flash::get('success'),
            'error'       => Flash::get('error'),
        ]);
    }

    public function editWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $order = $this->workOrderRepo->find($id);
        if (!$order) {
            Flash::set('error', 'Arbetsordern hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }

        return $this->render($response, 'maintenance/work-orders/edit', [
            'title'       => 'Redigera arbetsorder – ZYNC ERP',
            'order'       => $order,
            'machines'    => $this->machineRepo->all(),
            'equipment'   => $this->equipmentRepo->all(),
            'departments' => $this->getDepartments(),
            'users'       => $this->getUsers(),
            'costCenters' => $this->getCostCenters(),
        ]);
    }

    public function updateWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->workOrderRepo->update($id, $data);
        Flash::set('success', 'Arbetsorder uppdaterad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function deleteWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->workOrderRepo->delete($id);
        Flash::set('success', 'Arbetsorder raderad.');
        return $this->redirect($response, '/maintenance/work-orders');
    }

    // ─── STATUSÖVERGÅNGAR FÖR ARBETSORDER ────────────────────

    public function assignWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $assignedTo = $data['assigned_to'] ?? null;
        $this->workOrderRepo->assign($id, $assignedTo, Auth::user()['id']);
        Flash::set('success', 'Arbetsorder tilldelad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function startWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->workOrderRepo->updateStatus($id, 'in_progress');
        Flash::set('success', 'Arbetsorder påbörjad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function completeWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $notes = $data['completion_notes'] ?? null;
        $this->workOrderRepo->updateStatus($id, 'work_completed', null, $notes);
        Flash::set('success', 'Arbetsorder slutförd.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function submitForApproval(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->workOrderRepo->updateStatus($id, 'pending_approval');
        Flash::set('success', 'Arbetsorder skickad för godkännande.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function approveWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $userId = Auth::user()['id'];
        $notes = $data['approval_notes'] ?? null;
        $this->workOrderRepo->updateStatus($id, 'approved', $userId, $notes);
        Flash::set('success', 'Arbetsorder godkänd.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function rejectWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $userId = Auth::user()['id'];
        $reason = $data['rejected_reason'] ?? null;
        $this->workOrderRepo->updateStatus($id, 'rejected', $userId, null, $reason);
        Flash::set('success', 'Arbetsorder avvisad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function closeWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $userId = Auth::user()['id'];
        $this->workOrderRepo->updateStatus($id, 'closed', $userId);
        Flash::set('success', 'Arbetsorder stängd.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function archiveWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->workOrderRepo->updateStatus($id, 'archived');
        Flash::set('success', 'Arbetsorder arkiverad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    // ─── TIDREGISTRERING ──────────────────────────────────────

    public function addTimeEntry(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['hours'])) {
            Flash::set('error', 'Timmar krävs.');
            return $this->redirect($response, "/maintenance/work-orders/{$id}");
        }

        $data['user_id'] = Auth::user()['id'];
        $this->workOrderRepo->addTimeEntry($id, $data);
        Flash::set('success', 'Tid registrerad.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function removeTimeEntry(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $entryId = (int) $args['entryId'];
        $this->workOrderRepo->removeTimeEntry($entryId, $id);
        Flash::set('success', 'Tidspost borttagen.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function approveTimeEntry(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $entryId = (int) $args['entryId'];
        $userId = Auth::user()['id'];
        $this->workOrderRepo->approveTimeEntry($entryId, $userId);
        Flash::set('success', 'Tidspost godkänd.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    // ─── RESERVDELAR ──────────────────────────────────────────

    public function addPart(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['article_id']) || empty($data['quantity'])) {
            Flash::set('error', 'Artikel och antal krävs.');
            return $this->redirect($response, "/maintenance/work-orders/{$id}");
        }

        $data['added_by'] = Auth::user()['id'];
        $this->workOrderRepo->addPart($id, $data);
        Flash::set('success', 'Reservdel tillagd.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function removePart(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $partId = (int) $args['partId'];
        $this->workOrderRepo->removePart($partId, $id);
        Flash::set('success', 'Reservdel borttagen.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    public function approvePart(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $partId = (int) $args['partId'];
        $userId = Auth::user()['id'];
        $this->workOrderRepo->approvePart($partId, $userId);
        Flash::set('success', 'Reservdel godkänd.');
        return $this->redirect($response, "/maintenance/work-orders/{$id}");
    }

    // ─── ARBETSORDERARKIV ─────────────────────────────────────

    public function workOrderArchive(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/work-orders/archive/index', [
            'title'   => 'Arbetsorderarkiv – ZYNC ERP',
            'orders'  => $this->workOrderRepo->getByStatus('archived'),
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    public function showArchivedWorkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $order = $this->workOrderRepo->find($id);
        if (!$order) {
            Flash::set('error', 'Arbetsordern hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders/archive');
        }

        return $this->render($response, 'maintenance/work-orders/archive/show', [
            'title'       => ($order['work_order_number'] ?? 'Arbetsorder') . ' – ZYNC ERP',
            'order'       => $order,
            'timeEntries' => $this->workOrderRepo->getTimeEntries($id),
            'parts'       => $this->workOrderRepo->getParts($id),
            'users'       => $this->getUsers(),
            'articles'    => $this->getArticles(),
            'success'     => Flash::get('success'),
            'error'       => Flash::get('error'),
        ]);
    }

    // ─── SUPERVISOR-DASHBOARD ─────────────────────────────────

    public function supervisorDashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $unassigned     = $this->workOrderRepo->getUnassigned();
        $pendingApproval = $this->workOrderRepo->getPendingApproval();

        return $this->render($response, 'maintenance/supervisor/index', [
            'title'                => 'Supervisor – ZYNC ERP',
            'unassignedCount'      => count($unassigned),
            'pendingApprovalCount' => count($pendingApproval),
            'unassigned'           => $unassigned,
            'pendingApproval'      => $pendingApproval,
            'stats'                => $this->workOrderRepo->getStatistics(),
            'success'              => Flash::get('success'),
            'error'                => Flash::get('error'),
        ]);
    }

    public function supervisorUnassigned(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/supervisor/unassigned', [
            'title'      => 'Otilldelade arbetsorder – ZYNC ERP',
            'unassigned' => $this->workOrderRepo->getUnassigned(),
            'users'      => $this->getUsers(),
            'success'    => Flash::get('success'),
            'error'      => Flash::get('error'),
        ]);
    }

    public function supervisorPendingApproval(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/supervisor/pending-approval', [
            'title'          => 'Väntar på godkännande – ZYNC ERP',
            'pendingApproval' => $this->workOrderRepo->getPendingApproval(),
            'success'        => Flash::get('success'),
            'error'          => Flash::get('error'),
        ]);
    }

    public function supervisorMyTeam(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $allOrders = array_filter(
            $this->workOrderRepo->all(),
            fn($o) => ($o['status'] ?? '') !== 'archived'
        );

        $grouped = [];
        foreach ($allOrders as $order) {
            $key = $order['assigned_to'] ?? 0;
            $grouped[$key][] = $order;
        }

        return $this->render($response, 'maintenance/supervisor/my-team', [
            'title'   => 'Mitt team – ZYNC ERP',
            'grouped' => $grouped,
            'users'   => $this->getUsers(),
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    public function supervisorStatistics(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/supervisor/statistics', [
            'title'   => 'Statistik – ZYNC ERP',
            'stats'   => $this->workOrderRepo->getStatistics(),
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    // ─── INSPEKTIONER ─────────────────────────────────────────

    public function inspectionIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/inspections/index', [
            'title'       => 'Inspektioner – ZYNC ERP',
            'inspections' => $this->inspectionRepo->all(),
            'success'     => Flash::get('success'),
            'error'       => Flash::get('error'),
        ]);
    }

    public function createInspection(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/inspections/create', [
            'title'       => 'Ny inspektion – ZYNC ERP',
            'equipment'   => $this->equipmentRepo->all(),
            'machines'    => $this->machineRepo->all(),
            'departments' => $this->getDepartments(),
            'users'       => $this->getUsers(),
        ]);
    }

    public function storeInspection(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, '/maintenance/inspections/create');
        }

        $data['created_by'] = Auth::user()['id'];
        $id = $this->inspectionRepo->create($data);
        Flash::set('success', 'Inspektion skapad.');
        return $this->redirect($response, "/maintenance/inspections/{$id}");
    }

    public function showInspection(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $inspection = $this->inspectionRepo->find($id);
        if (!$inspection) {
            Flash::set('error', 'Inspektionen hittades inte.');
            return $this->redirect($response, '/maintenance/inspections');
        }

        return $this->render($response, 'maintenance/inspections/show', [
            'title'      => ($inspection['title'] ?? 'Inspektion') . ' – ZYNC ERP',
            'inspection' => $inspection,
            'success'    => Flash::get('success'),
            'error'      => Flash::get('error'),
        ]);
    }

    public function editInspection(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $inspection = $this->inspectionRepo->find($id);
        if (!$inspection) {
            Flash::set('error', 'Inspektionen hittades inte.');
            return $this->redirect($response, '/maintenance/inspections');
        }

        return $this->render($response, 'maintenance/inspections/edit', [
            'title'       => 'Redigera inspektion – ZYNC ERP',
            'inspection'  => $inspection,
            'equipment'   => $this->equipmentRepo->all(),
            'machines'    => $this->machineRepo->all(),
            'departments' => $this->getDepartments(),
            'users'       => $this->getUsers(),
        ]);
    }

    public function updateInspection(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->inspectionRepo->update($id, $data);
        Flash::set('success', 'Inspektion uppdaterad.');
        return $this->redirect($response, "/maintenance/inspections/{$id}");
    }

    public function deleteInspection(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->inspectionRepo->delete($id);
        Flash::set('success', 'Inspektion raderad.');
        return $this->redirect($response, '/maintenance/inspections');
    }

    public function inspectionOverdue(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/inspections/overdue', [
            'title'       => 'Försenade inspektioner – ZYNC ERP',
            'inspections' => $this->inspectionRepo->getOverdue(),
            'success'     => Flash::get('success'),
            'error'       => Flash::get('error'),
        ]);
    }

    public function completeInspection(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->inspectionRepo->complete($id, $data);
        Flash::set('success', 'Inspektion slutförd.');
        return $this->redirect($response, "/maintenance/inspections/{$id}");
    }

    // ─── HELPERS ─────────────────────────────────────────────

    private function getDepartments(): array
    {
        return Database::pdo()->query("SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getUsers(): array
    {
        return Database::pdo()->query("SELECT id, full_name, username FROM users WHERE is_deleted = 0 AND is_active = 1 ORDER BY full_name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getArticles(): array
    {
        return Database::pdo()->query("SELECT id, article_number, name, unit, purchase_price FROM articles WHERE is_deleted = 0 AND is_active = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCostCenters(): array
    {
        return Database::pdo()->query("SELECT id, code, name FROM cost_centers WHERE is_active = 1 AND is_deleted = 0 ORDER BY code")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
