<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\TransportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TransportController extends Controller
{
    private TransportRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new TransportRepository();
    }

    /** GET /transport */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'transport/dashboard', [
            'title'  => 'Transport – ZYNC ERP',
            'stats'  => $this->repo->stats(),
            'orders' => $this->repo->recentOrders(5),
        ]);
    }

    /** GET /transport/orders */
    public function orders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'transport/orders/index', [
            'title'  => 'Transportordrar – ZYNC ERP',
            'orders' => $this->repo->allOrders(),
        ]);
    }

    /** GET /transport/orders/create */
    public function createOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'transport/orders/create', [
            'title'     => 'Ny transportorder – ZYNC ERP',
            'carriers'  => $this->repo->allCarriers(),
            'customers' => $this->repo->allCustomers(),
            'suppliers' => $this->repo->allSuppliers(),
            'articles'  => $this->repo->allArticles(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /transport/orders */
    public function storeOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractOrderData($request);
        $errors = $this->validateOrder($data);

        if (!empty($errors)) {
            return $this->render($response, 'transport/orders/create', [
                'title'     => 'Ny transportorder – ZYNC ERP',
                'carriers'  => $this->repo->allCarriers(),
                'customers' => $this->repo->allCustomers(),
                'suppliers' => $this->repo->allSuppliers(),
                'articles'  => $this->repo->allArticles(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->createOrder($data);
        Flash::set('success', 'Transportordern skapades.');
        return $this->redirect($response, '/transport/orders/' . $id);
    }

    /** GET /transport/orders/{id} */
    public function showOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if ($order === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'transport/orders/show', [
            'title' => htmlspecialchars($order['transport_number'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'order' => $order,
        ]);
    }

    /** GET /transport/orders/{id}/edit */
    public function editOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if ($order === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'transport/orders/edit', [
            'title'     => 'Redigera transportorder – ZYNC ERP',
            'order'     => $order,
            'carriers'  => $this->repo->allCarriers(),
            'customers' => $this->repo->allCustomers(),
            'suppliers' => $this->repo->allSuppliers(),
            'errors'    => [],
        ]);
    }

    /** POST /transport/orders/{id} */
    public function updateOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id    = (int) $args['id'];
        $order = $this->repo->findOrder($id);
        if ($order === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractOrderData($request);
        $errors = $this->validateOrder($data);

        if (!empty($errors)) {
            return $this->render($response, 'transport/orders/edit', [
                'title'     => 'Redigera transportorder – ZYNC ERP',
                'order'     => array_merge($order, $data),
                'carriers'  => $this->repo->allCarriers(),
                'customers' => $this->repo->allCustomers(),
                'suppliers' => $this->repo->allSuppliers(),
                'errors'    => $errors,
            ]);
        }

        $this->repo->updateOrder($id, $data);
        Flash::set('success', 'Transportordern uppdaterades.');
        return $this->redirect($response, '/transport/orders/' . $id);
    }

    /** POST /transport/orders/{id}/status */
    public function updateOrderStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $body   = (array) $request->getParsedBody();
        $status = trim((string) ($body['status'] ?? ''));

        $allowed = ['planned', 'confirmed', 'in_transit', 'delivered', 'cancelled'];
        if (in_array($status, $allowed, true)) {
            $this->repo->updateOrderStatus($id, $status);
            Flash::set('success', 'Status uppdaterades.');
        }

        return $this->redirect($response, '/transport/orders/' . $id);
    }

    /** POST /transport/orders/{id}/delete */
    public function deleteOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->deleteOrder((int) $args['id']);
        Flash::set('success', 'Transportordern togs bort.');
        return $this->redirect($response, '/transport/orders');
    }

    // ─── Carriers ────────────────────────────────────────────────────────

    /** GET /transport/carriers */
    public function carriers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'transport/carriers/index', [
            'title'    => 'Transportörer – ZYNC ERP',
            'carriers' => $this->repo->allCarriers(),
        ]);
    }

    /** GET /transport/carriers/create */
    public function createCarrier(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'transport/carriers/create', [
            'title'     => 'Ny transportör – ZYNC ERP',
            'suppliers' => $this->repo->allSuppliers(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /transport/carriers */
    public function storeCarrier(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractCarrierData($request);
        $errors = $this->validateCarrier($data);

        if (!empty($errors)) {
            return $this->render($response, 'transport/carriers/create', [
                'title'     => 'Ny transportör – ZYNC ERP',
                'suppliers' => $this->repo->allSuppliers(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $carrierId = $this->repo->createCarrier($data);

        // B6: Synkronisera med leverantörsregistret
        $body = (array) $request->getParsedBody();
        $syncSupplier    = !empty($body['sync_supplier']);
        $existingSupplId = (int) ($body['existing_supplier_id'] ?? 0);
        if ($syncSupplier || $existingSupplId > 0) {
            $this->repo->syncCarrierWithSupplier(
                $carrierId,
                $data['name'],
                $data['email'] ?? '',
                $data['phone'] ?? '',
                (int) Auth::id(),
                $existingSupplId ?: null
            );
        }

        Flash::set('success', 'Transportören skapades.');
        return $this->redirect($response, '/transport/carriers');
    }

    /** GET /transport/carriers/{id}/edit */
    public function editCarrier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $carrier = $this->repo->findCarrier((int) $args['id']);
        if ($carrier === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'transport/carriers/edit', [
            'title'   => 'Redigera transportör – ZYNC ERP',
            'carrier' => $carrier,
            'errors'  => [],
        ]);
    }

    /** POST /transport/carriers/{id} */
    public function updateCarrier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $carrier = $this->repo->findCarrier($id);
        if ($carrier === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractCarrierData($request);
        $errors = $this->validateCarrier($data);

        if (!empty($errors)) {
            return $this->render($response, 'transport/carriers/edit', [
                'title'   => 'Redigera transportör – ZYNC ERP',
                'carrier' => array_merge($carrier, $data),
                'errors'  => $errors,
            ]);
        }

        $this->repo->updateCarrier($id, $data);
        Flash::set('success', 'Transportören uppdaterades.');
        return $this->redirect($response, '/transport/carriers');
    }

    /** POST /transport/carriers/{id}/delete */
    public function deleteCarrier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->deleteCarrier((int) $args['id']);
        Flash::set('success', 'Transportören togs bort.');
        return $this->redirect($response, '/transport/carriers');
    }

    /** @return array<string, string> */
    private function extractOrderData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'transport_number' => trim((string) ($body['transport_number'] ?? '')),
            'type'             => trim((string) ($body['type']             ?? 'outbound')),
            'carrier_id'       => trim((string) ($body['carrier_id']       ?? '')),
            'customer_id'      => trim((string) ($body['customer_id']      ?? '')),
            'supplier_id'      => trim((string) ($body['supplier_id']      ?? '')),
            'sales_order_id'   => trim((string) ($body['sales_order_id']   ?? '')),
            'article_id'       => trim((string) ($body['article_id']       ?? '')),
            'pickup_address'   => trim((string) ($body['pickup_address']   ?? '')),
            'delivery_address' => trim((string) ($body['delivery_address'] ?? '')),
            'pickup_date'      => trim((string) ($body['pickup_date']      ?? '')),
            'delivery_date'    => trim((string) ($body['delivery_date']    ?? '')),
            'weight'           => trim((string) ($body['weight']           ?? '')),
            'volume'           => trim((string) ($body['volume']           ?? '')),
            'tracking_number'  => trim((string) ($body['tracking_number']  ?? '')),
            'status'           => trim((string) ($body['status']           ?? 'planned')),
            'cost'             => trim((string) ($body['cost']             ?? '')),
            'currency'         => trim((string) ($body['currency']         ?? 'SEK')),
            'notes'            => trim((string) ($body['notes']            ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validateOrder(array $data): array
    {
        $errors = [];
        if ($data['transport_number'] === '') {
            $errors['transport_number'] = 'Transportnummer är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, mixed> */
    private function extractCarrierData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'                 => trim((string) ($body['name']                 ?? '')),
            'code'                 => trim((string) ($body['code']                 ?? '')),
            'type'                 => trim((string) ($body['type']                 ?? 'external')),
            'contact_person'       => trim((string) ($body['contact_person']       ?? '')),
            'phone'                => trim((string) ($body['phone']                ?? '')),
            'email'                => trim((string) ($body['email']                ?? '')),
            'contract_number'      => trim((string) ($body['contract_number']      ?? '')),
            'contract_valid_until' => trim((string) ($body['contract_valid_until'] ?? '')),
            'is_active'            => $body['is_active'] ?? null,
            'notes'                => trim((string) ($body['notes']                ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validateCarrier(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        return $errors;
    }

}
