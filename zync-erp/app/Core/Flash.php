<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple one-time flash message helper backed by the PHP session.
 */
class Flash
{
    private const PREFIX = '_flash_';

    /** Store a flash message under the given key. */
    public static function set(string $key, string $message): void
    {
        $_SESSION[self::PREFIX . $key] = $message;
    }

    /** Retrieve and immediately clear a flash message. Returns null if not set. */
    public static function get(string $key): ?string
    {
        $sessionKey = self::PREFIX . $key;

        if (!isset($_SESSION[$sessionKey])) {
            return null;
        }

        $message = (string) $_SESSION[$sessionKey];
        unset($_SESSION[$sessionKey]);
        return $message;
    }
}
