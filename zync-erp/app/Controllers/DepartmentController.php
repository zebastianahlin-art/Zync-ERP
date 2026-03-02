<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\DepartmentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DepartmentController extends Controller
{
    private DepartmentRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new DepartmentRepository();
    }

    /** GET /departments */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'departments/index', [
            'title'       => 'Avdelningar – ZYNC ERP',
            'departments' => $this->repo->all(),
        ]);
    }

    /** GET /departments/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'departments/create', [
            'title'       => 'Skapa avdelning – ZYNC ERP',
            'departments' => $this->repo->allForDropdown(),
            'managers'    => $this->repo->allManagers(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    /** POST /departments */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseBody($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'departments/create', [
                'title'       => 'Skapa avdelning – ZYNC ERP',
                'departments' => $this->repo->allForDropdown(),
                'managers'    => $this->repo->allManagers(),
                'errors'      => $errors,
                'old'         => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Avdelningen har skapats.');
        return $this->redirect($response, '/departments');
    }

    /** GET /departments/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $dept = $this->repo->find((int) $args['id']);
        if ($dept === null) {
            Flash::set('error', 'Avdelningen hittades inte.');
            return $this->redirect($response, '/departments');
        }

        return $this->render($response, 'departments/edit', [
            'title'       => 'Redigera avdelning – ZYNC ERP',
            'department'  => $dept,
            'departments' => $this->repo->allForDropdown((int) $args['id']),
            'managers'    => $this->repo->allManagers(),
            'members'     => $this->repo->members((int) $args['id']),
            'errors'      => [],
        ]);
    }

    /** POST /departments/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $dept = $this->repo->find($id);
        if ($dept === null) {
            Flash::set('error', 'Avdelningen hittades inte.');
            return $this->redirect($response, '/departments');
        }

        $data   = $this->parseBody($request);
        $errors = $this->validate($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'departments/edit', [
                'title'       => 'Redigera avdelning – ZYNC ERP',
                'department'  => array_merge($dept, $data),
                'departments' => $this->repo->allForDropdown($id),
                'managers'    => $this->repo->allManagers(),
                'members'     => $this->repo->members($id),
                'errors'      => $errors,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Avdelningen har uppdaterats.');
        return $this->redirect($response, '/departments');
    }

    /** POST /departments/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->repo->delete($id);
        Flash::set('success', 'Avdelningen har tagits bort.');
        return $this->redirect($response, '/departments');
    }

    /** @return array<string, string> */
    private function parseBody(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'       => trim((string) ($body['name'] ?? '')),
            'code'       => strtoupper(trim((string) ($body['code'] ?? ''))),
            'manager_id' => trim((string) ($body['manager_id'] ?? '')),
            'parent_id'  => trim((string) ($body['parent_id'] ?? '')),
            'color'      => trim((string) ($body['color'] ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validate(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }

        if ($data['code'] === '') {
            $errors['code'] = 'Kod är obligatoriskt.';
        } elseif ($this->repo->codeExists($data['code'], $excludeId)) {
            $errors['code'] = 'Koden används redan.';
        }

        return $errors;
    }
}
