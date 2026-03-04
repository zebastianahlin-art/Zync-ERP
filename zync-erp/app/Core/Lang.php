<?php
declare(strict_types=1);
namespace App\Core;

class Lang
{
    private static string $locale = 'sv';
    private static array $translations = [];

    public static function load(string $locale): void
    {
        self::$locale = $locale;
        $file = dirname(__DIR__, 2) . '/lang/' . $locale . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$translations[$key] ?? $default ?: $key;
    }

    public static function locale(): string
    {
        return self::$locale;
    }
}
