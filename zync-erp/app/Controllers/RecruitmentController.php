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
        return $this->render($response, 'hr/recruitment/index', [
            'title'     => 'Rekrytering – ZYNC ERP',
            'positions' => $this->repo->allPositions(),
            'success'   => Flash::get('success'),
        ]);
    }

    public function createPosition(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/recruitment/create', [
            'title'       => 'Ny tjänst – ZYNC ERP',
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    public function storePosition(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $data = [
            'title'         => trim((string) ($body['title'] ?? '')),
            'department_id' => trim((string) ($body['department_id'] ?? '')),
            'description'   => trim((string) ($body['description'] ?? '')),
            'requirements'  => trim((string) ($body['requirements'] ?? '')),
            'num_openings'  => max(1, (int) ($body['num_openings'] ?? 1)),
            'posted_at'     => trim((string) ($body['posted_at'] ?? '')),
            'closes_at'     => trim((string) ($body['closes_at'] ?? '')),
            'status'        => in_array($body['status'] ?? '', ['draft','open','on_hold','closed','filled'], true) ? $body['status'] : 'draft',
            'created_by'    => Auth::id(),
        ];

        $errors = [];
        if ($data['title'] === '') {
            $errors['title'] = 'Titel är obligatorisk.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/recruitment/create', [
                'title'       => 'Ny tjänst – ZYNC ERP',
                'departments' => $this->repo->allDepartments(),
                'errors'      => $errors,
                'old'         => $data,
            ]);
        }

        $positionId = $this->repo->createPosition($data);
        Flash::set('success', 'Tjänsten skapades.');
        return $this->redirect($response, '/hr/recruitment/positions/' . $positionId);
    }

    public function showPosition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $position = $this->repo->findPosition((int) $args['id']);
        if ($position === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/recruitment/show', [
            'title'      => htmlspecialchars($position['title'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'position'   => $position,
            'applicants' => $this->repo->positionApplicants((int) $args['id']),
            'stats'      => $this->repo->positionStats((int) $args['id']),
            'success'    => Flash::get('success'),
        ]);
    }

    public function editPosition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $position = $this->repo->findPosition((int) $args['id']);
        if ($position === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/recruitment/edit', [
            'title'       => 'Redigera tjänst – ZYNC ERP',
            'position'    => $position,
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
        ]);
    }

    public function updatePosition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id       = (int) $args['id'];
        $position = $this->repo->findPosition($id);
        if ($position === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $data = [
            'title'         => trim((string) ($body['title'] ?? '')),
            'department_id' => trim((string) ($body['department_id'] ?? '')),
            'description'   => trim((string) ($body['description'] ?? '')),
            'requirements'  => trim((string) ($body['requirements'] ?? '')),
            'num_openings'  => max(1, (int) ($body['num_openings'] ?? 1)),
            'posted_at'     => trim((string) ($body['posted_at'] ?? '')),
            'closes_at'     => trim((string) ($body['closes_at'] ?? '')),
            'status'        => in_array($body['status'] ?? '', ['draft','open','on_hold','closed','filled'], true) ? $body['status'] : 'draft',
        ];

        $errors = [];
        if ($data['title'] === '') {
            $errors['title'] = 'Titel är obligatorisk.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/recruitment/edit', [
                'title'       => 'Redigera tjänst – ZYNC ERP',
                'position'    => array_merge($position, $data),
                'departments' => $this->repo->allDepartments(),
                'errors'      => $errors,
            ]);
        }

        $this->repo->updatePosition($id, $data);
        Flash::set('success', 'Tjänsten uppdaterades.');
        return $this->redirect($response, '/hr/recruitment/positions/' . $id);
    }

    public function deletePosition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findPosition($id) !== null) {
            $this->repo->deletePosition($id);
            Flash::set('success', 'Tjänsten togs bort.');
        }
        return $this->redirect($response, '/hr/recruitment');
    }

    public function applicants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $statusFilter = $params['status'] ?? '';

        return $this->render($response, 'hr/recruitment/applicants', [
            'title'        => 'Sökande – ZYNC ERP',
            'applicants'   => $this->repo->allApplicants(),
            'statusFilter' => $statusFilter,
        ]);
    }

    public function createApplicant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $position = $this->repo->findPosition((int) $args['id']);
        if ($position === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/recruitment/applicants/create', [
            'title'    => 'Ny sökande – ZYNC ERP',
            'position' => $position,
            'errors'   => [],
            'old'      => [],
        ]);
    }

    public function storeApplicant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $positionId = (int) $args['id'];
        $position   = $this->repo->findPosition($positionId);
        if ($position === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $data = [
            'position_id' => $positionId,
            'first_name'  => trim((string) ($body['first_name'] ?? '')),
            'last_name'   => trim((string) ($body['last_name'] ?? '')),
            'email'       => trim((string) ($body['email'] ?? '')),
            'phone'       => trim((string) ($body['phone'] ?? '')),
            'applied_at'  => trim((string) ($body['applied_at'] ?? date('Y-m-d'))),
            'status'      => 'new',
            'notes'       => trim((string) ($body['notes'] ?? '')),
            'created_by'  => Auth::id(),
        ];

        $errors = [];
        if ($data['first_name'] === '') {
            $errors['first_name'] = 'Förnamn är obligatoriskt.';
        }
        if ($data['last_name'] === '') {
            $errors['last_name'] = 'Efternamn är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/recruitment/applicants/create', [
                'title'    => 'Ny sökande – ZYNC ERP',
                'position' => $position,
                'errors'   => $errors,
                'old'      => $data,
            ]);
        }

        $this->repo->createApplicant($data);
        Flash::set('success', 'Sökande registrerades.');
        return $this->redirect($response, '/hr/recruitment/positions/' . $positionId);
    }

    public function showApplicant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $applicant = $this->repo->findApplicant((int) $args['applicantId']);
        if ($applicant === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/recruitment/applicants/show', [
            'title'     => 'Sökande – ZYNC ERP',
            'applicant' => $applicant,
            'success'   => Flash::get('success'),
        ]);
    }

    public function editApplicant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $applicant = $this->repo->findApplicant((int) $args['applicantId']);
        if ($applicant === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/recruitment/applicants/edit', [
            'title'     => 'Redigera sökande – ZYNC ERP',
            'applicant' => $applicant,
            'errors'    => [],
        ]);
    }

    public function updateApplicant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $applicantId = (int) $args['applicantId'];
        $applicant   = $this->repo->findApplicant($applicantId);
        if ($applicant === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $data = [
            'first_name' => trim((string) ($body['first_name'] ?? '')),
            'last_name'  => trim((string) ($body['last_name'] ?? '')),
            'email'      => trim((string) ($body['email'] ?? '')),
            'phone'      => trim((string) ($body['phone'] ?? '')),
            'applied_at' => trim((string) ($body['applied_at'] ?? '')),
            'status'     => trim((string) ($body['status'] ?? 'new')),
            'notes'      => trim((string) ($body['notes'] ?? '')),
        ];

        $this->repo->updateApplicant($applicantId, $data);
        Flash::set('success', 'Sökande uppdaterades.');
        return $this->redirect($response, '/hr/recruitment/applicants/' . $applicantId);
    }

    public function deleteApplicant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $applicantId = (int) $args['applicantId'];
        $applicant   = $this->repo->findApplicant($applicantId);
        $positionId  = $applicant['position_id'] ?? null;

        if ($applicant !== null) {
            $this->repo->deleteApplicant($applicantId);
            Flash::set('success', 'Sökande togs bort.');
        }

        if ($positionId) {
            return $this->redirect($response, '/hr/recruitment/positions/' . $positionId);
        }
        return $this->redirect($response, '/hr/recruitment/applicants');
    }

    public function updateApplicantStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $applicantId = (int) $args['applicantId'];
        $body        = (array) $request->getParsedBody();
        $status      = trim((string) ($body['status'] ?? 'new'));

        $validStatuses = ['new', 'screening', 'interview', 'offer', 'hired', 'rejected'];
        if (in_array($status, $validStatuses, true)) {
            $this->repo->updateApplicantStatus($applicantId, $status);
            Flash::set('success', 'Status uppdaterades.');
        }

        return $this->redirect($response, '/hr/recruitment/applicants/' . $applicantId);
    }
}
