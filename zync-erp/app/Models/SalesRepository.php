<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class SalesRepository
{
    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'quotes' => (int) $pdo->query('SELECT COUNT(*) FROM sales_quotes WHERE is_deleted = 0')->fetchColumn(),
            'orders' => (int) $pdo->query('SELECT COUNT(*) FROM sales_orders WHERE is_deleted = 0')->fetchColumn(),
            'lists'  => (int) $pdo->query('SELECT COUNT(*) FROM sales_price_lists WHERE is_deleted = 0')->fetchColumn(),
        ];
    }

    public function allCustomers(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ─── Quotes ────────────────────────────────────────────────────────────────

    public function allQuotes(): array
    {
        return Database::pdo()->query(
            'SELECT q.*, c.name AS customer_name
             FROM sales_quotes q
             LEFT JOIN customers c ON q.customer_id = c.id
             WHERE q.is_deleted = 0
             ORDER BY q.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findQuote(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT q.*, c.name AS customer_name
             FROM sales_quotes q
             LEFT JOIN customers c ON q.customer_id = c.id
             WHERE q.id = ? AND q.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function quoteLines(int $quoteId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM sales_quote_lines WHERE quote_id = ? AND is_deleted = 0 ORDER BY id ASC'
        );
        $stmt->execute([$quoteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createQuote(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO sales_quotes (quote_number, customer_id, valid_until, status, notes, created_by)
             VALUES (:quote_number, :customer_id, :valid_until, :status, :notes, :created_by)'
        );
        $stmt->execute([
            'quote_number' => $data['quote_number'],
            'customer_id'  => $data['customer_id'] ?: null,
            'valid_until'  => $data['valid_until'] ?: null,
            'status'       => $data['status'] ?? 'draft',
            'notes'        => $data['notes'] ?: null,
            'created_by'   => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateQuote(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_quotes SET quote_number = :quote_number, customer_id = :customer_id,
             valid_until = :valid_until, status = :status, notes = :notes WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'quote_number' => $data['quote_number'],
            'customer_id'  => $data['customer_id'] ?: null,
            'valid_until'  => $data['valid_until'] ?: null,
            'status'       => $data['status'] ?? 'draft',
            'notes'        => $data['notes'] ?: null,
            'id'           => $id,
        ]);
    }

    public function deleteQuote(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE sales_quotes SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Orders ────────────────────────────────────────────────────────────────

    public function allOrders(): array
    {
        return Database::pdo()->query(
            'SELECT o.*, c.name AS customer_name
             FROM sales_orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             WHERE o.is_deleted = 0
             ORDER BY o.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ─── Price Lists ───────────────────────────────────────────────────────────

    public function allPriceLists(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM sales_price_lists WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
