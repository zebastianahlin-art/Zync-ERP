<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\View;
use App\Core\Controller;
use App\Models\InvoiceOutgoingRepository;
use App\Models\InvoiceIncomingRepository;
use App\Models\JournalEntryRepository;
use App\Models\CostCenterRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class FinanceController extends Controller
{
    private InvoiceOutgoingRepository $invoicesOut;
    private InvoiceIncomingRepository $invoicesIn;
    private JournalEntryRepository $journal;
    private CostCenterRepository $costCenters;

    public function __construct()
    {
        parent::__construct();
        $this->invoicesOut = new InvoiceOutgoingRepository();
        $this->invoicesIn = new InvoiceIncomingRepository();
        $this->journal = new JournalEntryRepository();
        $this->costCenters = new CostCenterRepository();
    }

    // ─── DASHBOARD ───────────────────────────────────────
    public function index(Request $request, Response $response): Response
    {
        $outStats = $this->invoicesOut->stats();
        $inStats = $this->invoicesIn->stats();
        $overdue = $this->invoicesOut->overdue();

        return $this->render($response, 'finance/index', [
            'title' => 'Ekonomi — Översikt',
            'outStats' => $outStats,
            'inStats' => $inStats,
            'overdue' => $overdue,
        ]);
    }

    // ─── UTGÅENDE FAKTUROR (KUNDFAKTUROR) ────────────────
    public function invoicesOut(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/invoices-out/index', [
            'title' => 'Utgående fakturor',
            'invoices' => $this->invoicesOut->all(),
        ]);
    }

    public function createInvoiceOut(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/invoices-out/create', [
            'title' => 'Ny kundfaktura',
            'customers' => $this->getCustomers(),
            'users' => $this->getUsers(),
        ]);
    }

    public function storeInvoiceOut(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->invoicesOut->create($data);
        Flash::set('success', 'Faktura skapad');
        return $this->redirect($response, "/finance/invoices-out/{$id}");
    }

    public function showInvoiceOut(Request $request, Response $response, array $args): Response
    {
        $invoice = $this->invoicesOut->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-out');

        return $this->render($response, 'finance/invoices-out/show', [
            'title' => 'Faktura ' . $invoice['invoice_number'],
            'invoice' => $invoice,
            'lines' => $this->invoicesOut->getLines((int) $args['id']),
            'articles' => $this->getArticles(),
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->costCenters->all(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function editInvoiceOut(Request $request, Response $response, array $args): Response
    {
        $invoice = $this->invoicesOut->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-out');

        return $this->render($response, 'finance/invoices-out/edit', [
            'title' => 'Redigera faktura',
            'invoice' => $invoice,
            'customers' => $this->getCustomers(),
        ]);
    }

    public function updateInvoiceOut(Request $request, Response $response, array $args): Response
    {
        $this->invoicesOut->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Faktura uppdaterad');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function deleteInvoiceOut(Request $request, Response $response, array $args): Response
    {
        $this->invoicesOut->delete((int) $args['id']);
        Flash::set('success', 'Faktura borttagen');
        return $this->redirect($response, '/finance/invoices-out');
    }

    public function statusInvoiceOut(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $this->invoicesOut->updateStatus((int) $args['id'], $data['status']);
        Flash::set('success', 'Status uppdaterad');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function addLineOut(Request $request, Response $response, array $args): Response
    {
        $this->invoicesOut->addLine((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function removeLineOut(Request $request, Response $response, array $args): Response
    {
        $this->invoicesOut->removeLine((int) $args['id'], (int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function paymentOut(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $this->invoicesOut->registerPayment(
            (int) $args['id'],
            (float) $data['amount'],
            $data['payment_method'] ?? 'bank_transfer',
            $data['payment_date'],
            $data['bank_reference'] ?? null,
            Auth::id()
        );
        Flash::set('success', 'Betalning registrerad');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    // ─── INKOMMANDE FAKTUROR (LEVERANTÖRSFAKTUROR) ───────
    public function invoicesIn(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/invoices-in/index', [
            'title' => 'Inkommande fakturor',
            'invoices' => $this->invoicesIn->all(),
        ]);
    }

    public function createInvoiceIn(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/invoices-in/create', [
            'title' => 'Registrera leverantörsfaktura',
            'suppliers' => $this->getSuppliers(),
            'purchaseOrders' => $this->getPurchaseOrders(),
        ]);
    }

    public function storeInvoiceIn(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->invoicesIn->create($data);
        Flash::set('success', 'Leverantörsfaktura registrerad');
        return $this->redirect($response, "/finance/invoices-in/{$id}");
    }

    public function showInvoiceIn(Request $request, Response $response, array $args): Response
    {
        $invoice = $this->invoicesIn->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-in');

        return $this->render($response, 'finance/invoices-in/show', [
            'title' => 'Lev.faktura ' . $invoice['internal_number'],
            'invoice' => $invoice,
            'lines' => $this->invoicesIn->getLines((int) $args['id']),
            'articles' => $this->getArticles(),
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->costCenters->all(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function editInvoiceIn(Request $request, Response $response, array $args): Response
    {
        $invoice = $this->invoicesIn->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-in');

        return $this->render($response, 'finance/invoices-in/edit', [
            'title' => 'Redigera leverantörsfaktura',
            'invoice' => $invoice,
            'suppliers' => $this->getSuppliers(),
            'purchaseOrders' => $this->getPurchaseOrders(),
        ]);
    }

    public function updateInvoiceIn(Request $request, Response $response, array $args): Response
    {
        $this->invoicesIn->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Faktura uppdaterad');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function deleteInvoiceIn(Request $request, Response $response, array $args): Response
    {
        $this->invoicesIn->delete((int) $args['id']);
        Flash::set('success', 'Faktura borttagen');
        return $this->redirect($response, '/finance/invoices-in');
    }

    public function statusInvoiceIn(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $this->invoicesIn->updateStatus((int) $args['id'], $data['status'], Auth::id());
        Flash::set('success', 'Status uppdaterad');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function addLineIn(Request $request, Response $response, array $args): Response
    {
        $this->invoicesIn->addLine((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function removeLineIn(Request $request, Response $response, array $args): Response
    {
        $this->invoicesIn->removeLine((int) $args['id'], (int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function paymentIn(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $this->invoicesIn->registerPayment(
            (int) $args['id'],
            (float) $data['amount'],
            $data['payment_method'] ?? 'bank_transfer',
            $data['payment_date'],
            $data['bank_reference'] ?? null,
            Auth::id()
        );
        Flash::set('success', 'Betalning registrerad');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    // ─── BOKFÖRING / VERIFIKATIONER ─────────────────────
    public function journal(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $year = (int) ($params['year'] ?? date('Y'));
        $period = isset($params['period']) && $params['period'] !== '' ? (int) $params['period'] : null;

        return $this->render($response, 'finance/journal/index', [
            'title' => 'Bokföring — Verifikationer',
            'entries' => $this->journal->all($year, $period),
            'year' => $year,
            'period' => $period,
            'success' => Flash::get('success'),
        ]);
    }

    public function createJournal(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/journal/create', [
            'title' => 'Ny verifikation',
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->costCenters->all(),
        ]);
    }

    public function storeJournal(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->journal->create($data);

        // Lägg till rader om de skickats med
        if (!empty($data['lines']) && is_array($data['lines'])) {
            foreach ($data['lines'] as $line) {
                if (!empty($line['account_id'])) {
                    $this->journal->addLine($id, $line);
                }
            }
        }

        Flash::set('success', 'Verifikation skapad');
        return $this->redirect($response, "/finance/journal/{$id}");
    }

    public function showJournal(Request $request, Response $response, array $args): Response
    {
        $entry = $this->journal->find((int) $args['id']);
        if (!$entry) return $this->redirect($response, '/finance/journal');

        return $this->render($response, 'finance/journal/show', [
            'title' => 'Verifikation ' . $entry['voucher_number'],
            'entry' => $entry,
            'lines' => $this->journal->getLines((int) $args['id']),
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->costCenters->all(),
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ]);
    }

    public function addJournalLine(Request $request, Response $response, array $args): Response
    {
        $this->journal->addLine((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/finance/journal/{$args['id']}");
    }

    public function removeJournalLine(Request $request, Response $response, array $args): Response
    {
        $this->journal->removeLine((int) $args['id'], (int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/finance/journal/{$args['id']}");
    }

    public function deleteJournal(Request $request, Response $response, array $args): Response
    {
        $this->journal->delete((int) $args['id']);
        Flash::set('success', 'Verifikation borttagen');
        return $this->redirect($response, '/finance/journal');
    }

    // ─── KONTOPLAN ───────────────────────────────────────
    public function chartOfAccounts(Request $request, Response $response): Response
    {
        $accounts = Database::pdo()->query(
            "SELECT * FROM chart_of_accounts ORDER BY account_number"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->render($response, 'finance/accounts/index', [
            'title' => 'Kontoplan',
            'accounts' => $accounts,
            'success' => Flash::get('success'),
        ]);
    }

    public function createAccount(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/accounts/create', [
            'title' => 'Nytt konto',
        ]);
    }

    public function storeAccount(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();
        Database::pdo()->prepare(
            "INSERT INTO chart_of_accounts (account_number, name, account_class, account_group, vat_code, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $data['account_number'],
            $data['name'],
            $data['account_class'],
            $data['account_group'] ?? null,
            $data['vat_code'] ?: null,
            $data['description'] ?? null,
        ]);
        Flash::set('success', 'Konto skapat');
        return $this->redirect($response, '/finance/accounts');
    }

    public function editAccount(Request $request, Response $response, array $args): Response
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM chart_of_accounts WHERE id = ?");
        $stmt->execute([(int) $args['id']]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$account) return $this->redirect($response, '/finance/accounts');

        return $this->render($response, 'finance/accounts/edit', [
            'title' => 'Redigera konto ' . $account['account_number'],
            'account' => $account,
        ]);
    }

    public function updateAccount(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        Database::pdo()->prepare(
            "UPDATE chart_of_accounts SET account_number = ?, name = ?, account_class = ?, account_group = ?, vat_code = ?, description = ?, is_active = ?
             WHERE id = ?"
        )->execute([
            $data['account_number'],
            $data['name'],
            $data['account_class'],
            $data['account_group'] ?? null,
            $data['vat_code'] ?: null,
            $data['description'] ?? null,
            isset($data['is_active']) ? 1 : 0,
            (int) $args['id'],
        ]);
        Flash::set('success', 'Konto uppdaterat');
        return $this->redirect($response, '/finance/accounts');
    }

    // ─── KOSTNADSSTÄLLEN ─────────────────────────────────
    public function costCentersIndex(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/cost-centers/index', [
            'title' => 'Kostnadsställen',
            'costCenters' => $this->costCenters->all(),
            'success' => Flash::get('success'),
        ]);
    }

    public function createCostCenter(Request $request, Response $response): Response
    {
        return $this->render($response, 'finance/cost-centers/create', [
            'title' => 'Nytt kostnadsställe',
            'departments' => $this->getDepartments(),
            'users' => $this->getUsers(),
            'costCenters' => $this->costCenters->all(),
        ]);
    }

    public function storeCostCenter(Request $request, Response $response): Response
    {
        $this->costCenters->create((array) $request->getParsedBody());
        Flash::set('success', 'Kostnadsställe skapat');
        return $this->redirect($response, '/finance/cost-centers');
    }

    public function editCostCenter(Request $request, Response $response, array $args): Response
    {
        $cc = $this->costCenters->find((int) $args['id']);
        if (!$cc) return $this->redirect($response, '/finance/cost-centers');

        return $this->render($response, 'finance/cost-centers/edit', [
            'title' => 'Redigera KS ' . $cc['code'],
            'costCenter' => $cc,
            'departments' => $this->getDepartments(),
            'users' => $this->getUsers(),
            'costCenters' => $this->costCenters->all(),
        ]);
    }

    public function updateCostCenter(Request $request, Response $response, array $args): Response
    {
        $this->costCenters->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Kostnadsställe uppdaterat');
        return $this->redirect($response, '/finance/cost-centers');
    }

    public function deleteCostCenter(Request $request, Response $response, array $args): Response
    {
        $this->costCenters->delete((int) $args['id']);
        Flash::set('success', 'Kostnadsställe borttaget');
        return $this->redirect($response, '/finance/cost-centers');
    }

    // ─── RAPPORTER ──────────────────────────────────────
    public function ledger(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');
        $accountFrom = $params['account_from'] ?? null;
        $accountTo = $params['account_to'] ?? null;

        return $this->render($response, 'finance/reports/ledger', [
            'title' => 'Huvudbok',
            'entries' => $this->journal->ledger($from, $to, $accountFrom, $accountTo),
            'from' => $from,
            'to' => $to,
            'accountFrom' => $accountFrom,
            'accountTo' => $accountTo,
        ]);
    }

    public function trialBalance(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');

        return $this->render($response, 'finance/reports/trial-balance', [
            'title' => 'Resultat- & balansräkning',
            'data' => $this->journal->trialBalance($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function costCenterReport(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');

        return $this->render($response, 'finance/reports/cost-centers', [
            'title' => 'Kostnadsställerapport',
            'data' => $this->journal->costCenterReport($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    // ─── HELPERS ────────────────────────────────────────
    private function getCustomers(): array
    {
        return Database::pdo()->query("SELECT id, name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSuppliers(): array
    {
        return Database::pdo()->query("SELECT id, name FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getArticles(): array
    {
        return Database::pdo()->query("SELECT id, article_number, name FROM articles ORDER BY article_number")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAccounts(): array
    {
        return Database::pdo()->query("SELECT id, account_number, name, account_class FROM chart_of_accounts WHERE is_active = 1 ORDER BY account_number")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUsers(): array
    {
        return Database::pdo()->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getDepartments(): array
    {
        return Database::pdo()->query("SELECT id, name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getPurchaseOrders(): array
    {
        return Database::pdo()->query(
            "SELECT po.id, po.order_number, s.name AS supplier_name 
             FROM purchase_orders po 
             LEFT JOIN suppliers s ON po.supplier_id = s.id 
             WHERE po.is_deleted = 0 
             ORDER BY po.order_number DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

}
