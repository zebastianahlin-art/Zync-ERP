<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Database;
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

    // ─── OVERVIEW ────────────────────────────────────────────

    /** GET /inventory */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $filters = [
            'search'       => $params['search'] ?? '',
            'warehouse_id' => $params['warehouse_id'] ?? '',
        ];

        return $this->render($response, 'inventory/index', [
            'title'      => 'Lager – ZYNC ERP',
            'stockItems' => $this->repo->getStockWithFilters($filters),
            'kpis'       => $this->repo->getStockKPIs(),
            'warehouses' => $this->repo->getWarehouses(),
            'filters'    => $filters,
            'success'    => Flash::get('success'),
            'error'      => Flash::get('error'),
        ]);
    }

    /** GET /inventory/{id} */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stock = $this->repo->findStock((int) $args['id']);
        if ($stock === null) {
            Flash::set('error', 'Lagerpost hittades inte.');
            return $this->redirect($response, '/inventory');
        }

        return $this->render($response, 'inventory/show', [
            'title'        => 'Lagerpost – ZYNC ERP',
            'stock'        => $stock,
            'transactions' => $this->repo->transactionsForStock((int) $args['id']),
            'success'      => Flash::get('success'),
            'error'        => Flash::get('error'),
        ]);
    }

    // ─── TRANSACTIONS ─────────────────────────────────────────

    /** GET /inventory/transactions */
    public function transactionIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $filters = [
            'type'      => $params['type'] ?? '',
            'date_from' => $params['date_from'] ?? '',
            'date_to'   => $params['date_to'] ?? '',
        ];

        return $this->render($response, 'inventory/transactions/index', [
            'title'        => 'Lagertransaktioner – ZYNC ERP',
            'transactions' => $this->repo->getTransactions($filters),
            'filters'      => $filters,
            'success'      => Flash::get('success'),
            'error'        => Flash::get('error'),
        ]);
    }

    /** GET /inventory/transactions/create */
    public function createTransaction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/transactions/create', [
            'title'      => 'Ny transaktion – ZYNC ERP',
            'articles'   => $this->getArticles(),
            'warehouses' => $this->repo->getWarehouses(),
        ]);
    }

    /** POST /inventory/transactions */
    public function storeTransaction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['article_id']) || empty($data['warehouse_id']) || empty($data['type']) || empty($data['quantity'])) {
            Flash::set('error', 'Artikel, lagerställe, typ och antal krävs.');
            return $this->redirect($response, '/inventory/transactions/create');
        }

        try {
            $id = $this->repo->createTransaction($data);
            Flash::set('success', 'Transaktion skapad.');
            return $this->redirect($response, "/inventory/transactions/{$id}");
        } catch (\Exception $e) {
            Flash::set('error', 'Fel vid skapande av transaktion: ' . $e->getMessage());
            return $this->redirect($response, '/inventory/transactions/create');
        }
    }

    /** GET /inventory/transactions/{id} */
    public function showTransaction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $transaction = $this->repo->getTransactionById((int) $args['id']);
        if (!$transaction) {
            Flash::set('error', 'Transaktion hittades inte.');
            return $this->redirect($response, '/inventory/transactions');
        }

        return $this->render($response, 'inventory/transactions/show', [
            'title'       => 'Transaktion – ZYNC ERP',
            'transaction' => $transaction,
        ]);
    }

    // ─── RECEIVING ────────────────────────────────────────────

    /** GET /inventory/receiving */
    public function receivingIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/receiving/index', [
            'title'   => 'Inleverans – ZYNC ERP',
            'orders'  => $this->repo->getReceivingOrders(),
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    /** GET /inventory/receiving/{poId} */
    public function receivingShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $this->repo->getReceivingOrder((int) $args['poId']);
        if (!$data) {
            Flash::set('error', 'Inköpsorder hittades inte.');
            return $this->redirect($response, '/inventory/receiving');
        }

        return $this->render($response, 'inventory/receiving/show', [
            'title'      => 'Inleverans – ZYNC ERP',
            'order'      => $data['order'],
            'lines'      => $data['lines'],
            'warehouses' => $this->repo->getWarehouses(),
        ]);
    }

    /** POST /inventory/receiving/{poId} */
    public function storeReceiving(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $poId = (int) $args['poId'];
        $data = (array) $request->getParsedBody();
        $lines = $data['lines'] ?? [];
        $warehouseId = (int) ($data['warehouse_id'] ?? 0);
        $userId = Auth::user()['id'];

        if ($warehouseId === 0) {
            Flash::set('error', 'Välj ett lagerställe.');
            return $this->redirect($response, "/inventory/receiving/{$poId}");
        }

        try {
            $this->repo->storeReceiving($poId, $lines, $warehouseId, $userId);
            Flash::set('success', 'Inleverans registrerad.');
        } catch (\Exception $e) {
            Flash::set('error', 'Fel vid inleverans: ' . $e->getMessage());
        }

        return $this->redirect($response, "/inventory/receiving/{$poId}");
    }

    // ─── ISSUES ───────────────────────────────────────────────

    /** GET /inventory/issues */
    public function issueIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $filters = [
            'date_from' => $params['date_from'] ?? '',
            'date_to'   => $params['date_to'] ?? '',
        ];

        return $this->render($response, 'inventory/issues/index', [
            'title'   => 'Uttag – ZYNC ERP',
            'issues'  => $this->repo->getIssues($filters),
            'filters' => $filters,
            'success' => Flash::get('success'),
            'error'   => Flash::get('error'),
        ]);
    }

    /** GET /inventory/issues/create */
    public function createIssue(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/issues/create', [
            'title'      => 'Nytt uttag – ZYNC ERP',
            'articles'   => $this->getArticles(),
            'warehouses' => $this->repo->getWarehouses(),
        ]);
    }

    /** POST /inventory/issues */
    public function storeIssue(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['article_id']) || empty($data['warehouse_id']) || empty($data['quantity'])) {
            Flash::set('error', 'Artikel, lagerställe och antal krävs.');
            return $this->redirect($response, '/inventory/issues/create');
        }

        try {
            $this->repo->createIssue($data);
            Flash::set('success', 'Uttag registrerat.');
            return $this->redirect($response, '/inventory/issues');
        } catch (\Exception $e) {
            Flash::set('error', 'Fel vid uttag: ' . $e->getMessage());
            return $this->redirect($response, '/inventory/issues/create');
        }
    }

    // ─── STOCKTAKING ──────────────────────────────────────────

    /** GET /inventory/stocktaking */
    public function stocktakingIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/stocktaking/index', [
            'title'        => 'Inventering – ZYNC ERP',
            'stocktakings' => $this->repo->getStocktakings(),
            'success'      => Flash::get('success'),
            'error'        => Flash::get('error'),
        ]);
    }

    /** GET /inventory/stocktaking/create */
    public function createStocktaking(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/stocktaking/create', [
            'title'      => 'Ny inventering – ZYNC ERP',
            'warehouses' => $this->repo->getWarehouses(),
        ]);
    }

    /** POST /inventory/stocktaking */
    public function storeStocktaking(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['name']) || empty($data['warehouse_id'])) {
            Flash::set('error', 'Namn och lagerställe krävs.');
            return $this->redirect($response, '/inventory/stocktaking/create');
        }

        try {
            $id = $this->repo->createStocktaking($data);
            Flash::set('success', 'Inventering skapad.');
            return $this->redirect($response, "/inventory/stocktaking/{$id}");
        } catch (\Exception $e) {
            Flash::set('error', 'Fel vid skapande av inventering: ' . $e->getMessage());
            return $this->redirect($response, '/inventory/stocktaking/create');
        }
    }

    /** GET /inventory/stocktaking/{id} */
    public function showStocktaking(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $this->repo->getStocktakingById((int) $args['id']);
        if (!$data) {
            Flash::set('error', 'Inventering hittades inte.');
            return $this->redirect($response, '/inventory/stocktaking');
        }

        return $this->render($response, 'inventory/stocktaking/show', [
            'title'       => 'Inventering – ZYNC ERP',
            'stocktaking' => $data['stocktaking'],
            'lines'       => $data['lines'],
            'articles'    => $this->getArticles(),
            'success'     => Flash::get('success'),
            'error'       => Flash::get('error'),
        ]);
    }

    /** POST /inventory/stocktaking/{id}/count */
    public function storeCount(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['article_id']) || !isset($data['counted_quantity'])) {
            Flash::set('error', 'Artikel och räknat antal krävs.');
            return $this->redirect($response, "/inventory/stocktaking/{$id}");
        }

        $this->repo->addCount($id, $data);
        Flash::set('success', 'Räkning registrerad.');
        return $this->redirect($response, "/inventory/stocktaking/{$id}");
    }

    /** POST /inventory/stocktaking/{id}/approve */
    public function approveStocktaking(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $userId = Auth::user()['id'];

        try {
            $this->repo->approveStocktaking($id, $userId);
            Flash::set('success', 'Inventering godkänd och lagersaldo uppdaterat.');
        } catch (\Exception $e) {
            Flash::set('error', 'Fel vid godkännande: ' . $e->getMessage());
        }

        return $this->redirect($response, "/inventory/stocktaking/{$id}");
    }

    // ─── WAREHOUSES ───────────────────────────────────────────

    /** GET /inventory/warehouses */
    public function warehouseIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/warehouses/index', [
            'title'      => 'Lagerställen – ZYNC ERP',
            'warehouses' => $this->repo->getWarehouses(),
            'success'    => Flash::get('success'),
            'error'      => Flash::get('error'),
        ]);
    }

    /** GET /inventory/warehouses/create */
    public function createWarehouse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'inventory/warehouses/create', [
            'title' => 'Nytt lagerställe – ZYNC ERP',
            'users' => $this->getUsers(),
        ]);
    }

    /** POST /inventory/warehouses */
    public function storeWarehouse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];
        $data['is_active'] = $this->parseCheckbox($data, 'is_active');

        if (empty($data['name']) || empty($data['code'])) {
            Flash::set('error', 'Namn och kod krävs.');
            return $this->redirect($response, '/inventory/warehouses/create');
        }

        $this->repo->createWarehouse($data);
        Flash::set('success', 'Lagerställe skapat.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    /** GET /inventory/warehouses/{id}/edit */
    public function editWarehouse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $warehouse = $this->repo->getWarehouseById((int) $args['id']);
        if (!$warehouse) {
            Flash::set('error', 'Lagerställe hittades inte.');
            return $this->redirect($response, '/inventory/warehouses');
        }

        return $this->render($response, 'inventory/warehouses/edit', [
            'title'     => 'Redigera lagerställe – ZYNC ERP',
            'warehouse' => $warehouse,
            'users'     => $this->getUsers(),
        ]);
    }

    /** POST /inventory/warehouses/{id} */
    public function updateWarehouse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $data['is_active'] = $this->parseCheckbox($data, 'is_active');

        if (empty($data['name']) || empty($data['code'])) {
            Flash::set('error', 'Namn och kod krävs.');
            return $this->redirect($response, "/inventory/warehouses/{$id}/edit");
        }

        $this->repo->updateWarehouse($id, $data);
        Flash::set('success', 'Lagerställe uppdaterat.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    /** POST /inventory/warehouses/{id}/delete */
    public function deleteWarehouse(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->deleteWarehouse((int) $args['id']);
        Flash::set('success', 'Lagerställe borttaget.');
        return $this->redirect($response, '/inventory/warehouses');
    }

    // ─── HELPERS ─────────────────────────────────────────────

    private function getArticles(): array
    {
        try {
            return Database::pdo()->query("SELECT id, article_number, name, unit, purchase_price FROM articles WHERE is_deleted = 0 AND is_active = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getUsers(): array
    {
        try {
            return Database::pdo()->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function parseCheckbox(array $data, string $field): int
    {
        return isset($data[$field]) ? 1 : 0;
    }
}
