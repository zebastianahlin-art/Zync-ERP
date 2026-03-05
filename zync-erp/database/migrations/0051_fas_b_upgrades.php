<?php

declare(strict_types=1);

/**
 * Migration 0051: Fas B — uppgradering av säljofferter, prislistor och transport
 *
 * - Lägger till delivery_terms, payment_terms, converted_to_order_id i sales_quotes
 * - Lägger till description i sales_price_lists (om den saknas)
 * - Lägger till article_id i transport_orders
 * - Lägger till supplier_id i transport_carriers (för synk med leverantörsregistret)
 */
return function (\PDO $pdo): void {

    // ── sales_quotes: extra columns ──────────────────────────────────────────
    $quoteColumns = $pdo->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_quotes'"
    )->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('delivery_terms', $quoteColumns, true)) {
        $pdo->exec("ALTER TABLE sales_quotes ADD COLUMN delivery_terms VARCHAR(255) NULL AFTER notes");
    }
    if (!in_array('payment_terms', $quoteColumns, true)) {
        $pdo->exec("ALTER TABLE sales_quotes ADD COLUMN payment_terms VARCHAR(255) NULL AFTER delivery_terms");
    }
    if (!in_array('converted_to_order_id', $quoteColumns, true)) {
        $pdo->exec("ALTER TABLE sales_quotes ADD COLUMN converted_to_order_id BIGINT UNSIGNED NULL AFTER payment_terms");
        // FK constraint only if sales_orders exists
        try {
            $pdo->exec("ALTER TABLE sales_quotes ADD CONSTRAINT fk_sq_converted_order FOREIGN KEY (converted_to_order_id) REFERENCES sales_orders(id) ON DELETE SET NULL");
        } catch (\Throwable $e) {
            // Ignore if constraint already exists or sales_orders not available
        }
    }

    // ── sales_price_lists: description column ────────────────────────────────
    $priceListColumns = $pdo->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_price_lists'"
    )->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('description', $priceListColumns, true)) {
        $pdo->exec("ALTER TABLE sales_price_lists ADD COLUMN description TEXT NULL AFTER name");
    }

    // ── transport_orders: article_id column ──────────────────────────────────
    $transportColumns = $pdo->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transport_orders'"
    )->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('article_id', $transportColumns, true)) {
        $pdo->exec("ALTER TABLE transport_orders ADD COLUMN article_id BIGINT UNSIGNED NULL AFTER sales_order_id");
    }

    // ── transport_carriers: supplier_id column ───────────────────────────────
    $carrierColumns = $pdo->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transport_carriers'"
    )->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('supplier_id', $carrierColumns, true)) {
        $pdo->exec("ALTER TABLE transport_carriers ADD COLUMN supplier_id BIGINT UNSIGNED NULL AFTER notes");
    }
};
