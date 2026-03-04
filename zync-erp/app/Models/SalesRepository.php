<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class SalesRepository
{
    // ─── Quotes ───────────────────────────────────────────────

    public function allQuotes(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT sq.*, c.name AS customer_name
             FROM sales_quotes sq
             LEFT JOIN customers c ON c.id = sq.customer_id
             WHERE sq.is_deleted = 0
             ORDER BY sq.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findQuote(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT sq.*, c.name AS customer_name
             FROM sales_quotes sq
             LEFT JOIN customers c ON c.id = sq.customer_id
             WHERE sq.id = ? AND sq.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createQuote(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO sales_quotes (quote_number, customer_id, contact_person, valid_until, status, total_amount, currency, notes)
             VALUES (:quote_number, :customer_id, :contact_person, :valid_until, :status, :total_amount, :currency, :notes)'
        );
        $stmt->execute([
            'quote_number'   => $data['quote_number'],
            'customer_id'    => $data['customer_id'] ?: null,
            'contact_person' => $data['contact_person'] ?? null,
            'valid_until'    => $data['valid_until'] ?: null,
            'status'         => $data['status'] ?? 'draft',
            'total_amount'   => $data['total_amount'] ?? 0,
            'currency'       => $data['currency'] ?? 'SEK',
            'notes'          => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateQuote(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_quotes SET quote_number = :quote_number, customer_id = :customer_id,
             contact_person = :contact_person, valid_until = :valid_until, status = :status,
             total_amount = :total_amount, currency = :currency, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'quote_number'   => $data['quote_number'],
            'customer_id'    => $data['customer_id'] ?: null,
            'contact_person' => $data['contact_person'] ?? null,
            'valid_until'    => $data['valid_until'] ?: null,
            'status'         => $data['status'] ?? 'draft',
            'total_amount'   => $data['total_amount'] ?? 0,
            'currency'       => $data['currency'] ?? 'SEK',
            'notes'          => $data['notes'] ?? null,
            'id'             => $id,
        ]);
    }

    public function deleteQuote(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE sales_quotes SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Orders ───────────────────────────────────────────────

    public function allOrders(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT so.*, c.name AS customer_name
             FROM sales_orders so
             LEFT JOIN customers c ON c.id = so.customer_id
             WHERE so.is_deleted = 0
             ORDER BY so.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findOrder(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT so.*, c.name AS customer_name
             FROM sales_orders so
             LEFT JOIN customers c ON c.id = so.customer_id
             WHERE so.id = ? AND so.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createOrder(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO sales_orders (order_number, quote_id, customer_id, order_date, delivery_date, status, total_amount, notes)
             VALUES (:order_number, :quote_id, :customer_id, :order_date, :delivery_date, :status, :total_amount, :notes)'
        );
        $stmt->execute([
            'order_number'  => $data['order_number'],
            'quote_id'      => $data['quote_id'] ?: null,
            'customer_id'   => $data['customer_id'] ?: null,
            'order_date'    => $data['order_date'] ?: null,
            'delivery_date' => $data['delivery_date'] ?: null,
            'status'        => $data['status'] ?? 'confirmed',
            'total_amount'  => $data['total_amount'] ?? 0,
            'notes'         => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateOrder(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_orders SET order_number = :order_number, quote_id = :quote_id,
             customer_id = :customer_id, order_date = :order_date, delivery_date = :delivery_date,
             status = :status, total_amount = :total_amount, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'order_number'  => $data['order_number'],
            'quote_id'      => $data['quote_id'] ?: null,
            'customer_id'   => $data['customer_id'] ?: null,
            'order_date'    => $data['order_date'] ?: null,
            'delivery_date' => $data['delivery_date'] ?: null,
            'status'        => $data['status'] ?? 'confirmed',
            'total_amount'  => $data['total_amount'] ?? 0,
            'notes'         => $data['notes'] ?? null,
            'id'            => $id,
        ]);
    }

    public function deleteOrder(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE sales_orders SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Price Lists ──────────────────────────────────────────

    public function allPriceLists(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT spl.*, c.name AS customer_name
             FROM sales_price_lists spl
             LEFT JOIN customers c ON c.id = spl.customer_id
             WHERE spl.is_deleted = 0
             ORDER BY spl.id DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allCustomers(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
