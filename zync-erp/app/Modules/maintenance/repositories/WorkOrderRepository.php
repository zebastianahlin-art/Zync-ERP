<?php

namespace Modules\Maintenance\Repositories;

use PDO;

class WorkOrderRepository
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
            ORDER BY node_type, name
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allByTenant(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                wo.*,
                an.name AS asset_name,
                an.code AS asset_code,
                an.node_type AS asset_type
            FROM maintenance_work_orders wo
            INNER JOIN asset_nodes an
                ON an.id = wo.asset_node_id
            WHERE wo.tenant_id = :tenant_id
            ORDER BY
                CASE wo.status
                    WHEN 'in_progress' THEN 1
                    WHEN 'planned' THEN 2
                    WHEN 'reported' THEN 3
                    WHEN 'approved' THEN 4
                    WHEN 'completed' THEN 5
                    WHEN 'closed' THEN 6
                    WHEN 'cancelled' THEN 7
                    ELSE 99
                END,
                wo.due_at IS NULL,
                wo.due_at ASC,
                wo.created_at DESC
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $tenantId, int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                wo.*,
                an.name AS asset_name,
                an.code AS asset_code,
                an.node_type AS asset_type
            FROM maintenance_work_orders wo
            INNER JOIN asset_nodes an
                ON an.id = wo.asset_node_id
            WHERE wo.tenant_id = :tenant_id
              AND wo.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $id,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function logsByWorkOrder(int $tenantId, int $workOrderId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM maintenance_work_order_logs
            WHERE tenant_id = :tenant_id
              AND work_order_id = :work_order_id
            ORDER BY created_at DESC, id DESC
        ");

        $stmt->execute([
            'tenant_id'     => $tenantId,
            'work_order_id' => $workOrderId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_work_orders (
                tenant_id,
                asset_node_id,
                work_order_no,
                title,
                description,
                type,
                priority,
                status,
                source,
                reported_by,
                assigned_to,
                planned_start_at,
                due_at,
                estimated_hours
            ) VALUES (
                :tenant_id,
                :asset_node_id,
                :work_order_no,
                :title,
                :description,
                :type,
                :priority,
                :status,
                :source,
                :reported_by,
                :assigned_to,
                :planned_start_at,
                :due_at,
                :estimated_hours
            )
        ");

        $stmt->execute([
            'tenant_id'        => $data['tenant_id'],
            'asset_node_id'    => $data['asset_node_id'],
            'work_order_no'    => $data['work_order_no'],
            'title'            => $data['title'],
            'description'      => $data['description'],
            'type'             => $data['type'],
            'priority'         => $data['priority'],
            'status'           => $data['status'],
            'source'           => $data['source'],
            'reported_by'      => $data['reported_by'],
            'assigned_to'      => $data['assigned_to'],
            'planned_start_at' => $data['planned_start_at'],
            'due_at'           => $data['due_at'],
            'estimated_hours'  => $data['estimated_hours'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function addLog(array $data): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_work_order_logs (
                tenant_id,
                work_order_id,
                log_type,
                message,
                hours_spent,
                created_by
            ) VALUES (
                :tenant_id,
                :work_order_id,
                :log_type,
                :message,
                :hours_spent,
                :created_by
            )
        ");

        $stmt->execute([
            'tenant_id'     => $data['tenant_id'],
            'work_order_id' => $data['work_order_id'],
            'log_type'      => $data['log_type'],
            'message'       => $data['message'],
            'hours_spent'   => $data['hours_spent'],
            'created_by'    => $data['created_by'],
        ]);
    }

    public function updateStatus(int $tenantId, int $id, string $status, array $timestamps = []): void
    {
        $fields = ['status = :status'];
        $params = [
            'tenant_id' => $tenantId,
            'id'        => $id,
            'status'    => $status,
        ];

        foreach ($timestamps as $field => $value) {
            $fields[] = "{$field} = :{$field}";
            $params[$field] = $value;
        }

        $sql = "
            UPDATE maintenance_work_orders
            SET " . implode(', ', $fields) . "
            WHERE tenant_id = :tenant_id
              AND id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function updateActualHours(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare("
            UPDATE maintenance_work_orders wo
            SET actual_hours = (
                SELECT COALESCE(SUM(hours_spent), 0)
                FROM maintenance_work_order_logs l
                WHERE l.tenant_id = wo.tenant_id
                  AND l.work_order_id = wo.id
                  AND l.log_type = 'work'
            )
            WHERE wo.tenant_id = :tenant_id
              AND wo.id = :id
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $id,
        ]);
    }

    public function nextWorkOrderNumber(int $tenantId): string
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) + 1
            FROM maintenance_work_orders
            WHERE tenant_id = :tenant_id
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
        ]);

        $next = (int) $stmt->fetchColumn();

        return sprintf('WO-%s-%05d', $tenantId, $next);
    }
}
