CREATE TABLE IF NOT EXISTS warehouses (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)    NOT NULL,
    code       VARCHAR(20)     NOT NULL UNIQUE,
    address    VARCHAR(255)    DEFAULT NULL,
    city       VARCHAR(100)    DEFAULT NULL,
    is_active  TINYINT(1)      NOT NULL DEFAULT 1,
    is_deleted TINYINT(1)      NOT NULL DEFAULT 0,
    created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id   BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity     DECIMAL(14,3)   NOT NULL DEFAULT 0,
    min_quantity DECIMAL(14,3)   DEFAULT NULL,
    max_quantity DECIMAL(14,3)   DEFAULT NULL,
    updated_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_article_warehouse (article_id, warehouse_id),
    FOREIGN KEY (article_id)   REFERENCES articles(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_transactions (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id     BIGINT UNSIGNED NOT NULL,
    warehouse_id   BIGINT UNSIGNED NOT NULL,
    type           ENUM('in','out','adjust','transfer') NOT NULL,
    quantity       DECIMAL(14,3)   NOT NULL,
    reference_type VARCHAR(50)     DEFAULT NULL,
    reference_id   BIGINT UNSIGNED DEFAULT NULL,
    note           VARCHAR(255)    DEFAULT NULL,
    created_by     BIGINT UNSIGNED DEFAULT NULL,
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id)   REFERENCES articles(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by)   REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO warehouses (name, code) VALUES ('Huvudlager', 'HL');
