<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Controller;
use App\Models\InvoiceOutgoingRepository;
use App\Models\InvoiceIncomingRepository;
use App\Models\JournalEntryRepository;
use App\Models\CostCenterRepository;
use App\Models\AccountGroupRepository;
use App\Models\FixedAssetRepository;
use App\Models\BudgetRepository;
use App\Models\FinanceReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PDO;

class FinanceController extends Controller
{
    private InvoiceOutgoingRepository $invoicesOut;
    private InvoiceIncomingRepository $invoicesIn;
    private JournalEntryRepository $journal;
    private CostCenterRepository $costCenters;
    private AccountGroupRepository $accountGroups;
    private FixedAssetRepository $assets;
    private BudgetRepository $budgets;
    private FinanceReportRepository $reports;

    public function __construct()
    {
        parent::__construct();
        $this->invoicesOut  = new InvoiceOutgoingRepository();
        $this->invoicesIn   = new InvoiceIncomingRepository();
        $this->journal      = new JournalEntryRepository();
        $this->costCenters  = new CostCenterRepository();
        $this->accountGroups = new AccountGroupRepository();
        $this->assets       = new FixedAssetRepository();
        $this->budgets      = new BudgetRepository();
        $this->reports      = new FinanceReportRepository();
    }

    // ─── DASHBOARD ───────────────────────────────────────
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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
    public function invoicesOut(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/invoices-out/index', [
            'title' => 'Utgående fakturor',
            'invoices' => $this->invoicesOut->all(),
        ]);
    }

    public function createInvoiceOut(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/invoices-out/create', [
            'title' => 'Ny kundfaktura',
            'customers' => $this->getCustomers(),
            'users' => $this->getUsers(),
        ]);
    }

    public function storeInvoiceOut(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->invoicesOut->create($data);
        Flash::set('success', 'Faktura skapad');
        return $this->redirect($response, "/finance/invoices-out/{$id}");
    }

    public function showInvoiceOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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

    public function editInvoiceOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $invoice = $this->invoicesOut->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-out');

        return $this->render($response, 'finance/invoices-out/edit', [
            'title' => 'Redigera faktura',
            'invoice' => $invoice,
            'customers' => $this->getCustomers(),
        ]);
    }

    public function updateInvoiceOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesOut->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Faktura uppdaterad');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function deleteInvoiceOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesOut->delete((int) $args['id']);
        Flash::set('success', 'Faktura borttagen');
        return $this->redirect($response, '/finance/invoices-out');
    }

    public function statusInvoiceOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->invoicesOut->updateStatus((int) $args['id'], $data['status']);
        Flash::set('success', 'Status uppdaterad');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function addLineOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesOut->addLine((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function removeLineOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesOut->removeLine((int) $args['id'], (int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/finance/invoices-out/{$args['id']}");
    }

    public function paymentOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
    public function invoicesIn(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/invoices-in/index', [
            'title' => 'Inkommande fakturor',
            'invoices' => $this->invoicesIn->all(),
        ]);
    }

    public function createInvoiceIn(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/invoices-in/create', [
            'title' => 'Registrera leverantörsfaktura',
            'suppliers' => $this->getSuppliers(),
            'purchaseOrders' => $this->getPurchaseOrders(),
        ]);
    }

    public function storeInvoiceIn(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->invoicesIn->create($data);
        Flash::set('success', 'Leverantörsfaktura registrerad');
        return $this->redirect($response, "/finance/invoices-in/{$id}");
    }

    public function showInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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

    public function editInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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

    public function updateInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesIn->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Faktura uppdaterad');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function deleteInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesIn->delete((int) $args['id']);
        Flash::set('success', 'Faktura borttagen');
        return $this->redirect($response, '/finance/invoices-in');
    }

    public function statusInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->invoicesIn->updateStatus((int) $args['id'], $data['status'], Auth::id());
        Flash::set('success', 'Status uppdaterad');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function addLineIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesIn->addLine((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function removeLineIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->invoicesIn->removeLine((int) $args['id'], (int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/finance/invoices-in/{$args['id']}");
    }

    public function paymentIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
    public function journal(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

    public function createJournal(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/journal/create', [
            'title' => 'Ny verifikation',
            'accounts' => $this->getAccounts(),
            'costCenters' => $this->costCenters->all(),
        ]);
    }

    public function storeJournal(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

    public function showJournal(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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

    public function addJournalLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->journal->addLine((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/finance/journal/{$args['id']}");
    }

    public function removeJournalLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->journal->removeLine((int) $args['id'], (int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/finance/journal/{$args['id']}");
    }

    public function deleteJournal(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->journal->delete((int) $args['id']);
        Flash::set('success', 'Verifikation borttagen');
        return $this->redirect($response, '/finance/journal');
    }

    // ─── KONTOPLAN ───────────────────────────────────────
    public function chartOfAccounts(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

    public function createAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/accounts/create', [
            'title' => 'Nytt konto',
        ]);
    }

    public function storeAccount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

    public function editAccount(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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

    public function updateAccount(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
    public function costCentersIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/cost-centers/index', [
            'title' => 'Kostnadsställen',
            'costCenters' => $this->costCenters->all(),
            'success' => Flash::get('success'),
        ]);
    }

    public function createCostCenter(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/cost-centers/create', [
            'title' => 'Nytt kostnadsställe',
            'departments' => $this->getDepartments(),
            'users' => $this->getUsers(),
            'costCenters' => $this->costCenters->all(),
        ]);
    }

    public function storeCostCenter(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->costCenters->create((array) $request->getParsedBody());
        Flash::set('success', 'Kostnadsställe skapat');
        return $this->redirect($response, '/finance/cost-centers');
    }

    public function editCostCenter(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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

    public function updateCostCenter(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->costCenters->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Kostnadsställe uppdaterat');
        return $this->redirect($response, '/finance/cost-centers');
    }

    public function deleteCostCenter(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->costCenters->delete((int) $args['id']);
        Flash::set('success', 'Kostnadsställe borttaget');
        return $this->redirect($response, '/finance/cost-centers');
    }

    // ─── RAPPORTER ──────────────────────────────────────
    public function ledger(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');
        $accountFrom = $params['account_from'] ?? null;
        $accountTo = $params['account_to'] ?? null;
        $account = $params['account'] ?? null;

        return $this->render($response, 'finance/reports/ledger', [
            'title' => 'Huvudbok',
            'entries' => $this->journal->ledger($from, $to, $accountFrom, $accountTo),
            'from' => $from,
            'to' => $to,
            'accountFrom' => $accountFrom,
            'accountTo' => $accountTo,
            'account' => $account,
        ]);
    }

    public function trialBalance(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');
        $compare = $params['compare'] ?? null;

        try {
            $data = $this->journal->trialBalance($from, $to);
            if ($compare === 'previous_year') {
                $data = $this->reports->getPreviousYearComparison($from, $to);
            }
        } catch (\Exception $e) {
            $data = [];
            Flash::set('error', 'Rapporten kunde inte laddas. Kontrollera att alla tabeller är skapade.');
        }

        return $this->render($response, 'finance/reports/trial-balance', [
            'title'   => 'Resultat- & balansräkning',
            'data'    => $data,
            'from'    => $from,
            'to'      => $to,
            'compare' => $compare,
        ]);
    }

    public function costCenterReport(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

    // ─── INVOICE IN — APPROVE / REJECT ──────────────────
    public function approveInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        Database::pdo()->prepare(
            "UPDATE invoices_incoming SET status = 'approved', approved_by = ?, approved_at = NOW()
             WHERE id = ? AND is_deleted = 0"
        )->execute([Auth::id(), $id]);
        Flash::set('success', 'Faktura godkänd');
        return $this->redirect($response, "/finance/invoices-in/{$id}");
    }

    public function rejectInvoiceIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        Database::pdo()->prepare(
            "UPDATE invoices_incoming SET status = 'disputed', rejected_by = ?, rejected_at = NOW(),
             rejection_reason = ? WHERE id = ? AND is_deleted = 0"
        )->execute([Auth::id(), $data['rejection_reason'] ?? null, $id]);
        Flash::set('success', 'Faktura nekad');
        return $this->redirect($response, "/finance/invoices-in/{$id}");
    }

    // ─── INVOICE OUT — CREDIT NOTE / PDF / REMINDER ─────
    public function creditNoteForm(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $invoice = $this->invoicesOut->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-out');

        return $this->render($response, 'finance/invoices-out/credit-note', [
            'title' => 'Kreditnota för ' . $invoice['invoice_number'],
            'invoice' => $invoice,
            'lines' => $this->invoicesOut->getLines((int) $args['id']),
        ]);
    }

    public function createCreditNote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $originalId = (int) $args['id'];
        $original = $this->invoicesOut->find($originalId);
        if (!$original) return $this->redirect($response, '/finance/invoices-out');

        $data = (array) $request->getParsedBody();

        // Generate unique credit note number
        $baseNumber = 'KN-' . $original['invoice_number'];
        $existing = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM invoices_outgoing WHERE invoice_number LIKE ? AND is_deleted = 0"
        );
        $existing->execute([$baseNumber . '%']);
        $count = (int) $existing->fetchColumn();
        $number = $count > 0 ? $baseNumber . '-' . ($count + 1) : $baseNumber;
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            "INSERT INTO invoices_outgoing
             (invoice_number, customer_id, status, invoice_date, due_date, payment_terms, currency,
              reference, our_reference, notes, credit_note_for, created_by)
             VALUES (?, ?, 'sent', ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $original['customer_id'],
            date('Y-m-d'),
            date('Y-m-d'),
            $original['payment_terms'],
            $original['currency'],
            'Kreditnota för ' . $original['invoice_number'],
            $original['our_reference'],
            $data['notes'] ?? ('Kreditnota för faktura ' . $original['invoice_number']),
            $originalId,
            Auth::id(),
        ]);
        $creditId = (int) $pdo->lastInsertId();

        // Copy lines with negative amounts
        $lines = $this->invoicesOut->getLines($originalId);
        foreach ($lines as $line) {
            $pdo->prepare(
                "INSERT INTO invoice_outgoing_lines (invoice_id, account_id, description, quantity, unit_price, vat_rate, discount)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            )->execute([
                $creditId,
                $line['account_id'],
                $line['description'],
                -(float) $line['quantity'],
                (float) $line['unit_price'],
                (float) $line['vat_rate'],
                (float) ($line['discount'] ?? 0),
            ]);
        }

        // Mark original as credited
        $pdo->prepare(
            "UPDATE invoices_outgoing SET status = 'credited' WHERE id = ?"
        )->execute([$originalId]);

        Flash::set('success', 'Kreditnota skapad');
        return $this->redirect($response, "/finance/invoices-out/{$creditId}");
    }

    public function pdfInvoiceOut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $invoice = $this->invoicesOut->find((int) $args['id']);
        if (!$invoice) return $this->redirect($response, '/finance/invoices-out');

        return $this->render($response, 'finance/invoices-out/pdf', [
            'title' => 'Faktura ' . $invoice['invoice_number'],
            'invoice' => $invoice,
            'lines' => $this->invoicesOut->getLines((int) $args['id']),
        ]);
    }

    public function sendReminder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        Database::pdo()->prepare(
            "UPDATE invoices_outgoing SET reminder_count = reminder_count + 1, last_reminder_at = NOW()
             WHERE id = ? AND is_deleted = 0"
        )->execute([$id]);
        Flash::set('success', 'Påminnelse registrerad');
        return $this->redirect($response, "/finance/invoices-out/{$id}");
    }

    // ─── KONTOPLANSGRUPPER ───────────────────────────────
    public function accountGroups(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/account-groups/index', [
            'title' => 'Kontoplansgrupper',
            'groups' => $this->accountGroups->all(),
            'success' => Flash::get('success'),
        ]);
    }

    public function createAccountGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/account-groups/create', [
            'title' => 'Ny kontoplansgrupp',
            'groups' => $this->accountGroups->all(),
        ]);
    }

    public function storeAccountGroup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->accountGroups->create($data);
        Flash::set('success', 'Grupp skapad');
        return $this->redirect($response, '/finance/account-groups');
    }

    public function editAccountGroup(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $group = $this->accountGroups->find((int) $args['id']);
        if (!$group) return $this->redirect($response, '/finance/account-groups');

        return $this->render($response, 'finance/account-groups/edit', [
            'title' => 'Redigera grupp',
            'group' => $group,
            'groups' => $this->accountGroups->all(),
        ]);
    }

    public function updateAccountGroup(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->accountGroups->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Grupp uppdaterad');
        return $this->redirect($response, '/finance/account-groups');
    }

    public function deleteAccountGroup(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->accountGroups->delete((int) $args['id']);
        Flash::set('success', 'Grupp borttagen');
        return $this->redirect($response, '/finance/account-groups');
    }

    // ─── KONTOPLAN EXPORT / IMPORT ───────────────────────
    public function exportAccounts(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $accounts = Database::pdo()->query(
            "SELECT account_number, name, account_class, is_active FROM chart_of_accounts ORDER BY account_number"
        )->fetchAll(PDO::FETCH_ASSOC);

        $csv = "Kontonummer,Namn,Klass,Aktiv\n";
        foreach ($accounts as $row) {
            $csv .= implode(',', [
                $row['account_number'],
                '"' . str_replace('"', '""', $row['name']) . '"',
                $row['account_class'],
                $row['is_active'] ? 'Ja' : 'Nej',
            ]) . "\n";
        }

        $response = $response
            ->withHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename=kontoplan.csv');
        $response->getBody()->write($csv);
        return $response;
    }

    public function importAccountsForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/accounts/import', [
            'title' => 'Importera kontoplan',
        ]);
    }

    // ─── LEDGER — ENSKILT KONTO ──────────────────────────
    public function accountLedger(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $accountId = (int) $args['accountId'];
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');

        $accountRow = Database::pdo()->prepare(
            "SELECT * FROM chart_of_accounts WHERE id = ?"
        );
        $accountRow->execute([$accountId]);
        $account = $accountRow->fetch(PDO::FETCH_ASSOC);
        if (!$account) return $this->redirect($response, '/finance/reports/ledger');

        $ledger = $this->reports->getAccountLedger($accountId, $from, $to);

        return $this->render($response, 'finance/reports/account-ledger', [
            'title' => 'Kontoutdrag ' . $account['account_number'] . ' — ' . $account['name'],
            'account' => $account,
            'from' => $from,
            'to' => $to,
            'opening_balance' => $ledger['opening_balance'],
            'closing_balance' => $ledger['closing_balance'],
            'lines' => $ledger['lines'],
        ]);
    }

    // ─── BALANSRÄKNING ───────────────────────────────────
    public function balanceSheet(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $from = $params['from'] ?? date('Y-01-01');
        $to = $params['to'] ?? date('Y-12-31');
        $data = $this->reports->getBalanceSheet($from, $to);

        return $this->render($response, 'finance/reports/balance-sheet', [
            'title' => 'Balansräkning',
            'from' => $from,
            'to' => $to,
            'assets' => $data['assets'],
            'liabilities' => $data['liabilities'],
        ]);
    }

    // ─── BUDGETAR ────────────────────────────────────────
    public function budgetsIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $year = (int) ($params['year'] ?? date('Y'));

        return $this->render($response, 'finance/budgets/index', [
            'title' => 'Budgetar',
            'budgets' => $this->budgets->allForYear($year),
            'year' => $year,
            'success' => Flash::get('success'),
        ]);
    }

    public function createBudget(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/budgets/create', [
            'title' => 'Ny budget',
            'accounts' => $this->getAccounts(),
        ]);
    }

    public function storeBudget(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->budgets->create($data);
        Flash::set('success', 'Budget sparad');
        return $this->redirect($response, '/finance/budgets?year=' . ($data['fiscal_year'] ?? date('Y')));
    }

    public function editBudget(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $budget = $this->budgets->find((int) $args['id']);
        if (!$budget) return $this->redirect($response, '/finance/budgets');

        return $this->render($response, 'finance/budgets/edit', [
            'title' => 'Redigera budget',
            'budget' => $budget,
            'accounts' => $this->getAccounts(),
        ]);
    }

    public function updateBudget(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->budgets->update((int) $args['id'], $data);
        Flash::set('success', 'Budget uppdaterad');
        return $this->redirect($response, '/finance/budgets?year=' . ($data['fiscal_year'] ?? date('Y')));
    }

    public function deleteBudget(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $budget = $this->budgets->find((int) $args['id']);
        $year = $budget['fiscal_year'] ?? date('Y');
        $this->budgets->delete((int) $args['id']);
        Flash::set('success', 'Budget borttagen');
        return $this->redirect($response, '/finance/budgets?year=' . $year);
    }

    // ─── ANLÄGGNINGSTILLGÅNGAR ───────────────────────────
    public function assetsIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/assets/index', [
            'title' => 'Anläggningstillgångar',
            'assets' => $this->assets->all(),
            'success' => Flash::get('success'),
        ]);
    }

    public function createAsset(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'finance/assets/create', [
            'title' => 'Ny anläggningstillgång',
            'departments' => $this->getDepartments(),
            'accounts' => $this->getAccounts(),
        ]);
    }

    public function storeAsset(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->assets->create($data);
        Flash::set('success', 'Tillgång skapad');
        return $this->redirect($response, "/finance/assets/{$id}");
    }

    public function showAsset(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $asset = $this->assets->find((int) $args['id']);
        if (!$asset) return $this->redirect($response, '/finance/assets');

        return $this->render($response, 'finance/assets/show', [
            'title' => $asset['name'],
            'asset' => $asset,
            'depreciations' => $this->assets->getDepreciations((int) $args['id']),
            'suggestedDepreciation' => $this->assets->calculateDepreciation((int) $args['id']),
            'success' => Flash::get('success'),
        ]);
    }

    public function editAsset(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $asset = $this->assets->find((int) $args['id']);
        if (!$asset) return $this->redirect($response, '/finance/assets');

        return $this->render($response, 'finance/assets/edit', [
            'title' => 'Redigera tillgång',
            'asset' => $asset,
            'departments' => $this->getDepartments(),
            'accounts' => $this->getAccounts(),
        ]);
    }

    public function updateAsset(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->assets->update((int) $args['id'], (array) $request->getParsedBody());
        Flash::set('success', 'Tillgång uppdaterad');
        return $this->redirect($response, "/finance/assets/{$args['id']}");
    }

    public function deleteAsset(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->assets->delete((int) $args['id']);
        Flash::set('success', 'Tillgång borttagen');
        return $this->redirect($response, '/finance/assets');
    }

    public function depreciateAsset(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $amount = isset($data['amount']) && $data['amount'] !== ''
            ? (float) $data['amount']
            : $this->assets->calculateDepreciation($id);
        $date = $data['depreciation_date'] ?? date('Y-m-d');

        if ($amount > 0) {
            $this->assets->addDepreciation($id, $amount, $date);
            Flash::set('success', 'Avskrivning bokförd');
        } else {
            Flash::set('success', 'Inget att skriva av');
        }

        return $this->redirect($response, "/finance/assets/{$id}");
    }

    public function reportKpi(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $kpiData = $this->getFinanceKpiData();
        return $this->render($response, 'finance/reports/kpi', [
            'title'   => 'KPI från Avdelningar – ZYNC ERP',
            'kpiData' => $kpiData,
        ]);
    }

    public function reportStocktaking(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters   = (array) $request->getQueryParams();
        $stockData = $this->getStocktakingData($filters);
        return $this->render($response, 'finance/reports/stocktaking', [
            'title'     => 'Inventering Ekonomi – ZYNC ERP',
            'stockData' => $stockData,
            'filters'   => $filters,
        ]);
    }

    private function getFinanceKpiData(): array
    {
        try {
            $pdo = \App\Core\Database::pdo();
            return [
                'unpaid_out'    => $pdo->query("SELECT COUNT(*) FROM invoices_outgoing WHERE status != 'paid' AND is_deleted = 0")->fetchColumn(),
                'unpaid_in'     => $pdo->query("SELECT COUNT(*) FROM invoices_incoming WHERE status != 'paid' AND is_deleted = 0")->fetchColumn(),
                'open_reqs'     => $pdo->query("SELECT COUNT(*) FROM purchase_requisitions WHERE status = 'pending_approval' AND is_deleted = 0")->fetchColumn(),
                'active_orders' => $pdo->query("SELECT COUNT(*) FROM purchase_orders WHERE status IN ('sent','confirmed','partially_received') AND is_deleted = 0")->fetchColumn(),
            ];
        } catch (\Throwable $e) {
            return ['unpaid_out' => 0, 'unpaid_in' => 0, 'open_reqs' => 0, 'active_orders' => 0];
        }
    }

    private function getStocktakingData(array $filters): array
    {
        try {
            $pdo  = \App\Core\Database::pdo();
            $rows = $pdo->query(
                "SELECT a.article_number, a.name, a.unit, a.stock_quantity,
                        a.min_stock_level, (a.stock_quantity * COALESCE(a.purchase_price, 0)) AS value
                 FROM articles a WHERE a.is_deleted = 0 ORDER BY a.name ASC"
            )->fetchAll(\PDO::FETCH_ASSOC);
            $total = array_sum(array_column($rows, 'value'));
            return ['items' => $rows, 'total_value' => $total];
        } catch (\Throwable $e) {
            return ['items' => [], 'total_value' => 0];
        }
    }

}
