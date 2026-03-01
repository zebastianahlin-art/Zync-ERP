<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Static session-based authentication helper.
 */
class Auth
{
    private const SESSION_KEY = 'user_id';

    /** Returns true when a user is currently logged in. */
    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    /** Returns the authenticated user's ID, or null when not logged in. */
    public static function id(): ?int
    {
        return isset($_SESSION[self::SESSION_KEY])
            ? (int) $_SESSION[self::SESSION_KEY]
            : null;
    }

    /** Store the given user ID in the session. */
    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $userId;
    }

    /** Remove authentication data from the session. */
    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
