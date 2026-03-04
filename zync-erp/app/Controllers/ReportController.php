<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'reports/index', ['title' => 'Rapporter – ZYNC ERP']);
    }
}
