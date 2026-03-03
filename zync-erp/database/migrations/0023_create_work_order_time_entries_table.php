<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS work_order_time_entries (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            work_order_id BIGINT UNSIGNED NOT NULL,
            user_id       BIGINT UNSIGNED NULL,
            work_date     DATE NOT NULL,
            hours         DECIMAL(5,2) NOT NULL,
            description   TEXT NULL,
            is_overtime   TINYINT(1) NOT NULL DEFAULT 0,
            hourly_rate   DECIMAL(8,2) NULL,
            is_approved   TINYINT(1) NOT NULL DEFAULT 0,
            approved_by   BIGINT UNSIGNED NULL,
            approved_at   TIMESTAMP NULL,
            PRIMARY KEY (id),
            INDEX idx_wote_work_order (work_order_id),
            CONSTRAINT fk_wote_work_order FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
            CONSTRAINT fk_wote_user FOREIGN KEY (user_id) REFERENCES users(id),
            CONSTRAINT fk_wote_approved_by FOREIGN KEY (approved_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
