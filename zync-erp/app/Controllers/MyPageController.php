<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\MyPageRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MyPageController extends Controller
{
    private MyPageRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new MyPageRepository();
    }

    /** GET /my-page */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user       = Auth::user();
        $userId     = (int) Auth::id();
        $employeeId = isset($user['employee_id']) && $user['employee_id'] ? (int) $user['employee_id'] : null;

        return $this->render($response, 'my-page/index', [
            'title'        => 'Min Sida – ZYNC ERP',
            'user'         => $user,
            'kpi'          => $this->repo->kpi($userId, $employeeId),
            'employee'     => $this->repo->employeeInfo($employeeId),
            'certificates' => $this->repo->employeeCertificates($employeeId),
            'attendance'   => $this->repo->recentAttendance($employeeId),
            'payslips'     => $this->repo->recentPayslips($employeeId),
            'contract'     => $this->repo->contract($employeeId),
            'tickets'      => $this->repo->tickets($userId),
            'success'      => Flash::get('success'),
        ]);
    }

    /** GET /my-page/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = Auth::user();

        return $this->render($response, 'my-page/edit', [
            'title'  => 'Redigera profil – ZYNC ERP',
            'user'   => $user,
            'errors' => [],
        ]);
    }

    /** POST /my-page */
    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id   = Auth::id();
        $user = Auth::user();
        $body = (array) $request->getParsedBody();

        $email    = trim((string) ($body['email'] ?? ''));
        $phone    = trim((string) ($body['phone'] ?? ''));
        $fullName = trim((string) ($body['full_name'] ?? ''));

        $errors = [];
        if ($email === '') {
            $errors['email'] = 'E-post är obligatorisk.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'my-page/edit', [
                'title'  => 'Redigera profil – ZYNC ERP',
                'user'   => array_merge($user ?? [], ['email' => $email, 'phone' => $phone, 'full_name' => $fullName]),
                'errors' => $errors,
            ]);
        }

        try {
            Database::pdo()->prepare(
                'UPDATE users SET email = ?, phone = ?, full_name = ? WHERE id = ?'
            )->execute([$email, $phone ?: null, $fullName ?: null, $id]);
        } catch (\Exception $e) {
            Flash::set('error', 'Profilen kunde inte sparas. Kontrollera att alla fält är giltiga.');
            return $this->redirect($response, '/my-page/edit');
        }

        // Clear user cache so next request reflects the update
        unset($_SESSION['_user_cache']);

        Flash::set('success', 'Profilen uppdaterades.');
        return $this->redirect($response, '/my-page');
    }

    /** GET /my-page/calendar-events (JSON API – E1) */
    public function calendarEvents(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user       = Auth::user();
        $userId     = (int) Auth::id();
        $employeeId = isset($user['employee_id']) && $user['employee_id'] ? (int) $user['employee_id'] : null;

        $params = (array) $request->getQueryParams();
        $from   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $params['from'] ?? '') ? $params['from'] : date('Y-m-01');
        $to     = preg_match('/^\d{4}-\d{2}-\d{2}$/', $params['to'] ?? '') ? $params['to'] : date('Y-m-t');

        $events = $this->repo->calendarEvents($userId, $employeeId, $from, $to);

        return $this->json($response, $events);
    }

    /** GET /my-page/payslips (E4) */
    public function payslips(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user       = Auth::user();
        $employeeId = isset($user['employee_id']) && $user['employee_id'] ? (int) $user['employee_id'] : null;

        return $this->render($response, 'my-page/payslips', [
            'title'    => 'Mina lönespecar – ZYNC ERP',
            'user'     => $user,
            'payslips' => $this->repo->allPayslips($employeeId),
        ]);
    }

    /** GET /my-page/payslips/{id} (E4 – detail) */
    public function showPayslip(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user       = Auth::user();
        $employeeId = isset($user['employee_id']) && $user['employee_id'] ? (int) $user['employee_id'] : null;
        $id         = (int) ($args['id'] ?? 0);

        $payslip = $this->repo->findPayslip($id, $employeeId);

        if ($payslip === null) {
            Flash::set('error', 'Lönespecen hittades inte.');
            return $this->redirect($response, '/my-page/payslips');
        }

        return $this->render($response, 'my-page/payslip', [
            'title'   => 'Lönespeca – ZYNC ERP',
            'user'    => $user,
            'payslip' => $payslip,
        ]);
    }

    /** GET /my-page/contract (E5) */
    public function contract(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user       = Auth::user();
        $employeeId = isset($user['employee_id']) && $user['employee_id'] ? (int) $user['employee_id'] : null;

        return $this->render($response, 'my-page/contract', [
            'title'    => 'Mitt anställningsavtal – ZYNC ERP',
            'user'     => $user,
            'employee' => $this->repo->employeeInfo($employeeId),
            'contract' => $this->repo->contract($employeeId),
        ]);
    }

    /** GET /my-page/tickets (E6) */
    public function tickets(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user   = Auth::user();
        $userId = (int) Auth::id();

        return $this->render($response, 'my-page/tickets', [
            'title'   => 'Mina ärenden – ZYNC ERP',
            'user'    => $user,
            'tickets' => $this->repo->tickets($userId),
        ]);
    }
}
