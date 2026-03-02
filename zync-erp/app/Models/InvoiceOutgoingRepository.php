<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class InvoiceOutgoingRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            "SELECT io.*, c.name AS customer_name
             FROM invoices_outgoing io
             LEFT JOIN customers c ON io.customer_id = c.id
             WHERE io.is_deleted = 0
             ORDER BY io.created_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT io.*, c.name AS customer_name, c.email AS customer_email,
                    c.address AS customer_address, c.city AS customer_city,
                    c.postal_code AS customer_postal_code,
                    u.full_name AS created_by_name
             FROM invoices_outgoing io
             LEFT JOIN customers c ON io.customer_id = c.id
             LEFT JOIN users u ON io.created_by = u.id
             WHERE io.id = ? AND io.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->nextNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO invoices_outgoing 
             (invoice_number, customer_id, status, invoice_date, due_date, payment_terms, currency,
              reference, our_reference, your_reference, delivery_date, notes, internal_notes, ocr_number, created_by)
             VALUES (?, ?, 'draft', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['customer_id'],
            $data['invoice_date'],
            $data['due_date'],
            $data['payment_terms'] ?? '30 dagar netto',
            $data['currency'] ?? 'SEK',
            $data['reference'] ?? null,
            $data['our_reference'] ?? null,
            $data['your_reference'] ?? null,
            $data['delivery_date'] ?: null,
            $data['notes'] ?? null,
            $data['internal_notes'] ?? null,
            $data['ocr_number'] ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE invoices_outgoing SET
             customer_id = ?, invoice_date = ?, due_date = ?, payment_terms = ?,
             reference = ?, our_reference = ?, your_reference = ?, delivery_date = ?,
             notes = ?, internal_notes = ?, ocr_number = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['customer_id'],
            $data['invoice_date'],
            $data['due_date'],
            $data['payment_terms'] ?? '30 dagar netto',
            $data['reference'] ?? null,
            $data['our_reference'] ?? null,
            $data['your_reference'] ?? null,
            $data['delivery_date'] ?: null,
            $data['notes'] ?? null,
            $data['internal_notes'] ?? null,
            $data['ocr_number'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare("UPDATE invoices_outgoing SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $extra = '';
        if ($status === 'sent') $extra = ", sent_at = NOW()";
        if ($status === 'paid') $extra = ", paid_at = NOW()";
        Database::pdo()->prepare("UPDATE invoices_outgoing SET status = ? $extra WHERE id = ?")->execute([$status, $id]);
    }

    public function recalcTotals(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            "SELECT COALESCE(SUM(line_total), 0) AS subtotal,
                    COALESCE(SUM(line_total * vat_rate / 100), 0) AS vat
             FROM invoice_outgoing_lines WHERE invoice_id = ?"
        );
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $subtotal = (float) $r['subtotal'];
        $vat = round((float) $r['vat'], 2);
        $total = $subtotal + $vat;
        $rounding = round(round($total) - $total, 2);
        $totalRounded = round($total);

        $inv = $this->find($id);
        $paid = (float) ($inv['paid_amount'] ?? 0);

        Database::pdo()->prepare(
            "UPDATE invoices_outgoing SET subtotal = ?, vat_amount = ?, rounding = ?, total_amount = ?, remaining_amount = ? WHERE id = ?"
        )->execute([$subtotal, $vat, $rounding, $totalRounded, $totalRounded - $paid, $id]);
    }

    // --- Rader ---
    public function getLines(int $invoiceId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT l.*, a.article_number, a.name AS article_name,
                    coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code, cc.name AS cost_center_name
             FROM invoice_outgoing_lines l
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
        $discount = (float) ($data['discount_percent'] ?? 0);
        $lineTotal = round($qty * $price * (1 - $discount / 100), 2);

        $stmt = Database::pdo()->prepare(
            "INSERT INTO invoice_outgoing_lines 
             (invoice_id, article_id, description, quantity, unit, unit_price, discount_percent, vat_rate, line_total, account_id, cost_center_id, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $invoiceId,
            $data['article_id'] ?: null,
            $data['description'],
            $qty,
            $data['unit'] ?? 'st',
            $price,
            $discount,
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
        Database::pdo()->prepare("DELETE FROM invoice_outgoing_lines WHERE id = ? AND invoice_id = ?")->execute([$lineId, $invoiceId]);
        $this->recalcTotals($invoiceId);
    }

    // --- Betalningar ---
    public function registerPayment(int $invoiceId, float $amount, string $method, string $date, ?string $ref, int $userId): void
    {
        $payNum = 'PAY-' . date('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        
        Database::pdo()->prepare(
            "INSERT INTO payments (payment_number, payment_type, invoice_outgoing_id, amount, payment_date, payment_method, bank_reference, created_by)
             VALUES (?, 'incoming', ?, ?, ?, ?, ?, ?)"
        )->execute([$payNum, $invoiceId, $amount, $date, $method, $ref, $userId]);

        Database::pdo()->prepare(
            "UPDATE invoices_outgoing SET 
             paid_amount = paid_amount + ?, 
             remaining_amount = remaining_amount - ?,
             status = IF(remaining_amount - ? <= 0, 'paid', 'partially_paid'),
             paid_at = IF(remaining_amount - ? <= 0, NOW(), paid_at)
             WHERE id = ?"
        )->execute([$amount, $amount, $amount, $amount, $invoiceId]);
    }

    // --- Statistik ---
    public function stats(): array
    {
        $pdo = Database::pdo();
        $s = [];
        $s['total_outstanding'] = (float) $pdo->query("SELECT COALESCE(SUM(remaining_amount), 0) FROM invoices_outgoing WHERE status IN ('sent','partially_paid','overdue') AND is_deleted = 0")->fetchColumn();
        $s['overdue_count'] = (int) $pdo->query("SELECT COUNT(*) FROM invoices_outgoing WHERE status IN ('sent','partially_paid') AND due_date < CURDATE() AND is_deleted = 0")->fetchColumn();
        $s['overdue_amount'] = (float) $pdo->query("SELECT COALESCE(SUM(remaining_amount), 0) FROM invoices_outgoing WHERE status IN ('sent','partially_paid') AND due_date < CURDATE() AND is_deleted = 0")->fetchColumn();
        $s['month_invoiced'] = (float) $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM invoices_outgoing WHERE MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE()) AND is_deleted = 0")->fetchColumn();
        $s['draft_count'] = (int) $pdo->query("SELECT COUNT(*) FROM invoices_outgoing WHERE status = 'draft' AND is_deleted = 0")->fetchColumn();
        return $s;
    }

    public function overdue(): array
    {
        return Database::pdo()->query(
            "SELECT io.*, c.name AS customer_name
             FROM invoices_outgoing io
             LEFT JOIN customers c ON io.customer_id = c.id
             WHERE io.status IN ('sent','partially_paid') AND io.due_date < CURDATE() AND io.is_deleted = 0
             ORDER BY io.due_date ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function nextNumber(): string
    {
        $year = date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT invoice_number FROM invoices_outgoing WHERE invoice_number LIKE ? ORDER BY invoice_number DESC LIMIT 1"
        );
        $stmt->execute(['F' . $year . '%']);
        $last = $stmt->fetchColumn();
        $seq = $last ? (int) substr((string) $last, 5) + 1 : 1;
        return 'F' . $year . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
