<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Handles persistence for the User model.
 */
class UserRepository
{
    /** Find a user by their email address, or return null if not found. */
    public function findByEmail(string $email): ?User
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, email, password_hash, created_at, updated_at, totp_secret, totp_enabled, totp_verified_at FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new User(
            id:             (int) $row['id'],
            email:          $row['email'],
            passwordHash:   $row['password_hash'],
            createdAt:      $row['created_at'],
            updatedAt:      $row['updated_at'],
            totpSecret:     $row['totp_secret'] ?? null,
            totpEnabled:    (int) ($row['totp_enabled'] ?? 0),
            totpVerifiedAt: $row['totp_verified_at'] ?? null,
        );
    }

    /** Find a user by their ID, or return null if not found. */
    public function findById(int $id): ?User
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, email, password_hash, created_at, updated_at, totp_secret, totp_enabled, totp_verified_at FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new User(
            id:             (int) $row['id'],
            email:          $row['email'],
            passwordHash:   $row['password_hash'],
            createdAt:      $row['created_at'],
            updatedAt:      $row['updated_at'],
            totpSecret:     $row['totp_secret'] ?? null,
            totpEnabled:    (int) ($row['totp_enabled'] ?? 0),
            totpVerifiedAt: $row['totp_verified_at'] ?? null,
        );
    }

    /** Enable TOTP for a user by storing their secret and marking it verified. */
    public function enableTotp(int $userId, string $secret): void
    {
        Database::pdo()
            ->prepare('UPDATE users SET totp_secret = ?, totp_enabled = 1, totp_verified_at = NOW() WHERE id = ?')
            ->execute([$secret, $userId]);
    }

    /** Disable TOTP for a user, clearing the secret. */
    public function disableTotp(int $userId): void
    {
        Database::pdo()
            ->prepare('UPDATE users SET totp_secret = NULL, totp_enabled = 0, totp_verified_at = NULL WHERE id = ?')
            ->execute([$userId]);
    }

    /** Create a new user, hashing the plain-text password before storage. */
    public function create(string $email, string $password): User
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (email, password_hash) VALUES (?, ?)'
        );
        $stmt->execute([$email, $hash]);

        $user = $this->findByEmail($email);

        if ($user === null) {
            throw new \RuntimeException("Failed to retrieve user after insertion: {$email}");
        }

        return $user;
    }
}
