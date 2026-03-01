<?php

declare(strict_types=1);

namespace App\Core;

/**
 * CSRF protection helper.
 *
 * Tokens are stored in the PHP session and verified on every POST request.
 * The Router automatically calls Csrf::verify() before dispatching POST handlers.
 *
 * In views/forms use the csrf_field() helper or Csrf::field():
 *   <?= Csrf::field() ?>
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Return the current CSRF token, generating one if it does not exist yet.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::SESSION_KEY];
    }

    /**
     * Verify that $token matches the session token.
     * Uses a timing-safe comparison to prevent timing attacks.
     */
    public static function verify(string $token): bool
    {
        $expected = $_SESSION[self::SESSION_KEY] ?? '';

        return $token !== '' && hash_equals($expected, $token);
    }

    /**
     * Return an HTML hidden input carrying the CSRF token.
     * Safe to echo directly inside a <form>.
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}
