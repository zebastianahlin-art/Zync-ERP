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

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'employees/index', [
            'title'     => 'Personal &#8211; ZYNC ERP',
            'employees' => $this->repo->all(),
            'success'   => Flash::get('success'),
        ]);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = $this->repo->findEmployee((int) $args['id']);
        if ($employee === null) {
            return $this->notFound($response);
        }
        return $this->render($response, 'employees/show', [
            'title'        => htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name'], ENT_QUOTES, 'UTF-8') . ' &#8211; ZYNC ERP',
            'employee'     => $employee,
            'certificates' => $this->repo->employeeCertificates((int) $args['id']),
            'training'     => $this->repo->employeeTraining((int) $args['id']),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Check for pre-fill data from recruitment conversion
        $old = [];
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!empty($_SESSION['employee_prefill'])) {
            $old = $_SESSION['employee_prefill'];
            unset($_SESSION['employee_prefill']);
        }

        return $this->render($response, 'employees/create', [
            'title'       => 'Ny anst&#228;lld &#8211; ZYNC ERP',
            'departments' => $this->deptRepo->all(),
            'managers'    => $this->repo->allManagers(),
            'errors'      => [],
            'old'         => $old,
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'employees/create', [
                'title'       => 'Ny anst&#228;lld &#8211; ZYNC ERP',
                'departments' => $this->deptRepo->all(),
                'managers'    => $this->repo->allManagers(),
                'errors'      => $errors,
                'old'         => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Anst&#228;lld skapades.');
        return $this->redirect($response, '/employees');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = $this->repo->findEmployee((int) $args['id']);
        if ($employee === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'employees/edit', [
            'title'       => 'Redigera anst&#228;lld &#8211; ZYNC ERP',
            'employee'    => $employee,
            'departments' => $this->deptRepo->all(),
            'managers'    => $this->repo->allManagers(),
            'errors'      => [],
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id       = (int) $args['id'];
        $employee = $this->repo->findEmployee($id);
        if ($employee === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'employees/edit', [
                'title'       => 'Redigera anst&#228;lld &#8211; ZYNC ERP',
                'employee'    => $employee,
                'departments' => $this->deptRepo->all(),
                'managers'    => $this->repo->allManagers(),
                'errors'      => $errors,
            ]);
        }

        $this->repo->updateEmployee($id, $data);
        Flash::set('success', 'Uppgifterna uppdaterades.');
        return $this->redirect($response, '/employees/' . $id);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Anst&#228;lld togs bort.');
        }
        return $this->redirect($response, '/employees');
    }

    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'first_name'              => trim((string) ($body['first_name'] ?? '')),
            'last_name'               => trim((string) ($body['last_name'] ?? '')),
            'employee_number'         => trim((string) ($body['employee_number'] ?? '')),
            'department_id'           => trim((string) ($body['department_id'] ?? '')),
            'position'                => trim((string) ($body['position'] ?? '')),
            'phone'                   => trim((string) ($body['phone'] ?? '')),
            'email'                   => trim((string) ($body['email'] ?? '')),
            'hire_date'               => trim((string) ($body['hire_date'] ?? '')),
            'end_date'                => trim((string) ($body['end_date'] ?? '')),
            'employment_type'         => trim((string) ($body['employment_type'] ?? '')),
            'status'                  => trim((string) ($body['status'] ?? 'active')),
            'salary'                  => trim((string) ($body['salary'] ?? '')),
            'notes'                   => trim((string) ($body['notes'] ?? '')),
            'manager_id'              => trim((string) ($body['manager_id'] ?? '')),
            'emergency_contact_name'  => trim((string) ($body['emergency_contact_name'] ?? '')),
            'emergency_contact_phone' => trim((string) ($body['emergency_contact_phone'] ?? '')),
            // Extended D2 fields
            'personal_number'         => trim((string) ($body['personal_number'] ?? '')),
            'birth_date'              => trim((string) ($body['birth_date'] ?? '')),
            'gender'                  => trim((string) ($body['gender'] ?? '')),
            'nationality'             => trim((string) ($body['nationality'] ?? '')),
            'civil_status'            => trim((string) ($body['civil_status'] ?? '')),
            'address_street'          => trim((string) ($body['address_street'] ?? '')),
            'address_zip'             => trim((string) ($body['address_zip'] ?? '')),
            'address_city'            => trim((string) ($body['address_city'] ?? '')),
            'private_email'           => trim((string) ($body['private_email'] ?? '')),
            'private_phone'           => trim((string) ($body['private_phone'] ?? '')),
            'ice_name'                => trim((string) ($body['ice_name'] ?? '')),
            'ice_phone'               => trim((string) ($body['ice_phone'] ?? '')),
            'employment_category'     => trim((string) ($body['employment_category'] ?? '')),
            'pay_type'                => trim((string) ($body['pay_type'] ?? 'monthly')),
            'work_percentage'         => trim((string) ($body['work_percentage'] ?? '100')),
            'profile_image_url'       => trim((string) ($body['profile_image_url'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['first_name'] === '') {
            $errors['first_name'] = 'F&#246;rnamn &#228;r obligatoriskt.';
        }
        if ($data['last_name'] === '') {
            $errors['last_name'] = 'Efternamn &#228;r obligatoriskt.';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
        }
        return $errors;
    }
}
