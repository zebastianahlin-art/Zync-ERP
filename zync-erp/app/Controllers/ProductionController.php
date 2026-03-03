<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Auth;
use App\Core\Validator;
use App\Models\ProductionRepository;
use App\Models\DepartmentRepository;
use App\Models\EmployeeRepository;
use App\Models\ArticleRepository;
use App\Models\InventoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductionController extends Controller
{
    private ProductionRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ProductionRepository();
    }

    // ── Dashboard ────────────────────────────────────

    public function index(Request $request, Response $response): Response
    {
        $stats = $this->repo->stats();
        $orders = $this->repo->allOrders();
        $lines = $this->repo->allLines();

        return $this->render($response, 'production/index', [
            'title'  => 'Produktion',
            'stats'  => $stats,
            'orders' => $orders,
            'lines'  => $lines,
        ]);
    }

    // ── Lines ────────────────────────────────────────

    public function lines(Request $request, Response $response): Response
    {
        $lines = $this->repo->allLines();
        return $this->render($response, 'production/lines/index', [
            'title' => 'Produktionslinjer',
            'lines' => $lines,
        ]);
    }

    public function createLine(Request $request, Response $response): Response
    {
        $deptRepo = new DepartmentRepository();
        return $this->render($response, 'production/lines/form', [
            'title'       => 'Ny produktionslinje',
            'line'        => null,
            'departments' => $deptRepo->all(),
            'machines'    => $this->getLineMachines(),
        ]);
    }

    public function storeLine(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        $v = new Validator($data);
        $v->required('name', 'Namn')->required('code', 'Kod');
        if ($v->fails()) {
            $deptRepo = new DepartmentRepository();
            return $this->render($response, 'production/lines/form', [
                'title'       => 'Ny produktionslinje',
                'line'        => null,
                'departments' => $deptRepo->all(),
                'machines'    => $this->getLineMachines(),
                'errors'      => $v->errors(),
                'old'         => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createLine($data);
        Flash::set('success', 'Produktionslinje skapad.');
        return $this->redirect($response, '/production/lines');
    }

    public function editLine(Request $request, Response $response, array $args): Response
    {
        $line = $this->repo->findLine((int) $args['id']);
        if (!$line) {
            Flash::set('error', 'Linjen hittades inte.');
            return $this->redirect($response, '/production/lines');
        }
        $deptRepo = new DepartmentRepository();
        return $this->render($response, 'production/lines/form', [
            'title'       => 'Redigera linje',
            'line'        => $line,
            'departments' => $deptRepo->all(),
            'machines'    => $this->getLineMachines(),
        ]);
    }

    public function updateLine(Request $request, Response $response, array $args): Response
    {
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        $v = new Validator($data);
        $v->required('name', 'Namn')->required('code', 'Kod');
        if ($v->fails()) {
            $deptRepo = new DepartmentRepository();
            return $this->render($response, 'production/lines/form', [
                'title'       => 'Redigera linje',
                'line'        => array_merge(['id' => $id], $data),
                'departments' => $deptRepo->all(),
                'machines'    => $this->getLineMachines(),
                'errors'      => $v->errors(),
                'old'         => $data,
            ]);
        }

        $this->repo->updateLine($id, $data);
        Flash::set('success', 'Linjen uppdaterad.');
        return $this->redirect($response, '/production/lines');
    }

    public function deleteLine(Request $request, Response $response, array $args): Response
    {
        $this->repo->deleteLine((int) $args['id']);
        Flash::set('success', 'Linjen borttagen.');
        return $this->redirect($response, '/production/lines');
    }

    // ── Orders ───────────────────────────────────────

    public function orders(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $status = $params['status'] ?? null;
        $orders = $this->repo->allOrders($status ?: null);

        return $this->render($response, 'production/orders/index', [
            'title'         => 'Produktionsordrar',
            'orders'        => $orders,
            'currentStatus' => $status,
        ]);
    }

    public function createOrder(Request $request, Response $response): Response
    {
        $empRepo = new EmployeeRepository();
        $artRepo = new ArticleRepository();
        return $this->render($response, 'production/orders/form', [
            'title'     => 'Ny produktionsorder',
            'order'     => null,
            'lines'     => $this->repo->activeLines(),
            'articles'  => $artRepo->all(),
            'employees' => $empRepo->all(),
            'nextNumber' => $this->repo->nextOrderNumber(),
        ]);
    }

    public function storeOrder(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        $v = new Validator($data);
        $v->required('order_number', 'Ordernummer')->required('quantity_planned', 'Planerat antal');
        if ($v->fails()) {
            $empRepo = new EmployeeRepository();
            $artRepo = new ArticleRepository();
            return $this->render($response, 'production/orders/form', [
                'title'     => 'Ny produktionsorder',
                'order'     => null,
                'lines'     => $this->repo->activeLines(),
                'articles'  => $artRepo->all(),
                'employees' => $empRepo->all(),
                'nextNumber' => $data['order_number'] ?? $this->repo->nextOrderNumber(),
                'errors'    => $v->errors(),
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->createOrder($data);
        Flash::set('success', 'Produktionsorder skapad.');
        return $this->redirect($response, '/production/orders/' . $id);
    }

    public function showOrder(Request $request, Response $response, array $args): Response
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if (!$order) {
            Flash::set('error', 'Ordern hittades inte.');
            return $this->redirect($response, '/production/orders');
        }

        $log      = $this->repo->getLog((int) $args['id']);
        $materials = $this->repo->getMaterialUsage((int) $args['id']);
        $timeLog  = $this->repo->getTimeLog((int) $args['id']);
        $bom      = $order['article_id'] ? $this->repo->getBom((int) $order['article_id']) : [];

        $empRepo  = new EmployeeRepository();
        $artRepo  = new ArticleRepository();
        $invRepo  = new InventoryRepository();

        return $this->render($response, 'production/orders/show', [
            'title'      => 'Order ' . $order['order_number'],
            'order'      => $order,
            'log'        => $log,
            'materials'  => $materials,
            'timeLog'    => $timeLog,
            'bom'        => $bom,
            'employees'  => $empRepo->all(),
            'articles'   => $artRepo->all(),
            'warehouses' => $invRepo->allWarehouses(),
        ]);
    }

    public function editOrder(Request $request, Response $response, array $args): Response
    {
        $order = $this->repo->findOrder((int) $args['id']);
        if (!$order) {
            Flash::set('error', 'Ordern hittades inte.');
            return $this->redirect($response, '/production/orders');
        }
        $empRepo = new EmployeeRepository();
        $artRepo = new ArticleRepository();
        return $this->render($response, 'production/orders/form', [
            'title'     => 'Redigera order',
            'order'     => $order,
            'lines'     => $this->repo->activeLines(),
            'articles'  => $artRepo->all(),
            'employees' => $empRepo->all(),
            'nextNumber' => $order['order_number'],
        ]);
    }

    public function updateOrder(Request $request, Response $response, array $args): Response
    {
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        $v = new Validator($data);
        $v->required('quantity_planned', 'Planerat antal');
        if ($v->fails()) {
            $empRepo = new EmployeeRepository();
            $artRepo = new ArticleRepository();
            return $this->render($response, 'production/orders/form', [
                'title'     => 'Redigera order',
                'order'     => array_merge(['id' => $id], $data),
                'lines'     => $this->repo->activeLines(),
                'articles'  => $artRepo->all(),
                'employees' => $empRepo->all(),
                'nextNumber' => $data['order_number'] ?? '',
                'errors'    => $v->errors(),
                'old'       => $data,
            ]);
        }

        $this->repo->updateOrder($id, $data);
        Flash::set('success', 'Ordern uppdaterad.');
        return $this->redirect($response, '/production/orders/' . $id);
    }

    public function updateOrderStatus(Request $request, Response $response, array $args): Response
    {
        $data   = (array) $request->getParsedBody();
        $status = $data['status'] ?? '';
        $valid  = ['draft', 'planned', 'released', 'in_progress', 'completed', 'cancelled'];

        if (!in_array($status, $valid, true)) {
            Flash::set('error', 'Ogiltig status.');
            return $this->redirect($response, '/production/orders/' . $args['id']);
        }

        $this->repo->updateOrderStatus((int) $args['id'], $status);
        Flash::set('success', 'Status uppdaterad.');
        return $this->redirect($response, '/production/orders/' . $args['id']);
    }

    public function deleteOrder(Request $request, Response $response, array $args): Response
    {
        $this->repo->deleteOrder((int) $args['id']);
        Flash::set('success', 'Ordern borttagen.');
        return $this->redirect($response, '/production/orders');
    }

    // ── Production Log ───────────────────────────────

    public function addLog(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $data['order_id'] = (int) $args['id'];

        $order = $this->repo->findOrder((int) $args['id']);
        $data['line_id']   = $order['line_id'] ?? null;
        $data['logged_by'] = Auth::id() ? $this->getEmployeeId() : null;

        $this->repo->addLog($data);
        Flash::set('success', 'Produktion rapporterad.');
        return $this->redirect($response, '/production/orders/' . $args['id']);
    }

    // ── Material Usage ───────────────────────────────

    public function addMaterial(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $data['order_id']      = (int) $args['id'];
        $data['registered_by'] = Auth::id() ? $this->getEmployeeId() : null;

        $this->repo->addMaterialUsage($data);
        Flash::set('success', 'Material registrerat.');
        return $this->redirect($response, '/production/orders/' . $args['id']);
    }

    // ── Time Log ─────────────────────────────────────

    public function addTime(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $data['order_id'] = (int) $args['id'];

        $this->repo->addTimeLog($data);
        Flash::set('success', 'Tid registrerad.');
        return $this->redirect($response, '/production/orders/' . $args['id']);
    }

    // ── BOM ──────────────────────────────────────────

    public function addBomLine(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $this->repo->addBomLine(
            (int) $args['articleId'],
            (int) ($data['material_article_id'] ?? 0),
            (float) ($data['quantity_per_unit'] ?? 1),
            $data['unit'] ?? 'st'
        );
        Flash::set('success', 'BOM-rad tillagd.');
        return $this->redirect($response, '/production/orders/' . ($data['return_order_id'] ?? ''));
    }

    public function removeBomLine(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $this->repo->removeBomLine((int) $args['bomId']);
        Flash::set('success', 'BOM-rad borttagen.');
        return $this->redirect($response, '/production/orders/' . ($data['return_order_id'] ?? ''));
    }

    // ── Helpers ──────────────────────────────────────

    private function getLineMachines(): array
    {
        $db = \App\Core\Database::pdo();
        return $db->query("SELECT id, name, code FROM machines WHERE type IN ('line','site','area') AND is_deleted = 0 ORDER BY name")
                  ->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getEmployeeId(): ?int
    {
        $userId = Auth::id();
        if (!$userId) return null;
        $db = \App\Core\Database::pdo();
        $stmt = $db->prepare("SELECT id FROM employees WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }
}
