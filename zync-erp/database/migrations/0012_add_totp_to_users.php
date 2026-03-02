<?php

declare(strict_types=1);

/**
 * Migration: add TOTP columns to users table
 */
return function (\PDO $pdo): void
{
    $pdo->exec("ALTER TABLE users ADD COLUMN totp_secret VARCHAR(255) NULL AFTER password_hash");
    $pdo->exec("ALTER TABLE users ADD COLUMN totp_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER totp_secret");
    $pdo->exec("ALTER TABLE users ADD COLUMN totp_verified_at TIMESTAMP NULL AFTER totp_enabled");
};
