<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class InventoryRepository
{
    private const STOCK_ADJUSTMENT_THRESHOLD = 0.001;

    // ─── Stock Methods ────────────────────────────────────────────────────

    public function getStockWithFilters(array $filters): array
    {
        $params = [];
        $where  = ['s.is_deleted = 0'];

        if (!empty($filters['search'])) {
            $where[]  = '(a.name LIKE ? OR a.article_number LIKE ?)';
            $like     = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($filters['warehouse_id'])) {
            $where[]  = 's.warehouse_id = ?';
            $params[] = (int) $filters['warehouse_id'];
        }

        $sql = "SELECT s.*, a.name AS article_name, a.article_number, a.unit, a.purchase_price,
                       w.name AS warehouse_name
                FROM inventory_stock s
                LEFT JOIN articles a ON s.article_id = a.id
                LEFT JOIN warehouses w ON s.warehouse_id = w.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY a.name ASC";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getStockKPIs(): array
    {
        $row = Database::pdo()->query(
            "SELECT
                COUNT(DISTINCT s.article_id) AS total_articles,
                SUM(CASE WHEN s.min_quantity IS NOT NULL AND s.quantity < s.min_quantity THEN 1 ELSE 0 END) AS below_minimum,
                SUM(s.quantity * COALESCE(a.purchase_price, 0)) AS total_value
             FROM inventory_stock s
             LEFT JOIN articles a ON s.article_id = a.id
             WHERE s.is_deleted = 0"
        )->fetch(\PDO::FETCH_ASSOC);

        return [
            'total_articles' => (int) ($row['total_articles'] ?? 0),
            'below_minimum'  => (int) ($row['below_minimum'] ?? 0),
            'total_value'    => (float) ($row['total_value'] ?? 0.0),
        ];
    }

    public function allStock(): array
    {
        return $this->getStockWithFilters([]);
    }

    public function findStock(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT s.*, a.name AS article_name, a.article_number, a.unit, a.purchase_price,
                    w.name AS warehouse_name
             FROM inventory_stock s
             LEFT JOIN articles a ON s.article_id = a.id
             LEFT JOIN warehouses w ON s.warehouse_id = w.id
             WHERE s.id = ? AND s.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function adjustStock(int $articleId, int $warehouseId, float $quantity, string $type, array $meta = []): void
    {
        $pdo = Database::pdo();

        $pdo->prepare(
            "INSERT INTO inventory_stock (article_id, warehouse_id, quantity, created_by)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
        )->execute([
            $articleId,
            $warehouseId,
            $quantity,
            $meta['created_by'] ?? null,
        ]);

        $pdo->prepare(
            "INSERT INTO inventory_transactions
                (article_id, warehouse_id, type, quantity, reference_type, reference_id, notes, to_warehouse_id, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        )->execute([
            $articleId,
            $warehouseId,
            $type,
            $quantity,
            $meta['reference_type'] ?? null,
            $meta['reference_id']   ?? null,
            $meta['notes']          ?? null,
            $meta['to_warehouse_id'] ?? null,
            $meta['created_by']     ?? null,
        ]);
    }

    // ─── Transaction Methods ──────────────────────────────────────────────

    public function getTransactions(array $filters = []): array
    {
        $params = [];
        $where  = ['t.is_deleted = 0'];

        if (!empty($filters['type'])) {
            $where[]  = 't.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 't.created_at >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 't.created_at <= ?';
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT t.*, a.name AS article_name, a.article_number,
                       w.name AS warehouse_name, u.full_name AS created_by_name,
                       tw.name AS to_warehouse_name
                FROM inventory_transactions t
                LEFT JOIN articles a ON t.article_id = a.id
                LEFT JOIN warehouses w ON t.warehouse_id = w.id
                LEFT JOIN warehouses tw ON t.to_warehouse_id = tw.id
                LEFT JOIN users u ON t.created_by = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.created_at DESC
                LIMIT 500";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTransactionById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT t.*, a.name AS article_name, a.article_number,
                    w.name AS warehouse_name, u.full_name AS created_by_name,
                    tw.name AS to_warehouse_name
             FROM inventory_transactions t
             LEFT JOIN articles a ON t.article_id = a.id
             LEFT JOIN warehouses w ON t.warehouse_id = w.id
             LEFT JOIN warehouses tw ON t.to_warehouse_id = tw.id
             LEFT JOIN users u ON t.created_by = u.id
             WHERE t.id = ? AND t.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createTransaction(array $data): int
    {
        $required = ['article_id', 'warehouse_id', 'type', 'quantity'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $this->adjustStock(
            (int)   $data['article_id'],
            (int)   $data['warehouse_id'],
            (float) $data['quantity'],
            (string) $data['type'],
            [
                'reference_type'  => $data['reference_type']  ?? null,
                'reference_id'    => $data['reference_id']    ?? null,
                'notes'           => $data['notes']           ?? null,
                'to_warehouse_id' => $data['to_warehouse_id'] ?? null,
                'created_by'      => $data['created_by']      ?? null,
            ]
        );

        return (int) Database::pdo()->lastInsertId();
    }

    // ─── Receiving Methods ────────────────────────────────────────────────

    public function getReceivingOrders(): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT po.*, s.name AS supplier_name, COUNT(pol.id) AS line_count
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             LEFT JOIN purchase_order_lines pol ON pol.order_id = po.id AND pol.is_deleted = 0
             WHERE po.status IN ('sent','confirmed','partially_received') AND po.is_deleted = 0
             GROUP BY po.id
             ORDER BY po.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getReceivingOrder(int $poId): ?array
    {
        $pdo = Database::pdo();

        $stmtOrder = $pdo->prepare(
            "SELECT po.*, s.name AS supplier_name
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             WHERE po.id = ? AND po.is_deleted = 0"
        );
        $stmtOrder->execute([$poId]);
        $order = $stmtOrder->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $stmtLines = $pdo->prepare(
            "SELECT pol.*, a.name AS article_name, a.article_number, a.unit
             FROM purchase_order_lines pol
             LEFT JOIN articles a ON pol.article_id = a.id
             WHERE pol.order_id = ? AND pol.is_deleted = 0"
        );
        $stmtLines->execute([$poId]);
        $lines = $stmtLines->fetchAll(\PDO::FETCH_ASSOC);

        return ['order' => $order, 'lines' => $lines];
    }

    public function storeReceiving(int $poId, array $lines, int $userId): void
    {
        $pdo = Database::pdo();

        foreach ($lines as $lineId => $lineData) {
            $qty = (float) ($lineData['quantity'] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $warehouseId = (int) ($lineData['warehouse_id'] ?? 0);
            $articleId   = (int) ($lineData['article_id'] ?? 0);

            if ($articleId === 0 || $warehouseId === 0) {
                continue;
            }

            $this->adjustStock($articleId, $warehouseId, $qty, 'receipt', [
                'reference_type' => 'purchase_order',
                'reference_id'   => $poId,
                'notes'          => $lineData['notes'] ?? '',
                'created_by'     => $userId,
            ]);

            $stmtUpdate = $pdo->prepare(
                "UPDATE purchase_order_lines
                 SET received_quantity = COALESCE(received_quantity, 0) + ?
                 WHERE id = ?"
            );
            $stmtUpdate->execute([$qty, (int) $lineId]);
        }

        // Determine new PO status based on received quantities vs ordered quantities
        $stmtCheck = $pdo->prepare(
            "SELECT
                SUM(quantity) AS total_ordered,
                SUM(COALESCE(received_quantity, 0)) AS total_received
             FROM purchase_order_lines
             WHERE order_id = ? AND is_deleted = 0"
        );
        $stmtCheck->execute([$poId]);
        $totals = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

        $newStatus = 'partially_received';
        if ($totals && (float) $totals['total_ordered'] > 0
            && (float) $totals['total_received'] >= (float) $totals['total_ordered']
        ) {
            $newStatus = 'received';
        }

        $pdo->prepare("UPDATE purchase_orders SET status = ? WHERE id = ?")
            ->execute([$newStatus, $poId]);
    }

    // ─── Issue Methods ────────────────────────────────────────────────────

    public function getIssues(array $filters = []): array
    {
        $filters['type'] = 'issue';
        return $this->getTransactions($filters);
    }

    public function createIssue(array $data): int
    {
        $qty = (float) ($data['quantity'] ?? 0);
        // Ensure quantity is stored as negative for issues
        if ($qty > 0) {
            $qty = -$qty;
        }

        $this->adjustStock(
            (int)   $data['article_id'],
            (int)   $data['warehouse_id'],
            $qty,
            'issue',
            [
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id'   => $data['reference_id']   ?? null,
                'notes'          => $data['notes']           ?? null,
                'created_by'     => $data['created_by']     ?? null,
            ]
        );

        return (int) Database::pdo()->lastInsertId();
    }

    // ─── Stocktaking Methods ──────────────────────────────────────────────

    public function getStocktakings(): array
    {
        return Database::pdo()->query(
            "SELECT st.*, w.name AS warehouse_name, u.full_name AS approved_by_name
             FROM stocktakings st
             LEFT JOIN warehouses w ON st.warehouse_id = w.id
             LEFT JOIN users u ON st.approved_by = u.id
             WHERE st.is_deleted = 0
             ORDER BY st.created_at DESC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getStocktakingById(int $id): ?array
    {
        $pdo = Database::pdo();

        $stmtSt = $pdo->prepare(
            "SELECT st.*, w.name AS warehouse_name, u.full_name AS approved_by_name
             FROM stocktakings st
             LEFT JOIN warehouses w ON st.warehouse_id = w.id
             LEFT JOIN users u ON st.approved_by = u.id
             WHERE st.id = ? AND st.is_deleted = 0"
        );
        $stmtSt->execute([$id]);
        $stocktaking = $stmtSt->fetch(\PDO::FETCH_ASSOC);

        if (!$stocktaking) {
            return null;
        }

        $stmtLines = $pdo->prepare(
            "SELECT sl.*, a.name AS article_name, a.article_number, a.unit
             FROM stocktaking_lines sl
             LEFT JOIN articles a ON sl.article_id = a.id
             WHERE sl.stocktaking_id = ?
             ORDER BY a.name ASC"
        );
        $stmtLines->execute([$id]);
        $lines = $stmtLines->fetchAll(\PDO::FETCH_ASSOC);

        return ['stocktaking' => $stocktaking, 'lines' => $lines];
    }

    public function createStocktaking(array $data): int
    {
        $pdo = Database::pdo();

        $warehouseId = (int) $data['warehouse_id'];

        $stmt = $pdo->prepare(
            "INSERT INTO stocktakings (warehouse_id, name, status, started_at, created_by)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $warehouseId,
            $data['name'],
            $data['status'] ?? 'draft',
            $data['started_at'] ?? null,
            $data['created_by'] ?? null,
        ]);

        $stocktakingId = (int) $pdo->lastInsertId();

        // Pre-populate lines from current inventory_stock for this warehouse
        $stmtStock = $pdo->prepare(
            "SELECT article_id, quantity FROM inventory_stock
             WHERE warehouse_id = ? AND is_deleted = 0"
        );
        $stmtStock->execute([$warehouseId]);
        $stockRows = $stmtStock->fetchAll(\PDO::FETCH_ASSOC);

        $stmtLine = $pdo->prepare(
            "INSERT IGNORE INTO stocktaking_lines (stocktaking_id, article_id, system_quantity)
             VALUES (?, ?, ?)"
        );
        foreach ($stockRows as $row) {
            $stmtLine->execute([$stocktakingId, (int) $row['article_id'], (float) $row['quantity']]);
        }

        return $stocktakingId;
    }

    public function addCount(int $id, array $data): void
    {
        $pdo = Database::pdo();

        $articleId = (int) $data['article_id'];

        // Get current system quantity from inventory_stock
        $stmtSys = $pdo->prepare(
            "SELECT quantity FROM inventory_stock
             WHERE article_id = ?
               AND warehouse_id = (SELECT warehouse_id FROM stocktakings WHERE id = ?)
               AND is_deleted = 0"
        );
        $stmtSys->execute([$articleId, $id]);
        $systemQty = (float) ($stmtSys->fetchColumn() ?: 0);

        $pdo->prepare(
            "INSERT INTO stocktaking_lines (stocktaking_id, article_id, system_quantity, counted_quantity, notes)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                counted_quantity = VALUES(counted_quantity),
                notes            = VALUES(notes)"
        )->execute([
            $id,
            $articleId,
            $systemQty,
            $data['counted_quantity'] ?? null,
            $data['notes'] ?? null,
        ]);
    }

    public function approveStocktaking(int $id, ?int $approvedBy = null): void
    {
        $pdo = Database::pdo();

        $stmtSt = $pdo->prepare(
            "SELECT warehouse_id, created_by FROM stocktakings WHERE id = ? AND is_deleted = 0"
        );
        $stmtSt->execute([$id]);
        $stocktaking = $stmtSt->fetch(\PDO::FETCH_ASSOC);

        if (!$stocktaking) {
            return;
        }

        $stmtLines = $pdo->prepare(
            "SELECT article_id, system_quantity, counted_quantity
             FROM stocktaking_lines
             WHERE stocktaking_id = ? AND counted_quantity IS NOT NULL"
        );
        $stmtLines->execute([$id]);
        $lines = $stmtLines->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($lines as $line) {
            $diff = (float) $line['counted_quantity'] - (float) $line['system_quantity'];
            if (abs($diff) < self::STOCK_ADJUSTMENT_THRESHOLD) {
                continue;
            }

            $this->adjustStock(
                (int) $line['article_id'],
                (int) $stocktaking['warehouse_id'],
                $diff,
                'adjustment',
                [
                    'reference_type' => 'stocktaking',
                    'reference_id'   => $id,
                    'notes'          => 'Stocktaking adjustment',
                    'created_by'     => $stocktaking['created_by'],
                ]
            );
        }

        $pdo->prepare(
            "UPDATE stocktakings SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?"
        )->execute([$approvedBy, $id]);
    }

    // ─── Warehouse Methods ────────────────────────────────────────────────

    public function getWarehouses(): array
    {
        return Database::pdo()->query(
            "SELECT w.*, u.full_name AS responsible_name
             FROM warehouses w
             LEFT JOIN users u ON w.responsible_user_id = u.id
             WHERE w.is_deleted = 0
             ORDER BY w.name ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getWarehouseById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT w.*, u.full_name AS responsible_name
             FROM warehouses w
             LEFT JOIN users u ON w.responsible_user_id = u.id
             WHERE w.id = ? AND w.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createWarehouse(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO warehouses (name, code, address, responsible_user_id, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['code'],
            $data['address']             ?? null,
            $data['responsible_user_id'] ?? null,
            $data['is_active']           ?? 1,
            $data['created_by']          ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateWarehouse(int $id, array $data): void
    {
        Database::pdo()->prepare(
            "UPDATE warehouses
             SET name = ?, code = ?, address = ?, responsible_user_id = ?, is_active = ?
             WHERE id = ?"
        )->execute([
            $data['name'],
            $data['code'],
            $data['address']             ?? null,
            $data['responsible_user_id'] ?? null,
            $data['is_active']           ?? 1,
            $id,
        ]);
    }

    public function deleteWarehouse(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE warehouses SET is_deleted = 1 WHERE id = ?"
        )->execute([$id]);
    }

    // ─── Legacy Methods ───────────────────────────────────────────────────

    public function allWarehouses(): array
    {
        return $this->getWarehouses();
    }

    public function transactionsForStock(int $stockId): array
    {
        $pdo = Database::pdo();

        $stmtArticle = $pdo->prepare(
            "SELECT article_id FROM inventory_stock WHERE id = ? AND is_deleted = 0"
        );
        $stmtArticle->execute([$stockId]);
        $articleId = $stmtArticle->fetchColumn();

        if (!$articleId) {
            return [];
        }

        $stmt = $pdo->prepare(
            "SELECT t.*, u.full_name AS created_by_name
             FROM inventory_transactions t
             LEFT JOIN users u ON t.created_by = u.id
             WHERE t.article_id = ? AND t.is_deleted = 0
             ORDER BY t.created_at DESC
             LIMIT 50"
        );
        $stmt->execute([(int) $articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
