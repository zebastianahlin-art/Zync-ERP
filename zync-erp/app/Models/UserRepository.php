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
            'SELECT id, email, password_hash, created_at, updated_at FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new User(
            id:           (int) $row['id'],
            email:        $row['email'],
            passwordHash: $row['password_hash'],
            createdAt:    $row['created_at'],
            updatedAt:    $row['updated_at'],
        );
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
