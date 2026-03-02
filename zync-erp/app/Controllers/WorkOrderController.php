<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\WorkOrderRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WorkOrderController extends Controller
{
    private WorkOrderRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new WorkOrderRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $p = $request->getQueryParams();
        return $this->render($response, 'maintenance/workorders/index', [
            'title'      => 'Arbetsordrar – ZYNC ERP',
            'orders'     => $this->repo->all($p['status'] ?? null, $p['type'] ?? null, isset($p['assigned']) ? (int) $p['assigned'] : null, isset($p['equipment']) ? (int) $p['equipment'] : null),
            'stats'      => $this->repo->stats(),
            'equipment'  => $this->repo->allEquipment(),
            'users'      => $this->repo->allUsers(),
            'filter'     => $p,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $p = $request->getQueryParams();
        $wo = [
            'type' => 'corrective', 'priority' => 'medium', 'status' => 'draft',
            'wo_number' => $this->repo->nextNumber(),
            'fault_report_id' => $p['fault'] ?? '',
            'equipment_id' => $p['equipment'] ?? '',
        ];
        return $this->render($response, 'maintenance/workorders/form', [
            'title'     => 'Ny arbetsorder – ZYNC ERP',
            'wo'        => $wo,
            'equipment' => $this->repo->allEquipment(),
            'users'     => $this->repo->allUsers(),
            'errors'    => [],
            'isNew'     => true,
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseBody($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'maintenance/workorders/form', [
                'title'     => 'Ny arbetsorder – ZYNC ERP',
                'wo'        => $data,
                'equipment' => $this->repo->allEquipment(),
                'users'     => $this->repo->allUsers(),
                'errors'    => $errors,
                'isNew'     => true,
            ]);
        }

        $data['created_by']  = Auth::id();
        $data['assigned_by'] = $data['assigned_to'] ? Auth::id() : null;
        if ($data['assigned_to']) $data['status'] = 'assigned';
        $id = $this->repo->create($data);
        Flash::set('success', 'Arbetsorder har skapats.');
        return $this->redirect($response, '/maintenance/work-orders/' . $id);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $wo = $this->repo->find((int) $args['id']);
        if (!$wo) {
            Flash::set('error', 'Arbetsorder hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }
        return $this->render($response, 'maintenance/workorders/show', [
            'title'     => $wo['wo_number'] . ' – ZYNC ERP',
            'wo'        => $wo,
            'time'      => $this->repo->timeEntries((int) $wo['id']),
            'comments'  => $this->repo->comments((int) $wo['id']),
            'materials' => $this->repo->materials((int) $wo['id']),
            'users'     => $this->repo->allUsers(),
        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $wo = $this->repo->find((int) $args['id']);
        if (!$wo) {
            Flash::set('error', 'Arbetsorder hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }
        return $this->render($response, 'maintenance/workorders/form', [
            'title'     => 'Redigera arbetsorder – ZYNC ERP',
            'wo'        => $wo,
            'equipment' => $this->repo->allEquipment(),
            'users'     => $this->repo->allUsers(),
            'errors'    => [],
            'isNew'     => false,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $wo = $this->repo->find($id);
        if (!$wo) {
            Flash::set('error', 'Arbetsorder hittades inte.');
            return $this->redirect($response, '/maintenance/work-orders');
        }

        $data   = $this->parseBody($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'maintenance/workorders/form', [
                'title'     => 'Redigera arbetsorder – ZYNC ERP',
                'wo'        => array_merge($wo, $data),
                'equipment' => $this->repo->allEquipment(),
                'users'     => $this->repo->allUsers(),
                'errors'    => $errors,
                'isNew'     => false,
            ]);
        }

        $data['assigned_by'] = $data['assigned_to'] ? Auth::id() : null;
        $this->repo->update($id, $data);
        Flash::set('success', 'Arbetsorder har uppdaterats.');
        return $this->redirect($response, '/maintenance/work-orders/' . $id);
    }

    public function updateStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $this->repo->updateStatus($id, $body['status'], Auth::id());
        $this->repo->addComment($id, Auth::id(), 'Status ändrad till: ' . $body['status'], 'status_change');
        Flash::set('success', 'Status uppdaterad.');
        return $this->redirect($response, '/maintenance/work-orders/' . $id);
    }

    public function addTime(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $this->repo->addTime($id, Auth::id(), $body['date'], (float) $body['hours'], $body['description'] ?? null);
        $this->repo->addComment($id, Auth::id(), 'Tid rapporterad: ' . $body['hours'] . 'h', 'time_log');
        Flash::set('success', 'Tid rapporterad.');
        return $this->redirect($response, '/maintenance/work-orders/' . $id);
    }

    public function addComment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $this->repo->addComment($id, Auth::id(), trim($body['comment']));
        Flash::set('success', 'Kommentar tillagd.');
        return $this->redirect($response, '/maintenance/work-orders/' . $id);
    }

    public function withdrawMaterial(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $this->repo->withdrawMaterial($id, (int) $body['article_id'], (float) $body['quantity'], Auth::id(), $body['notes'] ?? null);
        Flash::set('success', 'Material uttaget.');
        return $this->redirect($response, '/maintenance/work-orders/' . $id);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Arbetsorder har tagits bort.');
        return $this->redirect($response, '/maintenance/work-orders');
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'wo_number'       => trim((string) ($b['wo_number'] ?? '')),
            'fault_report_id' => (int) ($b['fault_report_id'] ?? 0) ?: null,
            'equipment_id'    => (int) ($b['equipment_id'] ?? 0),
            'title'           => trim((string) ($b['title'] ?? '')),
            'description'     => trim((string) ($b['description'] ?? '')),
            'type'            => trim((string) ($b['type'] ?? 'corrective')),
            'priority'        => trim((string) ($b['priority'] ?? 'medium')),
            'status'          => trim((string) ($b['status'] ?? 'draft')),
            'assigned_to'     => (int) ($b['assigned_to'] ?? 0) ?: null,
            'planned_start'   => trim((string) ($b['planned_start'] ?? '')),
            'planned_end'     => trim((string) ($b['planned_end'] ?? '')),
            'estimated_hours' => trim((string) ($b['estimated_hours'] ?? '')),
            'notes'           => trim((string) ($b['notes'] ?? '')),
            'root_cause'      => trim((string) ($b['root_cause'] ?? '')),
            'action_taken'    => trim((string) ($b['action_taken'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['equipment_id'] < 1) $errors['equipment_id'] = 'Välj utrustning.';
        if ($data['title'] === '')      $errors['title']        = 'Rubrik krävs.';
        return $errors;
    }
}
