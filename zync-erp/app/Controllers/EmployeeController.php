<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\EmployeeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EmployeeController extends Controller
{
    private EmployeeRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new EmployeeRepository();
    }

    /** GET /employees */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params  = $request->getQueryParams();
        $status  = $params['status'] ?? null;
        $deptId  = isset($params['department']) ? (int) $params['department'] : null;

        return $this->render($response, 'employees/index', [
            'title'       => 'Personal – ZYNC ERP',
            'employees'   => $this->repo->all($status, $deptId),
            'departments' => $this->repo->allDepartments(),
            'stats'       => $this->repo->stats(),
            'filter'      => ['status' => $status, 'department' => $deptId],
        ]);
    }

    /** GET /employees/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'employees/form', [
            'title'       => 'Ny anställd – ZYNC ERP',
            'employee'    => ['employee_number' => $this->repo->nextNumber(), 'country' => 'Sverige', 'employment_type' => 'full_time', 'status' => 'active'],
            'departments' => $this->repo->allDepartments(),
            'managers'    => $this->repo->allForManagerDropdown(),
            'users'       => $this->repo->availableUsers(),
            'errors'      => [],
            'isNew'       => true,
        ]);
    }

    /** POST /employees */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseBody($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'employees/form', [
                'title'       => 'Ny anställd – ZYNC ERP',
                'employee'    => $data,
                'departments' => $this->repo->allDepartments(),
                'managers'    => $this->repo->allForManagerDropdown(),
                'users'       => $this->repo->availableUsers(),
                'errors'      => $errors,
                'isNew'       => true,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Anställd har skapats.');
        return $this->redirect($response, '/employees');
    }

    /** GET /employees/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $emp = $this->repo->find((int) $args['id']);
        if ($emp === null) {
            Flash::set('error', 'Anställd hittades inte.');
            return $this->redirect($response, '/employees');
        }

        return $this->render($response, 'employees/form', [
            'title'       => 'Redigera anställd – ZYNC ERP',
            'employee'    => $emp,
            'departments' => $this->repo->allDepartments(),
            'managers'    => $this->repo->allForManagerDropdown((int) $args['id']),
            'users'       => $this->repo->availableUsers($emp['user_id'] ? (int) $emp['user_id'] : null),
            'errors'      => [],
            'isNew'       => false,
        ]);
    }

    /** POST /employees/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id  = (int) $args['id'];
        $emp = $this->repo->find($id);
        if ($emp === null) {
            Flash::set('error', 'Anställd hittades inte.');
            return $this->redirect($response, '/employees');
        }

        $data   = $this->parseBody($request);
        $errors = $this->validate($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'employees/form', [
                'title'       => 'Redigera anställd – ZYNC ERP',
                'employee'    => array_merge($emp, $data),
                'departments' => $this->repo->allDepartments(),
                'managers'    => $this->repo->allForManagerDropdown($id),
                'users'       => $this->repo->availableUsers($emp['user_id'] ? (int) $emp['user_id'] : null),
                'errors'      => $errors,
                'isNew'       => false,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Anställd har uppdaterats.');
        return $this->redirect($response, '/employees');
    }

    /** POST /employees/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Anställd har tagits bort.');
        return $this->redirect($response, '/employees');
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'employee_number' => strtoupper(trim((string) ($b['employee_number'] ?? ''))),
            'first_name'      => trim((string) ($b['first_name'] ?? '')),
            'last_name'       => trim((string) ($b['last_name'] ?? '')),
            'email'           => trim((string) ($b['email'] ?? '')),
            'phone'           => trim((string) ($b['phone'] ?? '')),
            'title'           => trim((string) ($b['title'] ?? '')),
            'department_id'   => trim((string) ($b['department_id'] ?? '')),
            'manager_id'      => trim((string) ($b['manager_id'] ?? '')),
            'user_id'         => trim((string) ($b['user_id'] ?? '')),
            'hire_date'       => trim((string) ($b['hire_date'] ?? '')),
            'birth_date'      => trim((string) ($b['birth_date'] ?? '')),
            'address'         => trim((string) ($b['address'] ?? '')),
            'city'            => trim((string) ($b['city'] ?? '')),
            'postal_code'     => trim((string) ($b['postal_code'] ?? '')),
            'country'         => trim((string) ($b['country'] ?? 'Sverige')),
            'employment_type' => trim((string) ($b['employment_type'] ?? 'full_time')),
            'status'          => trim((string) ($b['status'] ?? 'active')),
            'salary'          => trim((string) ($b['salary'] ?? '')),
            'notes'           => trim((string) ($b['notes'] ?? '')),
        ];
    }

    private function validate(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        if ($data['employee_number'] === '') {
            $errors['employee_number'] = 'Anställningsnummer är obligatoriskt.';
        } elseif ($this->repo->numberExists($data['employee_number'], $excludeId)) {
            $errors['employee_number'] = 'Numret används redan.';
        }
        if ($data['first_name'] === '') $errors['first_name'] = 'Förnamn är obligatoriskt.';
        if ($data['last_name'] === '')  $errors['last_name']  = 'Efternamn är obligatoriskt.';
        return $errors;
    }
}
