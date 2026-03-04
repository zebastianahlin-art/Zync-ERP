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

    /** GET /hr/recruitment */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/recruitment/index', [
            'title'     => 'Rekrytering – ZYNC ERP',
            'positions' => $this->repo->allPositions(),
        ]);
    }

    /** GET /hr/recruitment/positions/create */
    public function createPosition(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/recruitment/create', [
            'title'       => 'Ny tjänst – ZYNC ERP',
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    /** POST /hr/recruitment/positions */
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

        $this->repo->createPosition($data);
        Flash::set('success', 'Tjänsten skapades.');
        return $this->redirect($response, '/hr/recruitment');
    }

    /** GET /hr/recruitment/applicants */
    public function applicants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/recruitment/applicants', [
            'title'      => 'Sökande – ZYNC ERP',
            'applicants' => $this->repo->allApplicants(),
        ]);
    }

    /** GET /hr/recruitment/positions/{id} */
    public function showPosition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $position = $this->repo->findPosition((int) $args['id']);
        if ($position === null) {
            $response->getBody()->write('<h1>404 – Hittades inte</h1>');
            return $response->withStatus(404);
        }

        return $this->render($response, 'hr/recruitment/show', [
            'title'      => htmlspecialchars($position['title'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'position'   => $position,
            'applicants' => $this->repo->positionApplicants((int) $args['id']),
        ]);
    }
}
