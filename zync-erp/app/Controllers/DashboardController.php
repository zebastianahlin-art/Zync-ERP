<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class DashboardController extends Controller
{
    /** GET /dashboard – protected dashboard page. */
    public function index(Request $request): Response
    {
        if ($guard = $this->requireAuth()) {
            return $guard;
        }

        return $this->render('dashboard/index', [
            'title'  => 'Dashboard – ZYNC ERP',
            'userId' => Auth::id(),
        ]);
    }
}
