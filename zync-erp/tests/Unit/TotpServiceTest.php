<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\TotpService;
use PHPUnit\Framework\TestCase;

class TotpServiceTest extends TestCase
{
    private TotpService $service;

    protected function setUp(): void
    {
        $this->service = new TotpService();
    }

    public function testGenerateSecretReturnsNonEmptyString(): void
    {
        $secret = $this->service->generateSecret();
        $this->assertNotEmpty($secret);
    }

    public function testGenerateSecretReturnsBase32String(): void
    {
        $secret = $this->service->generateSecret();
        // Base32 alphabet: A-Z and 2-7
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);
    }

    public function testGenerateSecretLengthIsAtLeast16Characters(): void
    {
        $secret = $this->service->generateSecret();
        $this->assertGreaterThanOrEqual(16, strlen($secret));
    }

    public function testGetQrCodeUriReturnsOtpauthUri(): void
    {
        $secret = $this->service->generateSecret();
        $uri    = $this->service->getQrCodeUri($secret, 'user@example.com');

        $this->assertStringStartsWith('otpauth://totp/', $uri);
        $this->assertStringContainsString('secret=' . $secret, $uri);
    }

    public function testGetQrCodeUriContainsIssuerName(): void
    {
        $secret = $this->service->generateSecret();
        $uri    = $this->service->getQrCodeUri($secret, 'user@example.com');

        $this->assertStringContainsString('ZYNC%20ERP', $uri);
    }

    public function testVerifyCodeReturnsFalseForInvalidCode(): void
    {
        $secret = $this->service->generateSecret();

        $this->assertFalse($this->service->verifyCode($secret, '000000'));
        $this->assertFalse($this->service->verifyCode($secret, 'abcdef'));
        $this->assertFalse($this->service->verifyCode($secret, ''));
    }

    public function testVerifyCodeReturnsTrueForCurrentCode(): void
    {
        // Use the Google2FA library directly to generate the current OTP
        // so we can test that TotpService accepts it
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret    = $google2fa->generateSecretKey();
        $currentCode = $google2fa->getCurrentOtp($secret);

        $this->assertTrue($this->service->verifyCode($secret, $currentCode));
    }

    public function testTwoSecretsAreUnique(): void
    {
        $secret1 = $this->service->generateSecret();
        $secret2 = $this->service->generateSecret();

        $this->assertNotSame($secret1, $secret2);
    }
}
