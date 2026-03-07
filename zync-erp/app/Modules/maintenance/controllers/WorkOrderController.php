<?php

namespace Modules\Maintenance\Controllers;

use Modules\Maintenance\Repositories\InventoryIntegrationRepository;
use Modules\Maintenance\Repositories\WorkOrderRepository;
use Modules\Maintenance\Services\WorkOrderMaterialInventoryService;
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

    private function inventoryRepo(): InventoryIntegrationRepository
    {
        return new InventoryIntegrationRepository($this->db);
    }

    private function service(): WorkOrderService
    {
        return new WorkOrderService($this->repo());
    }

    private function materialInventoryService(): WorkOrderMaterialInventoryService
    {
        return new WorkOrderMaterialInventoryService(
            db: $this->db,
            workOrderRepository: $this->repo(),
            inventoryRepository: $this->inventoryRepo()
        );
    }

    public function index(): void
    {
        $tenantId = $this->tenantId();

        $filters = [
            'status'        => trim((string) ($_GET['status'] ?? '')),
            'priority'      => trim((string) ($_GET['priority'] ?? '')),
            'type'          => trim((string) ($_GET['type'] ?? '')),
            'source'        => trim((string) ($_GET['source'] ?? '')),
            'asset_node_id' => trim((string) ($_GET['asset_node_id'] ?? '')),
            'q'             => trim((string) ($_GET['q'] ?? '')),
            'only_open'     => trim((string) ($_GET['only_open'] ?? '')),
            'overdue'       => trim((string) ($_GET['overdue'] ?? '')),
        ];

        $workOrders = $this->repo()->allByTenant($tenantId, $filters);
        $assetOptions = $this->repo()->getAssetOptions($tenantId);

        require __DIR__ . '/../views/work_orders/index.php';
    }

    public function create(): void
    {
        $tenantId = $this->tenantId();
        $assetOptions = $this->repo()->getAssetOptions($tenantId);
        $errors = [];
        $workOrder = [
            'asset_node_id'    => '',
            'title'            => '',
            'description'      => '',
            'type'             => 'corrective',
            'priority'         => 'medium',
            'status'           => 'reported',
            'source'           => 'manual',
            'assigned_to'      => '',
            'planned_start_at' => '',
            'due_at'           => '',
            'estimated_hours'  => '',
        ];

        require __DIR__ . '/../views/work_orders/create.php';
    }

    public function store(): void
    {
        $tenantId = $this->tenantId();

        $data = [
            'tenant_id'         => $tenantId,
            'asset_node_id'     => (int) ($_POST['asset_node_id'] ?? 0),
            'pm_schedule_id'    => null,
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
        $materials = $this->repo()->materialsByWorkOrder($tenantId, $id);
        $materialTotals = $this->repo()->materialTotalsByWorkOrder($tenantId, $id);
        $articleOptions = $this->repo()->getArticleOptions($tenantId);
        $warehouseOptions = $this->inventoryRepo()->getWarehouseOptions();
        $inventoryMovements = $this->inventoryRepo()->movementsByWorkOrder($tenantId, $id);

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

    public function addMaterial(): void
    {
        $tenantId = $this->tenantId();
        $workOrderId = (int) ($_POST['work_order_id'] ?? 0);

        $workOrder = $this->repo()->findById($tenantId, $workOrderId);
        if (!$workOrder) {
            http_response_code(404);
            echo 'Arbetsorder hittades inte.';
            return;
        }

        $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);

        $data = [
            'tenant_id'        => $tenantId,
            'work_order_id'    => $workOrderId,
            'article_id'       => (int) ($_POST['article_id'] ?? 0),
            'warehouse_id'     => $warehouseId,
            'planned_quantity' => (float) ($_POST['planned_quantity'] ?? 0),
            'issued_quantity'  => 0,
            'unit_cost'        => (float) ($_POST['unit_cost'] ?? 0),
            'notes'            => trim((string) ($_POST['notes'] ?? '')),
        ];

        $requestedIssuedQuantity = (float) ($_POST['issued_quantity'] ?? 0);

        $errors = $this->service()->validateMaterialData(array_merge($data, [
            'issued_quantity' => $requestedIssuedQuantity,
        ]));

        if (!empty($errors)) {
            http_response_code(422);
            echo htmlspecialchars(implode(' ', $errors));
            return;
        }

        $materialId = $this->repo()->addMaterial($data);

        if ($requestedIssuedQuantity > 0) {
            $this->materialInventoryService()->syncIssuedQuantity(
                tenantId: $tenantId,
                materialId: $materialId,
                newIssuedQuantity: $requestedIssuedQuantity,
                userId: $this->userId()
            );
        }

        $this->repo()->addLog([
            'tenant_id'     => $tenantId,
            'work_order_id' => $workOrderId,
            'log_type'      => 'system',
            'message'       => 'Material tillagt på arbetsorder.',
            'hours_spent'   => 0,
            'created_by'    => $this->userId(),
        ]);

        header('Location: /maintenance/work-orders/show?id=' . $workOrderId);
        exit;
    }

    public function updateMaterial(): void
    {
        $tenantId = $this->tenantId();
        $materialId = (int) ($_POST['material_id'] ?? 0);

        $material = $this->repo()->findMaterialById($tenantId, $materialId);
        if (!$material) {
            http_response_code(404);
            echo 'Materialrad hittades inte.';
            return;
        }

        $newWarehouseId = (int) ($_POST['warehouse_id'] ?? 0);

        if ($newWarehouseId !== (int) $material['warehouse_id'] && (float) $material['issued_quantity'] > 0) {
            http_response_code(422);
            echo 'Du kan inte byta lager på en materialrad som redan har uttagen kvantitet. Returnera först till 0.';
            return;
        }

        $newPlannedQuantity = (float) ($_POST['planned_quantity'] ?? 0);
        $newIssuedQuantity = (float) ($_POST['issued_quantity'] ?? 0);
        $newUnitCost = (float) ($_POST['unit_cost'] ?? 0);
        $newNotes = trim((string) ($_POST['notes'] ?? ''));

        $errors = $this->service()->validateMaterialData([
            'article_id'        => (int) $material['article_id'],
            'warehouse_id'      => $newWarehouseId,
            'planned_quantity'  => $newPlannedQuantity,
            'issued_quantity'   => $newIssuedQuantity,
            'unit_cost'         => $newUnitCost,
        ]);

        if (!empty($errors)) {
            http_response_code(422);
            echo htmlspecialchars(implode(' ', $errors));
            return;
        }

        $this->db->beginTransaction();

        try {
            $this->repo()->updateMaterial($tenantId, $materialId, [
                'warehouse_id'     => $newWarehouseId,
                'planned_quantity' => $newPlannedQuantity,
                'issued_quantity'  => (float) $material['issued_quantity'],
                'unit_cost'        => $newUnitCost,
                'notes'            => $newNotes,
            ]);

            $this->materialInventoryService()->syncIssuedQuantity(
                tenantId: $tenantId,
                materialId: $materialId,
                newIssuedQuantity: $newIssuedQuantity,
                userId: $this->userId()
            );

            $this->repo()->addLog([
                'tenant_id'     => $tenantId,
                'work_order_id' => (int) $material['work_order_id'],
                'log_type'      => 'system',
                'message'       => 'Materialrad uppdaterad och lager synkat.',
                'hours_spent'   => 0,
                'created_by'    => $this->userId(),
            ]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            http_response_code(422);
            echo htmlspecialchars($e->getMessage());
            return;
        }

        header('Location: /maintenance/work-orders/show?id=' . (int) $material['work_order_id']);
        exit;
    }

    public function deleteMaterial(): void
    {
        $tenantId = $this->tenantId();
        $materialId = (int) ($_POST['material_id'] ?? 0);

        $material = $this->repo()->findMaterialById($tenantId, $materialId);
        if (!$material) {
            http_response_code(404);
            echo 'Materialrad hittades inte.';
            return;
        }

        if ((float) $material['issued_quantity'] > 0) {
            http_response_code(422);
            echo 'Materialrad med uttaget material kan inte tas bort. Returnera först uttagen kvantitet till 0.';
            return;
        }

        $workOrderId = (int) $material['work_order_id'];

        $this->repo()->deleteMaterial($tenantId, $materialId);

        $this->repo()->addLog([
            'tenant_id'     => $tenantId,
            'work_order_id' => $workOrderId,
            'log_type'      => 'system',
            'message'       => 'Materialrad borttagen.',
            'hours_spent'   => 0,
            'created_by'    => $this->userId(),
        ]);

        header('Location: /maintenance/work-orders/show?id=' . $workOrderId);
        exit;
    }
}
