<?php

namespace Modules\Maintenance\Controllers;

use Modules\Maintenance\Repositories\PreventiveMaintenanceRepository;
use Modules\Maintenance\Repositories\WorkOrderRepository;
use Modules\Maintenance\Services\PreventiveMaintenanceService;
use PDO;
use RuntimeException;

class PreventiveMaintenanceController
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

    private function repo(): PreventiveMaintenanceRepository
    {
        return new PreventiveMaintenanceRepository($this->db);
    }

    private function workOrderRepo(): WorkOrderRepository
    {
        return new WorkOrderRepository($this->db);
    }

    private function service(): PreventiveMaintenanceService
    {
        return new PreventiveMaintenanceService();
    }

    public function index(): void
    {
        $tenantId = $this->tenantId();
        $schedules = $this->repo()->allSchedulesByTenant($tenantId);

        require __DIR__ . '/../views/preventive_maintenance/index.php';
    }

    public function create(): void
    {
        $tenantId = $this->tenantId();
        $assetOptions = $this->repo()->getAssetOptions($tenantId);
        $errors = [];
        $schedule = [
            'asset_node_id'           => '',
            'title'                   => '',
            'description'             => '',
            'interval_type'           => 'monthly',
            'interval_value'          => 1,
            'next_due_at'             => '',
            'priority'                => 'medium',
            'estimated_hours'         => '',
            'auto_create_work_order'  => 1,
            'default_work_order_type' => 'preventive',
        ];

        require __DIR__ . '/../views/preventive_maintenance/create.php';
    }

    public function store(): void
    {
        $tenantId = $this->tenantId();

        $rawNextDueAt = trim((string) ($_POST['next_due_at'] ?? ''));

        $data = [
            'tenant_id'               => $tenantId,
            'asset_node_id'           => (int) ($_POST['asset_node_id'] ?? 0),
            'title'                   => trim((string) ($_POST['title'] ?? '')),
            'description'             => trim((string) ($_POST['description'] ?? '')),
            'is_active'               => 1,
            'interval_type'           => trim((string) ($_POST['interval_type'] ?? 'monthly')),
            'interval_value'          => (int) ($_POST['interval_value'] ?? 1),
            'next_due_at'             => $rawNextDueAt !== '' ? date('Y-m-d H:i:s', strtotime($rawNextDueAt)) : '',
            'priority'                => trim((string) ($_POST['priority'] ?? 'medium')),
            'estimated_hours'         => ($_POST['estimated_hours'] ?? '') !== '' ? (float) $_POST['estimated_hours'] : null,
            'auto_create_work_order'  => isset($_POST['auto_create_work_order']) ? 1 : 0,
            'default_work_order_type' => trim((string) ($_POST['default_work_order_type'] ?? 'preventive')),
            'created_by'              => $this->userId(),
        ];

        $errors = $this->service()->validateCreateData($data);

        if (empty($errors)) {
            $id = $this->repo()->createSchedule($data);

            header('Location: /maintenance/preventive/show?id=' . $id);
            exit;
        }

        $assetOptions = $this->repo()->getAssetOptions($tenantId);
        $schedule = array_merge($data, [
            'next_due_at' => $rawNextDueAt,
        ]);

        require __DIR__ . '/../views/preventive_maintenance/create.php';
    }

    public function show(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_GET['id'] ?? 0);

        $schedule = $this->repo()->findScheduleById($tenantId, $id);
        if (!$schedule) {
            http_response_code(404);
            echo 'PM-schema hittades inte.';
            return;
        }

        $runs = $this->repo()->runsBySchedule($tenantId, $id);

        require __DIR__ . '/../views/preventive_maintenance/show.php';
    }

    public function runDueSchedules(): void
    {
        $tenantId = $this->tenantId();
        $now = date('Y-m-d H:i:s');

        $dueSchedules = $this->repo()->dueSchedules($tenantId, $now);

        foreach ($dueSchedules as $schedule) {
            $workOrderId = $this->workOrderRepo()->create([
                'tenant_id'         => $tenantId,
                'asset_node_id'     => (int) $schedule['asset_node_id'],
                'work_order_no'     => $this->workOrderRepo()->nextWorkOrderNumber($tenantId),
                'title'             => $schedule['title'],
                'description'       => $schedule['description'],
                'type'              => $schedule['default_work_order_type'],
                'priority'          => $schedule['priority'],
                'status'            => 'reported',
                'source'            => 'pm_schedule',
                'reported_by'       => $this->userId(),
                'assigned_to'       => null,
                'planned_start_at'  => null,
                'due_at'            => $schedule['next_due_at'],
                'estimated_hours'   => $schedule['estimated_hours'],
            ]);

            $this->workOrderRepo()->addLog([
                'tenant_id'     => $tenantId,
                'work_order_id' => $workOrderId,
                'log_type'      => 'system',
                'message'       => 'Arbetsorder skapad automatiskt från PM-schema #' . $schedule['id'] . '.',
                'hours_spent'   => 0,
                'created_by'    => $this->userId(),
            ]);

            $this->repo()->createRun([
                'tenant_id'               => $tenantId,
                'schedule_id'             => (int) $schedule['id'],
                'generated_work_order_id' => $workOrderId,
                'run_status'              => 'generated',
                'due_at'                  => $schedule['next_due_at'],
                'notes'                   => 'Automatiskt genererad arbetsorder.',
            ]);

            $nextDueAt = $this->service()->calculateNextDueAt(
                $schedule['next_due_at'],
                $schedule['interval_type'],
                (int) $schedule['interval_value']
            );

            $this->repo()->updateScheduleAfterGeneration(
                $tenantId,
                (int) $schedule['id'],
                $now,
                $nextDueAt
            );
        }

        header('Location: /maintenance/preventive');
        exit;
    }

    public function completeRun(): void
    {
        $tenantId = $this->tenantId();
        $runId = (int) ($_POST['run_id'] ?? 0);
        $notes = trim((string) ($_POST['notes'] ?? ''));

        $run = $this->repo()->findRunById($tenantId, $runId);
        if (!$run) {
            http_response_code(404);
            echo 'PM-körning hittades inte.';
            return;
        }

        $completedAt = date('Y-m-d H:i:s');

        $this->repo()->completeRun($tenantId, $runId, $completedAt, $notes !== '' ? $notes : null);
        $this->repo()->updateScheduleLastCompleted($tenantId, (int) $run['schedule_id'], $completedAt);

        header('Location: /maintenance/preventive/show?id=' . (int) $run['schedule_id']);
        exit;
    }
}
