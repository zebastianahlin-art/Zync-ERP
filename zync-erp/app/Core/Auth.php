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

    /**
     * Return the authenticated user's data (including role info), or null when not logged in.
     * Results are cached in the session to avoid repeated DB queries per request.
     */
    public static function user(): ?array
    {
        $id = self::id();
        if ($id === null) {
            return null;
        }

        // Cache in session to avoid repeated DB queries per request
        if (isset($_SESSION['_user_cache']) && ($_SESSION['_user_cache']['id'] ?? null) === $id) {
            return $_SESSION['_user_cache'];
        }

        $db = \App\Core\Database::pdo();
        $stmt = $db->prepare('
            SELECT u.*, r.slug AS role_slug, r.level AS role_level, r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ? AND u.is_active = 1 AND u.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;

        if ($user) {
            $_SESSION['_user_cache'] = $user;
        }

        return $user;
    }

    /** Remove authentication data from the session. */
    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION['_user_cache'], $_SESSION['2fa_pending']);
    }

    /**
     * Mark the session as requiring 2FA verification.
     * Called after successful password login when the user has 2FA enabled.
     */
    public static function set2faPending(): void
    {
        $_SESSION['2fa_pending'] = true;
    }

    /** Returns true when the user is logged in but 2FA verification is still pending. */
    public static function is2faPending(): bool
    {
        return isset($_SESSION['2fa_pending']) && $_SESSION['2fa_pending'] === true;
    }

    /** Mark 2FA as completed for this session. */
    public static function complete2fa(): void
    {
        unset($_SESSION['2fa_pending']);
    }
}
