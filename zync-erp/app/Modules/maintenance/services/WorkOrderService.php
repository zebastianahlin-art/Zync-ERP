<?php

namespace Modules\Maintenance\Services;

use Modules\Maintenance\Repositories\WorkOrderRepository;
use RuntimeException;

class WorkOrderService
{
    private const VALID_TYPES = ['corrective', 'preventive', 'inspection', 'emergency'];
    private const VALID_PRIORITIES = ['low', 'medium', 'high', 'critical'];
    private const VALID_STATUSES = ['reported', 'approved', 'planned', 'in_progress', 'completed', 'closed', 'cancelled'];

    private const ALLOWED_STATUS_TRANSITIONS = [
        'reported'    => ['approved', 'cancelled'],
        'approved'    => ['planned', 'cancelled'],
        'planned'     => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
        'completed'   => ['closed'],
        'closed'      => [],
        'cancelled'   => [],
    ];

    public function __construct(private WorkOrderRepository $repository)
    {
    }

    public function validateCreateData(array $data): array
    {
        $errors = [];

        if ((int) ($data['asset_node_id'] ?? 0) <= 0) {
            $errors[] = 'Asset är obligatoriskt.';
        }

        if (trim((string) ($data['title'] ?? '')) === '') {
            $errors[] = 'Titel är obligatorisk.';
        }

        if (!in_array($data['type'] ?? '', self::VALID_TYPES, true)) {
            $errors[] = 'Ogiltig typ.';
        }

        if (!in_array($data['priority'] ?? '', self::VALID_PRIORITIES, true)) {
            $errors[] = 'Ogiltig prioritet.';
        }

        if (!in_array($data['status'] ?? '', self::VALID_STATUSES, true)) {
            $errors[] = 'Ogiltig status.';
        }

        return $errors;
    }

    public function validateMaterialData(array $data): array
    {
        $errors = [];

        if ((int) ($data['article_id'] ?? 0) <= 0) {
            $errors[] = 'Artikel är obligatorisk.';
        }

        if ((int) ($data['warehouse_id'] ?? 0) <= 0) {
            $errors[] = 'Lager är obligatoriskt.';
        }

        if ((float) ($data['planned_quantity'] ?? 0) < 0) {
            $errors[] = 'Planerad kvantitet kan inte vara negativ.';
        }

        if ((float) ($data['issued_quantity'] ?? 0) < 0) {
            $errors[] = 'Uttagen kvantitet kan inte vara negativ.';
        }

        if ((float) ($data['unit_cost'] ?? 0) < 0) {
            $errors[] = 'Styckkostnad kan inte vara negativ.';
        }

        return $errors;
    }

    public function validateStatusChange(string $currentStatus, string $newStatus): void
    {
        if (!in_array($newStatus, self::VALID_STATUSES, true)) {
            throw new RuntimeException('Ogiltig status.');
        }

        $allowed = self::ALLOWED_STATUS_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException("Statusbyte från {$currentStatus} till {$newStatus} är inte tillåtet.");
        }
    }

    public function getStatusTimestamps(string $newStatus): array
    {
        $now = date('Y-m-d H:i:s');

        return match ($newStatus) {
            'in_progress' => ['started_at' => $now],
            'completed'   => ['completed_at' => $now],
            'closed'      => ['closed_at' => $now],
            default       => [],
        };
    }
}
