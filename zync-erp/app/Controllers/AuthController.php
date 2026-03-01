<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;

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
        $username = (string) $request->input('username', '');
        $password = (string) $request->input('password', '');

        if ($username !== 'admin' || $password !== 'admin') {
            Flash::set('error', 'Invalid username or password.');
            return $this->redirect('/login');
        }

        Auth::login(1);
        return $this->redirect('/dashboard');
    }

    /** GET /logout – log out and redirect to home. */
    public function logout(Request $request): Response
    {
        Auth::logout();
        return $this->redirect('/');
    }
}
