<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\PayrollRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PayrollController extends Controller
{
    private PayrollRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new PayrollRepository();
    }

    /** GET /hr/payroll */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/payroll/index', [
            'title'   => 'Lönehantering – ZYNC ERP',
            'periods' => $this->repo->allPeriods(),
        ]);
    }

    /** GET /hr/payroll/periods/create */
    public function createPeriod(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/payroll/create', [
            'title'  => 'Ny löneperiod – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /hr/payroll/periods */
    public function storePeriod(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body   = (array) $request->getParsedBody();
        $data   = [
            'name'        => trim((string) ($body['name'] ?? '')),
            'period_from' => trim((string) ($body['period_from'] ?? '')),
            'period_to'   => trim((string) ($body['period_to'] ?? '')),
            'status'      => 'open',
            'created_by'  => Auth::id(),
        ];
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/payroll/create', [
                'title'  => 'Ny löneperiod – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $this->repo->createPeriod($data);
        Flash::set('success', 'Löneperioden skapades.');
        return $this->redirect($response, '/hr/payroll');
    }

    /** GET /hr/payroll/periods/{id} */
    public function showPeriod(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $period = $this->repo->findPeriod((int) $args['id']);
        if ($period === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/show', [
            'title'    => htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'period'   => $period,
            'payslips' => $this->repo->periodPayslips((int) $args['id']),
        ]);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Hittades inte</h1>');
        return $response->withStatus(404);
    }
}
