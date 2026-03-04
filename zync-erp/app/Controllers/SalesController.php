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

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'sales/index', [
            'title'  => 'Sales – ZYNC ERP',
            'quotes' => $this->repo->allQuotes(),
            'orders' => $this->repo->allOrders(),
        ]);
    }

    // ─── Quotes ───────────────────────────────────────────────

    public function quotesIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'sales/quotes/index', [
            'title' => 'Offerter – ZYNC ERP',
            'items' => $this->repo->allQuotes(),
        ]);
    }

    public function quotesCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'sales/quotes/create', [
            'title'     => 'Ny offert – ZYNC ERP',
            'customers' => $this->repo->allCustomers(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function quotesStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'quote_number'   => trim((string) ($body['quote_number'] ?? '')),
            'customer_id'    => $body['customer_id'] ?? '',
            'contact_person' => trim((string) ($body['contact_person'] ?? '')),
            'valid_until'    => $body['valid_until'] ?? '',
            'status'         => $body['status'] ?? 'draft',
            'total_amount'   => $body['total_amount'] ?? 0,
            'currency'       => $body['currency'] ?? 'SEK',
            'notes'          => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['quote_number'] === '') { $errors['quote_number'] = 'Offertnummer är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'sales/quotes/create', [
                'title' => 'Ny offert – ZYNC ERP', 'customers' => $this->repo->allCustomers(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->createQuote($data);
        Flash::set('success', 'Offerten har skapats.');
        return $this->redirect($response, '/sales/quotes/' . $id);
    }

    public function quotesShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findQuote((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'sales/quotes/show', [
            'title' => $item['quote_number'] . ' – Offert – ZYNC ERP',
            'item'  => $item,
        ]);
    }

    public function quotesEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findQuote((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'sales/quotes/edit', [
            'title'     => 'Redigera offert – ZYNC ERP',
            'item'      => $item,
            'customers' => $this->repo->allCustomers(),
            'errors'    => [],
        ]);
    }

    public function quotesUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id   = (int) $args['id'];
        $item = $this->repo->findQuote($id);
        if ($item === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'quote_number'   => trim((string) ($body['quote_number'] ?? '')),
            'customer_id'    => $body['customer_id'] ?? '',
            'contact_person' => trim((string) ($body['contact_person'] ?? '')),
            'valid_until'    => $body['valid_until'] ?? '',
            'status'         => $body['status'] ?? 'draft',
            'total_amount'   => $body['total_amount'] ?? 0,
            'currency'       => $body['currency'] ?? 'SEK',
            'notes'          => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['quote_number'] === '') { $errors['quote_number'] = 'Offertnummer är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'sales/quotes/edit', [
                'title' => 'Redigera offert – ZYNC ERP', 'item' => array_merge($item, $data),
                'customers' => $this->repo->allCustomers(), 'errors' => $errors,
            ]);
        }
        $this->repo->updateQuote($id, $data);
        Flash::set('success', 'Offerten har uppdaterats.');
        return $this->redirect($response, '/sales/quotes/' . $id);
    }

    public function quotesDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deleteQuote((int) $args['id']);
        Flash::set('success', 'Offerten har tagits bort.');
        return $this->redirect($response, '/sales/quotes');
    }

    // ─── Orders ───────────────────────────────────────────────

    public function ordersIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'sales/orders/index', [
            'title' => 'Orderingångar – ZYNC ERP',
            'items' => $this->repo->allOrders(),
        ]);
    }

    public function ordersCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'sales/orders/create', [
            'title'     => 'Ny order – ZYNC ERP',
            'customers' => $this->repo->allCustomers(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function ordersStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'order_number'  => trim((string) ($body['order_number'] ?? '')),
            'quote_id'      => $body['quote_id'] ?? '',
            'customer_id'   => $body['customer_id'] ?? '',
            'order_date'    => $body['order_date'] ?? '',
            'delivery_date' => $body['delivery_date'] ?? '',
            'status'        => $body['status'] ?? 'confirmed',
            'total_amount'  => $body['total_amount'] ?? 0,
            'notes'         => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['order_number'] === '') { $errors['order_number'] = 'Ordernummer är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'sales/orders/create', [
                'title' => 'Ny order – ZYNC ERP', 'customers' => $this->repo->allCustomers(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $id = $this->repo->createOrder($data);
        Flash::set('success', 'Ordern har skapats.');
        return $this->redirect($response, '/sales/orders/' . $id);
    }

    public function ordersShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findOrder((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'sales/orders/show', [
            'title' => $item['order_number'] . ' – Order – ZYNC ERP',
            'item'  => $item,
        ]);
    }

    public function ordersEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findOrder((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'sales/orders/edit', [
            'title'     => 'Redigera order – ZYNC ERP',
            'item'      => $item,
            'customers' => $this->repo->allCustomers(),
            'errors'    => [],
        ]);
    }

    public function ordersUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id   = (int) $args['id'];
        $item = $this->repo->findOrder($id);
        if ($item === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'order_number'  => trim((string) ($body['order_number'] ?? '')),
            'quote_id'      => $body['quote_id'] ?? '',
            'customer_id'   => $body['customer_id'] ?? '',
            'order_date'    => $body['order_date'] ?? '',
            'delivery_date' => $body['delivery_date'] ?? '',
            'status'        => $body['status'] ?? 'confirmed',
            'total_amount'  => $body['total_amount'] ?? 0,
            'notes'         => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['order_number'] === '') { $errors['order_number'] = 'Ordernummer är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'sales/orders/edit', [
                'title' => 'Redigera order – ZYNC ERP', 'item' => array_merge($item, $data),
                'customers' => $this->repo->allCustomers(), 'errors' => $errors,
            ]);
        }
        $this->repo->updateOrder($id, $data);
        Flash::set('success', 'Ordern har uppdaterats.');
        return $this->redirect($response, '/sales/orders/' . $id);
    }

    public function ordersDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deleteOrder((int) $args['id']);
        Flash::set('success', 'Ordern har tagits bort.');
        return $this->redirect($response, '/sales/orders');
    }

    // ─── Pricing ──────────────────────────────────────────────

    public function pricingIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'sales/pricing/index', [
            'title'  => 'Prissättning – ZYNC ERP',
            'lists'  => $this->repo->allPriceLists(),
        ]);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
