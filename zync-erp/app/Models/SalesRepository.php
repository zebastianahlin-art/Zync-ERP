<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class SalesRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /* ═══════════════════════════════════════════════════════
     *  KUNDER
     * ═══════════════════════════════════════════════════════ */

    public function allCustomers(?string $status = null, ?string $category = null, ?string $search = null): array
    {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM sales_orders WHERE customer_id = c.id AND is_deleted = 0) AS order_count,
                (SELECT COALESCE(SUM(total_amount),0) FROM sales_orders WHERE customer_id = c.id AND is_deleted = 0 AND status != 'cancelled') AS total_sales,
                (SELECT COALESCE(SUM(remaining_amount),0) FROM invoices_outgoing WHERE customer_id = c.id AND is_deleted = 0 AND status NOT IN ('paid','cancelled','credited')) AS outstanding
                FROM customers c WHERE c.is_deleted = 0";
        $params = [];
        if ($status) { $sql .= " AND c.status = ?"; $params[] = $status; }
        if ($category) { $sql .= " AND c.category = ?"; $params[] = $category; }
        if ($search) { $sql .= " AND (c.name LIKE ? OR c.customer_number LIKE ? OR c.org_number LIKE ?)"; $s = "%$search%"; $params[] = $s; $params[] = $s; $params[] = $s; }
        $sql .= " ORDER BY c.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findCustomer(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createCustomer(array $d): int
    {
        $num = $this->nextCustomerNumber();
        $stmt = $this->db->prepare("INSERT INTO customers (customer_number,name,org_number,vat_number,email,invoice_email,phone,website,address,delivery_address,payment_terms,currency,credit_limit,discount_percent,price_list_id,category,status,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$num,$d['name'],$d['org_number'],$d['vat_number']??null,$d['email'],$d['invoice_email']??null,$d['phone']??null,$d['website']??null,$d['address']??null,$d['delivery_address']??null,$d['payment_terms']??'30 dagar netto',$d['currency']??'SEK',$d['credit_limit']??0,$d['discount_percent']??0,$d['price_list_id']??null,$d['category']??'standard',$d['status']??'active',$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function updateCustomer(int $id, array $d): void
    {
        $stmt = $this->db->prepare("UPDATE customers SET name=?,org_number=?,vat_number=?,email=?,invoice_email=?,phone=?,website=?,address=?,delivery_address=?,payment_terms=?,currency=?,credit_limit=?,discount_percent=?,price_list_id=?,category=?,status=?,notes=? WHERE id=?");
        $stmt->execute([$d['name'],$d['org_number'],$d['vat_number']??null,$d['email'],$d['invoice_email']??null,$d['phone']??null,$d['website']??null,$d['address']??null,$d['delivery_address']??null,$d['payment_terms']??'30 dagar netto',$d['currency']??'SEK',$d['credit_limit']??0,$d['discount_percent']??0,$d['price_list_id']??null,$d['category']??'standard',$d['status']??'active',$d['notes']??null,$id]);
    }

    public function deleteCustomer(int $id): void
    {
        $this->db->prepare("UPDATE customers SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function nextCustomerNumber(): string
    {
        $stmt = $this->db->query("SELECT customer_number FROM customers ORDER BY id DESC LIMIT 1");
        $last = $stmt->fetchColumn();
        $num = $last ? (int)substr($last, 1) + 1 : 1;
        return 'C' . str_pad((string)$num, 5, '0', STR_PAD_LEFT);
    }

    public function customersForDropdown(): array
    {
        return $this->db->query("SELECT id, customer_number, name FROM customers WHERE is_deleted = 0 AND status = 'active' ORDER BY name")->fetchAll();
    }

    /* ═══════════════════════════════════════════════════════
     *  KONTAKTER
     * ═══════════════════════════════════════════════════════ */

    public function contactsByCustomer(int $cid): array
    {
        $stmt = $this->db->prepare("SELECT * FROM customer_contacts WHERE customer_id = ? AND is_deleted = 0 ORDER BY is_primary DESC, last_name");
        $stmt->execute([$cid]);
        return $stmt->fetchAll();
    }

    public function findContact(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM customer_contacts WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createContact(array $d): int
    {
        $stmt = $this->db->prepare("INSERT INTO customer_contacts (customer_id,first_name,last_name,title,email,phone,mobile,department,is_primary,is_invoice_contact,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['customer_id'],$d['first_name'],$d['last_name'],$d['title']??null,$d['email']??null,$d['phone']??null,$d['mobile']??null,$d['department']??null,$d['is_primary']??0,$d['is_invoice_contact']??0,$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function updateContact(int $id, array $d): void
    {
        $stmt = $this->db->prepare("UPDATE customer_contacts SET first_name=?,last_name=?,title=?,email=?,phone=?,mobile=?,department=?,is_primary=?,is_invoice_contact=?,notes=? WHERE id=?");
        $stmt->execute([$d['first_name'],$d['last_name'],$d['title']??null,$d['email']??null,$d['phone']??null,$d['mobile']??null,$d['department']??null,$d['is_primary']??0,$d['is_invoice_contact']??0,$d['notes']??null,$id]);
    }

    public function deleteContact(int $id): void
    {
        $this->db->prepare("UPDATE customer_contacts SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    /* ═══════════════════════════════════════════════════════
     *  PRISLISTOR
     * ═══════════════════════════════════════════════════════ */

    public function allPriceLists(): array
    {
        return $this->db->query("SELECT pl.*, (SELECT COUNT(*) FROM price_list_lines WHERE price_list_id = pl.id) AS line_count FROM price_lists pl ORDER BY pl.is_default DESC, pl.name")->fetchAll();
    }

    public function findPriceList(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM price_lists WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createPriceList(array $d): int
    {
        $stmt = $this->db->prepare("INSERT INTO price_lists (code,name,currency,is_default,valid_from,valid_to,is_active) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$d['code'],$d['name'],$d['currency']??'SEK',$d['is_default']??0,$d['valid_from']??null,$d['valid_to']??null,$d['is_active']??1]);
        return (int)$this->db->lastInsertId();
    }

    public function updatePriceList(int $id, array $d): void
    {
        $stmt = $this->db->prepare("UPDATE price_lists SET code=?,name=?,currency=?,is_default=?,valid_from=?,valid_to=?,is_active=? WHERE id=?");
        $stmt->execute([$d['code'],$d['name'],$d['currency']??'SEK',$d['is_default']??0,$d['valid_from']??null,$d['valid_to']??null,$d['is_active']??1,$id]);
    }

    public function priceListLines(int $plId): array
    {
        $stmt = $this->db->prepare("SELECT pll.*, a.article_number, a.name AS article_name FROM price_list_lines pll JOIN articles a ON a.id = pll.article_id WHERE pll.price_list_id = ? ORDER BY a.article_number");
        $stmt->execute([$plId]);
        return $stmt->fetchAll();
    }

    public function addPriceListLine(array $d): int
    {
        $stmt = $this->db->prepare("INSERT INTO price_list_lines (price_list_id,article_id,unit_price,min_quantity,discount_percent,valid_from,valid_to) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE unit_price=VALUES(unit_price),discount_percent=VALUES(discount_percent),valid_from=VALUES(valid_from),valid_to=VALUES(valid_to)");
        $stmt->execute([$d['price_list_id'],$d['article_id'],$d['unit_price'],$d['min_quantity']??1,$d['discount_percent']??0,$d['valid_from']??null,$d['valid_to']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function removePriceListLine(int $id): void
    {
        $this->db->prepare("DELETE FROM price_list_lines WHERE id = ?")->execute([$id]);
    }

    public function getPrice(int $articleId, int $customerId, float $quantity = 1): array
    {
        // 1. Kundspecifikt
        $stmt = $this->db->prepare("SELECT unit_price, discount_percent, 'customer' AS source FROM customer_prices WHERE customer_id=? AND article_id=? AND min_quantity<=? AND (valid_from IS NULL OR valid_from<=CURDATE()) AND (valid_to IS NULL OR valid_to>=CURDATE()) ORDER BY min_quantity DESC LIMIT 1");
        $stmt->execute([$customerId,$articleId,$quantity]);
        $r = $stmt->fetch();
        if ($r) return $r;
        // 2. Prislista
        $stmt = $this->db->prepare("SELECT pll.unit_price, pll.discount_percent, 'price_list' AS source FROM customers c JOIN price_list_lines pll ON pll.price_list_id=c.price_list_id AND pll.article_id=? AND pll.min_quantity<=? JOIN price_lists pl ON pl.id=pll.price_list_id AND pl.is_active=1 WHERE c.id=? AND (pll.valid_from IS NULL OR pll.valid_from<=CURDATE()) AND (pll.valid_to IS NULL OR pll.valid_to>=CURDATE()) ORDER BY pll.min_quantity DESC LIMIT 1");
        $stmt->execute([$articleId,$quantity,$customerId]);
        $r = $stmt->fetch();
        if ($r) return $r;
        // 3. Standard
        $stmt = $this->db->prepare("SELECT selling_price AS unit_price, 0 AS discount_percent, 'standard' AS source FROM articles WHERE id=?");
        $stmt->execute([$articleId]);
        return $stmt->fetch() ?: ['unit_price'=>0,'discount_percent'=>0,'source'=>'none'];
    }

    public function customerPrices(int $cid): array
    {
        $stmt = $this->db->prepare("SELECT cp.*, a.article_number, a.name AS article_name FROM customer_prices cp JOIN articles a ON a.id=cp.article_id WHERE cp.customer_id=? ORDER BY a.article_number");
        $stmt->execute([$cid]);
        return $stmt->fetchAll();
    }

    public function addCustomerPrice(array $d): int
    {
        $stmt = $this->db->prepare("INSERT INTO customer_prices (customer_id,article_id,unit_price,min_quantity,discount_percent,valid_from,valid_to) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE unit_price=VALUES(unit_price),discount_percent=VALUES(discount_percent),valid_from=VALUES(valid_from),valid_to=VALUES(valid_to)");
        $stmt->execute([$d['customer_id'],$d['article_id'],$d['unit_price'],$d['min_quantity']??1,$d['discount_percent']??0,$d['valid_from']??null,$d['valid_to']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function removeCustomerPrice(int $id): void
    {
        $this->db->prepare("DELETE FROM customer_prices WHERE id = ?")->execute([$id]);
    }

    public function priceListsForDropdown(): array
    {
        return $this->db->query("SELECT id, code, name FROM price_lists WHERE is_active = 1 ORDER BY is_default DESC, name")->fetchAll();
    }

    /* ═══════════════════════════════════════════════════════
     *  OFFERTER
     * ═══════════════════════════════════════════════════════ */

    public function allQuotes(?string $status = null, ?int $customerId = null, ?string $search = null): array
    {
        $sql = "SELECT q.*, c.name AS customer_name, c.customer_number FROM quotes q JOIN customers c ON c.id = q.customer_id WHERE q.is_deleted = 0";
        $params = [];
        if ($status) { $sql .= " AND q.status = ?"; $params[] = $status; }
        if ($customerId) { $sql .= " AND q.customer_id = ?"; $params[] = $customerId; }
        if ($search) { $s = "%$search%"; $sql .= " AND (q.quote_number LIKE ? OR c.name LIKE ? OR q.your_reference LIKE ?)"; $params[] = $s; $params[] = $s; $params[] = $s; }
        $sql .= " ORDER BY q.quote_date DESC, q.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findQuote(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT q.*, c.name AS customer_name, c.customer_number FROM quotes q JOIN customers c ON c.id = q.customer_id WHERE q.id = ? AND q.is_deleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createQuote(array $d): int
    {
        $num = $this->nextQuoteNumber();
        $stmt = $this->db->prepare("INSERT INTO quotes (quote_number,customer_id,contact_id,status,quote_date,valid_until,payment_terms,delivery_terms,delivery_address,currency,our_reference,your_reference,header_text,footer_text,internal_notes,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$num,$d['customer_id'],$d['contact_id']??null,'draft',$d['quote_date'],$d['valid_until'],$d['payment_terms']??'30 dagar netto',$d['delivery_terms']??null,$d['delivery_address']??null,$d['currency']??'SEK',$d['our_reference']??null,$d['your_reference']??null,$d['header_text']??null,$d['footer_text']??null,$d['internal_notes']??null,$d['created_by']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function updateQuote(int $id, array $d): void
    {
        $stmt = $this->db->prepare("UPDATE quotes SET customer_id=?,contact_id=?,quote_date=?,valid_until=?,payment_terms=?,delivery_terms=?,delivery_address=?,currency=?,our_reference=?,your_reference=?,header_text=?,footer_text=?,internal_notes=? WHERE id=?");
        $stmt->execute([$d['customer_id'],$d['contact_id']??null,$d['quote_date'],$d['valid_until'],$d['payment_terms']??'30 dagar netto',$d['delivery_terms']??null,$d['delivery_address']??null,$d['currency']??'SEK',$d['our_reference']??null,$d['your_reference']??null,$d['header_text']??null,$d['footer_text']??null,$d['internal_notes']??null,$id]);
    }

    public function updateQuoteStatus(int $id, string $status): void
    {
        $extra = '';
        if ($status === 'sent') $extra = ', sent_at = NOW()';
        if ($status === 'accepted') $extra = ', accepted_at = NOW()';
        if ($status === 'rejected') $extra = ', rejected_at = NOW()';
        $this->db->prepare("UPDATE quotes SET status = ?{$extra} WHERE id = ?")->execute([$status, $id]);
    }

    public function deleteQuote(int $id): void
    {
        $this->db->prepare("UPDATE quotes SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function nextQuoteNumber(): string
    {
        $y = date('y');
        $stmt = $this->db->prepare("SELECT quote_number FROM quotes WHERE quote_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute(["OF{$y}%"]);
        $last = $stmt->fetchColumn();
        $seq = $last ? (int)substr($last, 4) + 1 : 1;
        return "OF{$y}" . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    public function quoteLines(int $qid): array
    {
        $stmt = $this->db->prepare("SELECT ql.*, a.article_number, a.name AS article_name FROM quote_lines ql LEFT JOIN articles a ON a.id = ql.article_id WHERE ql.quote_id = ? ORDER BY ql.sort_order, ql.line_number");
        $stmt->execute([$qid]);
        return $stmt->fetchAll();
    }

    public function addQuoteLine(array $d): int
    {
        $lt = round(($d['quantity'] * $d['unit_price']) * (1 - ($d['discount_percent'] ?? 0) / 100), 2);
        $nl = $this->db->prepare("SELECT COALESCE(MAX(line_number),0)+1 FROM quote_lines WHERE quote_id = ?");
        $nl->execute([$d['quote_id']]);
        $ln = (int)$nl->fetchColumn();
        $stmt = $this->db->prepare("INSERT INTO quote_lines (quote_id,line_number,article_id,description,quantity,unit,unit_price,discount_percent,line_total,vat_rate,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['quote_id'],$ln,$d['article_id']??null,$d['description'],$d['quantity'],$d['unit']??'st',$d['unit_price'],$d['discount_percent']??0,$lt,$d['vat_rate']??25,$d['sort_order']??$ln]);
        $this->recalcQuote((int)$d['quote_id']);
        return (int)$this->db->lastInsertId();
    }

    public function removeQuoteLine(int $id): void
    {
        $stmt = $this->db->prepare("SELECT quote_id FROM quote_lines WHERE id = ?");
        $stmt->execute([$id]);
        $qid = (int)$stmt->fetchColumn();
        $this->db->prepare("DELETE FROM quote_lines WHERE id = ?")->execute([$id]);
        if ($qid) $this->recalcQuote($qid);
    }

    private function recalcQuote(int $qid): void
    {
        $lines = $this->quoteLines($qid);
        $sub = 0; $vat = 0;
        foreach ($lines as $l) { $sub += (float)$l['line_total']; $vat += round((float)$l['line_total'] * (float)$l['vat_rate'] / 100, 2); }
        $tot = $sub + $vat;
        $rnd = round(round($tot) - $tot, 2);
        $this->db->prepare("UPDATE quotes SET subtotal=?,vat_amount=?,rounding=?,total_amount=? WHERE id=?")->execute([$sub,$vat,$rnd,$tot+$rnd,$qid]);
    }

    public function convertQuoteToOrder(int $qid, ?int $userId = null): int
    {
        $q = $this->findQuote($qid);
        if (!$q) throw new \RuntimeException('Offert saknas');
        $oid = $this->createSalesOrder(['customer_id'=>$q['customer_id'],'contact_id'=>$q['contact_id'],'quote_id'=>$qid,'order_date'=>date('Y-m-d'),'payment_terms'=>$q['payment_terms'],'delivery_terms'=>$q['delivery_terms'],'delivery_address'=>$q['delivery_address'],'currency'=>$q['currency'],'our_reference'=>$q['our_reference'],'your_reference'=>$q['your_reference'],'header_text'=>$q['header_text'],'footer_text'=>$q['footer_text'],'created_by'=>$userId]);
        foreach ($this->quoteLines($qid) as $l) {
            $this->addSalesOrderLine(['sales_order_id'=>$oid,'article_id'=>$l['article_id'],'description'=>$l['description'],'quantity'=>$l['quantity'],'unit'=>$l['unit'],'unit_price'=>$l['unit_price'],'discount_percent'=>$l['discount_percent'],'vat_rate'=>$l['vat_rate'],'sort_order'=>$l['sort_order']]);
        }
        $this->updateQuoteStatus($qid, 'accepted');
        $this->db->prepare("UPDATE quotes SET converted_to_order_id = ? WHERE id = ?")->execute([$oid, $qid]);
        return $oid;
    }

    /* ═══════════════════════════════════════════════════════
     *  FÖRSÄLJNINGSORDRAR
     * ═══════════════════════════════════════════════════════ */

    public function allSalesOrders(?string $status = null, ?int $customerId = null, ?string $search = null): array
    {
        $sql = "SELECT so.*, c.name AS customer_name, c.customer_number FROM sales_orders so JOIN customers c ON c.id = so.customer_id WHERE so.is_deleted = 0";
        $params = [];
        if ($status) { $sql .= " AND so.status = ?"; $params[] = $status; }
        if ($customerId) { $sql .= " AND so.customer_id = ?"; $params[] = $customerId; }
        if ($search) { $s = "%$search%"; $sql .= " AND (so.order_number LIKE ? OR c.name LIKE ? OR so.customer_order_number LIKE ?)"; $params[] = $s; $params[] = $s; $params[] = $s; }
        $sql .= " ORDER BY so.order_date DESC, so.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findSalesOrder(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT so.*, c.name AS customer_name, c.customer_number, c.email AS customer_email FROM sales_orders so JOIN customers c ON c.id = so.customer_id WHERE so.id = ? AND so.is_deleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createSalesOrder(array $d): int
    {
        $num = $this->nextOrderNumber();
        $stmt = $this->db->prepare("INSERT INTO sales_orders (order_number,customer_id,contact_id,quote_id,status,order_date,requested_delivery,promised_delivery,payment_terms,delivery_terms,delivery_address,currency,our_reference,your_reference,customer_order_number,header_text,footer_text,internal_notes,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$num,$d['customer_id'],$d['contact_id']??null,$d['quote_id']??null,'draft',$d['order_date'],$d['requested_delivery']??null,$d['promised_delivery']??null,$d['payment_terms']??'30 dagar netto',$d['delivery_terms']??null,$d['delivery_address']??null,$d['currency']??'SEK',$d['our_reference']??null,$d['your_reference']??null,$d['customer_order_number']??null,$d['header_text']??null,$d['footer_text']??null,$d['internal_notes']??null,$d['created_by']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function updateSalesOrder(int $id, array $d): void
    {
        $stmt = $this->db->prepare("UPDATE sales_orders SET customer_id=?,contact_id=?,order_date=?,requested_delivery=?,promised_delivery=?,payment_terms=?,delivery_terms=?,delivery_address=?,currency=?,our_reference=?,your_reference=?,customer_order_number=?,header_text=?,footer_text=?,internal_notes=? WHERE id=?");
        $stmt->execute([$d['customer_id'],$d['contact_id']??null,$d['order_date'],$d['requested_delivery']??null,$d['promised_delivery']??null,$d['payment_terms']??'30 dagar netto',$d['delivery_terms']??null,$d['delivery_address']??null,$d['currency']??'SEK',$d['our_reference']??null,$d['your_reference']??null,$d['customer_order_number']??null,$d['header_text']??null,$d['footer_text']??null,$d['internal_notes']??null,$id]);
    }

    public function updateSalesOrderStatus(int $id, string $status): void
    {
        $extra = '';
        if ($status === 'confirmed') $extra = ', confirmed_at = NOW()';
        $this->db->prepare("UPDATE sales_orders SET status = ?{$extra} WHERE id = ?")->execute([$status,$id]);
    }

    public function deleteSalesOrder(int $id): void
    {
        $this->db->prepare("UPDATE sales_orders SET is_deleted = 1 WHERE id = ?")->execute([$id]);
    }

    public function nextOrderNumber(): string
    {
        $y = date('y');
        $stmt = $this->db->prepare("SELECT order_number FROM sales_orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute(["SO{$y}%"]);
        $last = $stmt->fetchColumn();
        $seq = $last ? (int)substr($last, 4) + 1 : 1;
        return "SO{$y}" . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    public function salesOrderLines(int $oid): array
    {
        $stmt = $this->db->prepare("SELECT sol.*, a.article_number, a.name AS article_name FROM sales_order_lines sol LEFT JOIN articles a ON a.id = sol.article_id WHERE sol.sales_order_id = ? ORDER BY sol.sort_order, sol.line_number");
        $stmt->execute([$oid]);
        return $stmt->fetchAll();
    }

    public function addSalesOrderLine(array $d): int
    {
        $lt = round(($d['quantity'] * $d['unit_price']) * (1 - ($d['discount_percent'] ?? 0) / 100), 2);
        $nl = $this->db->prepare("SELECT COALESCE(MAX(line_number),0)+1 FROM sales_order_lines WHERE sales_order_id = ?");
        $nl->execute([$d['sales_order_id']]);
        $ln = (int)$nl->fetchColumn();
        $stmt = $this->db->prepare("INSERT INTO sales_order_lines (sales_order_id,line_number,article_id,description,quantity,unit,unit_price,discount_percent,line_total,vat_rate,requested_delivery,promised_delivery,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['sales_order_id'],$ln,$d['article_id']??null,$d['description'],$d['quantity'],$d['unit']??'st',$d['unit_price'],$d['discount_percent']??0,$lt,$d['vat_rate']??25,$d['requested_delivery']??null,$d['promised_delivery']??null,$d['sort_order']??$ln]);
        $this->recalcOrder((int)$d['sales_order_id']);
        return (int)$this->db->lastInsertId();
    }

    public function removeSalesOrderLine(int $id): void
    {
        $stmt = $this->db->prepare("SELECT sales_order_id FROM sales_order_lines WHERE id = ?");
        $stmt->execute([$id]);
        $oid = (int)$stmt->fetchColumn();
        $this->db->prepare("DELETE FROM sales_order_lines WHERE id = ?")->execute([$id]);
        if ($oid) $this->recalcOrder($oid);
    }

    private function recalcOrder(int $oid): void
    {
        $lines = $this->salesOrderLines($oid);
        $sub = 0; $vat = 0;
        foreach ($lines as $l) { $sub += (float)$l['line_total']; $vat += round((float)$l['line_total'] * (float)$l['vat_rate'] / 100, 2); }
        $tot = $sub + $vat;
        $rnd = round(round($tot) - $tot, 2);
        $this->db->prepare("UPDATE sales_orders SET subtotal=?,vat_amount=?,rounding=?,total_amount=? WHERE id=?")->execute([$sub,$vat,$rnd,$tot+$rnd,$oid]);
    }

    public function createProductionFromOrder(int $oid, ?int $userId = null): array
    {
        $order = $this->findSalesOrder($oid);
        $lines = $this->salesOrderLines($oid);
        $created = [];
        foreach ($lines as $l) {
            if (!$l['article_id']) continue;
            $poNum = 'PO' . date('y') . str_pad((string)(rand(1,9999)), 4, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("INSERT INTO production_orders (order_number,article_id,quantity_planned,unit,status,priority,planned_start,description,created_by) VALUES (?,?,?,?,'planned','normal',?,?,?)");
            $stmt->execute([$poNum,$l['article_id'],$l['quantity'],$l['unit'],$order['promised_delivery']??$order['requested_delivery']??date('Y-m-d'),"Fran order {$order['order_number']}, rad {$l['line_number']}",$userId]);
            $poId = (int)$this->db->lastInsertId();
            $this->db->prepare("UPDATE sales_order_lines SET production_order_id = ? WHERE id = ?")->execute([$poId,$l['id']]);
            $created[] = $poId;
        }
        if ($created) $this->updateSalesOrderStatus($oid, 'in_production');
        return $created;
    }

    /* ═══════════════════════════════════════════════════════
     *  AKTIVITET & STATISTIK
     * ═══════════════════════════════════════════════════════ */

    public function addActivity(array $d): int
    {
        $stmt = $this->db->prepare("INSERT INTO sales_activities (customer_id,quote_id,sales_order_id,activity_type,subject,description,activity_date,created_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['customer_id']??null,$d['quote_id']??null,$d['sales_order_id']??null,$d['activity_type'],$d['subject'],$d['description']??null,$d['activity_date']??date('Y-m-d H:i:s'),$d['created_by']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function activities(?int $cid = null, ?int $qid = null, ?int $oid = null, int $limit = 50): array
    {
        $sql = "SELECT sa.*, u.email AS user_email FROM sales_activities sa LEFT JOIN users u ON u.id = sa.created_by WHERE 1=1";
        $params = [];
        if ($cid) { $sql .= " AND sa.customer_id = ?"; $params[] = $cid; }
        if ($qid) { $sql .= " AND sa.quote_id = ?"; $params[] = $qid; }
        if ($oid) { $sql .= " AND sa.sales_order_id = ?"; $params[] = $oid; }
        $sql .= " ORDER BY sa.activity_date DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function stats(): array
    {
        $s = [];
        $s['customers_active'] = (int)$this->db->query("SELECT COUNT(*) FROM customers WHERE is_deleted=0 AND status='active'")->fetchColumn();
        $s['quotes_open'] = (int)$this->db->query("SELECT COUNT(*) FROM quotes WHERE is_deleted=0 AND status IN ('draft','sent')")->fetchColumn();
        $s['quotes_value'] = (float)$this->db->query("SELECT COALESCE(SUM(total_amount),0) FROM quotes WHERE is_deleted=0 AND status IN ('draft','sent')")->fetchColumn();
        $s['orders_active'] = (int)$this->db->query("SELECT COUNT(*) FROM sales_orders WHERE is_deleted=0 AND status NOT IN ('invoiced','cancelled')")->fetchColumn();
        $s['orders_value'] = (float)$this->db->query("SELECT COALESCE(SUM(total_amount),0) FROM sales_orders WHERE is_deleted=0 AND status NOT IN ('invoiced','cancelled')")->fetchColumn();
        $s['orders_month'] = (int)$this->db->query("SELECT COUNT(*) FROM sales_orders WHERE is_deleted=0 AND MONTH(order_date)=MONTH(CURDATE()) AND YEAR(order_date)=YEAR(CURDATE())")->fetchColumn();
        $s['orders_month_value'] = (float)$this->db->query("SELECT COALESCE(SUM(total_amount),0) FROM sales_orders WHERE is_deleted=0 AND MONTH(order_date)=MONTH(CURDATE()) AND YEAR(order_date)=YEAR(CURDATE()) AND status!='cancelled'")->fetchColumn();
        $s['hit_rate'] = 0;
        $tq = (int)$this->db->query("SELECT COUNT(*) FROM quotes WHERE is_deleted=0 AND status NOT IN ('draft')")->fetchColumn();
        $aq = (int)$this->db->query("SELECT COUNT(*) FROM quotes WHERE is_deleted=0 AND status='accepted'")->fetchColumn();
        if ($tq > 0) $s['hit_rate'] = round($aq / $tq * 100, 1);
        return $s;
    }
}
