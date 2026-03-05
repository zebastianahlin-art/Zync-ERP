<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\ProductionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductionController extends Controller
{
    private ProductionRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ProductionRepository();
    }

    /** GET /production */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/dashboard', [
            'title'  => 'Produktion – ZYNC ERP',
            'stats'  => $this->repo->stats(),
            'orders' => $this->repo->recentOrders(5),
        ]);
    }

    /** GET /production/lines */
    public function lines(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/lines/index', [
            'title' => 'Produktionslinjer – ZYNC ERP',
            'lines' => $this->repo->allLines(),
        ]);
    }

    /** GET /production/lines/create */
    public function createLine(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/lines/create', [
            'title'  => 'Ny produktionslinje – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /production/lines */
    public function storeLine(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractLineData($request);
        $errors = $this->validateLine($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/lines/create', [
                'title'  => 'Ny produktionslinje – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createLine($data);
        Flash::set('success', 'Produktionslinjen skapades.');
        return $this->redirect($response, '/production/lines');
    }

    /** GET /production/lines/{id} */
    public function showLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $line = $this->repo->findLine((int) $args['id']);
        if ($line === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/lines/show', [
            'title' => htmlspecialchars($line['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'line'  => $line,
        ]);
    }

    /** GET /production/lines/{id}/edit */
    public function editLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $line = $this->repo->findLine((int) $args['id']);
        if ($line === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/lines/edit', [
            'title'  => 'Redigera linje – ZYNC ERP',
            'line'   => $line,
            'errors' => [],
        ]);
    }

    /** POST /production/lines/{id} */
    public function updateLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $line = $this->repo->findLine($id);
        if ($line === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractLineData($request);
        $errors = $this->validateLine($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/lines/edit', [
                'title'  => 'Redigera linje – ZYNC ERP',
                'line'   => $line,
                'errors' => $errors,
            ]);
        }

        $this->repo->updateLine($id, $data);
        Flash::set('success', 'Linjen uppdaterades.');
        return $this->redirect($response, '/production/lines');
    }

    /** POST /production/lines/{id}/delete */
    public function deleteLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findLine($id) !== null) {
            $this->repo->deleteLine($id);
            Flash::set('success', 'Linjen togs bort.');
        }
        return $this->redirect($response, '/production/lines');
    }

    /** GET /production/orders */
    public function orders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/orders/index', [
            'title'  => 'Produktionsordrar – ZYNC ERP',
            'orders' => $this->repo->allOrders(),
        ]);
    }

    /** GET /production/stock */
    public function stock(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/stock/index', [
            'title' => 'Produktionslager – ZYNC ERP',
            'stock' => $this->repo->allStock(),
        ]);
    }

    // ─── Products ─────────────────────────────────────────────────────────

    /** GET /production/products */
    public function products(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/products/index', [
            'title'    => 'Produkter – ZYNC ERP',
            'products' => $this->repo->allProducts(),
        ]);
    }

    /** GET /production/products/create */
    public function createProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/products/create', [
            'title'  => 'Ny produkt – ZYNC ERP',
            'lines'  => $this->repo->allLines(),
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /production/products */
    public function storeProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractProductData($request);
        $errors = $this->validateProduct($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/products/create', [
                'title'  => 'Ny produkt – ZYNC ERP',
                'lines'  => $this->repo->allLines(),
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createProduct($data);
        Flash::set('success', 'Produkten skapades.');
        return $this->redirect($response, '/production/products');
    }

    /** GET /production/products/{id} */
    public function showProduct(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $product = $this->repo->findProduct((int) $args['id']);
        if ($product === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/products/show', [
            'title'   => htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'product' => $product,
        ]);
    }

    /** GET /production/products/{id}/edit */
    public function editProduct(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $product = $this->repo->findProduct((int) $args['id']);
        if ($product === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/products/edit', [
            'title'   => 'Redigera produkt – ZYNC ERP',
            'product' => $product,
            'lines'   => $this->repo->allLines(),
            'errors'  => [],
        ]);
    }

    /** POST /production/products/{id} */
    public function updateProduct(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $product = $this->repo->findProduct($id);
        if ($product === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractProductData($request);
        $errors = $this->validateProduct($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/products/edit', [
                'title'   => 'Redigera produkt – ZYNC ERP',
                'product' => array_merge($product, $data),
                'lines'   => $this->repo->allLines(),
                'errors'  => $errors,
            ]);
        }

        $this->repo->updateProduct($id, $data);
        Flash::set('success', 'Produkten uppdaterades.');
        return $this->redirect($response, '/production/products/' . $id);
    }

    /** POST /production/products/{id}/delete */
    public function deleteProduct(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findProduct($id) !== null) {
            $this->repo->deleteProduct($id);
            Flash::set('success', 'Produkten togs bort.');
        }
        return $this->redirect($response, '/production/products');
    }

    // ─── Orders CRUD ──────────────────────────────────────────────────────

    /** GET /production/orders/create */
    public function createOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/orders/create', [
            'title'  => 'Ny produktionsorder – ZYNC ERP',
            'lines'  => $this->repo->allLines(),
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /production/orders */
    public function storeOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractOrderData($request);
        $errors = $this->validateOrder($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/orders/create', [
                'title'  => 'Ny produktionsorder – ZYNC ERP',
                'lines'  => $this->repo->allLines(),
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createOrder($data);
        Flash::set('success', 'Ordern skapades.');
        return $this->redirect($response, '/production/orders');
    }

    /** GET /production/orders/{id} */
    public function showOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if ($order === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/orders/show', [
            'title' => htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'order' => $order,
        ]);
    }

    /** GET /production/orders/{id}/edit */
    public function editOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if ($order === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/orders/edit', [
            'title'  => 'Redigera order – ZYNC ERP',
            'order'  => $order,
            'lines'  => $this->repo->allLines(),
            'errors' => [],
        ]);
    }

    /** POST /production/orders/{id} */
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
            return $this->render($response, 'production/orders/edit', [
                'title'  => 'Redigera order – ZYNC ERP',
                'order'  => array_merge($order, $data),
                'lines'  => $this->repo->allLines(),
                'errors' => $errors,
            ]);
        }

        $this->repo->updateOrder($id, $data);
        Flash::set('success', 'Ordern uppdaterades.');
        return $this->redirect($response, '/production/orders/' . $id);
    }

    /** POST /production/orders/{id}/status */
    public function updateOrderStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id    = (int) $args['id'];
        $order = $this->repo->findOrder($id);
        if ($order === null) {
            return $this->notFound($response);
        }

        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? '';
        $allowed = ['draft', 'planned', 'in_progress', 'completed', 'cancelled'];

        if (in_array($status, $allowed, true)) {
            $this->repo->updateOrderStatus($id, $status);
            Flash::set('success', 'Orderstatus uppdaterades.');
        } else {
            Flash::set('error', 'Ogiltig status.');
        }

        return $this->redirect($response, '/production/orders/' . $id);
    }

    /** POST /production/orders/{id}/delete */
    public function deleteOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findOrder($id) !== null) {
            $this->repo->deleteOrder($id);
            Flash::set('success', 'Ordern togs bort.');
        }
        return $this->redirect($response, '/production/orders');
    }

    // ─── Stock management ─────────────────────────────────────────────────

    /** GET /production/stock/manage */
    public function manageStock(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/stock/manage', [
            'title' => 'Hantera lager – ZYNC ERP',
            'stock' => $this->repo->allStockLocations(),
        ]);
    }

    /** GET /production/stock/create */
    public function createStockEntry(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/stock/create', [
            'title'  => 'Ny lagerpost – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /production/stock */
    public function storeStockEntry(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body   = (array) $request->getParsedBody();
        $data   = [
            'article_id' => trim((string) ($body['article_id'] ?? '')),
            'location'   => trim((string) ($body['location'] ?? '')),
            'quantity'   => trim((string) ($body['quantity'] ?? '')),
            'unit'       => trim((string) ($body['unit'] ?? 'st')),
        ];
        $errors = [];

        if ($data['location'] === '') {
            $errors['location'] = 'Plats är obligatorisk.';
        }
        if ($data['quantity'] === '') {
            $errors['quantity'] = 'Antal är obligatoriskt.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'production/stock/create', [
                'title'  => 'Ny lagerpost – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createStockEntry($data);
        Flash::set('success', 'Lagerposten skapades.');
        return $this->redirect($response, '/production/stock/manage');
    }

    /** GET /production/stock/{id}/move */
    public function moveStock(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $entry = $this->repo->findStockEntry((int) $args['id']);
        if ($entry === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/stock/move', [
            'title' => 'Flytta lagerpost – ZYNC ERP',
            'entry' => $entry,
        ]);
    }

    /** POST /production/stock/{id}/move */
    public function storeMoveStock(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id    = (int) $args['id'];
        $entry = $this->repo->findStockEntry($id);
        if ($entry === null) {
            return $this->notFound($response);
        }

        $body        = (array) $request->getParsedBody();
        $newLocation = trim((string) ($body['new_location'] ?? ''));

        if ($newLocation === '') {
            Flash::set('error', 'Ny plats är obligatorisk.');
            return $this->redirect($response, '/production/stock/' . $id . '/move');
        }

        $this->repo->moveStock($id, $newLocation);
        Flash::set('success', 'Lagerposten flyttades.');
        return $this->redirect($response, '/production/stock/manage');
    }

    /** POST /production/stock/{id}/delete */
    public function deleteStockEntry(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findStockEntry($id) !== null) {
            $this->repo->deleteStockEntry($id);
            Flash::set('success', 'Lagerposten togs bort.');
        }
        return $this->redirect($response, '/production/stock/manage');
    }

    /** @return array<string, string> */
    private function extractProductData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'product_number'     => trim((string) ($body['product_number'] ?? '')),
            'name'               => trim((string) ($body['name'] ?? '')),
            'description'        => trim((string) ($body['description'] ?? '')),
            'category'           => trim((string) ($body['category'] ?? '')),
            'datasheet_url'      => trim((string) ($body['datasheet_url'] ?? '')),
            'composition'        => trim((string) ($body['composition'] ?? '')),
            'weight'             => trim((string) ($body['weight'] ?? '')),
            'weight_unit'        => in_array($body['weight_unit'] ?? '', ['kg', 'g', 'ton'], true) ? $body['weight_unit'] : 'kg',
            'dimensions'         => trim((string) ($body['dimensions'] ?? '')),
            'sku'                => trim((string) ($body['sku'] ?? '')),
            'barcode'            => trim((string) ($body['barcode'] ?? '')),
            'unit_price'         => trim((string) ($body['unit_price'] ?? '')),
            'currency'           => in_array($body['currency'] ?? '', ['SEK', 'EUR', 'USD'], true) ? $body['currency'] : 'SEK',
            'production_line_id' => trim((string) ($body['production_line_id'] ?? '')),
            'min_stock_level'    => trim((string) ($body['min_stock_level'] ?? '')),
            'lead_time_days'     => trim((string) ($body['lead_time_days'] ?? '')),
            'status'             => in_array($body['status'] ?? '', ['active', 'inactive', 'discontinued'], true) ? $body['status'] : 'active',
        ];
    }

    /** @return array<string, string> */
    private function validateProduct(array $data): array
    {
        $errors = [];
        if ($data['product_number'] === '') {
            $errors['product_number'] = 'Produktnummer är obligatoriskt.';
        }
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, string> */
    private function extractOrderData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'order_number'  => trim((string) ($body['order_number'] ?? '')),
            'line_id'       => trim((string) ($body['line_id'] ?? '')),
            'article_id'    => trim((string) ($body['article_id'] ?? '')),
            'quantity'      => trim((string) ($body['quantity'] ?? '0')),
            'planned_start' => trim((string) ($body['planned_start'] ?? '')),
            'planned_end'   => trim((string) ($body['planned_end'] ?? '')),
            'status'        => in_array($body['status'] ?? '', ['draft', 'planned', 'in_progress', 'completed', 'cancelled'], true) ? $body['status'] : 'draft',
            'notes'         => trim((string) ($body['notes'] ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validateOrder(array $data): array
    {
        $errors = [];
        if ($data['order_number'] === '') {
            $errors['order_number'] = 'Ordernummer är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, string> */
    private function extractLineData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'        => trim((string) ($body['name'] ?? '')),
            'code'        => trim((string) ($body['code'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'status'      => in_array($body['status'] ?? '', ['active', 'inactive'], true) ? $body['status'] : 'active',
        ];
    }

    /** @return array<string, string> */
    private function validateLine(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        if ($data['code'] === '') {
            $errors['code'] = 'Kod är obligatorisk.';
        }
        return $errors;
    }

}
