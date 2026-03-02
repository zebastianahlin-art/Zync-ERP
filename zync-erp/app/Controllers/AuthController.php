<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    /** GET /login – show the login form. */
    public function showLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (Auth::check()) {
            return $this->redirect($response, '/dashboard');
        }

        return $this->render($response, 'auth/login', [
            'title' => 'Login – ZYNC ERP',
            'error' => Flash::get('error'),
        ]);
    }

    /** POST /login – validate credentials and log in. */
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body     = (array) $request->getParsedBody();
        $email    = (string) ($body['email'] ?? '');
        $password = (string) ($body['password'] ?? '');

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);

        if ($user === null || !password_verify($password, $user->passwordHash)) {
            Flash::set('error', 'Felaktig e-postadress eller lösenord.');
            return $this->redirect($response, '/login');
        }

        Auth::login($user->id);

        // If the user has 2FA enabled, redirect to the verification step
        if (!empty($user->totpEnabled)) {
            Auth::set2faPending();
            return $this->redirect($response, '/2fa/verify');
        }

        return $this->redirect($response, '/dashboard');
    }

    /** GET /logout – log out and redirect to home. */
    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        Auth::logout();
        return $this->redirect($response, '/');
    }
}
