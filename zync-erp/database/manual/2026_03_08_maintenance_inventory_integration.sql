-- ZYNC ERP
-- Maintenance -> warehouse-based inventory integration
-- Applied manually in MariaDB because current migrate runner is unsafe on existing DB.

START TRANSACTION;

-- -------------------------------------------------------------------
-- 1) Extend maintenance_work_order_materials
-- -------------------------------------------------------------------

ALTER TABLE maintenance_work_order_materials
    ADD COLUMN warehouse_id BIGINT(20) UNSIGNED NULL AFTER article_id,
    ADD COLUMN reserved_qty DECIMAL(14,4) NOT NULL DEFAULT 0.0000 AFTER planned_qty,
    ADD COLUMN issued_qty DECIMAL(14,4) NOT NULL DEFAULT 0.0000 AFTER reserved_qty,
    ADD COLUMN returned_qty DECIMAL(14,4) NOT NULL DEFAULT 0.0000 AFTER issued_qty,
    ADD COLUMN reservation_status VARCHAR(30) NOT NULL DEFAULT 'none' AFTER returned_qty,
    ADD COLUMN stock_status VARCHAR(30) NOT NULL DEFAULT 'not_issued' AFTER reservation_status;

ALTER TABLE maintenance_work_order_materials
    ADD INDEX idx_mwom_warehouse_id (warehouse_id),
    ADD INDEX idx_mwom_article_warehouse (article_id, warehouse_id),
    ADD INDEX idx_mwom_work_order_id (work_order_id);

-- Optional FK only if inventory_warehouses.id exists and matches type exactly.
-- Uncomment only if your DB already has this table and uses InnoDB everywhere.
--
-- ALTER TABLE maintenance_work_order_materials
--     ADD CONSTRAINT fk_mwom_warehouse
--         FOREIGN KEY (warehouse_id) REFERENCES inventory_warehouses(id)
--         ON DELETE SET NULL
--         ON UPDATE CASCADE;

-- -------------------------------------------------------------------
-- 2) Extend inventory_transactions for maintenance traceability
-- -------------------------------------------------------------------

ALTER TABLE inventory_transactions
    ADD COLUMN quantity DECIMAL(14,4) NOT NULL DEFAULT 0.0000 AFTER warehouse_id,
    ADD COLUMN direction VARCHAR(10) NULL AFTER quantity,
    ADD COLUMN reference_type VARCHAR(50) NULL AFTER direction,
    ADD COLUMN reference_id BIGINT(20) UNSIGNED NULL AFTER reference_type,
    ADD COLUMN reference_line_id BIGINT(20) UNSIGNED NULL AFTER reference_id,
    ADD COLUMN note VARCHAR(255) NULL AFTER reference_line_id;

ALTER TABLE inventory_transactions
    ADD INDEX idx_inventory_transactions_direction (direction),
    ADD INDEX idx_inventory_transactions_reference (reference_type, reference_id),
    ADD INDEX idx_inventory_transactions_reference_line (reference_line_id),
    ADD INDEX idx_inventory_transactions_article_warehouse (article_id, warehouse_id);

-- -------------------------------------------------------------------
-- 3) Normalize existing rows in maintenance_work_order_materials
-- -------------------------------------------------------------------

UPDATE maintenance_work_order_materials
SET
    reserved_qty = COALESCE(reserved_qty, 0.0000),
    issued_qty = COALESCE(issued_qty, 0.0000),
    returned_qty = COALESCE(returned_qty, 0.0000),
    reservation_status = CASE
        WHEN COALESCE(reserved_qty, 0.0000) <= 0 THEN 'none'
        WHEN COALESCE(reserved_qty, 0.0000) >= COALESCE(planned_qty, 0.0000) THEN 'reserved'
        ELSE 'partial'
    END,
    stock_status = CASE
        WHEN COALESCE(issued_qty, 0.0000) <= 0 AND COALESCE(returned_qty, 0.0000) <= 0 THEN 'not_issued'
        WHEN (COALESCE(issued_qty, 0.0000) - COALESCE(returned_qty, 0.0000)) <= 0
             AND COALESCE(returned_qty, 0.0000) > 0 THEN 'returned'
        WHEN COALESCE(returned_qty, 0.0000) > 0
             AND (COALESCE(issued_qty, 0.0000) - COALESCE(returned_qty, 0.0000)) > 0 THEN 'partially_returned'
        WHEN (COALESCE(issued_qty, 0.0000) - COALESCE(returned_qty, 0.0000)) >= COALESCE(planned_qty, 0.0000) THEN 'issued'
        ELSE 'partial'
    END;

COMMIT;
