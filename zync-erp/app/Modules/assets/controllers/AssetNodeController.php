<?php

namespace Modules\Assets\Controllers;

use Modules\Assets\Repositories\AssetNodeRepository;
use Modules\Assets\Services\AssetHierarchyService;
use PDO;
use RuntimeException;

class AssetNodeController
{
    public function __construct(
        private PDO $db
    ) {
    }

    private function tenantId(): int
    {
        if (!isset($_SESSION['tenant_id'])) {
            throw new RuntimeException('Tenant saknas i session.');
        }

        return (int) $_SESSION['tenant_id'];
    }

    private function repo(): AssetNodeRepository
    {
        return new AssetNodeRepository($this->db);
    }

    private function service(): AssetHierarchyService
    {
        return new AssetHierarchyService($this->repo());
    }

    public function index(): void
    {
        $tenantId = $this->tenantId();
        $tree = $this->repo()->getTreeByTenant($tenantId);

        require __DIR__ . '/../views/asset_nodes/index.php';
    }

    public function create(): void
    {
        $tenantId = $this->tenantId();
        $parents = $this->repo()->getPossibleParents($tenantId);
        $assetNode = null;
        $errors = [];

        require __DIR__ . '/../views/asset_nodes/create.php';
    }

    public function store(): void
    {
        $tenantId = $this->tenantId();

        $data = [
            'tenant_id'   => $tenantId,
            'parent_id'   => ($_POST['parent_id'] ?? '') !== '' ? (int) $_POST['parent_id'] : null,
            'node_type'   => trim((string) ($_POST['node_type'] ?? '')),
            'name'        => trim((string) ($_POST['name'] ?? '')),
            'code'        => trim((string) ($_POST['code'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status'      => trim((string) ($_POST['status'] ?? 'active')),
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        ];

        $errors = $this->validateBasic($data);

        if (empty($errors)) {
            try {
                $this->service()->validateNode($tenantId, $data);
                $this->repo()->create($data);

                header('Location: /assets');
                exit;
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        $parents = $this->repo()->getPossibleParents($tenantId);
        $assetNode = $data;

        require __DIR__ . '/../views/asset_nodes/create.php';
    }

    public function edit(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_GET['id'] ?? 0);

        $assetNode = $this->repo()->findById($tenantId, $id);
        if (!$assetNode) {
            http_response_code(404);
            echo 'Asset node hittades inte.';
            return;
        }

        $parents = $this->repo()->getPossibleParents($tenantId, $id);
        $errors = [];

        require __DIR__ . '/../views/asset_nodes/edit.php';
    }

    public function update(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_POST['id'] ?? 0);

        $existing = $this->repo()->findById($tenantId, $id);
        if (!$existing) {
            http_response_code(404);
            echo 'Asset node hittades inte.';
            return;
        }

        $data = [
            'parent_id'   => ($_POST['parent_id'] ?? '') !== '' ? (int) $_POST['parent_id'] : null,
            'node_type'   => trim((string) ($_POST['node_type'] ?? '')),
            'name'        => trim((string) ($_POST['name'] ?? '')),
            'code'        => trim((string) ($_POST['code'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status'      => trim((string) ($_POST['status'] ?? 'active')),
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        ];

        $errors = $this->validateBasic($data);

        if (empty($errors)) {
            try {
                $this->service()->validateNode($tenantId, $data, $id);
                $this->repo()->update($tenantId, $id, $data);

                header('Location: /assets');
                exit;
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        $assetNode = array_merge($existing, $data);
        $parents = $this->repo()->getPossibleParents($tenantId, $id);

        require __DIR__ . '/../views/asset_nodes/edit.php';
    }

    public function archive(): void
    {
        $tenantId = $this->tenantId();
        $id = (int) ($_POST['id'] ?? 0);

        $node = $this->repo()->findById($tenantId, $id);
        if (!$node) {
            http_response_code(404);
            echo 'Asset node hittades inte.';
            return;
        }

        $this->repo()->archive($tenantId, $id);

        header('Location: /assets');
        exit;
    }

    private function validateBasic(array $data): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors[] = 'Namn är obligatoriskt.';
        }

        if (!in_array($data['node_type'], ['site', 'area', 'line', 'machine', 'component'], true)) {
            $errors[] = 'Ogiltig nodtyp.';
        }

        if (!in_array($data['status'], ['active', 'inactive', 'archived'], true)) {
            $errors[] = 'Ogiltig status.';
        }

        return $errors;
    }
}
