<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
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
        $q = $request->getQueryParams();
        return $this->render($response, 'inventory/index', [
            'title'      => 'Lager – ZYNC ERP',
            'stock'      => $this->repo->stockSummary($q['search'] ?? null),
            'stats'      => $this->repo->stats(),
            'warehouses' => $this->repo->allWarehouses(),
            'filters'    => $q,
        ]);
    }

    /** GET /inventory/detail */
    public function detail(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = $request->getQueryParams();
        $whId = isset($q['warehouse']) ? (int) $q['warehouse'] : null;
        return $this->render($response, 'inventory/detail', [
            'title'      => 'Lagersaldo per plats – ZYNC ERP',
            'stock'      => $this->repo->stockOverview($whId, $q['search'] ?? null),
            'warehouses' => $this->repo->allWarehouses(),
            'filters'    => $q,
        ]);
    }

    /** GET /inventory/move */
    public function moveForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/move', [
            'title'      => 'Lagerrörelse – ZYNC ERP',
            'articles'   => $this->repo->allArticles(),
            'warehouses' => $this->repo->allWarehouses(),
            'errors'     => [],
            'data'       => [],
        ]);
    }

    /** POST /inventory/move */
    public function moveStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $b = (array) $request->getParsedBody();
        $data = [
            'article_id'   => (int) ($b['article_id'] ?? 0),
            'warehouse_id' => (int) ($b['warehouse_id'] ?? 0),
            'type'         => $b['type'] ?? '',
            'quantity'     => (float) ($b['quantity'] ?? 0),
            'note'         => trim((string) ($b['note'] ?? '')),
        ];

        $errors = [];
        if ($data['article_id'] < 1) $errors['article_id'] = 'Välj en artikel.';
        if ($data['warehouse_id'] < 1) $errors['warehouse_id'] = 'Välj ett lager.';
        if (!in_array($data['type'], ['in', 'out', 'adjust'])) $errors['type'] = 'Välj typ.';
        if ($data['quantity'] <= 0) $errors['quantity'] = 'Ange ett positivt antal.';

        if (!empty($errors)) {
            return $this->render($response, 'inventory/move', [
                'title'      => 'Lagerrörelse – ZYNC ERP',
                'articles'   => $this->repo->allArticles(),
                'warehouses' => $this->repo->allWarehouses(),
                'errors'     => $errors,
                'data'       => $data,
            ]);
        }

        $this->repo->adjustStock(
            $data['article_id'],
            $data['warehouse_id'],
            $data['quantity'],
            $data['type'],
            $data['note'],
            Auth::id()
        );

        $typeLabels = ['in' => 'Inleverans', 'out' => 'Uttag', 'adjust' => 'Justering'];
        Flash::set('success', ($typeLabels[$data['type']] ?? 'Rörelse') . ' registrerad.');
        return $this->redirect($response, '/inventory');
    }

    /** GET /inventory/transactions */
    public function transactions(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = $request->getQueryParams();
        return $this->render($response, 'inventory/transactions', [
            'title'        => 'Transaktionshistorik – ZYNC ERP',
            'transactions' => $this->repo->transactions(
                isset($q['article']) ? (int) $q['article'] : null,
                isset($q['warehouse']) ? (int) $q['warehouse'] : null,
            ),
            'articles'   => $this->repo->allArticles(),
            'warehouses' => $this->repo->allWarehouses(),
            'filters'    => $q,
        ]);
    }

    /* ── Warehouse CRUD ── */

    /** GET /inventory/warehouses */
    public function warehouses(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/warehouses', [
            'title'      => 'Lagerplatser – ZYNC ERP',
            'warehouses' => $this->repo->allWarehouses(),
        ]);
    }

    /** GET /inventory/warehouses/create */
    public function warehouseCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/warehouse_form', [
            'title'     => 'Ny lagerplats – ZYNC ERP',
            'warehouse' => ['is_active' => 1],
            'errors'    => [],
            'isEdit'    => false,
        ]);
    }

    /** POST /inventory/warehouses */
    public function warehouseStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseWarehouse($request);
        $errors = $this->validateWarehouse($data);

        if (!empty($errors)) {
            return $this->render($response, 'inventory/warehouse_form', [
                'title' => 'Ny lagerplats – ZYNC ERP', 'warehouse' => $data, 'errors' => $errors, 'isEdit' => false,
            ]);
        }

        $this->repo->createWarehouse($data);
        Flash::set('success', 'Lagerplats skapad.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    /** GET /inventory/warehouses/{id}/edit */
    public function warehouseEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $wh = $this->repo->findWarehouse((int) $args['id']);
        if (!$wh) { Flash::set('error', 'Hittades inte.'); return $this->redirect($response, '/inventory/warehouses'); }

        return $this->render($response, 'inventory/warehouse_form', [
            'title' => 'Redigera lagerplats – ZYNC ERP', 'warehouse' => $wh, 'errors' => [], 'isEdit' => true,
        ]);
    }

    /** POST /inventory/warehouses/{id} */
    public function warehouseUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data   = $this->parseWarehouse($request);
        $errors = $this->validateWarehouse($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'inventory/warehouse_form', [
                'title' => 'Redigera lagerplats – ZYNC ERP', 'warehouse' => array_merge(['id' => $id], $data), 'errors' => $errors, 'isEdit' => true,
            ]);
        }

        $this->repo->updateWarehouse($id, $data);
        Flash::set('success', 'Lagerplats uppdaterad.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    /** POST /inventory/warehouses/{id}/delete */
    public function warehouseDestroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->deleteWarehouse((int) $args['id']);
        Flash::set('success', 'Lagerplats borttagen.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    private function parseWarehouse(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'name'      => trim((string) ($b['name'] ?? '')),
            'code'      => strtoupper(trim((string) ($b['code'] ?? ''))),
            'address'   => trim((string) ($b['address'] ?? '')),
            'city'      => trim((string) ($b['city'] ?? '')),
            'is_active' => isset($b['is_active']) ? 1 : 0,
        ];
    }

    private function validateWarehouse(array $d, ?int $excludeId = null): array
    {
        $errors = [];
        if ($d['name'] === '') $errors['name'] = 'Namn är obligatoriskt.';
        if ($d['code'] === '') $errors['code'] = 'Kod är obligatoriskt.';
        elseif ($this->repo->warehouseCodeExists($d['code'], $excludeId)) $errors['code'] = 'Koden används redan.';
        return $errors;
    }
}
