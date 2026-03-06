<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Modules\Inventory\Services\InventoryTransactionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class InventoryTransactionController extends Controller
{
    private InventoryTransactionService $service;

    public function __construct(?InventoryTransactionService $service = null)
    {
        parent::__construct();
        $this->service = $service ?? new InventoryTransactionService();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/transactions/index', [
            'title' => 'Lagertransaktioner – ZYNC ERP',
            'transactions' => $this->service->getTransactions(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/transactions/create', [
            'title' => 'Ny lagertransaktion – ZYNC ERP',
            'articles' => $this->service->getArticles(),
            'warehouses' => $this->service->getWarehouses(),
            'users' => $this->service->getUsers(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'] ?? null;

        $result = $this->service->createTransaction($data);

        if (!$result['ok']) {
            Flash::set('error', $result['message']);
            return $this->redirect($response, '/inventory/transactions/create');
        }

        Flash::set('success', 'Lagertransaktion skapad.');
        return $this->redirect($response, '/inventory/transactions/' . $result['id']);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) ($args['id'] ?? 0);
        $transaction = $this->service->getTransactionById($id);

        if (!$transaction) {
            Flash::set('error', 'Transaktionen hittades inte.');
            return $this->redirect($response, '/inventory/transactions');
        }

        return $this->render($response, 'inventory/transactions/show', [
            'title' => 'Transaktion – ZYNC ERP',
            'transaction' => $transaction,
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }
}