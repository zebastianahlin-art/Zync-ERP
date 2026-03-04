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
        $project = $this->repo->find((int) $args['id']);
        if ($project === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'projects/show', [
            'title'   => htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'project' => $project,
            'tasks'   => $this->repo->tasks((int) $args['id']),
            'budget'  => $this->repo->budgetLines((int) $args['id']),
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
            'budget'         => trim((string) ($body['budget'] ?? '0')),
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

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Hittades inte</h1>');
        return $response->withStatus(404);
    }
}
