<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Models\AdminUserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AdminController — Handles admin panel and user management.
 * Requires role level >= 7 (enforced via RoleMiddleware on the route group).
 */
class AdminController extends Controller
{
    private AdminUserRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new AdminUserRepository();
    }

    /** GET /admin — Admin dashboard with stats. */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $stats = $this->repo->stats();

        return $this->render($response, 'admin/index', [
            'title' => 'Admin – ZYNC ERP',
            'stats' => $stats,
        ]);
    }

    /** GET /admin/users — List all users. */
    public function users(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/users/index', [
            'title' => 'Användare – Admin – ZYNC ERP',
            'users' => $this->repo->all(),
        ]);
    }

    /** GET /admin/users/create — Show create user form. */
    public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/users/create', [
            'title'       => 'Skapa användare – Admin – ZYNC ERP',
            'roles'       => $this->repo->allRoles(),
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    /** POST /admin/users — Create a new user. */
    public function storeUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseBody($request);
        $errors = $this->validateCreate($data);

        if (!empty($errors)) {
            return $this->render($response, 'admin/users/create', [
                'title'       => 'Skapa användare – Admin – ZYNC ERP',
                'roles'       => $this->repo->allRoles(),
                'departments' => $this->repo->allDepartments(),
                'errors'      => $errors,
                'old'         => $data,
            ]);
        }

        $this->repo->create($data);
        Flash::set('success', 'Användaren har skapats.');
        return $this->redirect($response, '/admin/users');
    }

    /** GET /admin/users/{id}/edit — Show edit user form. */
    public function editUser(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $this->repo->find((int) $args['id']);
        if ($user === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'admin/users/edit', [
            'title'       => 'Redigera användare – Admin – ZYNC ERP',
            'user'        => $user,
            'roles'       => $this->repo->allRoles(),
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
        ]);
    }

    /** POST /admin/users/{id} — Update a user. */
    public function updateUser(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $user = $this->repo->find($id);
        if ($user === null) {
            return $this->notFound($response);
        }

        $data   = $this->parseBody($request);
        $errors = $this->validateUpdate($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'admin/users/edit', [
                'title'       => 'Redigera användare – Admin – ZYNC ERP',
                'user'        => array_merge($user, $data),
                'roles'       => $this->repo->allRoles(),
                'departments' => $this->repo->allDepartments(),
                'errors'      => $errors,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Användaren har uppdaterats.');
        return $this->redirect($response, '/admin/users');
    }

    /** POST /admin/users/{id}/toggle — Toggle is_active. */
    public function toggleUser(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $user = $this->repo->find($id);
        if ($user === null) {
            return $this->notFound($response);
        }

        $this->repo->toggleActive($id);
        $status = $user['is_active'] ? 'inaktiverad' : 'aktiverad';
        Flash::set('success', "Användaren har {$status}.");
        return $this->redirect($response, '/admin/users');
    }

    /** @return array<string, string> */
    private function parseBody(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'username'      => trim((string) ($body['username'] ?? '')),
            'email'         => trim((string) ($body['email'] ?? '')),
            'password'      => (string) ($body['password'] ?? ''),
            'role_id'       => trim((string) ($body['role_id'] ?? '')),
            'department_id' => trim((string) ($body['department_id'] ?? '')),
        ];
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    private function validateCreate(array $data): array
    {
        $errors = [];

        if ($data['username'] === '') {
            $errors['username'] = 'Användarnamn är obligatoriskt.';
        } elseif ($this->repo->usernameExists($data['username'])) {
            $errors['username'] = 'Användarnamnet är redan taget.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'E-post är obligatoriskt.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
        } elseif ($this->repo->emailExists($data['email'])) {
            $errors['email'] = 'E-postadressen används redan.';
        }

        if (strlen($data['password']) < 8) {
            $errors['password'] = 'Lösenordet måste vara minst 8 tecken.';
        }

        if ($data['role_id'] === '' || !ctype_digit($data['role_id'])) {
            $errors['role_id'] = 'Roll är obligatoriskt.';
        }

        return $errors;
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    private function validateUpdate(array $data, int $excludeId): array
    {
        $errors = [];

        if ($data['username'] === '') {
            $errors['username'] = 'Användarnamn är obligatoriskt.';
        } elseif ($this->repo->usernameExists($data['username'], $excludeId)) {
            $errors['username'] = 'Användarnamnet är redan taget.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'E-post är obligatoriskt.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
        } elseif ($this->repo->emailExists($data['email'], $excludeId)) {
            $errors['email'] = 'E-postadressen används redan.';
        }

        if ($data['password'] !== '' && strlen($data['password']) < 8) {
            $errors['password'] = 'Lösenordet måste vara minst 8 tecken.';
        }

        if ($data['role_id'] === '' || !ctype_digit($data['role_id'])) {
            $errors['role_id'] = 'Roll är obligatoriskt.';
        }

        return $errors;
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Användaren hittades inte</h1>');
        return $response->withStatus(404);
    }
}
