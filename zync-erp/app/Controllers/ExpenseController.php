<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\ExpenseRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExpenseController extends Controller
{
    private ExpenseRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ExpenseRepository();
    }

    /** GET /hr/expenses */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'hr/expenses/index', [
            'title'    => 'Reseräkningar – ZYNC ERP',
            'reports'  => $this->repo->all($filters),
            'filters'  => $filters,
            'success'  => Flash::get('success'),
        ]);
    }

    /** GET /hr/expenses/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/expenses/create', [
            'title'     => 'Ny reseräkning – ZYNC ERP',
            'employees' => $this->repo->allEmployees(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /hr/expenses */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractData($request);
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $this->render($response, 'hr/expenses/create', [
                'title'     => 'Ny reseräkning – ZYNC ERP',
                'employees' => $this->repo->allEmployees(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }
        $data['created_by'] = Auth::id();
        $id = $this->repo->create($data);
        Flash::set('success', 'Reseräkningen har skapats.');
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    /** GET /hr/expenses/{id} */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $report = $this->repo->find((int) $args['id']);
        if ($report === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/expenses/show', [
            'title'   => 'Reseräkning – ZYNC ERP',
            'report'  => $report,
            'lines'   => $this->repo->lines((int) $args['id']),
            'success' => Flash::get('success'),
        ]);
    }

    /** GET /hr/expenses/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $report = $this->repo->find((int) $args['id']);
        if ($report === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/expenses/edit', [
            'title'     => 'Redigera reseräkning – ZYNC ERP',
            'report'    => $report,
            'employees' => $this->repo->allEmployees(),
            'errors'    => [],
        ]);
    }

    /** POST /hr/expenses/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $report = $this->repo->find($id);
        if ($report === null) { return $this->notFound($response); }
        $data   = $this->extractData($request);
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $this->render($response, 'hr/expenses/edit', [
                'title'     => 'Redigera reseräkning – ZYNC ERP',
                'report'    => array_merge($report, $data),
                'employees' => $this->repo->allEmployees(),
                'errors'    => $errors,
            ]);
        }
        $this->repo->update($id, $data);
        Flash::set('success', 'Reseräkningen har uppdaterats.');
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    /** POST /hr/expenses/{id}/delete */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Reseräkningen har tagits bort.');
        }
        return $this->redirect($response, '/hr/expenses');
    }

    /** POST /hr/expenses/{id}/submit */
    public function submit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->submit($id);
            Flash::set('success', 'Reseräkningen har skickats in.');
        }
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    /** POST /hr/expenses/{id}/approve */
    public function approve(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->approve($id, (int) Auth::id());
            Flash::set('success', 'Reseräkningen har godkänts.');
        }
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    /** POST /hr/expenses/{id}/reject */
    public function reject(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->reject($id);
            Flash::set('success', 'Reseräkningen har avslagits.');
        }
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    /** POST /hr/expenses/{id}/lines */
    public function addLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $report = $this->repo->find($id);
        if ($report === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $desc = trim((string) ($body['description'] ?? ''));
        if ($desc !== '') {
            $this->repo->addLine($id, [
                'expense_date' => trim((string) ($body['expense_date'] ?? date('Y-m-d'))),
                'category'     => trim((string) ($body['category'] ?? 'other')),
                'description'  => $desc,
                'amount'       => trim((string) ($body['amount'] ?? '0')),
                'currency'     => trim((string) ($body['currency'] ?? 'SEK')),
                'receipt_ref'  => trim((string) ($body['receipt_ref'] ?? '')),
                'notes'        => trim((string) ($body['notes'] ?? '')),
            ]);
            Flash::set('success', 'Raden har lagts till.');
        }
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    /** POST /hr/expenses/{id}/lines/{lineId}/delete */
    public function removeLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $lineId = (int) $args['lineId'];
        if ($this->repo->find($id) !== null) {
            $this->repo->removeLine($lineId);
            Flash::set('success', 'Raden har tagits bort.');
        }
        return $this->redirect($response, '/hr/expenses/' . $id);
    }

    // ── Private helpers ───────────────────────────────────────

    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'employee_id' => trim((string) ($body['employee_id'] ?? '')),
            'title'       => trim((string) ($body['title'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'trip_start'  => trim((string) ($body['trip_start'] ?? '')),
            'trip_end'    => trim((string) ($body['trip_end'] ?? '')),
            'destination' => trim((string) ($body['destination'] ?? '')),
            'purpose'     => trim((string) ($body['purpose'] ?? '')),
            'currency'    => trim((string) ($body['currency'] ?? 'SEK')),
            'notes'       => trim((string) ($body['notes'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['employee_id'] === '') { $errors['employee_id'] = 'Anställd är obligatorisk.'; }
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        return $errors;
    }

}
