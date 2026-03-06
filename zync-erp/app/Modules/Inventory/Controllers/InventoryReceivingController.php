<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Modules\Inventory\Services\InventoryReceivingService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class InventoryReceivingController extends Controller
{
    private InventoryReceivingService $service;

    public function __construct(?InventoryReceivingService $service = null)
    {
        parent::__construct();
        $this->service = $service ?? new InventoryReceivingService();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/receiving/index', [
            'title' => 'Inleveranser – ZYNC ERP',
            'purchaseOrders' => $this->service->getReceivablePurchaseOrders(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $poId = (int) ($args['poId'] ?? 0);
        $purchaseOrder = $this->service->getPurchaseOrderForReceiving($poId);

        if (!$purchaseOrder) {
            Flash::set('error', 'Inköpsordern hittades inte.');
            return $this->redirect($response, '/inventory/receiving');
        }

        return $this->render($response, 'inventory/receiving/show', [
            'title' => 'Registrera inleverans – ZYNC ERP',
            'purchaseOrder' => $purchaseOrder,
            'lines' => $this->service->getPurchaseOrderLines($poId),
            'warehouses' => $this->service->getWarehouses(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $poId = (int) ($args['poId'] ?? 0);
        $data = (array) $request->getParsedBody();

        $result = $this->service->receivePurchaseOrder($poId, $data);

        if (!$result['ok']) {
            Flash::set('error', $result['message']);
            return $this->redirect($response, "/inventory/receiving/{$poId}");
        }

        Flash::set('success', 'Inleverans registrerad.');
        return $this->redirect($response, "/inventory/receiving/{$poId}");
    }
}
