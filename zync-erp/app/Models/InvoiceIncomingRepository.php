<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class InvoiceIncomingRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            "SELECT ii.*, s.name AS supplier_name
             FROM invoices_incoming ii
             LEFT JOIN suppliers s ON ii.supplier_id = s.id
             WHERE ii.is_deleted = 0
             ORDER BY ii.created_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT ii.*, s.name AS supplier_name, s.email AS supplier_email,
                    u.full_name AS created_by_name, ua.full_name AS approved_by_name,
                    po.order_number AS po_number
             FROM invoices_incoming ii
             LEFT JOIN suppliers s ON ii.supplier_id = s.id
             LEFT JOIN users u ON ii.created_by = u.id
             LEFT JOIN users ua ON ii.approved_by = ua.id
             LEFT JOIN purchase_orders po ON ii.purchase_order_id = po.id
             WHERE ii.id = ? AND ii.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->nextNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO invoices_incoming 
             (invoice_number, internal_number, supplier_id, purchase_order_id, status, invoice_date, due_date,
              payment_terms, currency, reference, subtotal, vat_amount, total_amount, remaining_amount,
              notes, internal_notes, file_path, created_by)
             VALUES (?, ?, ?, ?, 'registered', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $vat = (float) ($data['vat_amount'] ?? 0);
        $total = (float) ($data['total_amount'] ?? $subtotal + $vat);
        $stmt->execute([
            $data['invoice_number'],
            $number,
            $data['supplier_id'],
            $data['purchase_order_id'] ?: null,
            $data['invoice_date'],
            $data['due_date'],
            $data['payment_terms'] ?? '30 dagar netto',
            $data['currency'] ?? 'SEK',
            $data['reference'] ?? null,
            $subtotal,
            $vat,
            $total,
            $total,
            $data['notes'] ?? null,
            $data['internal_notes'] ?? null,
            $data['file_path'] ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $vat = (float) ($data['vat_amount'] ?? 0);
        $total = (float) ($data['total_amount'] ?? $subtotal + $vat);
        $inv = $this->find($id);
        $paid = (float) ($inv['paid_amount'] ?? 0);

        $stmt = Database::pdo()->prepare(
            "UPDATE invoices_incoming SET
             invoice_number = ?, supplier_id = ?, purchase_order_id = ?,
             invoice_date = ?, due_date = ?, payment_terms = ?, reference = ?,
             subtotal = ?, vat_amount = ?, total_amount = ?, remaining_amount = ?,
             notes = ?, internal_notes = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['invoice_number'],
            $data['supplier_id'],
            $data['purchase_order_id'] ?: null,
            $data['invoice_date'],
            $data['due_date'],
            $data['payment_terms'] ?? '30 dagar netto',
            $data['reference'] ?? null,
            $subtotal, $vat, $total, $total - $paid,
            $data['notes'] ?? null,
            $data['internal_notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare("UPDATE invoices_incoming SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function updateStatus(int $id, string $status, ?int $userId = null): void
    {
        $extra = '';
        $params = [$status];
        if ($status === 'approved') {
            $extra = ", approved_by = ?, approved_at = NOW()";
            $params[] = $userId;
        }
        if ($status === 'paid') {
            $extra .= ", paid_at = NOW()";
        }
        $params[] = $id;
        Database::pdo()->prepare("UPDATE invoices_incoming SET status = ? $extra WHERE id = ?")->execute($params);
    }

    public function registerPayment(int $invoiceId, float $amount, string $method, string $date, ?string $ref, int $userId): void
    {
        $payNum = 'POUT-' . date('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        
        Database::pdo()->prepare(
            "INSERT INTO payments (payment_number, payment_type, invoice_incoming_id, amount, payment_date, payment_method, bank_reference, created_by)
             VALUES (?, 'outgoing', ?, ?, ?, ?, ?, ?)"
        )->execute([$payNum, $invoiceId, $amount, $date, $method, $ref, $userId]);

        Database::pdo()->prepare(
            "UPDATE invoices_incoming SET 
             paid_amount = paid_amount + ?, 
             remaining_amount = remaining_amount - ?,
             status = IF(remaining_amount - ? <= 0, 'paid', 'payment_pending'),
             paid_at = IF(remaining_amount - ? <= 0, NOW(), paid_at)
             WHERE id = ?"
        )->execute([$amount, $amount, $amount, $amount, $invoiceId]);
    }

    // --- Rader ---
    public function getLines(int $invoiceId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT l.*, a.article_number, a.name AS article_name,
                    coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code, cc.name AS cost_center_name
             FROM invoice_incoming_lines l
             LEFT JOIN articles a ON l.article_id = a.id
             LEFT JOIN chart_of_accounts coa ON l.account_id = coa.id
             LEFT JOIN cost_centers cc ON l.cost_center_id = cc.id
             WHERE l.invoice_id = ?"
        );
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addLine(int $invoiceId, array $data): void
    {
        $qty = (float) ($data['quantity'] ?? 1);
        $price = (float) ($data['unit_price'] ?? 0);
        $lineTotal = round($qty * $price, 2);

        Database::pdo()->prepare(
            "INSERT INTO invoice_incoming_lines 
             (invoice_id, article_id, description, quantity, unit, unit_price, vat_rate, line_total, account_id, cost_center_id, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        )->execute([
            $invoiceId,
            $data['article_id'] ?: null,
            $data['description'],
            $qty,
            $data['unit'] ?? 'st',
            $price,
            $data['vat_rate'] ?? 25,
            $lineTotal,
            $data['account_id'] ?: null,
            $data['cost_center_id'] ?: null,
            $data['notes'] ?? null,
        ]);
        $this->recalcTotals($invoiceId);
    }

    public function removeLine(int $invoiceId, int $lineId): void
    {
        Database::pdo()->prepare("DELETE FROM invoice_incoming_lines WHERE id = ? AND invoice_id = ?")->execute([$lineId, $invoiceId]);
        $this->recalcTotals($invoiceId);
    }

    public function recalcTotals(int $invoiceId): void
    {
        $stmt = Database::pdo()->prepare(
            "SELECT COALESCE(SUM(line_total), 0) AS subtotal,
                    COALESCE(SUM(line_total * vat_rate / 100), 0) AS vat
             FROM invoice_incoming_lines WHERE invoice_id = ?"
        );
        $stmt->execute([$invoiceId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $subtotal = (float) $r['subtotal'];
        $vat = round((float) $r['vat'], 2);
        $total = round($subtotal + $vat, 2);

        $inv = $this->find($invoiceId);
        $paid = (float) ($inv['paid_amount'] ?? 0);

        Database::pdo()->prepare(
            "UPDATE invoices_incoming SET subtotal = ?, vat_amount = ?, total_amount = ?, remaining_amount = ? WHERE id = ?"
        )->execute([$subtotal, $vat, $total, $total - $paid, $invoiceId]);
    }

    // --- Statistik ---
    public function stats(): array
    {
        $pdo = Database::pdo();
        $s = [];
        $s['total_payable'] = (float) $pdo->query("SELECT COALESCE(SUM(remaining_amount), 0) FROM invoices_incoming WHERE status IN ('registered','approved','payment_pending') AND is_deleted = 0")->fetchColumn();
        $s['overdue_count'] = (int) $pdo->query("SELECT COUNT(*) FROM invoices_incoming WHERE status IN ('registered','approved','payment_pending') AND due_date < CURDATE() AND is_deleted = 0")->fetchColumn();
        $s['overdue_amount'] = (float) $pdo->query("SELECT COALESCE(SUM(remaining_amount), 0) FROM invoices_incoming WHERE status IN ('registered','approved','payment_pending') AND due_date < CURDATE() AND is_deleted = 0")->fetchColumn();
        $s['month_received'] = (float) $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM invoices_incoming WHERE MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE()) AND is_deleted = 0")->fetchColumn();
        $s['unapproved_count'] = (int) $pdo->query("SELECT COUNT(*) FROM invoices_incoming WHERE status = 'registered' AND is_deleted = 0")->fetchColumn();
        return $s;
    }

    private function nextNumber(): string
    {
        $year = date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT internal_number FROM invoices_incoming WHERE internal_number LIKE ? ORDER BY internal_number DESC LIMIT 1"
        );
        $stmt->execute(['LF' . $year . '%']);
        $last = $stmt->fetchColumn();
        $seq = $last ? (int) substr((string) $last, 6) + 1 : 1;
        return 'LF' . $year . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
