<?php

declare(strict_types=1);

namespace App\Core;

final class Tenant
{
    private static ?array $current = null;

    public static function set(array $tenant): void
    {
        self::$current = $tenant;
    }

    public static function get(): ?array
    {
        return self::$current;
    }

    public static function id(): ?int
    {
        return isset(self::$current['id']) ? (int) self::$current['id'] : null;
    }

    public static function slug(): ?string
    {
        return self::$current['slug'] ?? null;
    }

    public static function domain(): ?string
    {
        return self::$current['domain'] ?? null;
    }

    public static function isResolved(): bool
    {
        return self::$current !== null;
    }
}