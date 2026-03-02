<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PurchaseOrderRepository
{
    public function all(): array
    {
        $sql = "SELECT po.*, s.name AS supplier_name, u.full_name AS buyer_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                LEFT JOIN users u ON po.buyer_id = u.id
                WHERE po.is_deleted = 0
                ORDER BY po.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT po.*, s.name AS supplier_name, s.email AS supplier_email,
                    s.phone AS supplier_phone, s.address AS supplier_address,
                    s.city AS supplier_city, s.postal_code AS supplier_postal_code,
                    s.contact_person AS supplier_contact,
                    u.full_name AS buyer_name, u2.full_name AS created_by_name
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             LEFT JOIN users u ON po.buyer_id = u.id
             LEFT JOIN users u2 ON po.created_by = u2.id
             WHERE po.id = ? AND po.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getLines(int $orderId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT pol.*, a.article_number, a.name AS article_name,
                    coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code, cc.name AS cost_center_name
             FROM purchase_order_lines pol
             LEFT JOIN articles a ON pol.article_id = a.id
             LEFT JOIN chart_of_accounts coa ON pol.account_id = coa.id
             LEFT JOIN cost_centers cc ON pol.cost_center_id = cc.id
             WHERE pol.order_id = ?
             ORDER BY pol.id ASC"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO purchase_orders 
             (order_number, requisition_id, supplier_id, buyer_id, reference,
              delivery_address, delivery_date, payment_terms, currency, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['requisition_id'] ?: null,
            $data['supplier_id'],
            $data['buyer_id'],
            $data['reference'] ?? null,
            $data['delivery_address'] ?? null,
            $data['delivery_date'] ?: null,
            $data['payment_terms'] ?? '30 dagar netto',
            $data['currency'] ?? 'SEK',
            $data['notes'] ?? null,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE purchase_orders 
             SET supplier_id = ?, reference = ?, delivery_address = ?, 
                 delivery_date = ?, payment_terms = ?, currency = ?, notes = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['supplier_id'],
            $data['reference'] ?? null,
            $data['delivery_address'] ?? null,
            $data['delivery_date'] ?: null,
            $data['payment_terms'] ?? '30 dagar netto',
            $data['currency'] ?? 'SEK',
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function addLine(int $orderId, array $data): int
    {
        $qty = (float) ($data['quantity'] ?? 1);
        $price = (float) ($data['unit_price'] ?? 0);
        $vat = (float) ($data['vat_rate'] ?? 25);
        $lineTotal = round($qty * $price, 2);

        $stmt = Database::pdo()->prepare(
            "INSERT INTO purchase_order_lines 
             (order_id, article_id, description, quantity, unit, unit_price, vat_rate, line_total, notes, account_id, cost_center_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $orderId,
            $data['article_id'] ?: null,
            $data['description'],
            $qty,
            $data['unit'] ?? 'st',
            $price,
            $vat,
            $lineTotal,
            $data['notes'] ?? null,
            $data['account_id'] ?: null,
            $data['cost_center_id'] ?: null,
        ]);
        $this->recalcTotals($orderId);
        return (int) Database::pdo()->lastInsertId();
    }

    public function removeLine(int $lineId, int $orderId): void
    {
        $stmt = Database::pdo()->prepare("DELETE FROM purchase_order_lines WHERE id = ? AND order_id = ?");
        $stmt->execute([$lineId, $orderId]);
        $this->recalcTotals($orderId);
    }

    public function updateStatus(int $id, string $status): void
    {
        $extra = '';
        if ($status === 'sent') {
            $extra = ', sent_at = NOW()';
        }
        $stmt = Database::pdo()->prepare("UPDATE purchase_orders SET status = ?{$extra} WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE purchase_orders SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function createFromRequisition(int $requisitionId, int $buyerId): ?int
    {
        $reqRepo = new PurchaseRequisitionRepository();
        $req = $reqRepo->find($requisitionId);
        if (!$req) return null;

        $lines = $reqRepo->getLines($requisitionId);
        if (empty($lines)) return null;

        // Gruppera per leverantör — ta första leverantören om blandade
        $supplierId = null;
        foreach ($lines as $line) {
            if (!empty($line['supplier_id'])) {
                $supplierId = (int) $line['supplier_id'];
                break;
            }
        }
        if (!$supplierId) return null;

        $orderId = $this->create([
            'requisition_id' => $requisitionId,
            'supplier_id' => $supplierId,
            'buyer_id' => $buyerId,
            'reference' => $req['requisition_number'],
            'created_by' => $buyerId,
        ]);

        foreach ($lines as $line) {
            $this->addLine($orderId, [
                'article_id' => $line['article_id'],
                'description' => $line['description'] ?: ($line['article_name'] ?? 'Artikel'),
                'quantity' => $line['quantity'],
                'unit' => $line['unit'],
                'unit_price' => $line['estimated_price'] ?? 0,
                'vat_rate' => 25,
                'account_id' => $line['account_id'] ?? null,
                'cost_center_id' => $line['cost_center_id'] ?? null,
            ]);
        }

        $reqRepo->updateStatus($requisitionId, 'ordered');
        return $orderId;
    }

    private function recalcTotals(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "SELECT COALESCE(SUM(line_total), 0) AS subtotal,
                    COALESCE(SUM(line_total * vat_rate / 100), 0) AS vat
             FROM purchase_order_lines WHERE order_id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $subtotal = round((float) $row['subtotal'], 2);
        $vat = round((float) $row['vat'], 2);

        $stmt2 = Database::pdo()->prepare(
            "UPDATE purchase_orders SET subtotal = ?, vat_amount = ?, total_amount = ? WHERE id = ?"
        );
        $stmt2->execute([$subtotal, $vat, $subtotal + $vat, $id]);
    }

    private function generateNumber(): string
    {
        $year = date('Y');
        $stmt = Database::pdo()->query(
            "SELECT COUNT(*) FROM purchase_orders WHERE YEAR(created_at) = {$year}"
        );
        $count = (int) $stmt->fetchColumn() + 1;
        return "IO-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
