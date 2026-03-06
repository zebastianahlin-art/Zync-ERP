<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InventoryController extends Controller
{
    public function index(Request $request, Response $response): Response
    {
        return $this->view($response, 'Inventory/index', [
            'pageTitle' => 'Inventory',
        ]);
    }
}