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

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $lines  = $this->repo->allLines();
        $orders = $this->repo->allOrders();
        $stock  = $this->repo->allStock();
        return $this->render($response, 'production/index', [
            'title'  => 'Produktion – ZYNC ERP',
            'lines'  => $lines,
            'orders' => $orders,
            'stock'  => $stock,
        ]);
    }

    // ─── Production Lines ─────────────────────────────────────

    public function linesIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'production/lines/index', [
            'title' => 'Produktionslinjer – ZYNC ERP',
            'items' => $this->repo->allLines(),
        ]);
    }

    public function linesCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'production/lines/create', [
            'title'  => 'Ny produktionslinje – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function linesStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'name'        => trim((string) ($body['name'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'capacity'    => trim((string) ($body['capacity'] ?? '')),
            'status'      => $body['status'] ?? 'active',
            'sort_order'  => (int) ($body['sort_order'] ?? 0),
        ];
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'production/lines/create', [
                'title' => 'Ny produktionslinje – ZYNC ERP', 'errors' => $errors, 'old' => $data,
            ]);
        }
        $this->repo->createLine($data);
        Flash::set('success', 'Produktionslinjen har skapats.');
        return $this->redirect($response, '/production/lines');
    }

    public function linesShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findLine((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'production/lines/show', [
            'title' => $item['name'] . ' – Produktionslinje – ZYNC ERP',
            'item'  => $item,
        ]);
    }

    public function linesEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findLine((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'production/lines/edit', [
            'title'  => 'Redigera ' . $item['name'] . ' – ZYNC ERP',
            'item'   => $item,
            'errors' => [],
        ]);
    }

    public function linesUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $id   = (int) $args['id'];
        $item = $this->repo->findLine($id);
        if ($item === null) { return $this->notFound($response); }
        $body = (array) $request->getParsedBody();
        $data = [
            'name'        => trim((string) ($body['name'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'capacity'    => trim((string) ($body['capacity'] ?? '')),
            'status'      => $body['status'] ?? 'active',
            'sort_order'  => (int) ($body['sort_order'] ?? 0),
        ];
        $errors = [];
        if ($data['name'] === '') { $errors['name'] = 'Namn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'production/lines/edit', [
                'title' => 'Redigera – ZYNC ERP', 'item' => array_merge($item, $data), 'errors' => $errors,
            ]);
        }
        $this->repo->updateLine($id, $data);
        Flash::set('success', 'Produktionslinjen har uppdaterats.');
        return $this->redirect($response, '/production/lines');
    }

    public function linesDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deleteLine((int) $args['id']);
        Flash::set('success', 'Produktionslinjen har tagits bort.');
        return $this->redirect($response, '/production/lines');
    }

    // ─── Production Orders ────────────────────────────────────

    public function ordersIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'production/orders/index', [
            'title' => 'Produktionsordrar – ZYNC ERP',
            'items' => $this->repo->allOrders(),
        ]);
    }

    public function ordersCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'production/orders/create', [
            'title'  => 'Ny produktionsorder – ZYNC ERP',
            'lines'  => $this->repo->allLines(),
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function ordersStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $body = (array) $request->getParsedBody();
        $data = [
            'order_number'       => trim((string) ($body['order_number'] ?? '')),
            'product_name'       => trim((string) ($body['product_name'] ?? '')),
            'production_line_id' => $body['production_line_id'] ?? '',
            'quantity'           => $body['quantity'] ?? 1,
            'unit'               => $body['unit'] ?? 'st',
            'planned_start'      => $body['planned_start'] ?? '',
            'planned_end'        => $body['planned_end'] ?? '',
            'status'             => $body['status'] ?? 'planned',
            'priority'           => $body['priority'] ?? 'normal',
            'notes'              => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['order_number'] === '') { $errors['order_number'] = 'Ordernummer är obligatoriskt.'; }
        if ($data['product_name'] === '') { $errors['product_name'] = 'Produktnamn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'production/orders/create', [
                'title' => 'Ny produktionsorder – ZYNC ERP', 'lines' => $this->repo->allLines(),
                'errors' => $errors, 'old' => $data,
            ]);
        }
        $this->repo->createOrder($data);
        Flash::set('success', 'Produktionsordern har skapats.');
        return $this->redirect($response, '/production/orders');
    }

    public function ordersShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findOrder((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'production/orders/show', [
            'title' => $item['order_number'] . ' – Produktionsorder – ZYNC ERP',
            'item'  => $item,
        ]);
    }

    public function ordersEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $item = $this->repo->findOrder((int) $args['id']);
        if ($item === null) { return $this->notFound($response); }
        return $this->render($response, 'production/orders/edit', [
            'title'  => 'Redigera ' . $item['order_number'] . ' – ZYNC ERP',
            'item'   => $item,
            'lines'  => $this->repo->allLines(),
            'errors' => [],
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
            'order_number'       => trim((string) ($body['order_number'] ?? '')),
            'product_name'       => trim((string) ($body['product_name'] ?? '')),
            'production_line_id' => $body['production_line_id'] ?? '',
            'quantity'           => $body['quantity'] ?? 1,
            'unit'               => $body['unit'] ?? 'st',
            'planned_start'      => $body['planned_start'] ?? '',
            'planned_end'        => $body['planned_end'] ?? '',
            'actual_start'       => $body['actual_start'] ?? '',
            'actual_end'         => $body['actual_end'] ?? '',
            'status'             => $body['status'] ?? 'planned',
            'priority'           => $body['priority'] ?? 'normal',
            'notes'              => $body['notes'] ?? '',
        ];
        $errors = [];
        if ($data['order_number'] === '') { $errors['order_number'] = 'Ordernummer är obligatoriskt.'; }
        if ($data['product_name'] === '') { $errors['product_name'] = 'Produktnamn är obligatoriskt.'; }
        if (!empty($errors)) {
            return $this->render($response, 'production/orders/edit', [
                'title' => 'Redigera – ZYNC ERP', 'item' => array_merge($item, $data),
                'lines' => $this->repo->allLines(), 'errors' => $errors,
            ]);
        }
        $this->repo->updateOrder($id, $data);
        Flash::set('success', 'Produktionsordern har uppdaterats.');
        return $this->redirect($response, '/production/orders');
    }

    public function ordersDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        $this->repo->deleteOrder((int) $args['id']);
        Flash::set('success', 'Produktionsordern har tagits bort.');
        return $this->redirect($response, '/production/orders');
    }

    // ─── Stock ────────────────────────────────────────────────

    public function stockIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) { return $this->redirect($response, '/login'); }
        return $this->render($response, 'production/stock/index', [
            'title' => 'Produktionslager – ZYNC ERP',
            'items' => $this->repo->allStock(),
        ]);
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Sidan hittades inte</h1>');
        return $response->withStatus(404);
    }
}
