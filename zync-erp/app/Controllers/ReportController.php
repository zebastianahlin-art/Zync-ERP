<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController extends Controller
{
    /** GET /reports */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'reports/index', [
            'title' => 'Rapporter – ZYNC ERP',
        ]);
    }
}
