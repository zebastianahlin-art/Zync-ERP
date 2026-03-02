<?php

declare(strict_types=1);

/**
 * Migration: upgrade users table to full DOC-02 schema (§2.1)
 *
 * The users table already exists with a basic schema. This migration adds
 * all missing columns using conditional ALTER TABLE statements.
 * On a fresh install, CREATE TABLE IF NOT EXISTS handles the case where
 * the table does not yet exist.
 */
return function (\PDO $pdo): void
{
    // Ensure the table exists with at minimum the core columns.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by    BIGINT UNSIGNED NULL,
            is_deleted    TINYINT(1) NOT NULL DEFAULT 0,
            employee_id   BIGINT UNSIGNED NULL,
            username      VARCHAR(60) NOT NULL DEFAULT '',
            email         VARCHAR(255) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role_id       BIGINT UNSIGNED NOT NULL DEFAULT 1,
            department_id BIGINT UNSIGNED NULL,
            language      VARCHAR(5) NOT NULL DEFAULT 'sv',
            theme         VARCHAR(10) NOT NULL DEFAULT 'dark',
            avatar_path   VARCHAR(500) NULL,
            last_login_at TIMESTAMP NULL,
            is_active     TINYINT(1) NOT NULL DEFAULT 1,
            `2fa_secret`  VARCHAR(64) NULL,
            `2fa_enabled` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY idx_users_username (username),
            UNIQUE KEY idx_users_email (email),
            INDEX idx_users_role (role_id),
            INDEX idx_users_department (department_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Helper: check whether a column already exists in a table.
    $hasColumn = static function (string $table, string $column) use ($pdo): bool {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = ?
               AND COLUMN_NAME  = ?"
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    };

    // Helper: check whether an index already exists on a table.
    $hasIndex = static function (string $table, string $index) use ($pdo): bool {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = ?
               AND INDEX_NAME   = ?"
        );
        $stmt->execute([$table, $index]);
        return (int) $stmt->fetchColumn() > 0;
    };

    // Add missing columns one by one.
    $columns = [
        'created_by'    => 'ALTER TABLE users ADD COLUMN created_by    BIGINT UNSIGNED NULL            AFTER updated_at',
        'is_deleted'    => 'ALTER TABLE users ADD COLUMN is_deleted    TINYINT(1) NOT NULL DEFAULT 0   AFTER created_by',
        'employee_id'   => 'ALTER TABLE users ADD COLUMN employee_id   BIGINT UNSIGNED NULL            AFTER is_deleted',
        'username'      => "ALTER TABLE users ADD COLUMN username      VARCHAR(60) NOT NULL DEFAULT '' AFTER employee_id",
        // role_id DEFAULT 1: temporary default satisfying NOT NULL for any pre-existing rows;
        // the real role is assigned when users are created (via seed.php or application logic).
        'role_id'       => 'ALTER TABLE users ADD COLUMN role_id       BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER password_hash',
        'department_id' => 'ALTER TABLE users ADD COLUMN department_id BIGINT UNSIGNED NULL            AFTER role_id',
        'language'      => "ALTER TABLE users ADD COLUMN language      VARCHAR(5)  NOT NULL DEFAULT 'sv' AFTER department_id",
        'theme'         => "ALTER TABLE users ADD COLUMN theme         VARCHAR(10) NOT NULL DEFAULT 'dark' AFTER language",
        'avatar_path'   => 'ALTER TABLE users ADD COLUMN avatar_path   VARCHAR(500) NULL               AFTER theme',
        'last_login_at' => 'ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL                  AFTER avatar_path',
        'is_active'     => 'ALTER TABLE users ADD COLUMN is_active     TINYINT(1) NOT NULL DEFAULT 1   AFTER last_login_at',
        '2fa_secret'    => 'ALTER TABLE users ADD COLUMN `2fa_secret`  VARCHAR(64) NULL                AFTER is_active',
        '2fa_enabled'   => 'ALTER TABLE users ADD COLUMN `2fa_enabled` TINYINT(1) NOT NULL DEFAULT 0   AFTER `2fa_secret`',
    ];

    foreach ($columns as $column => $sql) {
        if (!$hasColumn('users', $column)) {
            $pdo->exec($sql);
        }
    }

    // Add missing indexes.
    $indexes = [
        'idx_users_username'   => 'ALTER TABLE users ADD UNIQUE KEY idx_users_username (username)',
        'idx_users_email'      => 'ALTER TABLE users ADD UNIQUE KEY idx_users_email (email)',
        'idx_users_role'       => 'ALTER TABLE users ADD INDEX idx_users_role (role_id)',
        'idx_users_department' => 'ALTER TABLE users ADD INDEX idx_users_department (department_id)',
    ];

    foreach ($indexes as $index => $sql) {
        if (!$hasIndex('users', $index)) {
            $pdo->exec($sql);
        }
    }

    // Add foreign keys (roles and departments must exist first).
    $hasFk = static function (string $constraintName) use ($pdo): bool {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA     = DATABASE()
               AND TABLE_NAME       = 'users'
               AND CONSTRAINT_NAME  = ?
               AND CONSTRAINT_TYPE  = 'FOREIGN KEY'"
        );
        $stmt->execute([$constraintName]);
        return (int) $stmt->fetchColumn() > 0;
    };

    if (!$hasFk('fk_users_role')) {
        $pdo->exec('ALTER TABLE users ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)');
    }

    if (!$hasFk('fk_users_department')) {
        $pdo->exec('ALTER TABLE users ADD CONSTRAINT fk_users_department FOREIGN KEY (department_id) REFERENCES departments(id)');
    }
};
