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

    // ─── Quote filters ─────────────────────────────────────────────────────────

    public function acceptedQuotes(): array
    {
        return Database::pdo()->query(
            "SELECT q.*, c.name AS customer_name
             FROM sales_quotes q
             LEFT JOIN customers c ON q.customer_id = c.id
             WHERE q.is_deleted = 0 AND q.status = 'accepted'
             ORDER BY q.created_at DESC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function historyQuotes(): array
    {
        return Database::pdo()->query(
            "SELECT q.*, c.name AS customer_name
             FROM sales_quotes q
             LEFT JOIN customers c ON q.customer_id = c.id
             WHERE q.is_deleted = 0 AND q.status IN ('expired','rejected','cancelled')
             ORDER BY q.created_at DESC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ─── Quote Templates ───────────────────────────────────────────────────────

    public function allTemplates(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM sales_quote_templates WHERE is_deleted = 0 AND is_active = 1 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findTemplate(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM sales_quote_templates WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createTemplate(array $data): int
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO sales_quote_templates (name, description, default_valid_days, template_lines, is_active, created_by)
             VALUES (:name, :description, :default_valid_days, :template_lines, :is_active, :created_by)'
        );
        $stmt->execute([
            'name'               => $data['name'],
            'description'        => $data['description'] ?: null,
            'default_valid_days' => (int) ($data['default_valid_days'] ?? 30),
            'template_lines'     => $data['template_lines'] ?? null,
            'is_active'          => isset($data['is_active']) ? 1 : 0,
            'created_by'         => $data['created_by'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updateTemplate(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_quote_templates SET name = :name, description = :description,
             default_valid_days = :default_valid_days, is_active = :is_active
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'               => $data['name'],
            'description'        => $data['description'] ?: null,
            'default_valid_days' => (int) ($data['default_valid_days'] ?? 30),
            'is_active'          => isset($data['is_active']) ? 1 : 0,
            'id'                 => $id,
        ]);
    }

    public function deleteTemplate(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_quote_templates SET is_deleted = 1 WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    // ─── Price List CRUD ───────────────────────────────────────────────────────

    public function findPriceList(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM sales_price_lists WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createPriceList(array $data): int
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO sales_price_lists (name, description, currency, valid_from, valid_to, created_by)
             VALUES (:name, :description, :currency, :valid_from, :valid_to, :created_by)'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'] ?: null,
            'currency'    => $data['currency'] ?? 'SEK',
            'valid_from'  => $data['valid_from'] ?: null,
            'valid_to'    => $data['valid_until'] ?: null,
            'created_by'  => $data['created_by'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updatePriceList(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_price_lists SET name = :name, description = :description,
             currency = :currency, valid_from = :valid_from, valid_to = :valid_to
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'] ?: null,
            'currency'    => $data['currency'] ?? 'SEK',
            'valid_from'  => $data['valid_from'] ?: null,
            'valid_to'    => $data['valid_until'] ?: null,
            'id'          => $id,
        ]);
    }

    public function deletePriceList(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_price_lists SET is_deleted = 1 WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    public function priceListItems(int $priceListId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM sales_price_list_items WHERE price_list_id = ? AND is_deleted = 0 ORDER BY id ASC'
        );
        $stmt->execute([$priceListId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addPriceListItem(int $priceListId, array $data): int
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO sales_price_list_items (price_list_id, product_name, description, unit_price, currency, unit)
             VALUES (:price_list_id, :product_name, :description, :unit_price, :currency, :unit)'
        );
        $stmt->execute([
            'price_list_id' => $priceListId,
            'product_name'  => $data['product_name'],
            'description'   => $data['description'] ?: null,
            'unit_price'    => (float) ($data['unit_price'] ?? 0),
            'currency'      => $data['currency'] ?? 'SEK',
            'unit'          => $data['unit'] ?: null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function removePriceListItem(int $itemId): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_price_list_items SET is_deleted = 1 WHERE id = ?'
        );
        $stmt->execute([$itemId]);
    }

    // ─── Sales Order CRUD ──────────────────────────────────────────────────────

    public function findOrder(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT o.*, c.name AS customer_name
             FROM sales_orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             WHERE o.id = ? AND o.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createOrder(array $data): int
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO sales_orders (order_number, customer_id, quote_id, status, notes, created_by)
             VALUES (:order_number, :customer_id, :quote_id, :status, :notes, :created_by)'
        );
        $stmt->execute([
            'order_number' => $data['order_number'],
            'customer_id'  => $data['customer_id'] ?: null,
            'quote_id'     => $data['quote_id'] ?: null,
            'status'       => $data['status'] ?? 'confirmed',
            'notes'        => $data['notes'] ?: null,
            'created_by'   => $data['created_by'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updateOrder(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_orders SET order_number = :order_number, customer_id = :customer_id,
             quote_id = :quote_id, status = :status, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'order_number' => $data['order_number'],
            'customer_id'  => $data['customer_id'] ?: null,
            'quote_id'     => $data['quote_id'] ?: null,
            'status'       => $data['status'] ?? 'confirmed',
            'notes'        => $data['notes'] ?: null,
            'id'           => $id,
        ]);
    }

    public function deleteOrder(int $id): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_orders SET is_deleted = 1 WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    public function updateOrderStatus(int $id, string $status): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE sales_orders SET status = ? WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$status, $id]);
    }

    public function allAcceptedQuotes(): array
    {
        return Database::pdo()->query(
            "SELECT id, quote_number FROM sales_quotes WHERE is_deleted = 0 AND status = 'accepted' ORDER BY quote_number ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
