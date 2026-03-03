<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class LeaveRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Leave Types ──────────────────────────────

    public function allTypes(): array
    {
        $stmt = $this->db->query("SELECT * FROM leave_types ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findType(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM leave_types WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ── Leave Requests ───────────────────────────

    public function allRequests(?string $status = null, ?int $employeeId = null): array
    {
        $sql = "SELECT lr.*, lt.name AS type_name, lt.code AS type_code, lt.color AS type_color,
                    e.first_name, e.last_name, e.employee_number,
                    d.name AS department_name,
                    approver.first_name AS approver_first, approver.last_name AS approver_last
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN employees e ON lr.employee_id = e.id
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN employees approver ON lr.approved_by = approver.id
                WHERE 1=1";
        $params = [];

        if ($status) {
            $sql .= " AND lr.status = ?";
            $params[] = $status;
        }
        if ($employeeId) {
            $sql .= " AND lr.employee_id = ?";
            $params[] = $employeeId;
        }

        $sql .= " ORDER BY lr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findRequest(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT lr.*, lt.name AS type_name, lt.code AS type_code, lt.color AS type_color,
                    e.first_name, e.last_name, e.employee_number
             FROM leave_requests lr
             JOIN leave_types lt ON lr.leave_type_id = lt.id
             JOIN employees e ON lr.employee_id = e.id
             WHERE lr.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createRequest(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, days, reason)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['employee_id'], $data['leave_type_id'],
            $data['start_date'], $data['end_date'],
            $data['days'], $data['reason'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function approveRequest(int $id, int $approvedBy): void
    {
        $stmt = $this->db->prepare("UPDATE leave_requests SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?");
        $stmt->execute([$approvedBy, $id]);
    }

    public function rejectRequest(int $id, int $approvedBy, string $reason): void
    {
        $stmt = $this->db->prepare("UPDATE leave_requests SET status = 'rejected', approved_by = ?, approved_at = NOW(), rejection_reason = ? WHERE id = ?");
        $stmt->execute([$approvedBy, $reason, $id]);
    }

    public function cancelRequest(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE leave_requests SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function deleteRequest(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM leave_requests WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
    }

    /** Remaining leave days for an employee for a given type in current year */
    public function remainingDays(int $employeeId, int $leaveTypeId): ?float
    {
        $type = $this->findType($leaveTypeId);
        if (!$type || $type['max_days_per_year'] === null) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(days), 0) FROM leave_requests 
             WHERE employee_id = ? AND leave_type_id = ? AND status IN ('approved','pending') AND YEAR(start_date) = YEAR(CURDATE())"
        );
        $stmt->execute([$employeeId, $leaveTypeId]);
        $used = (float) $stmt->fetchColumn();

        return (float) $type['max_days_per_year'] - $used;
    }

    // ── Attendance ───────────────────────────────

    public function attendanceForDate(string $date): array
    {
        $stmt = $this->db->prepare(
            "SELECT ar.*, e.first_name, e.last_name, e.employee_number, d.name AS department_name
             FROM attendance_records ar
             JOIN employees e ON ar.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE ar.date = ?
             ORDER BY e.last_name, e.first_name"
        );
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function attendanceForEmployee(int $employeeId, string $fromDate, string $toDate): array
    {
        $stmt = $this->db->prepare(
            "SELECT ar.* FROM attendance_records ar
             WHERE ar.employee_id = ? AND ar.date BETWEEN ? AND ?
             ORDER BY ar.date DESC"
        );
        $stmt->execute([$employeeId, $fromDate, $toDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsertAttendance(array $data): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO attendance_records (employee_id, date, check_in, check_out, status, notes)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE check_in = VALUES(check_in), check_out = VALUES(check_out), status = VALUES(status), notes = VALUES(notes)"
        );
        $stmt->execute([
            $data['employee_id'], $data['date'],
            $data['check_in'] ?? null, $data['check_out'] ?? null,
            $data['status'] ?? 'present', $data['notes'] ?? null,
        ]);
    }

    // ── Stats ────────────────────────────────────

    public function stats(): array
    {
        $s = [];
        $stmt = $this->db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'pending'");
        $s['pending_requests'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'approved' AND end_date >= CURDATE()");
        $s['active_leaves'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM leave_requests WHERE YEAR(created_at) = YEAR(CURDATE())");
        $s['total_this_year'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM attendance_records WHERE date = CURDATE() AND status = 'present'");
        $s['present_today'] = (int) $stmt->fetchColumn();

        return $s;
    }
}
