<?php

namespace Modules\Maintenance\Repositories;

use PDO;

class PreventiveMaintenanceRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function getAssetOptions(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, parent_id, node_type, name, code, status
            FROM asset_nodes
            WHERE tenant_id = :tenant_id
              AND status = 'active'
              AND node_type IN ('machine', 'component')
            ORDER BY node_type, name
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allSchedulesByTenant(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                s.*,
                an.name AS asset_name,
                an.code AS asset_code,
                an.node_type AS asset_type
            FROM maintenance_pm_schedules s
            INNER JOIN asset_nodes an
                ON an.id = s.asset_node_id
            WHERE s.tenant_id = :tenant_id
            ORDER BY s.next_due_at ASC, s.id DESC
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findScheduleById(int $tenantId, int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                s.*,
                an.name AS asset_name,
                an.code AS asset_code,
                an.node_type AS asset_type
            FROM maintenance_pm_schedules s
            INNER JOIN asset_nodes an
                ON an.id = s.asset_node_id
            WHERE s.tenant_id = :tenant_id
              AND s.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $id,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function createSchedule(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_pm_schedules (
                tenant_id,
                asset_node_id,
                title,
                description,
                is_active,
                interval_type,
                interval_value,
                next_due_at,
                priority,
                estimated_hours,
                auto_create_work_order,
                default_work_order_type,
                created_by
            ) VALUES (
                :tenant_id,
                :asset_node_id,
                :title,
                :description,
                :is_active,
                :interval_type,
                :interval_value,
                :next_due_at,
                :priority,
                :estimated_hours,
                :auto_create_work_order,
                :default_work_order_type,
                :created_by
            )
        ");

        $stmt->execute([
            'tenant_id'               => $data['tenant_id'],
            'asset_node_id'           => $data['asset_node_id'],
            'title'                   => $data['title'],
            'description'             => $data['description'],
            'is_active'               => $data['is_active'],
            'interval_type'           => $data['interval_type'],
            'interval_value'          => $data['interval_value'],
            'next_due_at'             => $data['next_due_at'],
            'priority'                => $data['priority'],
            'estimated_hours'         => $data['estimated_hours'],
            'auto_create_work_order'  => $data['auto_create_work_order'],
            'default_work_order_type' => $data['default_work_order_type'],
            'created_by'              => $data['created_by'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function dueSchedules(int $tenantId, string $now): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM maintenance_pm_schedules
            WHERE tenant_id = :tenant_id
              AND is_active = 1
              AND auto_create_work_order = 1
              AND next_due_at <= :now
            ORDER BY next_due_at ASC, id ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'now'       => $now,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRun(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_pm_runs (
                tenant_id,
                schedule_id,
                generated_work_order_id,
                run_status,
                due_at,
                notes
            ) VALUES (
                :tenant_id,
                :schedule_id,
                :generated_work_order_id,
                :run_status,
                :due_at,
                :notes
            )
        ");

        $stmt->execute([
            'tenant_id'               => $data['tenant_id'],
            'schedule_id'             => $data['schedule_id'],
            'generated_work_order_id' => $data['generated_work_order_id'],
            'run_status'              => $data['run_status'],
            'due_at'                  => $data['due_at'],
            'notes'                   => $data['notes'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateScheduleAfterGeneration(
        int $tenantId,
        int $scheduleId,
        string $lastGeneratedAt,
        string $nextDueAt
    ): void {
        $stmt = $this->db->prepare("
            UPDATE maintenance_pm_schedules
            SET
                last_generated_at = :last_generated_at,
                next_due_at = :next_due_at
            WHERE tenant_id = :tenant_id
              AND id = :id
        ");

        $stmt->execute([
            'tenant_id'         => $tenantId,
            'id'                => $scheduleId,
            'last_generated_at' => $lastGeneratedAt,
            'next_due_at'       => $nextDueAt,
        ]);
    }

    public function runsBySchedule(int $tenantId, int $scheduleId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                r.*,
                wo.work_order_no,
                wo.title AS work_order_title,
                wo.status AS work_order_status
            FROM maintenance_pm_runs r
            LEFT JOIN maintenance_work_orders wo
                ON wo.id = r.generated_work_order_id
            WHERE r.tenant_id = :tenant_id
              AND r.schedule_id = :schedule_id
            ORDER BY r.generated_at DESC, r.id DESC
        ");

        $stmt->execute([
            'tenant_id'   => $tenantId,
            'schedule_id' => $scheduleId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findRunById(int $tenantId, int $runId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM maintenance_pm_runs
            WHERE tenant_id = :tenant_id
              AND id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $runId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function completeRun(int $tenantId, int $runId, string $completedAt, ?string $notes = null): void
    {
        $stmt = $this->db->prepare("
            UPDATE maintenance_pm_runs
            SET
                run_status = 'completed',
                completed_at = :completed_at,
                notes = :notes
            WHERE tenant_id = :tenant_id
              AND id = :id
        ");

        $stmt->execute([
            'tenant_id'   => $tenantId,
            'id'          => $runId,
            'completed_at'=> $completedAt,
            'notes'       => $notes,
        ]);
    }

    public function updateScheduleLastCompleted(int $tenantId, int $scheduleId, string $completedAt): void
    {
        $stmt = $this->db->prepare("
            UPDATE maintenance_pm_schedules
            SET last_completed_at = :completed_at
            WHERE tenant_id = :tenant_id
              AND id = :id
        ");

        $stmt->execute([
            'tenant_id'   => $tenantId,
            'id'          => $scheduleId,
            'completed_at'=> $completedAt,
        ]);
    }
}
