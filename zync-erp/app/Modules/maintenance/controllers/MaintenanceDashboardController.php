<?php

namespace Modules\Maintenance\Controllers;

use Modules\Maintenance\Repositories\PreventiveMaintenanceRepository;
use Modules\Maintenance\Repositories\WorkOrderRepository;
use PDO;
use RuntimeException;

class MaintenanceDashboardController
{
    public function __construct(private PDO $db)
    {
    }

    private function tenantId(): int
    {
        if (!isset($_SESSION['tenant_id'])) {
            throw new RuntimeException('Tenant saknas i session.');
        }

        return (int) $_SESSION['tenant_id'];
    }

    public function index(): void
    {
        $tenantId = $this->tenantId();

        $workOrderRepo = new WorkOrderRepository($this->db);
        $pmRepo = new PreventiveMaintenanceRepository($this->db);

        $workOrderCounts = $workOrderRepo->dashboardCounts($tenantId);
        $pmCounts = $pmRepo->dashboardCounts($tenantId);
        $recentOpenWorkOrders = $workOrderRepo->recentOpenWorkOrders($tenantId, 10);
        $dueSoonSchedules = $pmRepo->dueSoonSchedules($tenantId, 10);

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
