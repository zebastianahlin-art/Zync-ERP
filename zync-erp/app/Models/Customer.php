<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Customer data-transfer object.
 */
class Customer
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $orgNumber,
        public readonly string  $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}
}
