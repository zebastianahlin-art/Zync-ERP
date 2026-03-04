<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\InventoryRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InventoryController extends Controller
{
    private InventoryRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new InventoryRepository();
    }

    /** GET /inventory */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'inventory/index', [
            'title'      => 'Lager – ZYNC ERP',
            'stockItems' => $this->repo->allStock(),
        ]);
    }

    /** GET /inventory/{id} */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $stock = $this->repo->findStock((int) $args['id']);
        if ($stock === null) {
            $response->getBody()->write('<h1>404 – Lagerpost hittades inte</h1>');
            return $response->withStatus(404);
        }

        return $this->render($response, 'inventory/show', [
            'title'        => 'Lagerpost – ZYNC ERP',
            'stock'        => $stock,
            'transactions' => $this->repo->transactionsForStock((int) $args['id']),
        ]);
    }
}
