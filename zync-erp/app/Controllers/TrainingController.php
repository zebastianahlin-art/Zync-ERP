<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\TrainingRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TrainingController extends Controller
{
    private TrainingRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new TrainingRepository();
    }

    /** GET /hr/training */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/index', [
            'title'   => 'Utbildningar – ZYNC ERP',
            'courses' => $this->repo->allCourses(),
        ]);
    }

    /** GET /hr/training/courses/create */
    public function createCourse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/create', [
            'title'  => 'Ny utbildning – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /hr/training/courses */
    public function storeCourse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $data = [
            'name'         => trim((string) ($body['name'] ?? '')),
            'description'  => trim((string) ($body['description'] ?? '')),
            'duration_h'   => trim((string) ($body['duration_h'] ?? '')),
            'provider'     => trim((string) ($body['provider'] ?? '')),
            'category'     => trim((string) ($body['category'] ?? '')),
            'is_mandatory' => isset($body['is_mandatory']) ? 1 : 0,
            'created_by'   => Auth::id(),
        ];

        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/training/create', [
                'title'  => 'Ny utbildning – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $this->repo->createCourse($data);
        Flash::set('success', 'Utbildningen skapades.');
        return $this->redirect($response, '/hr/training');
    }

    /** GET /hr/training/sessions */
    public function sessions(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/sessions', [
            'title'    => 'Kurstillfällen – ZYNC ERP',
            'sessions' => $this->repo->allSessions(),
        ]);
    }

    /** GET /hr/training/participants */
    public function participants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/participants', [
            'title'        => 'Deltagare – ZYNC ERP',
            'participants' => $this->repo->allParticipants(),
        ]);
    }
}
