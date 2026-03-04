<?php

declare(strict_types=1);

/**
 * Migration: create transport tables (transport_carriers + transport_orders)
 */
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transport_carriers (
            id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name                 VARCHAR(255) NOT NULL,
            code                 VARCHAR(20)  NULL,
            type                 ENUM('internal','external') NOT NULL DEFAULT 'external',
            contact_person       VARCHAR(100) NULL,
            phone                VARCHAR(50)  NULL,
            email                VARCHAR(255) NULL,
            contract_number      VARCHAR(50)  NULL,
            contract_valid_until DATE NULL,
            is_active            TINYINT(1) NOT NULL DEFAULT 1,
            notes                TEXT NULL,
            created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by           BIGINT UNSIGNED NULL,
            is_deleted           TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            CONSTRAINT fk_tc_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transport_orders (
            id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            transport_number VARCHAR(50)  NOT NULL,
            type             ENUM('inbound','outbound','internal') NOT NULL DEFAULT 'outbound',
            carrier_id       BIGINT UNSIGNED NULL,
            customer_id      BIGINT UNSIGNED NULL,
            supplier_id      BIGINT UNSIGNED NULL,
            sales_order_id   BIGINT UNSIGNED NULL,
            pickup_address   TEXT NULL,
            delivery_address TEXT NULL,
            pickup_date      DATETIME NULL,
            delivery_date    DATETIME NULL,
            actual_pickup    DATETIME NULL,
            actual_delivery  DATETIME NULL,
            weight           DECIMAL(10,2) NULL,
            volume           DECIMAL(10,2) NULL,
            tracking_number  VARCHAR(100) NULL,
            status           ENUM('planned','confirmed','in_transit','delivered','cancelled') NOT NULL DEFAULT 'planned',
            cost             DECIMAL(12,2) NULL,
            currency         VARCHAR(3) NOT NULL DEFAULT 'SEK',
            notes            TEXT NULL,
            created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by       BIGINT UNSIGNED NULL,
            is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY uq_transport_number (transport_number),
            INDEX idx_to_status (status),
            INDEX idx_to_carrier (carrier_id),
            CONSTRAINT fk_to_carrier      FOREIGN KEY (carrier_id)     REFERENCES transport_carriers(id) ON DELETE SET NULL,
            CONSTRAINT fk_to_customer     FOREIGN KEY (customer_id)    REFERENCES customers(id)          ON DELETE SET NULL,
            CONSTRAINT fk_to_supplier     FOREIGN KEY (supplier_id)    REFERENCES suppliers(id)          ON DELETE SET NULL,
            CONSTRAINT fk_to_sales_order  FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id)       ON DELETE SET NULL,
            CONSTRAINT fk_to_created_by   FOREIGN KEY (created_by)     REFERENCES users(id)              ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
