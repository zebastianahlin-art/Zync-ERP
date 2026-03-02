<?php

declare(strict_types=1);

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

/**
 * JWT token service.
 *
 * Wraps firebase/php-jwt to provide token generation, validation and refresh.
 * The secret key is read from the JWT_SECRET environment variable.
 */
class JwtService
{
    private const ALGO = 'HS256';

    private string $secret;

    public function __construct()
    {
        $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET');
        if (!$secret) {
            throw new \RuntimeException('JWT_SECRET environment variable is not set.');
        }
        $this->secret = (string) $secret;
    }

    /**
     * Generate a signed JWT.
     *
     * @param array<string, mixed> $payload  User data to embed (user_id, email, role, …)
     * @param int                  $expiresIn Lifetime in seconds (default 3600)
     */
    public function generateToken(array $payload, int $expiresIn = 3600): string
    {
        $now = time();

        $claims = array_merge($payload, [
            'iat' => $now,
            'exp' => $now + $expiresIn,
        ]);

        return JWT::encode($claims, $this->secret, self::ALGO);
    }

    /**
     * Validate and decode a JWT.
     *
     * @return array<string, mixed>|null Decoded payload, or null if invalid/expired.
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, self::ALGO));
            return (array) $decoded;
        } catch (ExpiredException | SignatureInvalidException | BeforeValidException | \UnexpectedValueException | \InvalidArgumentException | \DomainException $e) {
            return null;
        }
    }

    /**
     * Issue a new token based on an existing (still-valid) token.
     *
     * @param int $expiresIn Lifetime for the new token in seconds (default 3600)
     *
     * @return string|null New JWT, or null if the old token is invalid/expired.
     */
    public function refreshToken(string $token, int $expiresIn = 3600): ?string
    {
        $payload = $this->validateToken($token);
        if ($payload === null) {
            return null;
        }

        // Strip standard JWT claims so they are regenerated
        unset($payload['iat'], $payload['exp'], $payload['nbf']);

        return $this->generateToken($payload, $expiresIn);
    }
}
