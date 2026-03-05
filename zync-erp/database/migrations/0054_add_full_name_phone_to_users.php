<?php

declare(strict_types=1);

/**
 * Migration: add full_name and phone columns to users table.
 *
 * These columns are referenced throughout repositories and controllers
 * (JOIN queries selecting u.full_name, MyPageController updating phone/full_name, etc.)
 * but were missing from the initial schema.
 */
return function (\PDO $pdo): void {

    $existingColumns = $pdo->query(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'"
    )->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('full_name', $existingColumns, true)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(200) NULL AFTER username");
    }

    if (!in_array('phone', $existingColumns, true)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(50) NULL AFTER full_name");
    }
};
