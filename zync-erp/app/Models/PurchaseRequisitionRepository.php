<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PurchaseRequisitionRepository
{
    public function all(): array
    {
        $sql = "SELECT pr.*, u.full_name AS requested_by_name, 
                       u2.full_name AS approved_by_name, d.name AS department_name
                FROM purchase_requisitions pr
                LEFT JOIN users u ON pr.requested_by = u.id
                LEFT JOIN users u2 ON pr.approved_by = u2.id
                LEFT JOIN departments d ON pr.department_id = d.id
                WHERE pr.is_deleted = 0
                ORDER BY pr.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT pr.*, u.full_name AS requested_by_name,
                    u2.full_name AS approved_by_name, d.name AS department_name
             FROM purchase_requisitions pr
             LEFT JOIN users u ON pr.requested_by = u.id
             LEFT JOIN users u2 ON pr.approved_by = u2.id
             LEFT JOIN departments d ON pr.department_id = d.id
             WHERE pr.id = ? AND pr.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getLines(int $requisitionId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT prl.*, a.article_number, a.name AS article_name, s.name AS supplier_name,
                    coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code, cc.name AS cost_center_name
             FROM purchase_requisition_lines prl
             LEFT JOIN articles a ON prl.article_id = a.id
             LEFT JOIN suppliers s ON prl.supplier_id = s.id
             LEFT JOIN chart_of_accounts coa ON prl.account_id = coa.id
             LEFT JOIN cost_centers cc ON prl.cost_center_id = cc.id
             WHERE prl.requisition_id = ?
             ORDER BY prl.id ASC"
        );
        $stmt->execute([$requisitionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO purchase_requisitions 
             (requisition_number, title, description, priority, requested_by, department_id, needed_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['title'],
            $data['description'] ?? null,
            $data['priority'] ?? 'normal',
            $data['requested_by'],
            $data['department_id'] ?: null,
            $data['needed_by'] ?: null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function addLine(int $requisitionId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO purchase_requisition_lines 
             (requisition_id, article_id, description, quantity, unit, estimated_price, supplier_id, notes, account_id, cost_center_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $requisitionId,
            $data['article_id'] ?: null,
            $data['description'],
            $data['quantity'] ?? 1,
            $data['unit'] ?? 'st',
            $data['estimated_price'] ?? 0,
            $data['supplier_id'] ?: null,
            $data['notes'] ?? null,
            $data['account_id'] ?: null,
            $data['cost_center_id'] ?: null,
        ]);
        $this->recalcTotal($requisitionId);
        return (int) Database::pdo()->lastInsertId();
    }

    public function removeLine(int $lineId, int $requisitionId): void
    {
        $stmt = Database::pdo()->prepare("DELETE FROM purchase_requisition_lines WHERE id = ? AND requisition_id = ?");
        $stmt->execute([$lineId, $requisitionId]);
        $this->recalcTotal($requisitionId);
    }

    public function updateStatus(int $id, string $status, ?int $userId = null, ?string $reason = null): void
    {
        $extra = '';
        $params = [$status];
        if ($status === 'approved') {
            $extra = ', approved_by = ?, approved_at = NOW()';
            $params[] = $userId;
        }
        if ($status === 'rejected') {
            $extra = ', approved_by = ?, approved_at = NOW(), rejected_reason = ?';
            $params[] = $userId;
            $params[] = $reason;
        }
        $params[] = $id;
        $stmt = Database::pdo()->prepare("UPDATE purchase_requisitions SET status = ?{$extra} WHERE id = ?");
        $stmt->execute($params);
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE purchase_requisitions 
             SET title = ?, description = ?, priority = ?, department_id = ?, needed_by = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['priority'] ?? 'normal',
            $data['department_id'] ?: null,
            $data['needed_by'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE purchase_requisitions SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function recalcTotal(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE purchase_requisitions SET total_amount = 
             (SELECT COALESCE(SUM(quantity * estimated_price), 0) FROM purchase_requisition_lines WHERE requisition_id = ?)
             WHERE id = ?"
        );
        $stmt->execute([$id, $id]);
    }

    public function history(): array
    {
        $sql = "SELECT pr.*, u.full_name AS requested_by_name,
                       u2.full_name AS approved_by_name, d.name AS department_name
                FROM purchase_requisitions pr
                LEFT JOIN users u ON pr.requested_by = u.id
                LEFT JOIN users u2 ON pr.approved_by = u2.id
                LEFT JOIN departments d ON pr.department_id = d.id
                WHERE pr.is_deleted = 0
                  AND pr.status IN ('completed', 'rejected', 'cancelled')
                ORDER BY pr.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function generateNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM purchase_requisitions WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "IA-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
