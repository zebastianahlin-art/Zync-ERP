<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AdminSettingsRepository;
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
    private AdminSettingsRepository $settings;

    public function __construct()
    {
        parent::__construct();
        $this->repo     = new AdminUserRepository();
        $this->settings = new AdminSettingsRepository();
    }

    /** GET /admin — Admin dashboard with stats and system info. */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $stats      = $this->repo->stats();
        $sysInfo    = $this->settings->systemInfo();
        $auditResult = $this->settings->auditLog([], 1, 5);

        return $this->render($response, 'admin/index', [
            'title'        => 'Admin – ZYNC ERP',
            'stats'        => $stats,
            'sys_info'     => $sysInfo,
            'recent_audit' => $auditResult['rows'],
        ]);
    }

    // ─── System Settings ──────────────────────────────────────────────────────

    /** GET /admin/settings — Show all system settings grouped by category. */
    public function settings(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/settings', [
            'title'    => 'Systeminställningar – Admin – ZYNC ERP',
            'settings' => $this->settings->allSettingsGrouped(),
        ]);
    }

    /** POST /admin/settings — Batch-update settings. */
    public function updateSettings(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $keyValues = [];
        foreach ($body as $key => $value) {
            if (str_starts_with((string) $key, '_') || $key === '_token') {
                continue;
            }
            $keyValues[(string) $key] = (string) $value;
        }
        if (!empty($keyValues)) {
            $this->settings->setSettings($keyValues);
        }
        Flash::set('success', 'Inställningarna har uppdaterats.');
        return $this->redirect($response, '/admin/settings');
    }

    // ─── Module Administration ─────────────────────────────────────────────────

    /** GET /admin/modules — List all ERP modules. */
    public function modules(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/modules', [
            'title'   => 'Moduladministration – Admin – ZYNC ERP',
            'modules' => $this->settings->allModules(),
        ]);
    }

    /** POST /admin/modules/{id}/toggle — Toggle module active/inactive. */
    public function toggleModule(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $module = $this->settings->findModule($id);
        if ($module === null) {
            Flash::set('error', 'Modulen hittades inte.');
            return $this->redirect($response, '/admin/modules');
        }
        $this->settings->toggleModule($id);
        $label = $module['is_active'] ? 'inaktiverad' : 'aktiverad';
        Flash::set('success', "Modulen har {$label}.");
        return $this->redirect($response, '/admin/modules');
    }

    // ─── Site Settings ─────────────────────────────────────────────────────────

    /** GET /admin/site — Show site/company settings. */
    public function siteSettings(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/site', [
            'title' => 'Site-inställningar – Admin – ZYNC ERP',
            'site'  => $this->settings->getSiteSettings(),
        ]);
    }

    /** POST /admin/site — Update site settings. */
    public function updateSiteSettings(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->settings->updateSiteSettings($body);
        Flash::set('success', 'Site-inställningarna har uppdaterats.');
        return $this->redirect($response, '/admin/site');
    }

    // ─── Audit Log ─────────────────────────────────────────────────────────────

    /** GET /admin/audit-log — Show audit log with pagination and filters. */
    public function auditLog(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $filters = [
            'module'    => $query['module']    ?? '',
            'action'    => $query['action']    ?? '',
            'user_id'   => $query['user_id']   ?? '',
            'date_from' => $query['date_from'] ?? '',
            'date_to'   => $query['date_to']   ?? '',
        ];
        $page   = max(1, (int) ($query['page'] ?? 1));
        $result = $this->settings->auditLog($filters, $page, 50);
        $users  = $this->repo->all();

        return $this->render($response, 'admin/audit-log', [
            'title'   => 'Audit-logg – Admin – ZYNC ERP',
            'rows'    => $result['rows'],
            'total'   => $result['total'],
            'page'    => $page,
            'filters' => $filters,
            'users'   => $users,
        ]);
    }

    /** POST /admin/audit-log/clear — Clear old audit log entries. */
    public function clearAuditLog(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body   = (array) $request->getParsedBody();
        $days   = max(1, (int) ($body['days'] ?? 365));
        $deleted = $this->settings->clearAuditLog($days);
        Flash::set('success', "{$deleted} loggposter äldre än {$days} dagar har raderats.");
        return $this->redirect($response, '/admin/audit-log');
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

    // ─── Role Management ─────────────────────────────────────────────────────────

    /** GET /admin/roles */
    public function roles(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/roles/index', [
            'title' => 'Roller – Admin – ZYNC ERP',
            'roles' => $this->repo->allRoles(),
        ]);
    }

    /** GET /admin/roles/create */
    public function createRole(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'admin/roles/create', [
            'title'  => 'Ny roll – Admin – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /admin/roles */
    public function storeRole(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseRoleBody($request);
        $errors = $this->validateRole($data);

        if (!empty($errors)) {
            return $this->render($response, 'admin/roles/create', [
                'title'  => 'Ny roll – Admin – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $this->repo->createRole($data);
        Flash::set('success', 'Rollen skapades.');
        return $this->redirect($response, '/admin/roles');
    }

    /** GET /admin/roles/{id}/edit */
    public function editRole(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $role = $this->repo->findRole((int) $args['id']);
        if ($role === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'admin/roles/edit', [
            'title'  => 'Redigera roll – Admin – ZYNC ERP',
            'role'   => $role,
            'errors' => [],
        ]);
    }

    /** POST /admin/roles/{id} */
    public function updateRole(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $role = $this->repo->findRole($id);
        if ($role === null) {
            return $this->notFound($response);
        }

        $data   = $this->parseRoleBody($request);
        $errors = $this->validateRole($data);

        if (!empty($errors)) {
            return $this->render($response, 'admin/roles/edit', [
                'title'  => 'Redigera roll – Admin – ZYNC ERP',
                'role'   => $role,
                'errors' => $errors,
            ]);
        }

        $this->repo->updateRole($id, $data);
        Flash::set('success', 'Rollen uppdaterades.');
        return $this->redirect($response, '/admin/roles');
    }

    /** POST /admin/roles/{id}/delete */
    public function deleteRole(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findRole($id) !== null) {
            $this->repo->deleteRole($id);
            Flash::set('success', 'Rollen togs bort.');
        }
        return $this->redirect($response, '/admin/roles');
    }

    /** @return array<string, string> */
    private function parseRoleBody(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'  => trim((string) ($body['name'] ?? '')),
            'slug'  => trim((string) ($body['slug'] ?? '')),
            'level' => trim((string) ($body['level'] ?? '1')),
        ];
    }

    /** @return array<string, string> */
    private function validateRole(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        if ($data['slug'] === '') {
            $errors['slug'] = 'Slug är obligatorisk.';
        }
        if (!is_numeric($data['level']) || (int) $data['level'] < 1 || (int) $data['level'] > 10) {
            $errors['level'] = 'Nivå måste vara ett heltal 1–10.';
        }
        return $errors;
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
        $actionLabel = $user['is_active'] ? 'inaktiverad' : 'aktiverad';
        Flash::set('success', "Användaren har {$actionLabel}.");
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
