<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Models\UserRepository;

class AuthController extends Controller
{
    /** GET /login – show the login form. */
    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->render('auth/login', [
            'title' => 'Login – ZYNC ERP',
            'error' => Flash::get('error'),
        ]);
    }

    /** POST /login – validate credentials and log in. */
    public function login(Request $request): Response
    {
        $email    = (string) $request->input('email', '');
        $password = (string) $request->input('password', '');

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);

        if ($user === null || !password_verify($password, $user->passwordHash)) {
            Flash::set('error', 'Invalid email or password.');
            return $this->redirect('/login');
        }

        Auth::login($user->id);
        return $this->redirect('/dashboard');
    }

    /** GET /logout – log out and redirect to home. */
    public function logout(Request $request): Response
    {
        Auth::logout();
        return $this->redirect('/');
    }
}
