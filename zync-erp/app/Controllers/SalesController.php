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

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Hittades inte</h1>');
        return $response->withStatus(404);
    }
}
