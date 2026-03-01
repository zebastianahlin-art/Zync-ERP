<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal PDO wrapper.
 *
 * Reads connection settings from environment variables via Config::env()
 * and provides a lazy singleton PDO instance.
 */
class Database
{
    private static ?\PDO $instance = null;

    /** Return the shared PDO connection, creating it on first call. */
    public static function pdo(): \PDO
    {
        if (self::$instance === null) {
            $host    = (string) Config::env('DB_HOST', 'localhost');
            $port    = (int)    Config::env('DB_PORT', 3306);
            $name    = (string) Config::env('DB_NAME', '');
            $user    = (string) Config::env('DB_USER', '');
            $pass    = (string) Config::env('DB_PASS', '');
            $charset = (string) Config::env('DB_CHARSET', 'utf8mb4');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

            self::$instance = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$instance;
    }
}
