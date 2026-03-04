<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AttendanceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AttendanceController extends Controller
{
    private AttendanceRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new AttendanceRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/attendance/index', [
            'title'   => 'Närvaro/Frånvaro – ZYNC ERP',
            'records' => $this->repo->all(),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/attendance/create', [
            'title'     => 'Registrera frånvaro – ZYNC ERP',
            'employees' => $this->repo->allEmployees(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'employee_id' => $body['employee_id'] ?? '',
            'record_type' => $body['record_type'] ?? 'vacation',
            'start_date'  => $body['start_date'] ?? '',
            'end_date'    => $body['end_date'] ?? '',
            'status'      => 'pending',
            'notes'       => $body['notes'] ?? '',
        ];
        $errors = [];
        if (empty($data['employee_id'])) { $errors['employee_id'] = 'Anställd är obligatorisk.'; }
        if ($data['start_date'] === '') { $errors['start_date'] = 'Startdatum är obligatoriskt.'; }
        if ($data['end_date'] === '') { $errors['end_date'] = 'Slutdatum är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/attendance/create', [
                'title' => 'Registrera frånvaro – ZYNC ERP', 'employees' => $this->repo->allEmployees(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $this->repo->create($data);
        Flash::set('success', 'Frånvaron har registrerats.');
        return $this->redirect($response, '/hr/attendance');
    }

    public function calendar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/attendance/calendar', [
            'title'   => 'Frånvarokalender – ZYNC ERP',
            'records' => $this->repo->all(),
        ]);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $record = $this->repo->find((int) $args['id']);
        if ($record === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/attendance/index', [
            'title'   => 'Frånvaro – ZYNC ERP',
            'records' => [$record],
        ]);
    }

    public function editRecord(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $record = $this->repo->find((int) $args['id']);
        if ($record === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/attendance/create', [
            'title'     => 'Redigera frånvaro – ZYNC ERP',
            'employees' => $this->repo->allEmployees(),
            'record'    => $record,
            'errors'    => [],
            'old'       => $record,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id     = (int) $args['id'];
        $record = $this->repo->find($id);
        if ($record === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'employee_id' => $body['employee_id'] ?? $record['employee_id'],
            'record_type' => $body['record_type'] ?? 'vacation',
            'start_date'  => $body['start_date'] ?? '',
            'end_date'    => $body['end_date'] ?? '',
            'status'      => $body['status'] ?? 'pending',
            'notes'       => $body['notes'] ?? '',
        ];
        $this->repo->update($id, $data);
        Flash::set('success', 'Frånvaron har uppdaterats.');
        return $this->redirect($response, '/hr/attendance');
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Frånvaron har tagits bort.');
        return $this->redirect($response, '/hr/attendance');
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
