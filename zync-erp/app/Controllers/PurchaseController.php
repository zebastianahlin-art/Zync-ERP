<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Database;
use App\Models\PurchaseRequisitionRepository;
use App\Models\PurchaseOrderRepository;
use App\Models\PurchaseAgreementRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PurchaseController extends Controller
{
    private PurchaseRequisitionRepository $reqRepo;
    private PurchaseOrderRepository $orderRepo;
    private PurchaseAgreementRepository $agreementRepo;

    public function __construct()
    {
        parent::__construct();
        $this->reqRepo = new PurchaseRequisitionRepository();
        $this->orderRepo = new PurchaseOrderRepository();
        $this->agreementRepo = new PurchaseAgreementRepository();
    }

    // ─── DASHBOARD ───────────────────────────────────────────

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requisitions = $this->reqRepo->all();
        $orders = $this->orderRepo->all();
        $agreements = $this->agreementRepo->all();
        $expiring = $this->agreementRepo->getExpiring(30);

        // KPI:er
        $pendingReqs = count(array_filter($requisitions, fn($r) => $r['status'] === 'pending_approval'));
        $activeOrders = count(array_filter($orders, fn($o) => in_array($o['status'], ['sent','confirmed','partially_received'])));
        $activeAgreements = count(array_filter($agreements, fn($a) => $a['status'] === 'active'));
        $totalOrderValue = array_sum(array_column($orders, 'total_amount'));

        return $this->render($response, 'purchasing/index', [
            'title' => 'Inköp – ZYNC ERP',
            'requisitions' => $requisitions,
            'orders' => $orders,
            'agreements' => $agreements,
            'expiring' => $expiring,
            'pendingReqs' => $pendingReqs,
            'activeOrders' => $activeOrders,
            'activeAgreements' => $activeAgreements,
            'totalOrderValue' => $totalOrderValue,
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    // ─── INKÖPSANMODAN ───────────────────────────────────────

    public function requisitions(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'purchasing/requisitions/index', [
            'title' => 'Inköpsanmodan – ZYNC ERP',
            'requisitions' => $this->reqRepo->all(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function createRequisition(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'purchasing/requisitions/create', [
            'title' => 'Ny inköpsanmodan – ZYNC ERP',
            'departments' => $this->getDepartments(),
        ]);
    }

    public function storeRequisition(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['requested_by'] = Auth::user()['id'];

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, '/purchasing/requisitions/create');
        }

        $id = $this->reqRepo->create($data);
        Flash::set('success', 'Inköpsanmodan skapad.');
        return $this->redirect($response, "/purchasing/requisitions/{$id}");
    }

    public function showRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $req = $this->reqRepo->find($id);
        if (!$req) {
            Flash::set('error', 'Anmodan hittades inte.');
            return $this->redirect($response, '/purchasing/requisitions');
        }

        return $this->render($response, 'purchasing/requisitions/show', [
            'title' => $req['requisition_number'] . ' – ZYNC ERP',
            'requisition' => $req,
            'lines' => $this->reqRepo->getLines($id),
            'articles' => $this->getArticles(),
            'suppliers' => $this->getSuppliers(),
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->getCostCenters(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function editRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $req = $this->reqRepo->find($id);
        if (!$req) {
            Flash::set('error', 'Anmodan hittades inte.');
            return $this->redirect($response, '/purchasing/requisitions');
        }

        return $this->render($response, 'purchasing/requisitions/edit', [
            'title' => 'Redigera anmodan – ZYNC ERP',
            'requisition' => $req,
            'departments' => $this->getDepartments(),
        ]);
    }

    public function updateRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['title'])) {
            Flash::set('error', 'Titel krävs.');
            return $this->redirect($response, "/purchasing/requisitions/{$id}/edit");
        }

        $this->reqRepo->update($id, $data);
        Flash::set('success', 'Anmodan uppdaterad.');
        return $this->redirect($response, "/purchasing/requisitions/{$id}");
    }

    public function addRequisitionLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['description'])) {
            Flash::set('error', 'Beskrivning krävs.');
            return $this->redirect($response, "/purchasing/requisitions/{$id}");
        }

        $this->reqRepo->addLine($id, $data);
        Flash::set('success', 'Rad tillagd.');
        return $this->redirect($response, "/purchasing/requisitions/{$id}");
    }

    public function removeRequisitionLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $reqId = (int) $args['id'];
        $lineId = (int) $args['lineId'];
        $this->reqRepo->removeLine($lineId, $reqId);
        Flash::set('success', 'Rad borttagen.');
        return $this->redirect($response, "/purchasing/requisitions/{$reqId}");
    }

    public function approveRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->reqRepo->updateStatus($id, 'approved', Auth::user()['id']);
        Flash::set('success', 'Anmodan godkänd.');
        return $this->redirect($response, "/purchasing/requisitions/{$id}");
    }

    public function rejectRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->reqRepo->updateStatus($id, 'rejected', Auth::user()['id'], $data['reason'] ?? null);
        Flash::set('success', 'Anmodan avvisad.');
        return $this->redirect($response, "/purchasing/requisitions/{$id}");
    }

    public function submitRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->reqRepo->updateStatus($id, 'pending_approval');
        Flash::set('success', 'Anmodan skickad för godkännande.');
        return $this->redirect($response, "/purchasing/requisitions/{$id}");
    }

    public function deleteRequisition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->reqRepo->delete($id);
        Flash::set('success', 'Anmodan raderad.');
        return $this->redirect($response, '/purchasing/requisitions');
    }

    public function convertToOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $reqId = (int) $args['id'];
        $orderId = $this->orderRepo->createFromRequisition($reqId, Auth::user()['id']);

        if (!$orderId) {
            Flash::set('error', 'Kunde inte skapa inköpsorder. Kontrollera att rader och leverantör finns.');
            return $this->redirect($response, "/purchasing/requisitions/{$reqId}");
        }

        Flash::set('success', 'Inköpsorder skapad från anmodan.');
        return $this->redirect($response, "/purchasing/orders/{$orderId}");
    }

    // ─── INKÖPSORDER ─────────────────────────────────────────

    public function orders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'purchasing/orders/index', [
            'title' => 'Inköpsordrar – ZYNC ERP',
            'orders' => $this->orderRepo->all(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function createOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'purchasing/orders/create', [
            'title' => 'Ny inköpsorder – ZYNC ERP',
            'suppliers' => $this->getSuppliers(),
        ]);
    }

    public function storeOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $user = Auth::user();
        $data['buyer_id'] = $user['id'];
        $data['created_by'] = $user['id'];

        if (empty($data['supplier_id'])) {
            Flash::set('error', 'Leverantör krävs.');
            return $this->redirect($response, '/purchasing/orders/create');
        }

        $id = $this->orderRepo->create($data);
        Flash::set('success', 'Inköpsorder skapad.');
        return $this->redirect($response, "/purchasing/orders/{$id}");
    }

    public function showOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $order = $this->orderRepo->find($id);
        if (!$order) {
            Flash::set('error', 'Order hittades inte.');
            return $this->redirect($response, '/purchasing/orders');
        }

        return $this->render($response, 'purchasing/orders/show', [
            'title' => $order['order_number'] . ' – ZYNC ERP',
            'order' => $order,
            'lines' => $this->orderRepo->getLines($id),
            'articles' => $this->getArticles(),
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->getCostCenters(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function editOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $order = $this->orderRepo->find($id);
        if (!$order) {
            Flash::set('error', 'Order hittades inte.');
            return $this->redirect($response, '/purchasing/orders');
        }

        return $this->render($response, 'purchasing/orders/edit', [
            'title' => 'Redigera inköpsorder – ZYNC ERP',
            'order' => $order,
            'suppliers' => $this->getSuppliers(),
        ]);
    }

    public function updateOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->orderRepo->update($id, $data);
        Flash::set('success', 'Order uppdaterad.');
        return $this->redirect($response, "/purchasing/orders/{$id}");
    }

    public function addOrderLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();

        if (empty($data['description'])) {
            Flash::set('error', 'Beskrivning krävs.');
            return $this->redirect($response, "/purchasing/orders/{$id}");
        }

        $this->orderRepo->addLine($id, $data);
        Flash::set('success', 'Orderrad tillagd.');
        return $this->redirect($response, "/purchasing/orders/{$id}");
    }

    public function removeOrderLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $orderId = (int) $args['id'];
        $lineId = (int) $args['lineId'];
        $this->orderRepo->removeLine($lineId, $orderId);
        Flash::set('success', 'Orderrad borttagen.');
        return $this->redirect($response, "/purchasing/orders/{$orderId}");
    }

    public function updateOrderStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $status = $data['status'] ?? '';

        $valid = ['draft','sent','confirmed','partially_received','received','invoiced','closed','cancelled'];
        if (!in_array($status, $valid)) {
            Flash::set('error', 'Ogiltig status.');
            return $this->redirect($response, "/purchasing/orders/{$id}");
        }

        $this->orderRepo->updateStatus($id, $status);
        Flash::set('success', 'Orderstatus uppdaterad.');
        return $this->redirect($response, "/purchasing/orders/{$id}");
    }

    public function deleteOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->orderRepo->delete($id);
        Flash::set('success', 'Order raderad.');
        return $this->redirect($response, '/purchasing/orders');
    }

    // ─── AVTAL ───────────────────────────────────────────────

    public function agreements(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'purchasing/agreements/index', [
            'title' => 'Avtalshantering – ZYNC ERP',
            'agreements' => $this->agreementRepo->all(),
            'expiring' => $this->agreementRepo->getExpiring(60),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function createAgreement(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'purchasing/agreements/create', [
            'title' => 'Nytt avtal – ZYNC ERP',
            'suppliers' => $this->getSuppliers(),
            'users' => $this->getUsers(),
        ]);
    }

    public function storeAgreement(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::user()['id'];

        if (empty($data['title']) || empty($data['supplier_id']) || empty($data['start_date'])) {
            Flash::set('error', 'Titel, leverantör och startdatum krävs.');
            return $this->redirect($response, '/purchasing/agreements/create');
        }

        $id = $this->agreementRepo->create($data);
        Flash::set('success', 'Avtal skapat.');
        return $this->redirect($response, "/purchasing/agreements/{$id}");
    }

    public function showAgreement(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $agreement = $this->agreementRepo->find($id);
        if (!$agreement) {
            Flash::set('error', 'Avtal hittades inte.');
            return $this->redirect($response, '/purchasing/agreements');
        }

        return $this->render($response, 'purchasing/agreements/show', [
            'title' => $agreement['agreement_number'] . ' – ZYNC ERP',
            'agreement' => $agreement,
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function editAgreement(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $agreement = $this->agreementRepo->find($id);
        if (!$agreement) {
            Flash::set('error', 'Avtal hittades inte.');
            return $this->redirect($response, '/purchasing/agreements');
        }

        return $this->render($response, 'purchasing/agreements/edit', [
            'title' => 'Redigera avtal – ZYNC ERP',
            'agreement' => $agreement,
            'suppliers' => $this->getSuppliers(),
            'users' => $this->getUsers(),
        ]);
    }

    public function updateAgreement(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $this->agreementRepo->update($id, $data);
        Flash::set('success', 'Avtal uppdaterat.');
        return $this->redirect($response, "/purchasing/agreements/{$id}");
    }

    public function deleteAgreement(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $this->agreementRepo->delete($id);
        Flash::set('success', 'Avtal raderat.');
        return $this->redirect($response, '/purchasing/agreements');
    }

    // ─── HELPERS ─────────────────────────────────────────────

    private function getDepartments(): array
    {
        return Database::pdo()->query("SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getSuppliers(): array
    {
        return Database::pdo()->query("SELECT id, name FROM suppliers WHERE is_deleted = 0 AND is_active = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getArticles(): array
    {
        return Database::pdo()->query("SELECT id, article_number, name, unit, purchase_price, vat_rate FROM articles WHERE is_deleted = 0 AND is_active = 1 ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getUsers(): array
    {
        return Database::pdo()->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getAccounts(): array
    {
        return Database::pdo()->query("SELECT id, account_number, name, account_class FROM chart_of_accounts WHERE is_active = 1 ORDER BY account_number")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCostCenters(): array
    {
        return Database::pdo()->query("SELECT id, code, name FROM cost_centers WHERE is_active = 1 AND is_deleted = 0 ORDER BY code")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
