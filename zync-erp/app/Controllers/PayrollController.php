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
        return $this->render($response, 'hr/payroll/index', [
            'title'   => 'Lönehantering – ZYNC ERP',
            'periods' => $this->repo->allPeriods(),
            'success' => Flash::get('success'),
        ]);
    }

    public function createPeriod(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/payroll/create', [
            'title'  => 'Ny löneperiod – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

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

    public function showPeriod(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $period = $this->repo->findPeriod((int) $args['id']);
        if ($period === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/show', [
            'title'        => htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'period'       => $period,
            'payslips'     => $this->repo->periodPayslips((int) $args['id']),
            'payslipCount' => $this->repo->periodPayslipCount((int) $args['id']),
            'success'      => Flash::get('success'),
        ]);
    }

    public function editPeriod(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $period = $this->repo->findPeriod((int) $args['id']);
        if ($period === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/edit', [
            'title'  => 'Redigera löneperiod – ZYNC ERP',
            'period' => $period,
            'errors' => [],
        ]);
    }

    public function updatePeriod(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $period = $this->repo->findPeriod($id);
        if ($period === null) {
            return $this->notFound($response);
        }

        $body   = (array) $request->getParsedBody();
        $data   = [
            'name'        => trim((string) ($body['name'] ?? '')),
            'period_from' => trim((string) ($body['period_from'] ?? '')),
            'period_to'   => trim((string) ($body['period_to'] ?? '')),
        ];
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/payroll/edit', [
                'title'  => 'Redigera löneperiod – ZYNC ERP',
                'period' => array_merge($period, $data),
                'errors' => $errors,
            ]);
        }

        $this->repo->updatePeriod($id, $data);
        Flash::set('success', 'Löneperioden uppdaterades.');
        return $this->redirect($response, '/hr/payroll/periods/' . $id);
    }

    public function deletePeriod(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findPeriod($id) !== null) {
            $this->repo->deletePeriod($id);
            Flash::set('success', 'Löneperioden togs bort.');
        }
        return $this->redirect($response, '/hr/payroll');
    }

    public function closePeriod(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findPeriod($id) !== null) {
            $this->repo->closePeriod($id);
            Flash::set('success', 'Löneperioden låstes.');
        }
        return $this->redirect($response, '/hr/payroll/periods/' . $id);
    }

    public function generatePayslips(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findPeriod($id) === null) {
            return $this->notFound($response);
        }
        $count = $this->repo->generatePayslips($id);
        Flash::set('success', $count . ' lönespecifikationer genererades.');
        return $this->redirect($response, '/hr/payroll/periods/' . $id);
    }

    public function createPayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $period = $this->repo->findPeriod((int) $args['id']);
        if ($period === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/payslip/create', [
            'title'     => 'Ny lönespecifikation – ZYNC ERP',
            'period'    => $period,
            'employees' => $this->repo->allEmployees(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function storePayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $period = $this->repo->findPeriod((int) $args['id']);
        if ($period === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $basePay     = (float) ($body['base_pay'] ?? 0);
        $obAmount    = (float) ($body['ob_amount'] ?? 0);
        $otAmount    = (float) ($body['overtime_amount'] ?? 0);
        $deductions  = (float) ($body['deductions'] ?? 0);
        $taxAmount   = (float) ($body['tax_amount'] ?? 0);
        $grossPay    = $basePay + $obAmount + $otAmount;
        $netPay      = $grossPay - $deductions - $taxAmount;

        $data = [
            'period_id'       => $period['id'],
            'employee_id'     => trim((string) ($body['employee_id'] ?? '')),
            'base_pay'        => $basePay,
            'ob_amount'       => $obAmount,
            'overtime_amount' => $otAmount,
            'gross_pay'       => $grossPay,
            'deductions'      => $deductions,
            'tax_amount'      => $taxAmount,
            'net_pay'         => $netPay,
            'status'          => 'draft',
            'notes'           => trim((string) ($body['notes'] ?? '')),
            'created_by'      => Auth::id(),
        ];

        $errors = [];
        if ($data['employee_id'] === '') {
            $errors['employee_id'] = 'Välj en anställd.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/payroll/payslip/create', [
                'title'     => 'Ny lönespecifikation – ZYNC ERP',
                'period'    => $period,
                'employees' => $this->repo->allEmployees(),
                'errors'    => $errors,
                'old'       => $body,
            ]);
        }

        $slipId = $this->repo->createPayslip($data);
        Flash::set('success', 'Lönespecifikationen skapades.');
        return $this->redirect($response, '/hr/payroll/payslips/' . $slipId);
    }

    public function printPayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $slip = $this->repo->findPayslip((int) $args['slipId']);
        if ($slip === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/payslip/print', ['payslip' => $slip], null);
    }

    public function showPayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $slip = $this->repo->findPayslip((int) $args['slipId']);
        if ($slip === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/payslip/show', [
            'title'   => 'Lönespecifikation – ZYNC ERP',
            'payslip' => $slip,
        ]);
    }

    public function editPayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $slip = $this->repo->findPayslip((int) $args['slipId']);
        if ($slip === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/payroll/payslip/edit', [
            'title'   => 'Redigera lönespecifikation – ZYNC ERP',
            'payslip' => $slip,
            'errors'  => [],
        ]);
    }

    public function updatePayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $slipId = (int) $args['slipId'];
        $slip   = $this->repo->findPayslip($slipId);
        if ($slip === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $basePay     = (float) ($body['base_pay'] ?? 0);
        $obAmount    = (float) ($body['ob_amount'] ?? 0);
        $otAmount    = (float) ($body['overtime_amount'] ?? 0);
        $deductions  = (float) ($body['deductions'] ?? 0);
        $taxAmount   = (float) ($body['tax_amount'] ?? 0);
        $grossPay    = $basePay + $obAmount + $otAmount;
        $netPay      = $grossPay - $deductions - $taxAmount;

        $this->repo->updatePayslip($slipId, [
            'base_pay'        => $basePay,
            'ob_amount'       => $obAmount,
            'overtime_amount' => $otAmount,
            'gross_pay'       => $grossPay,
            'deductions'      => $deductions,
            'tax_amount'      => $taxAmount,
            'net_pay'         => $netPay,
            'status'          => trim((string) ($body['status'] ?? 'draft')),
            'notes'           => trim((string) ($body['notes'] ?? '')),
        ]);

        Flash::set('success', 'Lönespecifikationen uppdaterades.');
        return $this->redirect($response, '/hr/payroll/payslips/' . $slipId);
    }

    public function deletePayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $slipId   = (int) $args['slipId'];
        $slip     = $this->repo->findPayslip($slipId);
        $periodId = $slip['period_id'] ?? null;

        if ($slip !== null) {
            $this->repo->deletePayslip($slipId);
            Flash::set('success', 'Lönespecifikationen togs bort.');
        }

        if ($periodId) {
            return $this->redirect($response, '/hr/payroll/periods/' . $periodId);
        }
        return $this->redirect($response, '/hr/payroll');
    }
}
