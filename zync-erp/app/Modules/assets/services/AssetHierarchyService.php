<?php

namespace Modules\Assets\Services;

use Modules\Assets\Repositories\AssetNodeRepository;
use RuntimeException;

class AssetHierarchyService
{
    private const ALLOWED_PARENT_MAP = [
        'site'      => null,
        'area'      => 'site',
        'line'      => 'area',
        'machine'   => 'line',
        'component' => 'machine',
    ];

    public function __construct(private AssetNodeRepository $repository)
    {
    }

    public function validateNode(int $tenantId, array $data, ?int $currentId = null): void
    {
        $nodeType = $data['node_type'];
        $parentId = $data['parent_id'];

        if (!isset(self::ALLOWED_PARENT_MAP[$nodeType])) {
            throw new RuntimeException('Ogiltig nodtyp.');
        }

        $expectedParentType = self::ALLOWED_PARENT_MAP[$nodeType];

        if ($expectedParentType === null && $parentId !== null) {
            throw new RuntimeException('Site får inte ha någon parent.');
        }

        if ($expectedParentType !== null && $parentId === null) {
            throw new RuntimeException("{$nodeType} måste ha en parent.");
        }

        if ($parentId !== null) {
            $parent = $this->repository->findById($tenantId, (int) $parentId);

            if (!$parent) {
                throw new RuntimeException('Vald parent finns inte.');
            }

            if ($parent['node_type'] !== $expectedParentType) {
                throw new RuntimeException(
                    sprintf(
                        '%s måste ligga under %s, inte under %s.',
                        $nodeType,
                        $expectedParentType,
                        $parent['node_type']
                    )
                );
            }

            if ($currentId !== null && (int) $parent['id'] === $currentId) {
                throw new RuntimeException('En nod kan inte vara sin egen parent.');
            }
        }
    }
}
