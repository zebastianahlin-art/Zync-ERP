<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController extends Controller
{
    /** GET /dashboard – protected dashboard page. */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) {
            return $guard;
        }

        return $this->render($response, 'dashboard/index', [
            'title'  => 'Dashboard – ZYNC ERP',
            'userId' => Auth::id(),
        ]);
    }
}
