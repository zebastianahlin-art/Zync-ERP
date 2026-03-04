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

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/training/index', [
            'title'    => 'Utbildningar – ZYNC ERP',
            'courses'  => $this->repo->allCourses(),
            'sessions' => $this->repo->allSessions(),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/training/create', [
            'title'  => 'Ny utbildning – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'name'              => trim((string) ($body['name'] ?? '')),
            'description'       => $body['description'] ?? '',
            'provider'          => $body['provider'] ?? '',
            'duration_hours'    => $body['duration_hours'] ?? '',
            'category'          => $body['category'] ?? '',
            'is_recurring'      => isset($body['is_recurring']) ? 1 : 0,
            'recurrence_months' => $body['recurrence_months'] ?? '',
        ];
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/training/create', [
                'title' => 'Ny utbildning – ZYNC ERP', 'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->createCourse($data);
        Flash::set('success', 'Utbildningen har skapats.');
        return $this->redirect($response, '/hr/training/' . $id);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $course = $this->repo->findCourse((int) $args['id']);
        if ($course === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/training/show', [
            'title'  => $course['name'] . ' – Utbildning – ZYNC ERP',
            'course' => $course,
        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $course = $this->repo->findCourse((int) $args['id']);
        if ($course === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/training/edit', [
            'title'  => 'Redigera ' . $course['name'] . ' – ZYNC ERP',
            'course' => $course,
            'errors' => [],
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id     = (int) $args['id'];
        $course = $this->repo->findCourse($id);
        if ($course === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'name'              => trim((string) ($body['name'] ?? '')),
            'description'       => $body['description'] ?? '',
            'provider'          => $body['provider'] ?? '',
            'duration_hours'    => $body['duration_hours'] ?? '',
            'category'          => $body['category'] ?? '',
            'is_recurring'      => isset($body['is_recurring']) ? 1 : 0,
            'recurrence_months' => $body['recurrence_months'] ?? '',
        ];
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/training/edit', [
                'title' => 'Redigera – ZYNC ERP', 'course' => array_merge($course, $data), 'errors' => $errors,
            ]);
        }
        $this->repo->updateCourse($id, $data);
        Flash::set('success', 'Utbildningen har uppdaterats.');
        return $this->redirect($response, '/hr/training/' . $id);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deleteCourse((int) $args['id']);
        Flash::set('success', 'Utbildningen har tagits bort.');
        return $this->redirect($response, '/hr/training');
    }

    public function sessionsIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/training/index', [
            'title'    => 'Utbildningstillfällen – ZYNC ERP',
            'courses'  => $this->repo->allCourses(),
            'sessions' => $this->repo->allSessions(),
        ]);
    }

    public function sessionsCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'hr/training/create', [
            'title'   => 'Nytt utbildningstillfälle – ZYNC ERP',
            'courses' => $this->repo->allCourses(),
            'errors'  => [],
            'old'     => [],
        ]);
    }

    public function sessionsStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'course_id'        => $body['course_id'] ?? '',
            'scheduled_date'   => $body['scheduled_date'] ?? '',
            'location'         => $body['location'] ?? '',
            'instructor'       => $body['instructor'] ?? '',
            'max_participants' => $body['max_participants'] ?? '',
            'status'           => $body['status'] ?? 'planned',
            'notes'            => $body['notes'] ?? '',
        ];
        $errors = [];
        if (empty($data['course_id'])) { $errors['course_id'] = 'Utbildning är obligatorisk.'; }
        if ($data['scheduled_date'] === '') { $errors['scheduled_date'] = 'Datum är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'hr/training/create', [
                'title' => 'Nytt utbildningstillfälle – ZYNC ERP', 'courses' => $this->repo->allCourses(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->createSession($data);
        Flash::set('success', 'Utbildningstillfället har skapats.');
        return $this->redirect($response, '/hr/training/sessions/' . $id);
    }

    public function sessionsShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $session = $this->repo->findSession((int) $args['id']);
        if ($session === null) { return $this->notFound($response); }
        return $this->render($response, 'hr/training/show', [
            'title'   => 'Tillfälle – ' . ($session['course_name'] ?? '') . ' – ZYNC ERP',
            'course'  => $session,
            'session' => $session,
        ]);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
