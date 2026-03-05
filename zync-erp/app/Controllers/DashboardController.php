<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\DashboardRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController extends Controller
{
    private DashboardRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new DashboardRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) { return $guard; }
        $user = Auth::user();
        $roleLevel = (int) ($user['role_level'] ?? 1);
        $userId = Auth::id();

        $widgets = $this->repo->userWidgets($userId, $roleLevel);
        if (empty($widgets)) {
            $this->repo->initUserWidgets($userId, $roleLevel);
            $widgets = $this->repo->userWidgets($userId, $roleLevel);
        }

        $kpiData = [];
        foreach ($widgets as $w) {
            $kpiData[$w['slug']] = $this->getWidgetData($w['slug']);
        }

        return $this->render($response, 'dashboard/index', [
            'title'       => 'Dashboard – ZYNC ERP',
            'currentUser' => $user,
            'widgets'     => $widgets,
            'kpiData'     => $kpiData,
            'success'     => Flash::get('success'),
        ]);
    }

    public function configure(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) { return $guard; }
        $user = Auth::user();
        $roleLevel = (int) ($user['role_level'] ?? 1);
        $userId = Auth::id();

        $available = $this->repo->availableWidgets($roleLevel);
        $userWidgets = $this->repo->userWidgets($userId, $roleLevel);
        $activeIds = array_column($userWidgets, 'widget_id');

        return $this->render($response, 'dashboard/configure', [
            'title'      => 'Anpassa dashboard – ZYNC ERP',
            'available'  => $available,
            'activeIds'  => $activeIds,
            'success'    => Flash::get('success'),
            'error'      => Flash::get('error'),
        ]);
    }

    public function addWidget(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) { return $guard; }
        $body = (array) $request->getParsedBody();
        $widgetId = (int) ($body['widget_id'] ?? 0);
        if ($widgetId > 0) {
            $this->repo->addWidget(Auth::id(), $widgetId);
            Flash::set('success', 'Widget tillagd.');
        }
        return $this->redirect($response, '/dashboard/configure');
    }

    public function removeWidget(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) { return $guard; }
        $body = (array) $request->getParsedBody();
        $widgetId = (int) ($body['widget_id'] ?? 0);
        if ($widgetId > 0) {
            $this->repo->removeWidget(Auth::id(), $widgetId);
            Flash::set('success', 'Widget borttagen.');
        }
        return $this->redirect($response, '/dashboard/configure');
    }

    public function reorderWidgets(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) { return $guard; }
        $body = (array) $request->getParsedBody();
        $widgetIds = array_map('intval', (array) ($body['widget_ids'] ?? []));
        if (!empty($widgetIds)) {
            $this->repo->reorderWidgets(Auth::id(), $widgetIds);
        }
        return $this->json($response, ['ok' => true]);
    }

    private function getWidgetData(string $slug): array
    {
        return match($slug) {
            'kpi_maintenance'   => $this->repo->kpiMaintenance(),
            'kpi_inventory'     => $this->repo->kpiInventory(),
            'kpi_purchasing'    => $this->repo->kpiPurchasing(),
            'kpi_finance'       => $this->repo->kpiFinance(),
            'kpi_safety'        => $this->repo->kpiSafety(),
            'kpi_production'    => $this->repo->kpiProduction(),
            'kpi_sales'         => $this->repo->kpiSales(),
            'kpi_hr'            => $this->repo->kpiHr(),
            'kpi_projects'      => $this->repo->kpiProjects(),
            'kpi_cs'            => $this->repo->kpiCs(),
            'recent_workorders' => ['items' => $this->repo->recentWorkorders()],
            'recent_invoices'   => ['items' => $this->repo->recentInvoices()],
            'overdue_resources' => ['items' => $this->repo->overdueResources()],
            default             => [],
        };
    }
}
