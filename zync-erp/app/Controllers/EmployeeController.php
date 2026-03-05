<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\EmployeeRepository;
use App\Models\DepartmentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EmployeeController extends Controller
{
    private EmployeeRepository $repo;
    private DepartmentRepository $deptRepo;

    public function __construct()
    {
        parent::__construct();
        $this->repo     = new EmployeeRepository();
        $this->deptRepo = new DepartmentRepository();
    }

    /** GET /employees */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'employees/index', [
            'title'     => 'Personal – ZYNC ERP',
            'employees' => $this->repo->all(),
            'success'   => Flash::get('success'),
        ]);
    }

    /** GET /employees/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'employees/create', [
            'title'       => 'Ny anställd – ZYNC ERP',
            'departments' => $this->deptRepo->all(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    /** POST /employees */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'employees/create', [
                'title'       => 'Ny anställd – ZYNC ERP',
                'departments' => $this->deptRepo->all(),
                'errors'      => $errors,
                'old'         => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Anställd skapades.');
        return $this->redirect($response, '/employees');
    }

    /** GET /employees/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = $this->repo->find((int) $args['id']);
        if ($employee === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'employees/edit', [
            'title'       => 'Redigera anställd – ZYNC ERP',
            'employee'    => $employee,
            'departments' => $this->deptRepo->all(),
            'errors'      => [],
        ]);
    }

    /** POST /employees/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id       = (int) $args['id'];
        $employee = $this->repo->find($id);
        if ($employee === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'employees/edit', [
                'title'       => 'Redigera anställd – ZYNC ERP',
                'employee'    => $employee,
                'departments' => $this->deptRepo->all(),
                'errors'      => $errors,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Uppgifterna uppdaterades.');
        return $this->redirect($response, '/employees');
    }

    /** POST /employees/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Anställd togs bort.');
        }

        return $this->redirect($response, '/employees');
    }

    /** @return array<string, string> */
    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'first_name'      => trim((string) ($body['first_name'] ?? '')),
            'last_name'       => trim((string) ($body['last_name'] ?? '')),
            'employee_number' => trim((string) ($body['employee_number'] ?? '')),
            'department_id'   => trim((string) ($body['department_id'] ?? '')),
            'position'        => trim((string) ($body['position'] ?? '')),
            'phone'           => trim((string) ($body['phone'] ?? '')),
            'email'           => trim((string) ($body['email'] ?? '')),
            'hire_date'       => trim((string) ($body['hire_date'] ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validate(array $data): array
    {
        $errors = [];
        if ($data['first_name'] === '') {
            $errors['first_name'] = 'Förnamn är obligatoriskt.';
        }
        if ($data['last_name'] === '') {
            $errors['last_name'] = 'Efternamn är obligatoriskt.';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
        }
        return $errors;
    }

}
