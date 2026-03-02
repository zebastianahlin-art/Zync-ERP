<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Simple user data-transfer object.
 */
class User
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $email,
        public readonly string  $passwordHash,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
        public readonly ?string $totpSecret = null,
        public readonly int     $totpEnabled = 0,
        public readonly ?string $totpVerifiedAt = null,
    ) {}
}
