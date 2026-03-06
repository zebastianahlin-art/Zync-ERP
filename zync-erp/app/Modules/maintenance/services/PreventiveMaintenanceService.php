<?php

namespace Modules\Maintenance\Services;

use RuntimeException;

class PreventiveMaintenanceService
{
    private const VALID_INTERVAL_TYPES = ['daily', 'weekly', 'monthly', 'yearly'];
    private const VALID_PRIORITIES = ['low', 'medium', 'high', 'critical'];
    private const VALID_WO_TYPES = ['preventive', 'inspection'];

    public function validateCreateData(array $data): array
    {
        $errors = [];

        if ((int) ($data['asset_node_id'] ?? 0) <= 0) {
            $errors[] = 'Asset är obligatoriskt.';
        }

        if (trim((string) ($data['title'] ?? '')) === '') {
            $errors[] = 'Titel är obligatorisk.';
        }

        if (!in_array($data['interval_type'] ?? '', self::VALID_INTERVAL_TYPES, true)) {
            $errors[] = 'Ogiltig intervalltyp.';
        }

        if ((int) ($data['interval_value'] ?? 0) <= 0) {
            $errors[] = 'Intervallvärde måste vara större än 0.';
        }

        if (empty($data['next_due_at'])) {
            $errors[] = 'Nästa förfallodatum är obligatoriskt.';
        }

        if (!in_array($data['priority'] ?? '', self::VALID_PRIORITIES, true)) {
            $errors[] = 'Ogiltig prioritet.';
        }

        if (!in_array($data['default_work_order_type'] ?? '', self::VALID_WO_TYPES, true)) {
            $errors[] = 'Ogiltig arbetsordertyp.';
        }

        return $errors;
    }

    public function calculateNextDueAt(string $currentDueAt, string $intervalType, int $intervalValue): string
    {
        $date = new \DateTimeImmutable($currentDueAt);

        $date = match ($intervalType) {
            'daily'   => $date->modify('+' . $intervalValue . ' day'),
            'weekly'  => $date->modify('+' . $intervalValue . ' week'),
            'monthly' => $date->modify('+' . $intervalValue . ' month'),
            'yearly'  => $date->modify('+' . $intervalValue . ' year'),
            default   => throw new RuntimeException('Ogiltig intervalltyp.'),
        };

        return $date->format('Y-m-d H:i:s');
    }
}
