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
    /** GET /my-page */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $user = Auth::user();

        return $this->render($response, 'my-page/index', [
            'title' => 'Min Sida – ZYNC ERP',
            'user'  => $user,
        ]);
    }

    /** GET /my-page/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $user = Auth::user();

        return $this->render($response, 'my-page/edit', [
            'title'  => 'Redigera profil – ZYNC ERP',
            'user'   => $user,
            'errors' => [],
        ]);
    }

    /** POST /my-page */
    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id   = Auth::id();
        $user = Auth::user();
        $body = (array) $request->getParsedBody();

        $email = trim((string) ($body['email'] ?? ''));
        $phone = trim((string) ($body['phone'] ?? ''));
        $fullName = trim((string) ($body['full_name'] ?? ''));

        $errors = [];
        if ($email === '') {
            $errors['email'] = 'E-post är obligatorisk.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'my-page/edit', [
                'title'  => 'Redigera profil – ZYNC ERP',
                'user'   => array_merge($user ?? [], ['email' => $email, 'phone' => $phone, 'full_name' => $fullName]),
                'errors' => $errors,
            ]);
        }

        Database::pdo()->prepare(
            'UPDATE users SET email = ?, phone = ?, full_name = ? WHERE id = ?'
        )->execute([$email, $phone ?: null, $fullName ?: null, $id]);

        // Clear user cache so next request reflects the update
        unset($_SESSION['_user_cache']);

        Flash::set('success', 'Profilen uppdaterades.');
        return $this->redirect($response, '/my-page');
    }
}
