<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple i18n helper.
 *
 * Usage:
 *   Lang::setLocale('sv');
 *   echo Lang::get('nav.dashboard');  // "Dashboard"
 */
class Lang
{
    private static string $locale = 'sv';

    /** @var array<string, string> */
    private static array $strings = [];

    private static bool $loaded = false;

    /** Set the active locale and load its strings. */
    public static function setLocale(string $locale): void
    {
        self::$locale  = $locale;
        self::$loaded  = false;
        self::$strings = [];
    }

    /** Get the active locale. */
    public static function getLocale(): string
    {
        return self::$locale;
    }

    /**
     * Translate a key.
     *
     * @param array<string, string> $replace  Placeholder replacements, e.g. ['name' => 'World']
     */
    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? self::$locale;
        self::load($locale);

        $text = self::$strings[$key] ?? $key;

        foreach ($replace as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, $value, $text);
        }

        return $text;
    }

    /** Convenience alias. */
    public static function t(string $key, array $replace = []): string
    {
        return self::get($key, $replace);
    }

    /** Load language strings from file if not already loaded. */
    private static function load(string $locale): void
    {
        if (self::$loaded && self::$locale === $locale) {
            return;
        }

        $file = dirname(__DIR__, 2) . '/lang/' . $locale . '/app.php';

        if (file_exists($file)) {
            /** @var array<string, string> $strings */
            $strings       = require $file;
            self::$strings = $strings;
        } else {
            self::$strings = [];
        }

        self::$loaded = true;
    }
}
