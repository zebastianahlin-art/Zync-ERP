<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\SalesRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SalesController extends Controller
{
    private SalesRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new SalesRepository();
    }

    /** GET /sales */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/dashboard', [
            'title'  => 'Sales – ZYNC ERP',
            'stats'  => $this->repo->stats(),
        ]);
    }

    /** GET /sales/quotes */
    public function quotes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/quotes/index', [
            'title'  => 'Offerter – ZYNC ERP',
            'quotes' => $this->repo->allQuotes(),
        ]);
    }

    /** GET /sales/quotes/create */
    public function createQuote(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/quotes/create', [
            'title'     => 'Ny offert – ZYNC ERP',
            'customers' => $this->repo->allCustomers(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /sales/quotes */
    public function storeQuote(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractQuoteData($request);
        $errors = $this->validateQuote($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/quotes/create', [
                'title'     => 'Ny offert – ZYNC ERP',
                'customers' => $this->repo->allCustomers(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->createQuote($data);
        Flash::set('success', 'Offerten skapades.');
        return $this->redirect($response, '/sales/quotes/' . $id);
    }

    /** GET /sales/quotes/{id} */
    public function showQuote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $quote = $this->repo->findQuote((int) $args['id']);
        if ($quote === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/quotes/show', [
            'title' => 'Offert ' . htmlspecialchars($quote['quote_number'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'quote' => $quote,
            'lines' => $this->repo->quoteLines((int) $args['id']),
        ]);
    }

    /** GET /sales/quotes/{id}/edit */
    public function editQuote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $quote = $this->repo->findQuote((int) $args['id']);
        if ($quote === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/quotes/edit', [
            'title'     => 'Redigera offert – ZYNC ERP',
            'quote'     => $quote,
            'customers' => $this->repo->allCustomers(),
            'errors'    => [],
        ]);
    }

    /** POST /sales/quotes/{id} */
    public function updateQuote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id    = (int) $args['id'];
        $quote = $this->repo->findQuote($id);
        if ($quote === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractQuoteData($request);
        $errors = $this->validateQuote($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/quotes/edit', [
                'title'     => 'Redigera offert – ZYNC ERP',
                'quote'     => $quote,
                'customers' => $this->repo->allCustomers(),
                'errors'    => $errors,
            ]);
        }

        $this->repo->updateQuote($id, $data);
        Flash::set('success', 'Offerten uppdaterades.');
        return $this->redirect($response, '/sales/quotes/' . $id);
    }

    /** POST /sales/quotes/{id}/delete */
    public function deleteQuote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findQuote($id) !== null) {
            $this->repo->deleteQuote($id);
            Flash::set('success', 'Offerten togs bort.');
        }
        return $this->redirect($response, '/sales/quotes');
    }

    /** GET /sales/orders */
    public function orders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/orders/index', [
            'title'  => 'Säljordrar – ZYNC ERP',
            'orders' => $this->repo->allOrders(),
        ]);
    }

    /** GET /sales/pricing */
    public function pricing(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/pricing/index', [
            'title'  => 'Prislistor – ZYNC ERP',
            'lists'  => $this->repo->allPriceLists(),
        ]);
    }

    // ─── Quote filters ─────────────────────────────────────────────────────────

    /** GET /sales/quotes/accepted */
    public function acceptedQuotes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/quotes/accepted', [
            'title'  => 'Accepterade Offerter – ZYNC ERP',
            'quotes' => $this->repo->acceptedQuotes(),
        ]);
    }

    /** GET /sales/quotes/history */
    public function historyQuotes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/quotes/history', [
            'title'  => 'Historiska Offerter – ZYNC ERP',
            'quotes' => $this->repo->historyQuotes(),
        ]);
    }

    // ─── Quote Templates ───────────────────────────────────────────────────────

    /** GET /sales/quotes/templates */
    public function quoteTemplates(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/templates/index', [
            'title'     => 'Offertmallar – ZYNC ERP',
            'templates' => $this->repo->allTemplates(),
        ]);
    }

    /** GET /sales/quotes/templates/create */
    public function createQuoteTemplate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/templates/create', [
            'title'  => 'Ny offertmall – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /sales/quotes/templates */
    public function storeQuoteTemplate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractTemplateData($request);
        $errors = $this->validateTemplate($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/templates/create', [
                'title'  => 'Ny offertmall – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createTemplate($data);
        Flash::set('success', 'Offertmallen skapades.');
        return $this->redirect($response, '/sales/quotes/templates');
    }

    /** GET /sales/quotes/templates/{id}/edit */
    public function editQuoteTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $template = $this->repo->findTemplate((int) $args['id']);
        if ($template === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/templates/edit', [
            'title'    => 'Redigera offertmall – ZYNC ERP',
            'template' => $template,
            'errors'   => [],
        ]);
    }

    /** POST /sales/quotes/templates/{id} */
    public function updateQuoteTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id       = (int) $args['id'];
        $template = $this->repo->findTemplate($id);
        if ($template === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractTemplateData($request);
        $errors = $this->validateTemplate($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/templates/edit', [
                'title'    => 'Redigera offertmall – ZYNC ERP',
                'template' => $template,
                'errors'   => $errors,
            ]);
        }

        $this->repo->updateTemplate($id, $data);
        Flash::set('success', 'Offertmallen uppdaterades.');
        return $this->redirect($response, '/sales/quotes/templates');
    }

    /** POST /sales/quotes/templates/{id}/delete */
    public function deleteQuoteTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findTemplate($id) !== null) {
            $this->repo->deleteTemplate($id);
            Flash::set('success', 'Offertmallen togs bort.');
        }
        return $this->redirect($response, '/sales/quotes/templates');
    }

    /** GET /sales/quotes/templates/{id}/use */
    public function useTemplate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->redirect($response, '/sales/quotes/create');
    }

    // ─── Pricing CRUD ──────────────────────────────────────────────────────────

    /** GET /sales/pricing/manage */
    public function managePricing(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/pricing/manage', [
            'title' => 'Hantera Prislistor – ZYNC ERP',
            'lists' => $this->repo->allPriceLists(),
        ]);
    }

    /** GET /sales/pricing/create */
    public function createPriceList(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/pricing/create', [
            'title'  => 'Ny prislista – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /sales/pricing */
    public function storePriceList(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractPriceListData($request);
        $errors = $this->validatePriceList($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/pricing/create', [
                'title'  => 'Ny prislista – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createPriceList($data);
        Flash::set('success', 'Prislistan skapades.');
        return $this->redirect($response, '/sales/pricing/manage');
    }

    /** GET /sales/pricing/{id} */
    public function showPriceList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $list = $this->repo->findPriceList((int) $args['id']);
        if ($list === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/pricing/show', [
            'title' => 'Prislista – ZYNC ERP',
            'list'  => $list,
            'items' => $this->repo->priceListItems((int) $args['id']),
        ]);
    }

    /** GET /sales/pricing/{id}/edit */
    public function editPriceList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $list = $this->repo->findPriceList((int) $args['id']);
        if ($list === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/pricing/edit', [
            'title'  => 'Redigera prislista – ZYNC ERP',
            'list'   => $list,
            'errors' => [],
        ]);
    }

    /** POST /sales/pricing/{id} */
    public function updatePriceList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $list = $this->repo->findPriceList($id);
        if ($list === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractPriceListData($request);
        $errors = $this->validatePriceList($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/pricing/edit', [
                'title'  => 'Redigera prislista – ZYNC ERP',
                'list'   => $list,
                'errors' => $errors,
            ]);
        }

        $this->repo->updatePriceList($id, $data);
        Flash::set('success', 'Prislistan uppdaterades.');
        return $this->redirect($response, '/sales/pricing/' . $id);
    }

    /** POST /sales/pricing/{id}/delete */
    public function deletePriceList(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findPriceList($id) !== null) {
            $this->repo->deletePriceList($id);
            Flash::set('success', 'Prislistan togs bort.');
        }
        return $this->redirect($response, '/sales/pricing/manage');
    }

    /** POST /sales/pricing/{id}/items */
    public function addPriceListItem(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $list = $this->repo->findPriceList($id);
        if ($list === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $item = [
            'product_name' => trim((string) ($body['product_name'] ?? '')),
            'description'  => trim((string) ($body['description'] ?? '')),
            'unit_price'   => (float) ($body['unit_price'] ?? 0),
            'currency'     => in_array($body['currency'] ?? '', ['SEK','EUR','USD'], true) ? $body['currency'] : 'SEK',
            'unit'         => trim((string) ($body['unit'] ?? '')),
        ];

        if ($item['product_name'] !== '') {
            $this->repo->addPriceListItem($id, $item);
            Flash::set('success', 'Artikel lades till.');
        }

        return $this->redirect($response, '/sales/pricing/' . $id);
    }

    /** POST /sales/pricing/{id}/items/{itemId}/delete */
    public function removePriceListItem(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $itemId = (int) $args['itemId'];
        $this->repo->removePriceListItem($itemId);
        Flash::set('success', 'Artikeln togs bort.');
        return $this->redirect($response, '/sales/pricing/' . $id);
    }

    // ─── Orders CRUD ───────────────────────────────────────────────────────────

    /** GET /sales/orders/create */
    public function createOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'sales/orders/create', [
            'title'     => 'Ny order – ZYNC ERP',
            'customers' => $this->repo->allCustomers(),
            'quotes'    => $this->repo->allAcceptedQuotes(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /sales/orders */
    public function storeOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractOrderData($request);
        $errors = $this->validateOrder($data);

        if (!empty($errors)) {
            return $this->render($response, 'sales/orders/create', [
                'title'     => 'Ny order – ZYNC ERP',
                'customers' => $this->repo->allCustomers(),
                'quotes'    => $this->repo->allAcceptedQuotes(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->createOrder($data);
        Flash::set('success', 'Ordern skapades.');
        return $this->redirect($response, '/sales/orders/' . $id);
    }

    /** GET /sales/orders/{id} */
    public function showOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if ($order === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/orders/show', [
            'title' => 'Order ' . htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'order' => $order,
        ]);
    }

    /** GET /sales/orders/{id}/edit */
    public function editOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if ($order === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'sales/orders/edit', [
            'title'     => 'Redigera order – ZYNC ERP',
            'order'     => $order,
            'customers' => $this->repo->allCustomers(),
            'quotes'    => $this->repo->allAcceptedQuotes(),
            'errors'    => [],
        ]);
    }

    /** POST /sales/orders/{id} */
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
            return $this->render($response, 'sales/orders/edit', [
                'title'     => 'Redigera order – ZYNC ERP',
                'order'     => $order,
                'customers' => $this->repo->allCustomers(),
                'quotes'    => $this->repo->allAcceptedQuotes(),
                'errors'    => $errors,
            ]);
        }

        $this->repo->updateOrder($id, $data);
        Flash::set('success', 'Ordern uppdaterades.');
        return $this->redirect($response, '/sales/orders/' . $id);
    }

    /** POST /sales/orders/{id}/status */
    public function updateOrderStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id    = (int) $args['id'];
        $order = $this->repo->findOrder($id);
        if ($order === null) {
            return $this->notFound($response);
        }

        $body    = (array) $request->getParsedBody();
        $allowed = ['draft','confirmed','in_progress','shipped','completed','cancelled'];
        $status  = in_array($body['status'] ?? '', $allowed, true) ? $body['status'] : $order['status'];
        $this->repo->updateOrderStatus($id, $status);
        Flash::set('success', 'Orderstatus uppdaterades.');
        return $this->redirect($response, '/sales/orders/' . $id);
    }

    /** POST /sales/orders/{id}/delete */
    public function deleteOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findOrder($id) !== null) {
            $this->repo->deleteOrder($id);
            Flash::set('success', 'Ordern togs bort.');
        }
        return $this->redirect($response, '/sales/orders');
    }

    // ─── Private helpers ───────────────────────────────────────────────────────

    /** @return array<string, string> */
    private function extractQuoteData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'quote_number' => trim((string) ($body['quote_number'] ?? '')),
            'customer_id'  => trim((string) ($body['customer_id'] ?? '')),
            'valid_until'  => trim((string) ($body['valid_until'] ?? '')),
            'status'       => in_array($body['status'] ?? '', ['draft','sent','accepted','rejected','expired'], true) ? $body['status'] : 'draft',
            'notes'        => trim((string) ($body['notes'] ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validateQuote(array $data): array
    {
        $errors = [];
        if ($data['quote_number'] === '') {
            $errors['quote_number'] = 'Offertnummer är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, mixed> */
    private function extractTemplateData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'               => trim((string) ($body['name'] ?? '')),
            'description'        => trim((string) ($body['description'] ?? '')),
            'default_valid_days' => max(1, (int) ($body['default_valid_days'] ?? 30)),
            'is_active'          => isset($body['is_active']) ? $body['is_active'] : null,
        ];
    }

    /** @return array<string, string> */
    private function validateTemplate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, mixed> */
    private function extractPriceListData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'        => trim((string) ($body['name'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'currency'    => in_array($body['currency'] ?? '', ['SEK','EUR','USD'], true) ? $body['currency'] : 'SEK',
            'valid_from'  => trim((string) ($body['valid_from'] ?? '')),
            'valid_until' => trim((string) ($body['valid_until'] ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validatePriceList(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        return $errors;
    }

    /** @return array<string, mixed> */
    private function extractOrderData(ServerRequestInterface $request): array
    {
        $body    = (array) $request->getParsedBody();
        $allowed = ['draft','confirmed','in_progress','shipped','completed','cancelled'];
        return [
            'order_number' => trim((string) ($body['order_number'] ?? '')),
            'customer_id'  => trim((string) ($body['customer_id'] ?? '')),
            'quote_id'     => trim((string) ($body['quote_id'] ?? '')),
            'status'       => in_array($body['status'] ?? '', $allowed, true) ? $body['status'] : 'confirmed',
            'notes'        => trim((string) ($body['notes'] ?? '')),
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

}
