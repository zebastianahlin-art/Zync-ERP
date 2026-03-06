<?php

namespace Modules\Maintenance\Controllers;

use Modules\Maintenance\Repositories\WorkOrderRepository;
use Modules\Maintenance\Services\WorkOrderService;
use PDO;
use RuntimeException;

class WorkOrderController
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

    private function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    private function repo(): WorkOrderRepository
    {
        return new WorkOrderRepository($this->db);
    }

    private function service(): WorkOrderService
    {
        return new WorkOrderService($this->repo());
    }

    public function index(): void
    {
        $tenantId = $this->tenantId();
        $workOrders = $this->repo()->allByTenant($tenantId);

        require __DIR__ . '/../views/work_orders/index.php';
    }

    public function create(): void
    {
        $tenantId = $this->tenantId();
        $assetOptions = $this->repo()->getAssetOptions($tenantId);
        $errors = [];
        $workOrder = [
            'asset_node_id'   => '',
            'title'           => '',
            'description'     => '',
            'type'            => 'corrective',
            'priority'        => 'medium',
            'status'          => 'reported',
            'source'          => 'manual',
            'assigned_to'     => '',
            'planned_start_at'=> '',
            'due_at'          => '',
            'estimated_hours' => '',
        ];

        require __DIR__ . '/../views/work_orders/create.php';
    }

    public function store(): void
    {
        $tenantId = $this->tenantId();

        $data = [
            'tenant_id'         => $tenantId,
            'asset_node_id'     => (int) ($_POST['asset_node_id'] ?? 0),
            'work_order_no'     => $this->repo()->nextWorkOrderNumber($tenantId),
            'title'             => trim((string) ($_POST['title'] ?? '')),
            'description'       => trim((string) ($_POST['description'] ?? '')),
            'type'              => trim((string) ($_POST['type'] ?? 'corrective')),
            'priority'          => trim((string) ($_POST['priority'] ?? 'medium')),
            'status'            => trim((string) ($_POST['status'] ?? 'reported')),
            'source'            => 'manual',
            'reported_by'       => $this->userId(),
            'assigned_to'       => ($_POST['assigned_to'] ?? '') !== '' ? (int) $_POST['assigned_to'] : null,
            'planned_start_at'  => ($_POST['planned_start_at'] ?? '') !== '' ? (string) $_POST['planned_start_at'] : null,
            'due_at'            => ($_POST['due_at'] ?? '') !== '' ? (string) $_POST['due_at'] : null,
            'estimated_hours'   => ($_POST['estimated_hours'] ?? '') !== '' ? (float) $_POST['estimated_hours'] : null,
        ];

        $errors = $this->service()->validateCreateData($data);

        if (empty($errors)) {
            $id = $this->repo()->create($data);

            $this->repo()->addLog([
                'tenant_id'     => $tenantId,
                'work_order_id' => $id,
                'log_type'      => 'system',
                'message'       => 'Arbetsorder skapad.',
                'hours_spent'   => 0,
                'created_by'    => $this->userId(),
            ]);

            header('Location: /maintenance/work-orders/show?id=' . $id);
            exit;
        }

        $assetOptions = $this->repo()->getAssetOptions($tenantId);
        $workOrder = $data;

        require __DIR__ . '/../views/work_orders/create.php';
    }

    public function show(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_GET['id'] ?? 0);

        $workOrder = $this->repo()->findById($tenantId, $id);
        if (!$workOrder) {
            http_response_code(404);
            echo 'Arbetsorder hittades inte.';
            return;
        }

        $logs = $this->repo()->logsByWorkOrder($tenantId, $id);

        require __DIR__ . '/../views/work_orders/show.php';
    }

    public function updateStatus(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_POST['id'] ?? 0);
        $newStatus = trim((string) ($_POST['status'] ?? ''));

        $workOrder = $this->repo()->findById($tenantId, $id);
        if (!$workOrder) {
            http_response_code(404);
            echo 'Arbetsorder hittades inte.';
            return;
        }

        try {
            $this->service()->validateStatusChange($workOrder['status'], $newStatus);

            $timestamps = $this->service()->getStatusTimestamps($newStatus);
            $this->repo()->updateStatus($tenantId, $id, $newStatus, $timestamps);

            $this->repo()->addLog([
                'tenant_id'     => $tenantId,
                'work_order_id' => $id,
                'log_type'      => 'status_change',
                'message'       => 'Status ändrad från ' . $workOrder['status'] . ' till ' . $newStatus . '.',
                'hours_spent'   => 0,
                'created_by'    => $this->userId(),
            ]);

            header('Location: /maintenance/work-orders/show?id=' . $id);
            exit;
        } catch (RuntimeException $e) {
            http_response_code(422);
            echo htmlspecialchars($e->getMessage());
        }
    }

    public function addLog(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_POST['work_order_id'] ?? 0);

        $workOrder = $this->repo()->findById($tenantId, $id);
        if (!$workOrder) {
            http_response_code(404);
            echo 'Arbetsorder hittades inte.';
            return;
        }

        $message = trim((string) ($_POST['message'] ?? ''));
        $logType = trim((string) ($_POST['log_type'] ?? 'comment'));
        $hoursSpent = (float) ($_POST['hours_spent'] ?? 0);

        if ($message === '') {
            http_response_code(422);
            echo 'Meddelande är obligatoriskt.';
            return;
        }

        if (!in_array($logType, ['comment', 'work'], true)) {
            http_response_code(422);
            echo 'Ogiltig loggtyp.';
            return;
        }

        $this->repo()->addLog([
            'tenant_id'     => $tenantId,
            'work_order_id' => $id,
            'log_type'      => $logType,
            'message'       => $message,
            'hours_spent'   => $logType === 'work' ? $hoursSpent : 0,
            'created_by'    => $this->userId(),
        ]);

        if ($logType === 'work') {
            $this->repo()->updateActualHours($tenantId, $id);
        }

        header('Location: /maintenance/work-orders/show?id=' . $id);
        exit;
    }
}
