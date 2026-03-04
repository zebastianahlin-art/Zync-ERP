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

    /** GET /hr/attendance */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/attendance/index', [
            'title'   => 'Närvaro & Frånvaro – ZYNC ERP',
            'records' => $this->repo->recent(50),
        ]);
    }

    /** GET /hr/attendance/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/attendance/create', [
            'title'     => 'Ny närvaro/frånvaro – ZYNC ERP',
            'employees' => $this->repo->allEmployees(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /hr/attendance */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $data = [
            'employee_id' => trim((string) ($body['employee_id'] ?? '')),
            'date'        => trim((string) ($body['date'] ?? '')),
            'type'        => in_array($body['type'] ?? '', ['presence','absence','vacation','sick','other'], true) ? $body['type'] : 'presence',
            'time_in'     => trim((string) ($body['time_in'] ?? '')),
            'time_out'    => trim((string) ($body['time_out'] ?? '')),
            'notes'       => trim((string) ($body['notes'] ?? '')),
            'created_by'  => Auth::id(),
        ];

        $errors = [];
        if ($data['employee_id'] === '') {
            $errors['employee_id'] = 'Anställd är obligatorisk.';
        }
        if ($data['date'] === '') {
            $errors['date'] = 'Datum är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/attendance/create', [
                'title'     => 'Ny närvaro/frånvaro – ZYNC ERP',
                'employees' => $this->repo->allEmployees(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $this->repo->create($data);
        Flash::set('success', 'Registreringen sparades.');
        return $this->redirect($response, '/hr/attendance');
    }

    /** GET /hr/attendance/balances */
    public function balances(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/attendance/balances', [
            'title'    => 'Saldoöversikt – ZYNC ERP',
            'balances' => $this->repo->allBalances(),
        ]);
    }
}
