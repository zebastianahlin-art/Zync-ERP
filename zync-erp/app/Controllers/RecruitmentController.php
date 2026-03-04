<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\RecruitmentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RecruitmentController extends Controller
{
    private RecruitmentRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new RecruitmentRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/recruitment/index', [
            'title'      => 'Rekrytering – ZYNC ERP',
            'positions'  => $this->repo->allPositions(),
            'applicants' => $this->repo->allApplicants(),
        ]);
    }

    public function positionsCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/recruitment/positions/create', [
            'title'       => 'Ny tjänst – ZYNC ERP',
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    public function positionsStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'title'         => trim((string) ($body['title'] ?? '')),
            'department_id' => $body['department_id'] ?? '',
            'description'   => $body['description'] ?? '',
            'requirements'  => $body['requirements'] ?? '',
            'status'        => $body['status'] ?? 'draft',
            'deadline'      => $body['deadline'] ?? '',
        ];
        $errors = [];
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/recruitment/positions/create', [
                'title' => 'Ny tjänst – ZYNC ERP', 'departments' => $this->repo->allDepartments(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->createPosition($data);
        Flash::set('success', 'Tjänsten har skapats.');
        return $this->redirect($response, '/hr/recruitment/positions/' . $id);
    }

    public function positionsShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $position = $this->repo->findPosition((int) $args['id']);
        if ($position === null) { return $this->notFound($response); }
        $applicants = $this->repo->applicantsForPosition((int) $args['id']);
        return $this->render($response, 'hr/recruitment/positions/show', [
            'title'      => $position['title'] . ' – Rekrytering – ZYNC ERP',
            'position'   => $position,
            'applicants' => $applicants,
        ]);
    }

    public function positionsEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $position = $this->repo->findPosition((int) $args['id']);
        if ($position === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/recruitment/positions/create', [
            'title'       => 'Redigera tjänst – ZYNC ERP',
            'position'    => $position,
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'old'         => $position,
        ]);
    }

    public function positionsUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id       = (int) $args['id'];
        $position = $this->repo->findPosition($id);
        if ($position === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'title'         => trim((string) ($body['title'] ?? '')),
            'department_id' => $body['department_id'] ?? '',
            'description'   => $body['description'] ?? '',
            'requirements'  => $body['requirements'] ?? '',
            'status'        => $body['status'] ?? 'draft',
            'deadline'      => $body['deadline'] ?? '',
        ];
        $errors = [];
        if ($data['title'] === '') { $errors['title'] = 'Titel är obligatorisk.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/recruitment/positions/create', [
                'title' => 'Redigera tjänst – ZYNC ERP', 'position' => array_merge($position, $data),
                'departments' => $this->repo->allDepartments(), 'errors' => $errors, 'old' => $data,
            ]);
        }
        $this->repo->updatePosition($id, $data);
        Flash::set('success', 'Tjänsten har uppdaterats.');
        return $this->redirect($response, '/hr/recruitment/positions/' . $id);
    }

    public function positionsDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deletePosition((int) $args['id']);
        Flash::set('success', 'Tjänsten har tagits bort.');
        return $this->redirect($response, '/hr/recruitment');
    }

    public function applicantsShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $applicant = $this->repo->findApplicant((int) $args['id']);
        if ($applicant === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/recruitment/applicants/show', [
            'title'     => $applicant['name'] . ' – Sökande – ZYNC ERP',
            'applicant' => $applicant,
        ]);
    }

    public function applicantsStore(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'position_id'  => (int) $args['id'],
            'name'         => trim((string) ($body['name'] ?? '')),
            'email'        => trim((string) ($body['email'] ?? '')),
            'phone'        => $body['phone'] ?? '',
            'cover_letter' => $body['cover_letter'] ?? '',
            'status'       => 'applied',
            'notes'        => $body['notes'] ?? '',
        ];
        if ($data['name'] !== '' && $data['email'] !== '') {
            $this->repo->createApplicant($data);
            Flash::set('success', 'Ansökan har registrerats.');
        }
        return $this->redirect($response, '/hr/recruitment/positions/' . $args['id']);
    }

    public function applicantsUpdateStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? 'applied';
        $notes  = $body['notes'] ?? '';
        $this->repo->updateApplicantStatus((int) $args['id'], $status, $notes);
        Flash::set('success', 'Status uppdaterad.');
        return $this->redirect($response, '/hr/recruitment/applicants/' . $args['id']);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
