<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController extends Controller
{
    private ReportRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ReportRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'reports/index', [
            'title' => 'Rapporter – ZYNC ERP',
        ]);
    }

    public function maintenance(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/maintenance', [
            'title'   => 'Underhållsrapport – ZYNC ERP',
            'report'  => $this->repo->maintenanceReport($filters),
            'filters' => $filters,
        ]);
    }

    public function inventory(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/inventory', [
            'title'   => 'Lagerrapport – ZYNC ERP',
            'report'  => $this->repo->inventoryReport($filters),
            'filters' => $filters,
        ]);
    }

    public function purchasing(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/purchasing', [
            'title'   => 'Inköpsrapport – ZYNC ERP',
            'report'  => $this->repo->purchasingReport($filters),
            'filters' => $filters,
        ]);
    }

    public function finance(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/finance', [
            'title'   => 'Ekonomirapport – ZYNC ERP',
            'report'  => $this->repo->financeReport($filters),
            'filters' => $filters,
        ]);
    }

    public function safety(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/safety', [
            'title'   => 'H&S Rapport – ZYNC ERP',
            'report'  => $this->repo->safetyReport($filters),
            'filters' => $filters,
        ]);
    }

    public function production(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/production', [
            'title'   => 'Produktionsrapport – ZYNC ERP',
            'report'  => $this->repo->productionReport($filters),
            'filters' => $filters,
        ]);
    }

    public function sales(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/sales', [
            'title'   => 'Försäljningsrapport – ZYNC ERP',
            'report'  => $this->repo->salesReport($filters),
            'filters' => $filters,
        ]);
    }

    public function hr(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/hr', [
            'title'   => 'HR-rapport – ZYNC ERP',
            'report'  => $this->repo->hrReport($filters),
            'filters' => $filters,
        ]);
    }

    public function projects(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/projects', [
            'title'   => 'Projektrapport – ZYNC ERP',
            'report'  => $this->repo->projectReport($filters),
            'filters' => $filters,
        ]);
    }

    public function cs(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $filters = (array) $request->getQueryParams();
        return $this->render($response, 'reports/cs', [
            'title'   => 'Kundservice Rapport – ZYNC ERP',
            'report'  => $this->repo->csReport($filters),
            'filters' => $filters,
        ]);
    }
}
