<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class AiEngineerRepository
{
    /**
     * Top N machines by number of fault reports in the last N days.
     */
    public function topFaultyMachines(int $limit = 10, int $days = 365): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT m.id, m.name, m.location,
                    COUNT(fr.id) AS fault_count,
                    MAX(fr.created_at) AS last_fault_at
             FROM machines m
             JOIN fault_reports fr ON fr.machine_id = m.id
             WHERE m.is_deleted = 0
               AND fr.is_deleted = 0
               AND fr.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY m.id, m.name, m.location
             ORDER BY fault_count DESC
             LIMIT ?"
        );
        $stmt->execute([$days, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Mean Time Between Failures (MTBF) per machine in hours.
     * Calculated as: total observation period / number of faults.
     */
    public function mtbfPerMachine(): array
    {
        $sql = "SELECT m.id, m.name,
                       COUNT(fr.id) AS fault_count,
                       MIN(fr.created_at) AS first_fault_at,
                       MAX(fr.created_at) AS last_fault_at,
                       CASE
                         WHEN COUNT(fr.id) > 1
                         THEN TIMESTAMPDIFF(HOUR, MIN(fr.created_at), MAX(fr.created_at)) / (COUNT(fr.id) - 1)
                         ELSE NULL
                       END AS mtbf_hours
                FROM machines m
                JOIN fault_reports fr ON fr.machine_id = m.id AND fr.is_deleted = 0
                WHERE m.is_deleted = 0
                GROUP BY m.id, m.name
                HAVING fault_count >= 2
                ORDER BY mtbf_hours ASC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Mean Time To Repair (MTTR) per machine in hours.
     * Uses work_orders.actual_hours as repair time.
     */
    public function mttrPerMachine(): array
    {
        $sql = "SELECT m.id, m.name,
                       COUNT(wo.id) AS repair_count,
                       ROUND(AVG(wo.actual_hours), 2) AS mttr_hours,
                       ROUND(SUM(wo.actual_hours), 2) AS total_hours
                FROM machines m
                JOIN fault_reports fr ON fr.machine_id = m.id AND fr.is_deleted = 0
                JOIN work_orders wo ON wo.fault_report_id = fr.id AND wo.is_deleted = 0
                    AND wo.status IN ('completed','closed')
                    AND wo.actual_hours IS NOT NULL
                WHERE m.is_deleted = 0
                GROUP BY m.id, m.name
                ORDER BY mttr_hours DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Trend analysis: machines whose fault rate increased in the last 30 days vs previous 30 days.
     */
    public function trendingFaults(): array
    {
        $sql = "SELECT m.id, m.name,
                       SUM(CASE WHEN fr.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS faults_last_30,
                       SUM(CASE WHEN fr.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
                                 AND fr.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS faults_prev_30
                FROM machines m
                JOIN fault_reports fr ON fr.machine_id = m.id AND fr.is_deleted = 0
                WHERE m.is_deleted = 0
                  AND fr.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
                GROUP BY m.id, m.name
                HAVING faults_last_30 > faults_prev_30
                ORDER BY (faults_last_30 - faults_prev_30) DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Generate text-based recommendations.
     */
    public function recommendations(): array
    {
        $recs = [];

        // Machines with >= 3 faults in last 30 days
        $stmt = Database::pdo()->prepare(
            "SELECT m.id, m.name, COUNT(fr.id) AS cnt
             FROM machines m
             JOIN fault_reports fr ON fr.machine_id = m.id AND fr.is_deleted = 0
             WHERE m.is_deleted = 0 AND fr.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY m.id, m.name
             HAVING cnt >= 3
             ORDER BY cnt DESC"
        );
        $stmt->execute();
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $recs[] = [
                'machine_id'   => $row['id'],
                'machine_name' => $row['name'],
                'type'         => 'frequent_faults',
                'severity'     => $row['cnt'] >= 5 ? 'critical' : 'high',
                'message'      => "Maskin \"{$row['name']}\" har haft {$row['cnt']} felanmälningar de senaste 30 dagarna. Överväg förebyggande underhåll.",
            ];
        }

        // Machines with no PM schedule but multiple faults
        $stmt = Database::pdo()->prepare(
            "SELECT m.id, m.name, COUNT(fr.id) AS cnt
             FROM machines m
             JOIN fault_reports fr ON fr.machine_id = m.id AND fr.is_deleted = 0
             LEFT JOIN preventive_maintenance_schedules pms ON pms.machine_id = m.id AND pms.is_deleted = 0 AND pms.status = 'active'
             WHERE m.is_deleted = 0 AND pms.id IS NULL
             GROUP BY m.id, m.name
             HAVING cnt >= 2
             ORDER BY cnt DESC"
        );
        $stmt->execute();
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $recs[] = [
                'machine_id'   => $row['id'],
                'machine_name' => $row['name'],
                'type'         => 'no_pm_schedule',
                'severity'     => 'normal',
                'message'      => "Maskin \"{$row['name']}\" saknar aktivt FU-schema men har {$row['cnt']} registrerade fel. Skapa ett förebyggande underhållsschema.",
            ];
        }

        // Machines with overdue PM
        $stmt = Database::pdo()->prepare(
            "SELECT m.id, m.name, MIN(pms.next_due_at) AS overdue_since
             FROM machines m
             JOIN preventive_maintenance_schedules pms ON pms.machine_id = m.id AND pms.is_deleted = 0 AND pms.status = 'active'
             WHERE m.is_deleted = 0 AND pms.next_due_at < NOW()
             GROUP BY m.id, m.name
             ORDER BY overdue_since ASC"
        );
        $stmt->execute();
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $recs[] = [
                'machine_id'   => $row['id'],
                'machine_name' => $row['name'],
                'type'         => 'overdue_pm',
                'severity'     => 'high',
                'message'      => "Maskin \"{$row['name']}\" har ett förfallet FU-schema (förfallet sedan {$row['overdue_since']}). Utför underhåll snarast.",
            ];
        }

        return $recs;
    }

    /**
     * Health report for a single machine.
     */
    public function machineHealth(int $machineId): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM machines WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$machineId]);
        $machine = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$machine) {
            return null;
        }

        // Total faults
        $stmt = Database::pdo()->prepare("SELECT COUNT(*) FROM fault_reports WHERE machine_id = ? AND is_deleted = 0");
        $stmt->execute([$machineId]);
        $totalFaults = (int) $stmt->fetchColumn();

        // Faults last 30 days
        $stmt = Database::pdo()->prepare("SELECT COUNT(*) FROM fault_reports WHERE machine_id = ? AND is_deleted = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute([$machineId]);
        $faults30 = (int) $stmt->fetchColumn();

        // MTBF
        $stmt = Database::pdo()->prepare(
            "SELECT CASE WHEN COUNT(*) > 1
                    THEN TIMESTAMPDIFF(HOUR, MIN(created_at), MAX(created_at)) / (COUNT(*) - 1)
                    ELSE NULL END AS mtbf
             FROM fault_reports WHERE machine_id = ? AND is_deleted = 0"
        );
        $stmt->execute([$machineId]);
        $mtbf = $stmt->fetchColumn();

        // MTTR
        $stmt = Database::pdo()->prepare(
            "SELECT ROUND(AVG(wo.actual_hours), 2)
             FROM work_orders wo
             JOIN fault_reports fr ON wo.fault_report_id = fr.id
             WHERE fr.machine_id = ? AND wo.is_deleted = 0 AND wo.actual_hours IS NOT NULL
               AND wo.status IN ('completed','closed')"
        );
        $stmt->execute([$machineId]);
        $mttr = $stmt->fetchColumn();

        // Total repair cost
        $stmt = Database::pdo()->prepare(
            "SELECT ROUND(SUM(COALESCE(wo.total_cost, 0)), 2)
             FROM work_orders wo
             JOIN fault_reports fr ON wo.fault_report_id = fr.id
             WHERE fr.machine_id = ? AND wo.is_deleted = 0"
        );
        $stmt->execute([$machineId]);
        $totalCost = $stmt->fetchColumn();

        // Active PM schedules
        $stmt = Database::pdo()->prepare(
            "SELECT * FROM preventive_maintenance_schedules
             WHERE machine_id = ? AND is_deleted = 0 AND status = 'active'
             ORDER BY next_due_at ASC"
        );
        $stmt->execute([$machineId]);
        $pmSchedules = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Recent faults
        $stmt = Database::pdo()->prepare(
            "SELECT fr.*, u.full_name AS reported_by_name
             FROM fault_reports fr
             LEFT JOIN users u ON fr.reported_by = u.id
             WHERE fr.machine_id = ? AND fr.is_deleted = 0
             ORDER BY fr.created_at DESC LIMIT 10"
        );
        $stmt->execute([$machineId]);
        $recentFaults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'machine'      => $machine,
            'totalFaults'  => $totalFaults,
            'faults30'     => $faults30,
            'mtbf'         => $mtbf !== false ? round((float) $mtbf, 1) : null,
            'mttr'         => $mttr !== false ? (float) $mttr : null,
            'totalCost'    => $totalCost !== false ? (float) $totalCost : 0.0,
            'pmSchedules'  => $pmSchedules,
            'recentFaults' => $recentFaults,
        ];
    }

    /**
     * Summary statistics for the AI dashboard.
     */
    public function dashboardStats(): array
    {
        $pdo = Database::pdo();

        $totalFaults = (int) $pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0")->fetchColumn();
        $openFaults  = (int) $pdo->query("SELECT COUNT(*) FROM fault_reports WHERE is_deleted = 0 AND status NOT IN ('resolved','closed')")->fetchColumn();
        $totalWOs    = (int) $pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0")->fetchColumn();
        $activeWOs   = (int) $pdo->query("SELECT COUNT(*) FROM work_orders WHERE is_deleted = 0 AND status IN ('assigned','in_progress')")->fetchColumn();
        $activePM    = (int) $pdo->query("SELECT COUNT(*) FROM preventive_maintenance_schedules WHERE is_deleted = 0 AND status = 'active'")->fetchColumn();
        $overduePM   = (int) $pdo->query("SELECT COUNT(*) FROM preventive_maintenance_schedules WHERE is_deleted = 0 AND status = 'active' AND next_due_at < NOW()")->fetchColumn();

        return compact('totalFaults', 'openFaults', 'totalWOs', 'activeWOs', 'activePM', 'overduePM');
    }
}
