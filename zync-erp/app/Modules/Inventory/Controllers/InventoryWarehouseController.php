<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Modules\Inventory\Services\InventoryWarehouseService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class InventoryWarehouseController extends Controller
{
    private InventoryWarehouseService $service;

    public function __construct(?InventoryWarehouseService $service = null)
    {
        parent::__construct();
        $this->service = $service ?? new InventoryWarehouseService();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/warehouses/index', [
            'title' => 'Lagerställen – ZYNC ERP',
            'warehouses' => $this->service->getWarehouses(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/warehouses/create', [
            'title' => 'Nytt lagerställe – ZYNC ERP',
            'users' => $this->service->getAssignableUsers(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'] ?? null;

        $result = $this->service->createWarehouse($data);

        if (!$result['ok']) {
            Flash::set('error', $result['message']);
            return $this->redirect($response, '/inventory/warehouses/create');
        }

        Flash::set('success', 'Lagerställe skapat.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) ($args['id'] ?? 0);
        $warehouse = $this->service->getWarehouseById($id);

        if (!$warehouse) {
            Flash::set('error', 'Lagerställe hittades inte.');
            return $this->redirect($response, '/inventory/warehouses');
        }

        return $this->render($response, 'inventory/warehouses/edit', [
            'title' => 'Redigera lagerställe – ZYNC ERP',
            'warehouse' => $warehouse,
            'users' => $this->service->getAssignableUsers(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) ($args['id'] ?? 0);
        $data = (array) $request->getParsedBody();

        $result = $this->service->updateWarehouse($id, $data);

        if (!$result['ok']) {
            Flash::set('error', $result['message']);
            return $this->redirect($response, "/inventory/warehouses/{$id}/edit");
        }

        Flash::set('success', 'Lagerställe uppdaterat.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) ($args['id'] ?? 0);
        $this->service->deleteWarehouse($id);

        Flash::set('success', 'Lagerställe borttaget.');
        return $this->redirect($response, '/inventory/warehouses');
    }
}