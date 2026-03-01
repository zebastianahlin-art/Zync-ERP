<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Article data-transfer object.
 */
class Article
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $articleNumber,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly string  $unit,
        public readonly ?float  $purchasePrice,
        public readonly float   $sellingPrice,
        public readonly float   $vatRate,
        public readonly ?string $category,
        public readonly ?int    $supplierId,
        public readonly ?string $supplierName,
        public readonly bool    $isActive,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}
}
