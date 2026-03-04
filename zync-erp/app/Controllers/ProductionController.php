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

    /** GET /production */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/dashboard', [
            'title'  => 'Produktion – ZYNC ERP',
            'stats'  => $this->repo->stats(),
            'orders' => $this->repo->recentOrders(5),
        ]);
    }

    /** GET /production/lines */
    public function lines(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/lines/index', [
            'title' => 'Produktionslinjer – ZYNC ERP',
            'lines' => $this->repo->allLines(),
        ]);
    }

    /** GET /production/lines/create */
    public function createLine(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/lines/create', [
            'title'  => 'Ny produktionslinje – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /production/lines */
    public function storeLine(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractLineData($request);
        $errors = $this->validateLine($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/lines/create', [
                'title'  => 'Ny produktionslinje – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->createLine($data);
        Flash::set('success', 'Produktionslinjen skapades.');
        return $this->redirect($response, '/production/lines');
    }

    /** GET /production/lines/{id} */
    public function showLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $line = $this->repo->findLine((int) $args['id']);
        if ($line === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/lines/show', [
            'title' => htmlspecialchars($line['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'line'  => $line,
        ]);
    }

    /** GET /production/lines/{id}/edit */
    public function editLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $line = $this->repo->findLine((int) $args['id']);
        if ($line === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'production/lines/edit', [
            'title'  => 'Redigera linje – ZYNC ERP',
            'line'   => $line,
            'errors' => [],
        ]);
    }

    /** POST /production/lines/{id} */
    public function updateLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $line = $this->repo->findLine($id);
        if ($line === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractLineData($request);
        $errors = $this->validateLine($data);

        if (!empty($errors)) {
            return $this->render($response, 'production/lines/edit', [
                'title'  => 'Redigera linje – ZYNC ERP',
                'line'   => $line,
                'errors' => $errors,
            ]);
        }

        $this->repo->updateLine($id, $data);
        Flash::set('success', 'Linjen uppdaterades.');
        return $this->redirect($response, '/production/lines');
    }

    /** POST /production/lines/{id}/delete */
    public function deleteLine(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->findLine($id) !== null) {
            $this->repo->deleteLine($id);
            Flash::set('success', 'Linjen togs bort.');
        }
        return $this->redirect($response, '/production/lines');
    }

    /** GET /production/orders */
    public function orders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/orders/index', [
            'title'  => 'Produktionsordrar – ZYNC ERP',
            'orders' => $this->repo->allOrders(),
        ]);
    }

    /** GET /production/stock */
    public function stock(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'production/stock/index', [
            'title' => 'Produktionslager – ZYNC ERP',
            'stock' => $this->repo->allStock(),
        ]);
    }

    /** @return array<string, string> */
    private function extractLineData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'        => trim((string) ($body['name'] ?? '')),
            'code'        => trim((string) ($body['code'] ?? '')),
            'description' => trim((string) ($body['description'] ?? '')),
            'status'      => in_array($body['status'] ?? '', ['active', 'inactive'], true) ? $body['status'] : 'active',
        ];
    }

    /** @return array<string, string> */
    private function validateLine(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        if ($data['code'] === '') {
            $errors['code'] = 'Kod är obligatorisk.';
        }
        return $errors;
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Hittades inte</h1>');
        return $response->withStatus(404);
    }
}
