<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PreventiveMaintenanceRepository
{
    // ─── Schedules ───────────────────────────────────────────────────────

    public function allSchedules(): array
    {
        $sql = "SELECT s.*,
                       e.name AS equipment_name,
                       m.name AS machine_name,
                       u.full_name AS assigned_to_name
                FROM preventive_maintenance_schedules s
                LEFT JOIN equipment e ON s.equipment_id = e.id
                LEFT JOIN machines m ON s.machine_id = m.id
                LEFT JOIN users u ON s.assigned_to = u.id
                WHERE s.is_deleted = 0
                ORDER BY s.next_due_at ASC, s.title ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findSchedule(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT s.*,
                    e.name AS equipment_name,
                    m.name AS machine_name,
                    u.full_name AS assigned_to_name,
                    uc.full_name AS created_by_name
             FROM preventive_maintenance_schedules s
             LEFT JOIN equipment e ON s.equipment_id = e.id
             LEFT JOIN machines m ON s.machine_id = m.id
             LEFT JOIN users u ON s.assigned_to = u.id
             LEFT JOIN users uc ON s.created_by = uc.id
             WHERE s.id = ? AND s.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createSchedule(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO preventive_maintenance_schedules
             (title, description, equipment_id, machine_id, interval_type, interval_value,
              last_performed_at, next_due_at, priority, assigned_to, checklist, status, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['equipment_id'] ?: null,
            $data['machine_id'] ?: null,
            $data['interval_type'] ?? 'monthly',
            (int) ($data['interval_value'] ?? 1),
            $data['last_performed_at'] ?: null,
            $data['next_due_at'] ?: null,
            $data['priority'] ?? 'normal',
            $data['assigned_to'] ?: null,
            isset($data['checklist']) && is_array($data['checklist']) ? json_encode($data['checklist']) : null,
            $data['status'] ?? 'active',
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateSchedule(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE preventive_maintenance_schedules SET
                title            = ?,
                description      = ?,
                equipment_id     = ?,
                machine_id       = ?,
                interval_type    = ?,
                interval_value   = ?,
                last_performed_at = ?,
                next_due_at      = ?,
                priority         = ?,
                assigned_to      = ?,
                checklist        = ?,
                status           = ?
             WHERE id = ? AND is_deleted = 0"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['equipment_id'] ?: null,
            $data['machine_id'] ?: null,
            $data['interval_type'] ?? 'monthly',
            (int) ($data['interval_value'] ?? 1),
            $data['last_performed_at'] ?: null,
            $data['next_due_at'] ?: null,
            $data['priority'] ?? 'normal',
            $data['assigned_to'] ?: null,
            isset($data['checklist']) && is_array($data['checklist']) ? json_encode($data['checklist']) : null,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function deleteSchedule(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE preventive_maintenance_schedules SET is_deleted = 1 WHERE id = ?"
        );
        $stmt->execute([$id]);
    }

    /**
     * Get upcoming schedules for the calendar view (next N days).
     */
    public function getUpcoming(int $days = 60): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT s.*,
                    e.name AS equipment_name,
                    m.name AS machine_name,
                    u.full_name AS assigned_to_name
             FROM preventive_maintenance_schedules s
             LEFT JOIN equipment e ON s.equipment_id = e.id
             LEFT JOIN machines m ON s.machine_id = m.id
             LEFT JOIN users u ON s.assigned_to = u.id
             WHERE s.is_deleted = 0 AND s.status = 'active'
               AND s.next_due_at IS NOT NULL
               AND s.next_due_at <= DATE_ADD(NOW(), INTERVAL ? DAY)
             ORDER BY s.next_due_at ASC"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ─── Logs ────────────────────────────────────────────────────────────

    public function getLogsForSchedule(int $scheduleId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT l.*, u.full_name AS performed_by_name, wo.wo_number
             FROM preventive_maintenance_logs l
             LEFT JOIN users u ON l.performed_by = u.id
             LEFT JOIN work_orders wo ON l.work_order_id = wo.id
             WHERE l.schedule_id = ? AND l.is_deleted = 0
             ORDER BY l.performed_at DESC"
        );
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function logPerformance(int $scheduleId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO preventive_maintenance_logs
             (schedule_id, performed_at, performed_by, notes, work_order_id, created_by)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $scheduleId,
            $data['performed_at'],
            $data['performed_by'] ?? null,
            $data['notes'] ?? null,
            $data['work_order_id'] ?: null,
            $data['performed_by'] ?? null,
        ]);
        $logId = (int) Database::pdo()->lastInsertId();

        // Update last_performed_at and calculate next_due_at
        $schedule = $this->findSchedule($scheduleId);
        if ($schedule) {
            $nextDue = $this->calculateNextDue($data['performed_at'], $schedule['interval_type'], (int) $schedule['interval_value']);
            Database::pdo()->prepare(
                "UPDATE preventive_maintenance_schedules SET last_performed_at = ?, next_due_at = ? WHERE id = ?"
            )->execute([$data['performed_at'], $nextDue, $scheduleId]);
        }

        return $logId;
    }

    public function generateWorkOrder(int $scheduleId, int $createdBy): int
    {
        $schedule = $this->findSchedule($scheduleId);
        if (!$schedule) {
            throw new \RuntimeException('Schema hittades inte.');
        }

        $pdo = Database::pdo();
        $woNumber = 'AO-FU-' . date('Ymd') . '-' . str_pad((string) $scheduleId, 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare(
            "INSERT INTO work_orders
             (wo_number, title, description, equipment_id, priority, status, created_by)
             VALUES (?, ?, ?, ?, ?, 'draft', ?)"
        );
        $stmt->execute([
            $woNumber,
            'FU: ' . $schedule['title'],
            $schedule['description'],
            $schedule['equipment_id'],
            $schedule['priority'],
            $createdBy,
        ]);
        return (int) $pdo->lastInsertId();
    }

    private function calculateNextDue(string $lastPerformed, string $intervalType, int $intervalValue): string
    {
        $dt = new \DateTime($lastPerformed);
        match ($intervalType) {
            'daily'   => $dt->modify("+{$intervalValue} day"),
            'weekly'  => $dt->modify("+{$intervalValue} week"),
            'monthly' => $dt->modify("+{$intervalValue} month"),
            'yearly'  => $dt->modify("+{$intervalValue} year"),
            default   => $dt->modify("+{$intervalValue} month"),
        };
        return $dt->format('Y-m-d H:i:s');
    }
}
