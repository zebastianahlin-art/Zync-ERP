<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\ArticleRepository;
use PHPUnit\Framework\TestCase;

class ArticleRepositoryTest extends TestCase
{
    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'id'             => 1,
            'article_number' => 'ART-001',
            'name'           => 'Testprodukt',
            'description'    => 'En beskrivning',
            'unit'           => 'st',
            'purchase_price' => '50.00',
            'selling_price'  => '99.00',
            'vat_rate'       => '25.00',
            'category'       => 'Elektronik',
            'supplier_id'    => '3',
            'supplier_name'  => 'Acme AB',
            'is_active'      => 1,
            'created_at'     => '2025-01-01 00:00:00',
            'updated_at'     => '2025-01-01 00:00:00',
        ], $overrides);
    }

    public function testHydrateReturnsArticleDto(): void
    {
        $repo    = new ArticleRepository();
        $row     = $this->makeRow();

        $article = $repo->hydrate($row);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame(1, $article->id);
        $this->assertSame('ART-001', $article->articleNumber);
        $this->assertSame('Testprodukt', $article->name);
        $this->assertSame('En beskrivning', $article->description);
        $this->assertSame('st', $article->unit);
        $this->assertSame(50.0, $article->purchasePrice);
        $this->assertSame(99.0, $article->sellingPrice);
        $this->assertSame(25.0, $article->vatRate);
        $this->assertSame('Elektronik', $article->category);
        $this->assertSame(3, $article->supplierId);
        $this->assertSame('Acme AB', $article->supplierName);
        $this->assertTrue($article->isActive);
        $this->assertSame('2025-01-01 00:00:00', $article->createdAt);
        $this->assertSame('2025-01-01 00:00:00', $article->updatedAt);
    }

    public function testHydrateHandlesNullableFields(): void
    {
        $repo    = new ArticleRepository();
        $row     = $this->makeRow([
            'description'    => null,
            'purchase_price' => null,
            'category'       => null,
            'supplier_id'    => null,
            'supplier_name'  => null,
            'is_active'      => 0,
        ]);

        $article = $repo->hydrate($row);

        $this->assertNull($article->description);
        $this->assertNull($article->purchasePrice);
        $this->assertNull($article->category);
        $this->assertNull($article->supplierId);
        $this->assertNull($article->supplierName);
        $this->assertFalse($article->isActive);
    }

    public function testHydrateIdIsInt(): void
    {
        $repo    = new ArticleRepository();
        $article = $repo->hydrate($this->makeRow(['id' => '7']));

        $this->assertSame(7, $article->id);
    }

    public function testHydratePurchasePriceIsFloat(): void
    {
        $repo    = new ArticleRepository();
        $article = $repo->hydrate($this->makeRow(['purchase_price' => '123.45']));

        $this->assertSame(123.45, $article->purchasePrice);
    }
}
