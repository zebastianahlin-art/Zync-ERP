START TRANSACTION;

ALTER TABLE maintenance_work_order_materials
    ADD COLUMN reserved_quantity DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER planned_quantity,
    ADD COLUMN returned_quantity DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER issued_quantity,
    ADD COLUMN reservation_status VARCHAR(30) NOT NULL DEFAULT 'none' AFTER returned_quantity,
    ADD COLUMN stock_status VARCHAR(30) NOT NULL DEFAULT 'not_issued' AFTER reservation_status;

ALTER TABLE maintenance_work_order_materials
    ADD INDEX idx_mwom_tenant_work_order (tenant_id, work_order_id),
    ADD INDEX idx_mwom_tenant_article_warehouse (tenant_id, article_id, warehouse_id),
    ADD INDEX idx_mwom_reservation_status (reservation_status),
    ADD INDEX idx_mwom_stock_status (stock_status);

UPDATE maintenance_work_order_materials
SET
    reserved_quantity = 0.00,
    returned_quantity = 0.00,
    reservation_status = 'none',
    stock_status = CASE
        WHEN issued_quantity <= 0 THEN 'not_issued'
        WHEN issued_quantity >= planned_quantity THEN 'issued'
        ELSE 'partial'
    END;

COMMIT;
