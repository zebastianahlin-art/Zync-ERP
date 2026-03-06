<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $database = $_ENV['DB_NAME'] ?? '';
        $username = $_ENV['DB_USER'] ?? '';
        $password = $_ENV['DB_PASS'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        if ($database === '' || $username === '') {
            throw new RuntimeException('Databasinställningar saknas i .env');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

        try {
            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Databasanslutning misslyckades: ' . $e->getMessage());
        }

        return self::$connection;
    }

    /**
     * Bakåtkompatibilitet för äldre kod.
     */
    public static function pdo(): PDO
    {
        return self::connection();
    }

    /**
     * Bakåtkompatibilitet för äldre kod.
     */
    public static function getInstance(): PDO
    {
        return self::connection();
    }
}