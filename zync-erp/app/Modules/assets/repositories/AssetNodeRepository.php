<?php

namespace Modules\Assets\Repositories;

use PDO;
use RuntimeException;

class AssetNodeRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function allByTenant(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                tenant_id,
                parent_id,
                node_type,
                name,
                code,
                description,
                status,
                sort_order,
                created_at,
                updated_at
            FROM asset_nodes
            WHERE tenant_id = :tenant_id
            ORDER BY parent_id IS NULL DESC, parent_id ASC, sort_order ASC, name ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTreeByTenant(int $tenantId): array
    {
        $rows = $this->allByTenant($tenantId);

        $indexed = [];
        foreach ($rows as $row) {
            $row['children'] = [];
            $indexed[$row['id']] = $row;
        }

        $tree = [];
        foreach ($indexed as $id => $row) {
            if ($row['parent_id'] === null) {
                $tree[] = &$indexed[$id];
                continue;
            }

            if (isset($indexed[$row['parent_id']])) {
                $indexed[$row['parent_id']]['children'][] = &$indexed[$id];
            }
        }

        return $tree;
    }

    public function findById(int $tenantId, int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM asset_nodes
            WHERE tenant_id = :tenant_id
              AND id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $id,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getPossibleParents(int $tenantId, ?int $excludeId = null): array
    {
        $sql = "
            SELECT id, parent_id, node_type, name, code, status
            FROM asset_nodes
            WHERE tenant_id = :tenant_id
        ";

        $params = ['tenant_id' => $tenantId];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " ORDER BY node_type, name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO asset_nodes (
                tenant_id,
                parent_id,
                node_type,
                name,
                code,
                description,
                status,
                sort_order
            ) VALUES (
                :tenant_id,
                :parent_id,
                :node_type,
                :name,
                :code,
                :description,
                :status,
                :sort_order
            )
        ");

        $stmt->execute([
            'tenant_id'   => $data['tenant_id'],
            'parent_id'   => $data['parent_id'],
            'node_type'   => $data['node_type'],
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'sort_order'  => $data['sort_order'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data): void
    {
        $stmt = $this->db->prepare("
            UPDATE asset_nodes
            SET
                parent_id = :parent_id,
                node_type = :node_type,
                name = :name,
                code = :code,
                description = :description,
                status = :status,
                sort_order = :sort_order
            WHERE tenant_id = :tenant_id
              AND id = :id
        ");

        $stmt->execute([
            'tenant_id'   => $tenantId,
            'id'          => $id,
            'parent_id'   => $data['parent_id'],
            'node_type'   => $data['node_type'],
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'sort_order'  => $data['sort_order'],
        ]);
    }

    public function archive(int $tenantId, int $id): void
    {
        $stmt = $this->db->prepare("
            UPDATE asset_nodes
            SET status = 'archived'
            WHERE tenant_id = :tenant_id
              AND id = :id
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $id,
        ]);
    }

    public function hasChildren(int $tenantId, int $id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM asset_nodes
            WHERE tenant_id = :tenant_id
              AND parent_id = :id
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'id'        => $id,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
