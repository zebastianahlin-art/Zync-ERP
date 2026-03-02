<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\JwtService;
use PHPUnit\Framework\TestCase;

class JwtServiceTest extends TestCase
{
    private JwtService $service;

    protected function setUp(): void
    {
        // Set a test secret so JwtService can be instantiated without a real .env
        $_ENV['JWT_SECRET'] = 'test-secret-key-for-unit-tests-only-32chars!!';
        $this->service = new JwtService();
    }

    public function testGenerateTokenReturnsNonEmptyString(): void
    {
        $token = $this->service->generateToken(['user_id' => 1, 'email' => 'test@example.com']);
        $this->assertNotEmpty($token);
    }

    public function testGenerateTokenReturnsThreeSegments(): void
    {
        $token = $this->service->generateToken(['user_id' => 1]);
        $parts = explode('.', $token);
        $this->assertCount(3, $parts, 'JWT must have three dot-separated segments');
    }

    public function testValidateTokenReturnsPayloadForValidToken(): void
    {
        $token   = $this->service->generateToken(['user_id' => 42, 'email' => 'user@example.com']);
        $payload = $this->service->validateToken($token);

        $this->assertNotNull($payload);
        $this->assertSame(42, (int) $payload['user_id']);
        $this->assertSame('user@example.com', $payload['email']);
    }

    public function testValidateTokenContainsIssuedAtAndExpiry(): void
    {
        $before  = time();
        $token   = $this->service->generateToken(['user_id' => 1], 3600);
        $after   = time();
        $payload = $this->service->validateToken($token);

        $this->assertNotNull($payload);
        $this->assertGreaterThanOrEqual($before, (int) $payload['iat']);
        $this->assertLessThanOrEqual($after, (int) $payload['iat']);
        $this->assertGreaterThanOrEqual($before + 3600, (int) $payload['exp']);
    }

    public function testValidateTokenReturnsNullForExpiredToken(): void
    {
        // Generate a token that expired one second ago
        $token   = $this->service->generateToken(['user_id' => 1], -1);
        $payload = $this->service->validateToken($token);

        $this->assertNull($payload);
    }

    public function testValidateTokenReturnsNullForInvalidToken(): void
    {
        $this->assertNull($this->service->validateToken('not.a.valid.token'));
        $this->assertNull($this->service->validateToken(''));
        $this->assertNull($this->service->validateToken('eyJ.eyJ.bad'));
    }

    public function testValidateTokenReturnsNullForTamperedToken(): void
    {
        $token  = $this->service->generateToken(['user_id' => 1]);
        $parts  = explode('.', $token);
        // Flip the last character of the signature
        $parts[2] = strrev($parts[2]);
        $tampered = implode('.', $parts);

        $this->assertNull($this->service->validateToken($tampered));
    }

    public function testRefreshTokenReturnsNewTokenForValidToken(): void
    {
        $token    = $this->service->generateToken(['user_id' => 7, 'email' => 'a@b.com']);
        $newToken = $this->service->refreshToken($token);

        $this->assertNotNull($newToken);
        $this->assertIsString($newToken);
        $this->assertNotEmpty($newToken);
    }

    public function testRefreshTokenPreservesPayload(): void
    {
        $token    = $this->service->generateToken(['user_id' => 7, 'email' => 'a@b.com']);
        $newToken = $this->service->refreshToken($token);
        $payload  = $this->service->validateToken((string) $newToken);

        $this->assertNotNull($payload);
        $this->assertSame(7, (int) $payload['user_id']);
        $this->assertSame('a@b.com', $payload['email']);
    }

    public function testRefreshTokenReturnsNullForExpiredToken(): void
    {
        $token    = $this->service->generateToken(['user_id' => 1], -1);
        $newToken = $this->service->refreshToken($token);

        $this->assertNull($newToken);
    }

    public function testRefreshTokenReturnsNullForInvalidToken(): void
    {
        $this->assertNull($this->service->refreshToken('not.a.token'));
    }
}
