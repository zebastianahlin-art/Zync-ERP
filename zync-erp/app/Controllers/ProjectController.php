<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\ProjectRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProjectController extends Controller
{
    private ProjectRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ProjectRepository();
    }

    /** GET /projects */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'projects/index', [
            'title'    => 'Projekt – ZYNC ERP',
            'projects' => $this->repo->all(),
        ]);
    }

    /** GET /projects/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'projects/create', [
            'title'     => 'Nytt projekt – ZYNC ERP',
            'customers' => $this->repo->allCustomers(),
            'users'     => $this->repo->allUsers(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /projects */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'projects/create', [
                'title'     => 'Nytt projekt – ZYNC ERP',
                'customers' => $this->repo->allCustomers(),
                'users'     => $this->repo->allUsers(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->create($data);
        Flash::set('success', 'Projektet skapades.');
        return $this->redirect($response, '/projects/' . $id);
    }

    /** GET /projects/{id} */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $project = $this->repo->find($id);
        if ($project === null) {
            return $this->notFound($response);
        }

        // Recalculate actual_cost and merge result directly to avoid a second DB round-trip
        $this->repo->recalcActualCost($id);
        $actualCost = $this->repo->getActualCost($id);
        $project['actual_cost'] = $actualCost;

        return $this->render($response, 'projects/show', [
            'title'         => htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'project'       => $project,
            'tasks'         => $this->repo->tasks($id),
            'budget'        => $this->repo->budgetLines($id),
            'stakeholders'  => $this->repo->stakeholders($id),
            'linkedPOs'     => $this->repo->linkedPurchaseOrders($id),
            'allPOs'        => $this->repo->allPurchaseOrders(),
            'costs'         => $this->repo->costs($id),
            'success'       => Flash::get('success'),
        ]);
    }

    /** GET /projects/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $project = $this->repo->find((int) $args['id']);
        if ($project === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'projects/edit', [
            'title'     => 'Redigera projekt – ZYNC ERP',
            'project'   => $project,
            'customers' => $this->repo->allCustomers(),
            'users'     => $this->repo->allUsers(),
            'errors'    => [],
        ]);
    }

    /** POST /projects/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $project = $this->repo->find($id);
        if ($project === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'projects/edit', [
                'title'     => 'Redigera projekt – ZYNC ERP',
                'project'   => $project,
                'customers' => $this->repo->allCustomers(),
                'users'     => $this->repo->allUsers(),
                'errors'    => $errors,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Projektet uppdaterades.');
        return $this->redirect($response, '/projects/' . $id);
    }

    /** POST /projects/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Projektet togs bort.');
        }
        return $this->redirect($response, '/projects');
    }

    /** POST /projects/{id}/tasks */
    public function addTask(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $title = trim((string) ($body['title'] ?? ''));
        if ($title !== '') {
            $this->repo->addTask($id, [
                'title'       => $title,
                'assigned_to' => trim((string) ($body['assigned_to'] ?? '')),
                'due_date'    => trim((string) ($body['due_date'] ?? '')),
                'priority'    => in_array($body['priority'] ?? '', ['low','normal','high','urgent'], true) ? $body['priority'] : 'normal',
                'status'      => in_array($body['status'] ?? '', ['todo','in_progress','done','cancelled'], true) ? $body['status'] : 'todo',
            ]);
            Flash::set('success', 'Uppgiften lades till.');
        }
        return $this->redirect($response, '/projects/' . $id);
    }

    /** POST /projects/{id}/tasks/{taskId} */
    public function updateTask(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $taskId = (int) $args['taskId'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $title = trim((string) ($body['title'] ?? ''));
        if ($title !== '') {
            $this->repo->updateTask($taskId, [
                'title'       => $title,
                'assigned_to' => trim((string) ($body['assigned_to'] ?? '')),
                'due_date'    => trim((string) ($body['due_date'] ?? '')),
                'priority'    => in_array($body['priority'] ?? '', ['low','normal','high','urgent'], true) ? $body['priority'] : 'normal',
                'status'      => in_array($body['status'] ?? '', ['todo','in_progress','done','cancelled'], true) ? $body['status'] : 'todo',
            ]);
            Flash::set('success', 'Uppgiften uppdaterades.');
        }
        return $this->redirect($response, '/projects/' . $id);
    }

    /** POST /projects/{id}/tasks/{taskId}/delete */
    public function deleteTask(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $taskId = (int) $args['taskId'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $this->repo->deleteTask($taskId);
        Flash::set('success', 'Uppgiften togs bort.');
        return $this->redirect($response, '/projects/' . $id);
    }

    /** POST /projects/{id}/budget */
    public function addBudgetLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $desc = trim((string) ($body['description'] ?? ''));
        if ($desc !== '') {
            $this->repo->addBudgetLine($id, [
                'description'     => $desc,
                'budgeted_amount' => trim((string) ($body['budgeted_amount'] ?? '0')),
                'actual_amount'   => trim((string) ($body['actual_amount'] ?? '0')),
            ]);
            Flash::set('success', 'Budgetraden lades till.');
        }
        return $this->redirect($response, '/projects/' . $id);
    }

    /** POST /projects/{id}/budget/{lineId}/delete */
    public function deleteBudgetLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $lineId = (int) $args['lineId'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $this->repo->deleteBudgetLine($lineId);
        Flash::set('success', 'Budgetraden togs bort.');
        return $this->redirect($response, '/projects/' . $id);
    }

    // ─── C2: Stakeholders ────────────────────────────────────────────────────

    /** POST /projects/{id}/stakeholders */
    public function addStakeholder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $name = trim((string) ($body['name'] ?? ''));
        if ($name !== '') {
            $this->repo->addStakeholder($id, [
                'name'  => $name,
                'role'  => trim((string) ($body['role'] ?? 'Teammedlem')),
                'email' => trim((string) ($body['email'] ?? '')),
                'phone' => trim((string) ($body['phone'] ?? '')),
                'notes' => trim((string) ($body['notes'] ?? '')),
            ]);
            Flash::set('success', 'Intressenten lades till.');
        }
        return $this->redirect($response, '/projects/' . $id . '#stakeholders');
    }

    /** POST /projects/{id}/stakeholders/{stakeholderId}/delete */
    public function deleteStakeholder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $this->repo->deleteStakeholder((int) $args['stakeholderId']);
        Flash::set('success', 'Intressenten togs bort.');
        return $this->redirect($response, '/projects/' . $id . '#stakeholders');
    }

    // ─── C3: Koppling till Inköp ─────────────────────────────────────────────

    /** POST /projects/{id}/purchase-orders */
    public function linkPurchaseOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $poId = (int) ($body['purchase_order_id'] ?? 0);
        if ($poId > 0) {
            $this->repo->linkPurchaseOrder($id, $poId, trim((string) ($body['notes'] ?? '')));
            $this->repo->recalcActualCost($id);
            Flash::set('success', 'Inköpsordern kopplades till projektet.');
        }
        return $this->redirect($response, '/projects/' . $id . '#purchases');
    }

    /** POST /projects/{id}/purchase-orders/{linkId}/delete */
    public function unlinkPurchaseOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $this->repo->unlinkPurchaseOrder((int) $args['linkId']);
        $this->repo->recalcActualCost($id);
        Flash::set('success', 'Inköpsordern kopplades bort.');
        return $this->redirect($response, '/projects/' . $id . '#purchases');
    }

    // ─── C6: Kostnader ───────────────────────────────────────────────────────

    /** POST /projects/{id}/costs */
    public function addCost(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $desc = trim((string) ($body['description'] ?? ''));
        if ($desc !== '') {
            $this->repo->addCost($id, [
                'description' => $desc,
                'amount'      => trim((string) ($body['amount'] ?? '0')),
                'cost_date'   => trim((string) ($body['cost_date'] ?? '')),
                'category'    => trim((string) ($body['category'] ?? '')),
                'created_by'  => Auth::id(),
            ]);
            $this->repo->recalcActualCost($id);
            Flash::set('success', 'Kostnaden lades till.');
        }
        return $this->redirect($response, '/projects/' . $id . '#costs');
    }

    /** POST /projects/{id}/costs/{costId}/delete */
    public function deleteCost(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $this->repo->deleteCost((int) $args['costId']);
        $this->repo->recalcActualCost($id);
        Flash::set('success', 'Kostnaden togs bort.');
        return $this->redirect($response, '/projects/' . $id . '#costs');
    }

    // ─── C4: PDF-rapport ─────────────────────────────────────────────────────

    /** GET /projects/{id}/report */
    public function report(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $project = $this->repo->find($id);
        if ($project === null) {
            return $this->notFound($response);
        }
        $this->repo->recalcActualCost($id);
        $project['actual_cost'] = $this->repo->getActualCost($id);

        return $this->render($response, 'projects/report', [
            'title'        => 'Rapport: ' . htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'project'      => $project,
            'tasks'        => $this->repo->tasks($id),
            'budget'       => $this->repo->budgetLines($id),
            'stakeholders' => $this->repo->stakeholders($id),
            'linkedPOs'    => $this->repo->linkedPurchaseOrders($id),
            'costs'        => $this->repo->costs($id),
        ], 'print');
    }

    // ─── C5: Kanban-vy ───────────────────────────────────────────────────────

    /** GET /projects/{id}/kanban */
    public function kanban(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $project = $this->repo->find((int) $args['id']);
        if ($project === null) {
            return $this->notFound($response);
        }
        $tasks = $this->repo->tasks((int) $args['id']);
        $columns = [
            'todo'        => array_values(array_filter($tasks, fn($t) => $t['status'] === 'todo')),
            'in_progress' => array_values(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')),
            'done'        => array_values(array_filter($tasks, fn($t) => $t['status'] === 'done')),
        ];
        return $this->render($response, 'projects/kanban', [
            'title'   => 'Kanban: ' . htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'project' => $project,
            'columns' => $columns,
        ]);
    }

    /** POST /projects/{id}/tasks/{taskId}/status */
    public function updateTaskStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $taskId = (int) $args['taskId'];
        if ($this->repo->find($id) === null) { return $this->notFound($response); }
        $body   = (array) $request->getParsedBody();
        $status = in_array($body['status'] ?? '', ['todo','in_progress','done','cancelled'], true) ? $body['status'] : 'todo';
        $task   = $this->repo->findTask($taskId);
        if ($task !== null) {
            $this->repo->updateTask($taskId, array_merge($task, ['status' => $status]));
            Flash::set('success', 'Uppgiftsstatus uppdaterades.');
        }
        return $this->redirect($response, '/projects/' . $id . '/kanban');
    }

    /** @return array<string, string> */
    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'project_number' => trim((string) ($body['project_number'] ?? '')),
            'name'           => trim((string) ($body['name'] ?? '')),
            'description'    => trim((string) ($body['description'] ?? '')),
            'customer_id'    => trim((string) ($body['customer_id'] ?? '')),
            'manager_id'     => trim((string) ($body['manager_id'] ?? '')),
            'start_date'     => trim((string) ($body['start_date'] ?? '')),
            'end_date'       => trim((string) ($body['end_date'] ?? '')),
            'status'         => in_array($body['status'] ?? '', ['planning','active','on_hold','completed','cancelled'], true) ? $body['status'] : 'planning',
            'project_type'   => in_array($body['project_type'] ?? '', ['internal','external'], true) ? $body['project_type'] : 'internal',
            'budget'         => trim((string) ($body['budget'] ?? '0')),
            'planned_budget' => trim((string) ($body['planned_budget'] ?? '0')),
        ];
    }

    /** @return array<string, string> */
    private function validate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        if ($data['project_number'] === '') {
            $errors['project_number'] = 'Projektnummer är obligatoriskt.';
        }
        return $errors;
    }

}
