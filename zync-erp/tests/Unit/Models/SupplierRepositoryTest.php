<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Supplier;
use App\Models\SupplierRepository;
use PHPUnit\Framework\TestCase;

class SupplierRepositoryTest extends TestCase
{
    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'id'             => 1,
            'name'           => 'Acme AB',
            'org_number'     => '556000-0001',
            'email'          => 'info@acme.se',
            'phone'          => '08-123456',
            'address'        => 'Industrivägen 1',
            'city'           => 'Stockholm',
            'postal_code'    => '11120',
            'country'        => 'Sverige',
            'contact_person' => 'Anna Andersson',
            'website'        => 'https://acme.se',
            'notes'          => null,
            'is_active'      => 1,
            'created_at'     => '2025-01-01 00:00:00',
            'updated_at'     => '2025-01-01 00:00:00',
        ], $overrides);
    }

    public function testHydrateReturnsSupplierDto(): void
    {
        $repo = new SupplierRepository();
        $row  = $this->makeRow();

        $supplier = $repo->hydrate($row);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertSame(1, $supplier->id);
        $this->assertSame('Acme AB', $supplier->name);
        $this->assertSame('556000-0001', $supplier->orgNumber);
        $this->assertSame('info@acme.se', $supplier->email);
        $this->assertSame('08-123456', $supplier->phone);
        $this->assertSame('Industrivägen 1', $supplier->address);
        $this->assertSame('Stockholm', $supplier->city);
        $this->assertSame('11120', $supplier->postalCode);
        $this->assertSame('Sverige', $supplier->country);
        $this->assertSame('Anna Andersson', $supplier->contactPerson);
        $this->assertSame('https://acme.se', $supplier->website);
        $this->assertNull($supplier->notes);
        $this->assertTrue($supplier->isActive);
        $this->assertSame('2025-01-01 00:00:00', $supplier->createdAt);
        $this->assertSame('2025-01-01 00:00:00', $supplier->updatedAt);
    }

    public function testHydrateHandlesNullableFields(): void
    {
        $repo = new SupplierRepository();
        $row  = $this->makeRow([
            'phone'          => null,
            'address'        => null,
            'city'           => null,
            'postal_code'    => null,
            'contact_person' => null,
            'website'        => null,
            'notes'          => 'some note',
            'is_active'      => 0,
        ]);

        $supplier = $repo->hydrate($row);

        $this->assertNull($supplier->phone);
        $this->assertNull($supplier->address);
        $this->assertNull($supplier->city);
        $this->assertNull($supplier->postalCode);
        $this->assertNull($supplier->contactPerson);
        $this->assertNull($supplier->website);
        $this->assertSame('some note', $supplier->notes);
        $this->assertFalse($supplier->isActive);
    }

    public function testHydrateIdIsInt(): void
    {
        $repo     = new SupplierRepository();
        $supplier = $repo->hydrate($this->makeRow(['id' => '42']));

        $this->assertSame(42, $supplier->id);
    }
}
