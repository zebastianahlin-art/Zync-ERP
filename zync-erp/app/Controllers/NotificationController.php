<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Core\NotificationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * NotificationController — Hanterar in-app-notifikationer.
 */
class NotificationController extends Controller
{
    private NotificationService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new NotificationService(Database::pdo());
    }

    /** GET /notifications — Visa alla notifikationer */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->redirect($response, '/login');
        }

        try {
            $notifications = $this->service->forUser($userId, 50);
        } catch (\Throwable) {
            $notifications = [];
        }

        return $this->render($response, 'notifications/index', [
            'title'         => 'Notifikationer – ZYNC ERP',
            'notifications' => $notifications,
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => '/dashboard'],
                ['label' => 'Notifikationer'],
            ],
        ]);
    }

    /** POST /notifications/{id}/read — Markera som läst */
    public function markRead(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->json($response, ['error' => 'Ej inloggad'], 401);
        }

        try {
            $this->service->markRead((int) $args['id']);
        } catch (\Throwable) {
            // Tyst fel
        }

        return $this->redirect($response, '/notifications');
    }

    /** POST /notifications/read-all — Markera alla som lästa */
    public function markAllRead(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->json($response, ['error' => 'Ej inloggad'], 401);
        }

        try {
            $this->service->markAllRead($userId);
            Flash::set('success', 'Alla notifikationer markerades som lästa.');
        } catch (\Throwable) {
            Flash::set('error', 'Ett fel uppstod.');
        }

        return $this->redirect($response, '/notifications');
    }

    /** GET /api/notifications/unread-count — JSON för AJAX-polling */
    public function unreadCount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->json($response, ['count' => 0]);
        }

        try {
            $count = $this->service->unreadCount($userId);
        } catch (\Throwable) {
            $count = 0;
        }

        return $this->json($response, ['count' => $count]);
    }

    /** GET /api/notifications/recent — JSON för navbar-dropdown */
    public function recent(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->json($response, ['notifications' => []]);
        }

        try {
            $notifications = $this->service->forUser($userId, 10);
        } catch (\Throwable) {
            $notifications = [];
        }

        return $this->json($response, ['notifications' => $notifications]);
    }
}
