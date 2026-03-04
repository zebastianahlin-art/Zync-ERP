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

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/payroll/index', [
            'title'    => 'Lönehantering – ZYNC ERP',
            'periods'  => $this->repo->allPeriods(),
            'payslips' => $this->repo->allPayslips(),
        ]);
    }

    public function periodsIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/payroll/periods', [
            'title'   => 'Löneperioder – ZYNC ERP',
            'periods' => $this->repo->allPeriods(),
        ]);
    }

    public function createPeriod(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/payroll/create-period', [
            'title'  => 'Ny löneperiod – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function storePeriod(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'name'         => trim((string) ($body['name'] ?? '')),
            'period_start' => $body['period_start'] ?? '',
            'period_end'   => $body['period_end'] ?? '',
            'status'       => $body['status'] ?? 'open',
        ];
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if ($data['period_start'] === '') { $errors['period_start'] = 'Startdatum är obligatoriskt.'; }
        if ($data['period_end'] === '') { $errors['period_end'] = 'Slutdatum är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/payroll/create-period', [
                'title' => 'Ny löneperiod – ZYNC ERP', 'errors' => $errors, 'old' => $data,
            ]);
        }
        $this->repo->createPeriod($data);
        Flash::set('success', 'Löneperioden har skapats.');
        return $this->redirect($response, '/hr/payroll/periods');
    }

    public function payslipsIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/payroll/payslips/index', [
            'title'    => 'Lönebesked – ZYNC ERP',
            'payslips' => $this->repo->allPayslips(),
        ]);
    }

    public function payslipsCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/payroll/payslips/create', [
            'title'     => 'Nytt lönebesked – ZYNC ERP',
            'periods'   => $this->repo->allPeriods(),
            'employees' => $this->repo->allEmployees(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function payslipsStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'period_id'        => $body['period_id'] ?? '',
            'employee_id'      => $body['employee_id'] ?? '',
            'gross_salary'     => $body['gross_salary'] ?? 0,
            'tax_deduction'    => $body['tax_deduction'] ?? 0,
            'net_salary'       => $body['net_salary'] ?? 0,
            'overtime_hours'   => $body['overtime_hours'] ?? 0,
            'overtime_amount'  => $body['overtime_amount'] ?? 0,
            'other_deductions' => $body['other_deductions'] ?? 0,
            'other_additions'  => $body['other_additions'] ?? 0,
            'notes'            => $body['notes'] ?? '',
        ];
        $errors = [];
        if (empty($data['period_id'])) { $errors['period_id'] = 'Period är obligatorisk.'; }
        if (empty($data['employee_id'])) { $errors['employee_id'] = 'Anställd är obligatorisk.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/payroll/payslips/create', [
                'title' => 'Nytt lönebesked – ZYNC ERP', 'periods' => $this->repo->allPeriods(),
                'employees' => $this->repo->allEmployees(), 'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->createPayslip($data);
        Flash::set('success', 'Lönebeskeden har skapats.');
        return $this->redirect($response, '/hr/payroll/payslips/' . $id);
    }

    public function payslipsShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $payslip = $this->repo->findPayslip((int) $args['id']);
        if ($payslip === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/payroll/payslips/show', [
            'title'   => 'Lönebesked – ' . ($payslip['employee_name'] ?? '') . ' – ZYNC ERP',
            'payslip' => $payslip,
        ]);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
