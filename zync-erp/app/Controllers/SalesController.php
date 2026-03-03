<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\SalesRepository;
use App\Models\ArticleRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SalesController extends Controller
{
    private SalesRepository $repo;
    private ArticleRepository $articles;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new SalesRepository();
        $this->articles = new ArticleRepository();
    }

    /* ═══════════════════════════════════════════════════════
     *  DASHBOARD
     * ═══════════════════════════════════════════════════════ */

    public function index(Request $request, Response $response): Response
    {
        $stats = $this->repo->stats();
        $recentQuotes = $this->repo->allQuotes(null, null, null);
        $recentOrders = $this->repo->allSalesOrders(null, null, null);
        return $this->render($response, 'sales/index', [
            'title' => 'Försäljning — Översikt',
            'stats' => $stats,
            'recentQuotes' => array_slice($recentQuotes, 0, 10),
            'recentOrders' => array_slice($recentOrders, 0, 10),
        ]);
    }

    /* ═══════════════════════════════════════════════════════
     *  KUNDER
     * ═══════════════════════════════════════════════════════ */

    public function customers(Request $request, Response $response): Response
    {
        $q = $request->getQueryParams();
        $customers = $this->repo->allCustomers($q['status'] ?? null, $q['category'] ?? null, $q['search'] ?? null);
        return $this->render($response, 'sales/customers/index', [
            'title' => 'Kunder',
            'customers' => $customers,
            'filters' => $q,
        ]);
    }

    public function createCustomer(Request $request, Response $response): Response
    {
        return $this->render($response, 'sales/customers/form', [
            'title' => 'Ny kund',
            'customer' => null,
            'priceLists' => $this->repo->priceListsForDropdown(),
            'nextNumber' => $this->repo->nextCustomerNumber(),
        ]);
    }

    public function storeCustomer(Request $request, Response $response): Response
    {
        $d = $request->getParsedBody();
        $v = new Validator($d);
        $v->required('name', 'Namn')->required('org_number', 'Org.nummer')->required('email', 'E-post');
        if ($v->fails()) {
            Flash::set('error', implode(', ', $v->errors()));
            return $this->render($response, 'sales/customers/form', [
                'title' => 'Ny kund', 'customer' => null, 'old' => $d,
                'priceLists' => $this->repo->priceListsForDropdown(),
                'nextNumber' => $this->repo->nextCustomerNumber(),
            ]);
        }
        $id = $this->repo->createCustomer($d);
        $this->repo->addActivity(['customer_id' => $id, 'activity_type' => 'note', 'subject' => 'Kund skapad', 'created_by' => Auth::id()]);
        Flash::set('success', 'Kund skapad');
        return $this->redirect($response, "/sales/customers/{$id}");
    }

    public function showCustomer(Request $request, Response $response, array $args): Response
    {
        $customer = $this->repo->findCustomer((int) $args['id']);
        if (!$customer) { Flash::set('error', 'Kund hittades inte'); return $this->redirect($response, '/sales/customers'); }
        return $this->render($response, 'sales/customers/show', [
            'title' => $customer['name'],
            'customer' => $customer,
            'contacts' => $this->repo->contactsByCustomer((int) $args['id']),
            'customerPrices' => $this->repo->customerPrices((int) $args['id']),
            'quotes' => $this->repo->allQuotes(null, (int) $args['id']),
            'orders' => $this->repo->allSalesOrders(null, (int) $args['id']),
            'activities' => $this->repo->activities((int) $args['id']),
            'articles' => $this->articles->allAsArray(),
        ]);
    }

    public function editCustomer(Request $request, Response $response, array $args): Response
    {
        $customer = $this->repo->findCustomer((int) $args['id']);
        if (!$customer) { Flash::set('error', 'Kund hittades inte'); return $this->redirect($response, '/sales/customers'); }
        return $this->render($response, 'sales/customers/form', [
            'title' => 'Redigera kund',
            'customer' => $customer,
            'priceLists' => $this->repo->priceListsForDropdown(),
        ]);
    }

    public function updateCustomer(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $v = new Validator($d);
        $v->required('name', 'Namn')->required('org_number', 'Org.nummer')->required('email', 'E-post');
        if ($v->fails()) {
            Flash::set('error', implode(', ', $v->errors()));
            $customer = $this->repo->findCustomer((int) $args['id']);
            return $this->render($response, 'sales/customers/form', [
                'title' => 'Redigera kund', 'customer' => $customer, 'old' => $d,
                'priceLists' => $this->repo->priceListsForDropdown(),
            ]);
        }
        $this->repo->updateCustomer((int) $args['id'], $d);
        Flash::set('success', 'Kund uppdaterad');
        return $this->redirect($response, "/sales/customers/{$args['id']}");
    }

    public function deleteCustomer(Request $request, Response $response, array $args): Response
    {
        $this->repo->deleteCustomer((int) $args['id']);
        Flash::set('success', 'Kund borttagen');
        return $this->redirect($response, '/sales/customers');
    }

    /* ── Kontakter ──────────────────────────────────────── */

    public function storeContact(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $d['customer_id'] = (int) $args['id'];
        $v = new Validator($d);
        $v->required('first_name', 'Förnamn')->required('last_name', 'Efternamn');
        if ($v->fails()) { Flash::set('error', implode(', ', $v->errors())); }
        else { $this->repo->createContact($d); Flash::set('success', 'Kontakt tillagd'); }
        return $this->redirect($response, "/sales/customers/{$args['id']}");
    }

    public function deleteContact(Request $request, Response $response, array $args): Response
    {
        $this->repo->deleteContact((int) $args['contactId']);
        Flash::set('success', 'Kontakt borttagen');
        return $this->redirect($response, "/sales/customers/{$args['id']}");
    }

    /* ── Kundpriser ─────────────────────────────────────── */

    public function storeCustomerPrice(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $d['customer_id'] = (int) $args['id'];
        $this->repo->addCustomerPrice($d);
        Flash::set('success', 'Kundpris sparat');
        return $this->redirect($response, "/sales/customers/{$args['id']}");
    }

    public function deleteCustomerPrice(Request $request, Response $response, array $args): Response
    {
        $this->repo->removeCustomerPrice((int) $args['priceId']);
        Flash::set('success', 'Kundpris borttaget');
        return $this->redirect($response, "/sales/customers/{$args['id']}");
    }

    /* ═══════════════════════════════════════════════════════
     *  PRISLISTOR
     * ═══════════════════════════════════════════════════════ */

    public function priceLists(Request $request, Response $response): Response
    {
        return $this->render($response, 'sales/pricelists/index', [
            'title' => 'Prislistor',
            'priceLists' => $this->repo->allPriceLists(),
        ]);
    }

    public function createPriceList(Request $request, Response $response): Response
    {
        return $this->render($response, 'sales/pricelists/form', [
            'title' => 'Ny prislista', 'priceList' => null,
        ]);
    }

    public function storePriceList(Request $request, Response $response): Response
    {
        $d = $request->getParsedBody();
        $v = new Validator($d);
        $v->required('code', 'Kod')->required('name', 'Namn');
        if ($v->fails()) { Flash::set('error', implode(', ', $v->errors())); return $this->render($response, 'sales/pricelists/form', ['title' => 'Ny prislista', 'priceList' => null, 'old' => $d]); }
        $id = $this->repo->createPriceList($d);
        Flash::set('success', 'Prislista skapad');
        return $this->redirect($response, "/sales/pricelists/{$id}");
    }

    public function showPriceList(Request $request, Response $response, array $args): Response
    {
        $pl = $this->repo->findPriceList((int) $args['id']);
        if (!$pl) { Flash::set('error', 'Prislista hittades inte'); return $this->redirect($response, '/sales/pricelists'); }
        return $this->render($response, 'sales/pricelists/show', [
            'title' => $pl['name'],
            'priceList' => $pl,
            'lines' => $this->repo->priceListLines((int) $args['id']),
            'articles' => $this->articles->allAsArray(),
        ]);
    }

    public function editPriceList(Request $request, Response $response, array $args): Response
    {
        $pl = $this->repo->findPriceList((int) $args['id']);
        if (!$pl) { Flash::set('error', 'Prislista hittades inte'); return $this->redirect($response, '/sales/pricelists'); }
        return $this->render($response, 'sales/pricelists/form', ['title' => 'Redigera prislista', 'priceList' => $pl]);
    }

    public function updatePriceList(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $this->repo->updatePriceList((int) $args['id'], $d);
        Flash::set('success', 'Prislista uppdaterad');
        return $this->redirect($response, "/sales/pricelists/{$args['id']}");
    }

    public function addPriceListLine(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $d['price_list_id'] = (int) $args['id'];
        $this->repo->addPriceListLine($d);
        Flash::set('success', 'Artikel tillagd i prislista');
        return $this->redirect($response, "/sales/pricelists/{$args['id']}");
    }

    public function removePriceListLine(Request $request, Response $response, array $args): Response
    {
        $this->repo->removePriceListLine((int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/sales/pricelists/{$args['id']}");
    }

    /* ═══════════════════════════════════════════════════════
     *  OFFERTER
     * ═══════════════════════════════════════════════════════ */

    public function quotes(Request $request, Response $response): Response
    {
        $q = $request->getQueryParams();
        return $this->render($response, 'sales/quotes/index', [
            'title' => 'Offerter',
            'quotes' => $this->repo->allQuotes($q['status'] ?? null, isset($q['customer_id']) ? (int) $q['customer_id'] : null, $q['search'] ?? null),
            'filters' => $q,
        ]);
    }

    public function createQuote(Request $request, Response $response): Response
    {
        return $this->render($response, 'sales/quotes/form', [
            'title' => 'Ny offert',
            'quote' => null,
            'customers' => $this->repo->customersForDropdown(),
            'nextNumber' => $this->repo->nextQuoteNumber(),
        ]);
    }

    public function storeQuote(Request $request, Response $response): Response
    {
        $d = $request->getParsedBody();
        $v = new Validator($d);
        $v->required('customer_id', 'Kund')->required('quote_date', 'Offertdatum')->required('valid_until', 'Giltig till');
        if ($v->fails()) {
            Flash::set('error', implode(', ', $v->errors()));
            return $this->render($response, 'sales/quotes/form', [
                'title' => 'Ny offert', 'quote' => null, 'old' => $d,
                'customers' => $this->repo->customersForDropdown(),
                'nextNumber' => $this->repo->nextQuoteNumber(),
            ]);
        }
        $d['created_by'] = Auth::id();
        $id = $this->repo->createQuote($d);
        $this->repo->addActivity(['customer_id' => $d['customer_id'], 'quote_id' => $id, 'activity_type' => 'note', 'subject' => 'Offert skapad', 'created_by' => Auth::id()]);
        Flash::set('success', 'Offert skapad — lägg till rader');
        return $this->redirect($response, "/sales/quotes/{$id}");
    }

    public function showQuote(Request $request, Response $response, array $args): Response
    {
        $quote = $this->repo->findQuote((int) $args['id']);
        if (!$quote) { Flash::set('error', 'Offert hittades inte'); return $this->redirect($response, '/sales/quotes'); }
        return $this->render($response, 'sales/quotes/show', [
            'title' => "Offert {$quote['quote_number']}",
            'quote' => $quote,
            'lines' => $this->repo->quoteLines((int) $args['id']),
            'contacts' => $this->repo->contactsByCustomer((int) $quote['customer_id']),
            'articles' => $this->articles->allAsArray(),
            'activities' => $this->repo->activities(null, (int) $args['id']),
        ]);
    }

    public function editQuote(Request $request, Response $response, array $args): Response
    {
        $quote = $this->repo->findQuote((int) $args['id']);
        if (!$quote) { Flash::set('error', 'Offert hittades inte'); return $this->redirect($response, '/sales/quotes'); }
        return $this->render($response, 'sales/quotes/form', [
            'title' => 'Redigera offert',
            'quote' => $quote,
            'customers' => $this->repo->customersForDropdown(),
        ]);
    }

    public function updateQuote(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $this->repo->updateQuote((int) $args['id'], $d);
        Flash::set('success', 'Offert uppdaterad');
        return $this->redirect($response, "/sales/quotes/{$args['id']}");
    }

    public function addQuoteLine(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $d['quote_id'] = (int) $args['id'];
        if (!empty($d['article_id'])) {
            $quote = $this->repo->findQuote((int) $args['id']);
            $price = $this->repo->getPrice((int) $d['article_id'], (int) $quote['customer_id'], (float) ($d['quantity'] ?? 1));
            if (empty($d['unit_price'])) $d['unit_price'] = $price['unit_price'];
            if (empty($d['discount_percent'])) $d['discount_percent'] = $price['discount_percent'];
            $article = $this->articles->findAsArray((int) $d['article_id']);
            if (empty($d['description']) && $article) $d['description'] = $article['name'];
            if (empty($d['unit']) && $article) $d['unit'] = $article['unit'];
            if (!isset($d['vat_rate']) && $article) $d['vat_rate'] = $article['vat_rate'];
        }
        $this->repo->addQuoteLine($d);
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/sales/quotes/{$args['id']}");
    }

    public function removeQuoteLine(Request $request, Response $response, array $args): Response
    {
        $this->repo->removeQuoteLine((int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/sales/quotes/{$args['id']}");
    }

    public function updateQuoteStatus(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $status = $d['status'] ?? '';
        $this->repo->updateQuoteStatus((int) $args['id'], $status);
        $quote = $this->repo->findQuote((int) $args['id']);
        $this->repo->addActivity(['customer_id' => $quote['customer_id'], 'quote_id' => (int) $args['id'], 'activity_type' => 'status_change', 'subject' => "Offert status: {$status}", 'created_by' => Auth::id()]);
        Flash::set('success', 'Status uppdaterad');
        return $this->redirect($response, "/sales/quotes/{$args['id']}");
    }

    public function convertQuoteToOrder(Request $request, Response $response, array $args): Response
    {
        $orderId = $this->repo->convertQuoteToOrder((int) $args['id'], Auth::id());
        Flash::set('success', 'Offert konverterad till order');
        return $this->redirect($response, "/sales/orders/{$orderId}");
    }

    public function deleteQuote(Request $request, Response $response, array $args): Response
    {
        $this->repo->deleteQuote((int) $args['id']);
        Flash::set('success', 'Offert borttagen');
        return $this->redirect($response, '/sales/quotes');
    }

    /* ═══════════════════════════════════════════════════════
     *  FÖRSÄLJNINGSORDRAR
     * ═══════════════════════════════════════════════════════ */

    public function orders(Request $request, Response $response): Response
    {
        $q = $request->getQueryParams();
        return $this->render($response, 'sales/orders/index', [
            'title' => 'Försäljningsordrar',
            'orders' => $this->repo->allSalesOrders($q['status'] ?? null, isset($q['customer_id']) ? (int) $q['customer_id'] : null, $q['search'] ?? null),
            'filters' => $q,
        ]);
    }

    public function createOrder(Request $request, Response $response): Response
    {
        return $this->render($response, 'sales/orders/form', [
            'title' => 'Ny försäljningsorder',
            'order' => null,
            'customers' => $this->repo->customersForDropdown(),
            'nextNumber' => $this->repo->nextOrderNumber(),
        ]);
    }

    public function storeOrder(Request $request, Response $response): Response
    {
        $d = $request->getParsedBody();
        $v = new Validator($d);
        $v->required('customer_id', 'Kund')->required('order_date', 'Orderdatum');
        if ($v->fails()) {
            Flash::set('error', implode(', ', $v->errors()));
            return $this->render($response, 'sales/orders/form', [
                'title' => 'Ny försäljningsorder', 'order' => null, 'old' => $d,
                'customers' => $this->repo->customersForDropdown(),
                'nextNumber' => $this->repo->nextOrderNumber(),
            ]);
        }
        $d['created_by'] = Auth::id();
        $id = $this->repo->createSalesOrder($d);
        $this->repo->addActivity(['customer_id' => $d['customer_id'], 'sales_order_id' => $id, 'activity_type' => 'note', 'subject' => 'Order skapad', 'created_by' => Auth::id()]);
        Flash::set('success', 'Order skapad — lägg till rader');
        return $this->redirect($response, "/sales/orders/{$id}");
    }

    public function showOrder(Request $request, Response $response, array $args): Response
    {
        $order = $this->repo->findSalesOrder((int) $args['id']);
        if (!$order) { Flash::set('error', 'Order hittades inte'); return $this->redirect($response, '/sales/orders'); }
        return $this->render($response, 'sales/orders/show', [
            'title' => "Order {$order['order_number']}",
            'order' => $order,
            'lines' => $this->repo->salesOrderLines((int) $args['id']),
            'contacts' => $this->repo->contactsByCustomer((int) $order['customer_id']),
            'articles' => $this->articles->allAsArray(),
            'activities' => $this->repo->activities(null, null, (int) $args['id']),
        ]);
    }

    public function editOrder(Request $request, Response $response, array $args): Response
    {
        $order = $this->repo->findSalesOrder((int) $args['id']);
        if (!$order) { Flash::set('error', 'Order hittades inte'); return $this->redirect($response, '/sales/orders'); }
        return $this->render($response, 'sales/orders/form', [
            'title' => 'Redigera order',
            'order' => $order,
            'customers' => $this->repo->customersForDropdown(),
        ]);
    }

    public function updateOrder(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $this->repo->updateSalesOrder((int) $args['id'], $d);
        Flash::set('success', 'Order uppdaterad');
        return $this->redirect($response, "/sales/orders/{$args['id']}");
    }

    public function addOrderLine(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $d['sales_order_id'] = (int) $args['id'];
        if (!empty($d['article_id'])) {
            $order = $this->repo->findSalesOrder((int) $args['id']);
            $price = $this->repo->getPrice((int) $d['article_id'], (int) $order['customer_id'], (float) ($d['quantity'] ?? 1));
            if (empty($d['unit_price'])) $d['unit_price'] = $price['unit_price'];
            if (empty($d['discount_percent'])) $d['discount_percent'] = $price['discount_percent'];
            $article = $this->articles->findAsArray((int) $d['article_id']);
            if (empty($d['description']) && $article) $d['description'] = $article['name'];
            if (empty($d['unit']) && $article) $d['unit'] = $article['unit'];
            if (!isset($d['vat_rate']) && $article) $d['vat_rate'] = $article['vat_rate'];
        }
        $this->repo->addSalesOrderLine($d);
        Flash::set('success', 'Rad tillagd');
        return $this->redirect($response, "/sales/orders/{$args['id']}");
    }

    public function removeOrderLine(Request $request, Response $response, array $args): Response
    {
        $this->repo->removeSalesOrderLine((int) $args['lineId']);
        Flash::set('success', 'Rad borttagen');
        return $this->redirect($response, "/sales/orders/{$args['id']}");
    }

    public function updateOrderStatus(Request $request, Response $response, array $args): Response
    {
        $d = $request->getParsedBody();
        $status = $d['status'] ?? '';
        $this->repo->updateSalesOrderStatus((int) $args['id'], $status);
        $order = $this->repo->findSalesOrder((int) $args['id']);
        $this->repo->addActivity(['customer_id' => $order['customer_id'], 'sales_order_id' => (int) $args['id'], 'activity_type' => 'status_change', 'subject' => "Order status: {$status}", 'created_by' => Auth::id()]);
        Flash::set('success', 'Status uppdaterad');
        return $this->redirect($response, "/sales/orders/{$args['id']}");
    }

    public function createProductionFromOrder(Request $request, Response $response, array $args): Response
    {
        $created = $this->repo->createProductionFromOrder((int) $args['id'], Auth::id());
        Flash::set('success', count($created) . ' produktionsorder skapade');
        return $this->redirect($response, "/sales/orders/{$args['id']}");
    }

    public function deleteOrder(Request $request, Response $response, array $args): Response
    {
        $this->repo->deleteSalesOrder((int) $args['id']);
        Flash::set('success', 'Order borttagen');
        return $this->redirect($response, '/sales/orders');
    }

    /* ═══════════════════════════════════════════════════════
     *  AJAX — Prislookup
     * ═══════════════════════════════════════════════════════ */

    public function getArticlePrice(Request $request, Response $response, array $args): Response
    {
        $q = $request->getQueryParams();
        $customerId = (int) ($q['customer_id'] ?? 0);
        $articleId = (int) $args['articleId'];
        $quantity = (float) ($q['quantity'] ?? 1);
        $price = $this->repo->getPrice($articleId, $customerId, $quantity);
        $article = $this->articles->findAsArray($articleId);
        return $this->json($response, [
            'unit_price' => $price['unit_price'],
            'discount_percent' => $price['discount_percent'],
            'source' => $price['source'],
            'description' => $article['name'] ?? '',
            'unit' => $article['unit'] ?? 'st',
            'vat_rate' => $article['vat_rate'] ?? 25,
        ]);
    }
}
