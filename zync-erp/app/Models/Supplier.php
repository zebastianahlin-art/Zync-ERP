<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Supplier data-transfer object.
 */
class Supplier
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $orgNumber,
        public readonly string  $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly ?string $city,
        public readonly ?string $postalCode,
        public readonly string  $country,
        public readonly ?string $contactPerson,
        public readonly ?string $website,
        public readonly ?string $notes,
        public readonly bool    $isActive,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}
}
