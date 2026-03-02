<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MyPageController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = Auth::user();
        $db = Database::pdo();

        // Get linked employee data
        $employee = null;
        if (!empty($user['employee_id'])) {
            $stmt = $db->prepare('
                SELECT e.*, d.name AS department_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                WHERE e.id = ? AND e.is_deleted = 0
            ');
            $stmt->execute([$user['employee_id']]);
            $employee = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        // Get certificates for linked employee
        $certificates = [];
        if ($employee) {
            $stmt = $db->prepare('
                SELECT *
                FROM certificates
                WHERE employee_id = ? AND is_deleted = 0
                ORDER BY expiry_date ASC
            ');
            $stmt->execute([$employee['id']]);
            $certificates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Get assigned work orders
        $workOrders = [];
        $stmt = $db->prepare("
            SELECT wo.id, wo.title, wo.status, wo.priority, wo.planned_end,
                   e.name AS equipment_name
            FROM work_orders wo
            LEFT JOIN equipment e ON wo.equipment_id = e.id
            WHERE wo.assigned_to = ? AND wo.is_deleted = 0
                  AND wo.status NOT IN ('completed','closed','cancelled')
            ORDER BY
                CASE wo.priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END,
                wo.planned_end ASC
        ");
        $stmt->execute([$user['id']]);
        $workOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Build calendar events
        $calendarEvents = $this->buildCalendarEvents($certificates, $workOrders);

        return $this->render($response, 'mypage.index', [
            'title'          => 'Min Sida',
            'user'           => $user,
            'employee'       => $employee,
            'certificates'   => $certificates,
            'workOrders'     => $workOrders,
            'calendarEvents' => json_encode($calendarEvents, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function editProfile(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = Auth::user();
        $db = Database::pdo();

        $employee = null;
        if (!empty($user['employee_id'])) {
            $stmt = $db->prepare('SELECT * FROM employees WHERE id = ? AND is_deleted = 0');
            $stmt->execute([$user['employee_id']]);
            $employee = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        return $this->render($response, 'mypage.edit-profile', [
            'title'    => 'Redigera Profil',
            'user'     => $user,
            'employee' => $employee,
        ]);
    }

    public function updateProfile(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = Auth::user();
        $body = (array) $request->getParsedBody();
        $db = Database::pdo();

        $fullName = trim((string) ($body['full_name'] ?? ''));
        $email    = trim((string) ($body['email'] ?? ''));
        $phone    = trim((string) ($body['phone'] ?? ''));
        $address  = trim((string) ($body['address'] ?? ''));
        $city     = trim((string) ($body['city'] ?? ''));
        $postal   = trim((string) ($body['postal_code'] ?? ''));

        $errors = [];
        if ($fullName === '') $errors[] = 'Namn är obligatoriskt.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ogiltig e-postadress.';

        // Check email uniqueness
        if (empty($errors)) {
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ? AND is_deleted = 0');
            $stmt->execute([$email, $user['id']]);
            if ($stmt->fetch()) {
                $errors[] = 'E-postadressen används redan av en annan användare.';
            }
        }

        if (!empty($errors)) {
            Flash::set('error', implode(' ', $errors));
            return $this->redirect($response, '/my-page/edit');
        }

        // Update user
        $stmt = $db->prepare('UPDATE users SET full_name = ?, email = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$fullName, $email, $user['id']]);

        // Update linked employee if exists
        if (!empty($user['employee_id'])) {
            $stmt = $db->prepare('
                UPDATE employees SET email = ?, phone = ?, address = ?, city = ?, postal_code = ?, updated_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute([$email, $phone, $address, $city, $postal, $user['employee_id']]);
        }

        // Clear user cache
        unset($_SESSION['_user_cache']);

        Flash::set('success', 'Profilen uppdaterad.');
        return $this->redirect($response, '/my-page');
    }

    public function changePassword(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = Auth::user();
        $body = (array) $request->getParsedBody();
        $db = Database::pdo();

        $current = (string) ($body['current_password'] ?? '');
        $new     = (string) ($body['new_password'] ?? '');
        $confirm = (string) ($body['confirm_password'] ?? '');

        // Verify current password
        $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $hash = $stmt->fetchColumn();

        $errors = [];
        if (!password_verify($current, $hash)) $errors[] = 'Nuvarande lösenord är felaktigt.';
        if (strlen($new) < 8) $errors[] = 'Nytt lösenord måste vara minst 8 tecken.';
        if ($new !== $confirm) $errors[] = 'Lösenorden matchar inte.';

        if (!empty($errors)) {
            Flash::set('error', implode(' ', $errors));
            return $this->redirect($response, '/my-page/edit');
        }

        $stmt = $db->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);

        Flash::set('success', 'Lösenordet har ändrats.');
        return $this->redirect($response, '/my-page');
    }

    public function uploadAvatar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = Auth::user();
        $files = $request->getUploadedFiles();

        if (empty($files['avatar']) || $files['avatar']->getError() !== UPLOAD_ERR_OK) {
            Flash::set('error', 'Ingen fil vald eller uppladdning misslyckades.');
            return $this->redirect($response, '/my-page/edit');
        }

        $file = $files['avatar'];
        $ext  = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            Flash::set('error', 'Endast JPG, PNG eller WebP tillåts.');
            return $this->redirect($response, '/my-page/edit');
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            Flash::set('error', 'Maximal filstorlek är 2 MB.');
            return $this->redirect($response, '/my-page/edit');
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/avatars';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Delete old avatar
        if (!empty($user['avatar_path'])) {
            $oldFile = dirname(__DIR__, 2) . '/public' . $user['avatar_path'];
            if (is_file($oldFile)) unlink($oldFile);
        }

        $filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
        $file->moveTo($uploadDir . '/' . $filename);
        $avatarPath = '/uploads/avatars/' . $filename;

        $db = Database::pdo();
        $stmt = $db->prepare('UPDATE users SET avatar_path = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$avatarPath, $user['id']]);

        unset($_SESSION['_user_cache']);

        Flash::set('success', 'Profilbild uppdaterad.');
        return $this->redirect($response, '/my-page');
    }

    private function buildCalendarEvents(array $certificates, array $workOrders): array
    {
        $events = [];

        // Certificate expiry events
        foreach ($certificates as $cert) {
            if (!empty($cert['expiry_date'])) {
                $daysLeft = (int) ((strtotime($cert['expiry_date']) - time()) / 86400);
                $color = $daysLeft <= 30 ? '#ef4444' : ($daysLeft <= 90 ? '#f59e0b' : '#22c55e');
                $events[] = [
                    'title' => '📋 ' . ($cert['name'] ?? $cert['type'] ?? 'Certifikat') . ' utgår',
                    'start' => $cert['expiry_date'],
                    'color' => $color,
                    'url'   => '/certificates/' . $cert['id'] . '/edit',
                ];
            }
        }

        // Work order deadlines (planned_end)
        foreach ($workOrders as $wo) {
            if (!empty($wo['planned_end'])) {
                $color = match($wo['priority'] ?? 'medium') {
                    'critical' => '#ef4444',
                    'high'     => '#f97316',
                    'medium'   => '#3b82f6',
                    default    => '#6b7280',
                };
                $events[] = [
                    'title' => '🔧 ' . $wo['title'],
                    'start' => date('Y-m-d', strtotime($wo['planned_end'])),
                    'color' => $color,
                    'url'   => '/maintenance/work-orders/' . $wo['id'],
                ];
            }
        }

        return $events;
    }
}
