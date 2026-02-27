<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple configuration helper.
 *
 * Config files live in /config/*.php and must return an associative array.
 * Values can be read with dot-notation: Config::get('app.debug').
 * Falls back to env() helper when the config value is not found.
 */
class Config
{
    /** @var array<string, array<string, mixed>> */
    private static array $cache = [];

    /**
     * Retrieve a configuration value using dot-notation.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        [$file, $setting] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset(self::$cache[$file])) {
            $path = dirname(__DIR__, 2) . '/config/' . $file . '.php';

            if (is_file($path)) {
                /** @var array<string, mixed> $loaded */
                $loaded = require $path;
                self::$cache[$file] = $loaded;
            } else {
                self::$cache[$file] = [];
            }
        }

        if ($setting === null) {
            return self::$cache[$file] ?? $default;
        }

        return self::$cache[$file][$setting] ?? $default;
    }

    /**
     * Read an environment variable, with an optional default.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true'  => true,
            'false' => false,
            'null'  => null,
            default => $value,
        };
    }
}
