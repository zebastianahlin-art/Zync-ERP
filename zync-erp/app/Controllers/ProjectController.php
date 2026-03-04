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

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'projects/index', [
            'title'    => 'Projekt – ZYNC ERP',
            'projects' => $this->repo->all(),
        ]);
    }

    public function archive(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'projects/archive', [
            'title'    => 'Avslutade projekt – ZYNC ERP',
            'projects' => $this->repo->allArchived(),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'projects/create', [
            'title'       => 'Nytt projekt – ZYNC ERP',
            'customers'   => $this->repo->allCustomers(),
            'departments' => $this->repo->allDepartments(),
            'users'       => $this->repo->allUsers(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'project_number'     => trim((string) ($body['project_number'] ?? '')),
            'name'               => trim((string) ($body['name'] ?? '')),
            'description'        => trim((string) ($body['description'] ?? '')),
            'customer_id'        => $body['customer_id'] ?? '',
            'department_id'      => $body['department_id'] ?? '',
            'project_manager_id' => $body['project_manager_id'] ?? '',
            'status'             => $body['status'] ?? 'planning',
            'start_date'         => $body['start_date'] ?? '',
            'end_date'           => $body['end_date'] ?? '',
            'budget_amount'      => $body['budget_amount'] ?? '',
            'notes'              => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['project_number'] === '') { $errors['project_number'] = 'Projektnummer är obligatoriskt.'; }
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'projects/create', [
                'title' => 'Nytt projekt – ZYNC ERP', 'customers' => $this->repo->allCustomers(),
                'departments' => $this->repo->allDepartments(), 'users' => $this->repo->allUsers(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->create($data);
        Flash::set('success', 'Projektet har skapats.');
        return $this->redirect($response, '/projects/' . $id);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $project = $this->repo->find((int) $args['id']);
        if ($project === null) { return $this->notFound($response); }
        $tasks = $this->repo->allTasks((int) $args['id']);
        return $this->render($response, 'projects/show', [
            'title'   => $project['name'] . ' – Projekt – ZYNC ERP',
            'project' => $project,
            'tasks'   => $tasks,
            'users'   => $this->repo->allUsers(),
        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $project = $this->repo->find((int) $args['id']);
        if ($project === null) { return $this->notFound($response); }
        return $this->render($response, 'projects/edit', [
            'title'       => 'Redigera ' . $project['name'] . ' – ZYNC ERP',
            'project'     => $project,
            'customers'   => $this->repo->allCustomers(),
            'departments' => $this->repo->allDepartments(),
            'users'       => $this->repo->allUsers(),
            'errors'      => [],
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id      = (int) $args['id'];
        $project = $this->repo->find($id);
        if ($project === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'project_number'     => trim((string) ($body['project_number'] ?? '')),
            'name'               => trim((string) ($body['name'] ?? '')),
            'description'        => trim((string) ($body['description'] ?? '')),
            'customer_id'        => $body['customer_id'] ?? '',
            'department_id'      => $body['department_id'] ?? '',
            'project_manager_id' => $body['project_manager_id'] ?? '',
            'status'             => $body['status'] ?? 'planning',
            'start_date'         => $body['start_date'] ?? '',
            'end_date'           => $body['end_date'] ?? '',
            'budget_amount'      => $body['budget_amount'] ?? '',
            'notes'              => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['project_number'] === '') { $errors['project_number'] = 'Projektnummer är obligatoriskt.'; }
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'projects/edit', [
                'title' => 'Redigera – ZYNC ERP', 'project' => array_merge($project, $data),
                'customers' => $this->repo->allCustomers(), 'departments' => $this->repo->allDepartments(),
                'users' => $this->repo->allUsers(), 'errors' => $errors,
            ]);
        }
        $this->repo->update($id, $data);
        Flash::set('success', 'Projektet har uppdaterats.');
        return $this->redirect($response, '/projects/' . $id);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Projektet har tagits bort.');
        return $this->redirect($response, '/projects');
    }

    public function tasksIndex(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $project = $this->repo->find((int) $args['id']);
        if ($project === null) { return $this->notFound($response); }
        return $this->render($response, 'projects/show', [
            'title'   => 'Uppgifter – ' . $project['name'] . ' – ZYNC ERP',
            'project' => $project,
            'tasks'   => $this->repo->allTasks((int) $args['id']),
            'users'   => $this->repo->allUsers(),
        ]);
    }

    public function tasksStore(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'project_id'      => (int) $args['id'],
            'title'           => trim((string) ($body['title'] ?? '')),
            'description'     => $body['description'] ?? '',
            'assigned_to'     => $body['assigned_to'] ?? '',
            'status'          => $body['status'] ?? 'todo',
            'planned_start'   => $body['planned_start'] ?? '',
            'planned_end'     => $body['planned_end'] ?? '',
            'estimated_hours' => $body['estimated_hours'] ?? '',
            'sort_order'      => 0,
        ];
        if ($data['title'] !== '') {
            $this->repo->createTask($data);
            Flash::set('success', 'Uppgiften har lagts till.');
        }
        return $this->redirect($response, '/projects/' . $args['id']);
    }

    public function tasksDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deleteTask((int) $args['taskId']);
        Flash::set('success', 'Uppgiften har tagits bort.');
        return $this->redirect($response, '/projects/' . $args['id']);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
