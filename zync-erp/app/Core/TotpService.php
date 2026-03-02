<?php

declare(strict_types=1);

namespace App\Core;

use PragmaRX\Google2FA\Google2FA;

/**
 * TOTP (Time-based One-Time Password) service.
 *
 * Wraps pragmarx/google2fa to provide a simple interface for
 * generating secrets, QR code URIs and verifying codes.
 */
class TotpService
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /** Generate a new Base32-encoded TOTP secret. */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Return an otpauth:// URI that authenticator apps can scan as a QR code.
     *
     * @param string $secret The user's TOTP secret
     * @param string $email  The user's email address (used as account label)
     */
    public function getQrCodeUri(string $secret, string $email): string
    {
        return $this->google2fa->getQRCodeUrl(
            'ZYNC ERP',
            $email,
            $secret,
        );
    }

    /**
     * Verify a 6-digit TOTP code against the given secret.
     *
     * Uses a window of 1 (one time-step before/after) to allow
     * slight clock drift between server and authenticator app.
     *
     * @param string $secret The user's TOTP secret
     * @param string $code   The 6-digit code entered by the user
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return (bool) $this->google2fa->verifyKey($secret, $code, 1);
    }
}
