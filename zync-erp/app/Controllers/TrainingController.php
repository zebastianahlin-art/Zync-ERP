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
        return $this->render($response, 'hr/training/index', [
            'title'   => 'Utbildningar – ZYNC ERP',
            'courses' => $this->repo->allCourses(),
            'success' => Flash::get('success'),
        ]);
    }

    public function createCourse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/create', [
            'title'  => 'Ny utbildning – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

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

    public function showCourse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $course = $this->repo->findCourse((int) $args['id']);
        if ($course === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/training/show', [
            'title'    => htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'course'   => $course,
            'sessions' => $this->repo->allSessions(),
        ]);
    }

    public function editCourse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $course = $this->repo->findCourse((int) $args['id']);
        if ($course === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/training/edit', [
            'title'  => 'Redigera utbildning – ZYNC ERP',
            'course' => $course,
            'errors' => [],
        ]);
    }

    public function updateCourse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $course = $this->repo->findCourse($id);
        if ($course === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $data = [
            'name'         => trim((string) ($body['name'] ?? '')),
            'description'  => trim((string) ($body['description'] ?? '')),
            'duration_h'   => trim((string) ($body['duration_h'] ?? '')),
            'provider'     => trim((string) ($body['provider'] ?? '')),
            'category'     => trim((string) ($body['category'] ?? '')),
            'is_mandatory' => isset($body['is_mandatory']) ? 1 : 0,
        ];

        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/training/edit', [
                'title'  => 'Redigera utbildning – ZYNC ERP',
                'course' => array_merge($course, $data),
                'errors' => $errors,
            ]);
        }

        $this->repo->updateCourse($id, $data);
        Flash::set('success', 'Utbildningen uppdaterades.');
        return $this->redirect($response, '/hr/training/courses/' . $id);
    }

    public function deleteCourse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findCourse($id) !== null) {
            $this->repo->deleteCourse($id);
            Flash::set('success', 'Utbildningen togs bort.');
        }
        return $this->redirect($response, '/hr/training');
    }

    public function sessions(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/sessions', [
            'title'    => 'Kurstillfällen – ZYNC ERP',
            'sessions' => $this->repo->allSessions(),
            'courses'  => $this->repo->allCourses(),
            'success'  => Flash::get('success'),
        ]);
    }

    public function createSession(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/sessions/create', [
            'title'   => 'Nytt kurstillfälle – ZYNC ERP',
            'courses' => $this->repo->allCourses(),
            'errors'  => [],
            'old'     => [],
        ]);
    }

    public function storeSession(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $data = [
            'course_id'        => trim((string) ($body['course_id'] ?? '')),
            'start_date'       => trim((string) ($body['start_date'] ?? '')),
            'end_date'         => trim((string) ($body['end_date'] ?? '')),
            'location'         => trim((string) ($body['location'] ?? '')),
            'trainer'          => trim((string) ($body['trainer'] ?? '')),
            'max_participants' => trim((string) ($body['max_participants'] ?? '')),
            'status'           => trim((string) ($body['status'] ?? 'planned')),
            'created_by'       => Auth::id(),
        ];

        $errors = [];
        if ($data['course_id'] === '') {
            $errors['course_id'] = 'Välj en kurs.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/training/sessions/create', [
                'title'   => 'Nytt kurstillfälle – ZYNC ERP',
                'courses' => $this->repo->allCourses(),
                'errors'  => $errors,
                'old'     => $data,
            ]);
        }

        $sessionId = $this->repo->createSession($data);
        Flash::set('success', 'Kurstillfället skapades.');
        return $this->redirect($response, '/hr/training/sessions/' . $sessionId);
    }

    public function showSession(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $session = $this->repo->findSession((int) $args['id']);
        if ($session === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/training/sessions/show', [
            'title'        => 'Kurstillfälle – ZYNC ERP',
            'session'      => $session,
            'participants' => $this->repo->sessionParticipants((int) $args['id']),
            'employees'    => $this->repo->allEmployees(),
            'success'      => Flash::get('success'),
        ]);
    }

    public function editSession(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $session = $this->repo->findSession((int) $args['id']);
        if ($session === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'hr/training/sessions/edit', [
            'title'   => 'Redigera kurstillfälle – ZYNC ERP',
            'session' => $session,
            'courses' => $this->repo->allCourses(),
            'errors'  => [],
        ]);
    }

    public function updateSession(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $session = $this->repo->findSession($id);
        if ($session === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $data = [
            'course_id'        => trim((string) ($body['course_id'] ?? '')),
            'start_date'       => trim((string) ($body['start_date'] ?? '')),
            'end_date'         => trim((string) ($body['end_date'] ?? '')),
            'location'         => trim((string) ($body['location'] ?? '')),
            'trainer'          => trim((string) ($body['trainer'] ?? '')),
            'max_participants' => trim((string) ($body['max_participants'] ?? '')),
            'status'           => trim((string) ($body['status'] ?? 'planned')),
        ];

        $this->repo->updateSession($id, $data);
        Flash::set('success', 'Kurstillfället uppdaterades.');
        return $this->redirect($response, '/hr/training/sessions/' . $id);
    }

    public function deleteSession(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findSession($id) !== null) {
            $this->repo->deleteSession($id);
            Flash::set('success', 'Kurstillfället togs bort.');
        }
        return $this->redirect($response, '/hr/training/sessions');
    }

    public function addParticipant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $empId = (int) ($body['employee_id'] ?? 0);

        if ($empId > 0 && $this->repo->findSession($id) !== null) {
            $this->repo->addParticipant($id, $empId);
            Flash::set('success', 'Deltagare registrerades.');
        }

        return $this->redirect($response, '/hr/training/sessions/' . $id);
    }

    public function removeParticipant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionId     = (int) $args['id'];
        $participantId = (int) $args['participantId'];
        $this->repo->removeParticipant($participantId);
        Flash::set('success', 'Deltagare togs bort.');
        return $this->redirect($response, '/hr/training/sessions/' . $sessionId);
    }

    public function completeParticipant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $sessionId     = (int) $args['id'];
        $participantId = (int) $args['participantId'];
        $this->repo->updateParticipantStatus($participantId, 'completed');
        Flash::set('success', 'Status uppdaterades.');
        return $this->redirect($response, '/hr/training/sessions/' . $sessionId);
    }

    public function participants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/participants', [
            'title'        => 'Deltagare – ZYNC ERP',
            'participants' => $this->repo->allParticipants(),
        ]);
    }
}
