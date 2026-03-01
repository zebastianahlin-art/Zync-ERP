<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Handles persistence for the Supplier model.
 */
class SupplierRepository
{
    /** Return all active suppliers ordered by name. */
    public function all(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, name, org_number, email, phone, address, city, postal_code, country,
                    contact_person, website, notes, is_active, created_at, updated_at
             FROM suppliers
             WHERE is_deleted = 0
             ORDER BY name ASC'
        );
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    /** Find a supplier by ID, or return null if not found. */
    public function find(int $id): ?Supplier
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, name, org_number, email, phone, address, city, postal_code, country,
                    contact_person, website, notes, is_active, created_at, updated_at
             FROM suppliers
             WHERE id = ? AND is_deleted = 0
             LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $this->hydrate($row) : null;
    }

    /**
     * Create a new supplier.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Supplier
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO suppliers (name, org_number, email, phone, address, city, postal_code,
                                    country, contact_person, website, notes, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['org_number'],
            $data['email'],
            $data['phone'] ?: null,
            $data['address'] ?: null,
            $data['city'] ?: null,
            $data['postal_code'] ?: null,
            $data['country'] ?: 'Sverige',
            $data['contact_person'] ?: null,
            $data['website'] ?: null,
            $data['notes'] ?: null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
        ]);

        $id = (int) Database::pdo()->lastInsertId();
        return $this->find($id) ?? throw new \RuntimeException('Failed to retrieve supplier after insertion.');
    }

    /**
     * Update an existing supplier.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE suppliers
             SET name = ?, org_number = ?, email = ?, phone = ?, address = ?, city = ?,
                 postal_code = ?, country = ?, contact_person = ?, website = ?, notes = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['name'],
            $data['org_number'],
            $data['email'],
            $data['phone'] ?: null,
            $data['address'] ?: null,
            $data['city'] ?: null,
            $data['postal_code'] ?: null,
            $data['country'] ?: 'Sverige',
            $data['contact_person'] ?: null,
            $data['website'] ?: null,
            $data['notes'] ?: null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id,
        ]);
    }

    /** Soft-delete a supplier by ID. */
    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE suppliers SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Check whether an email already exists (optionally excluding a given ID). */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare(
                'SELECT COUNT(*) FROM suppliers WHERE email = ? AND id != ? AND is_deleted = 0'
            );
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare(
                'SELECT COUNT(*) FROM suppliers WHERE email = ? AND is_deleted = 0'
            );
            $stmt->execute([$email]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Check whether an org number already exists (optionally excluding a given ID). */
    public function orgNumberExists(string $orgNumber, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare(
                'SELECT COUNT(*) FROM suppliers WHERE org_number = ? AND id != ? AND is_deleted = 0'
            );
            $stmt->execute([$orgNumber, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare(
                'SELECT COUNT(*) FROM suppliers WHERE org_number = ? AND is_deleted = 0'
            );
            $stmt->execute([$orgNumber]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /** @param array<string, mixed> $row */
    public function hydrate(array $row): Supplier
    {
        return new Supplier(
            id:            (int) $row['id'],
            name:          $row['name'],
            orgNumber:     $row['org_number'],
            email:         $row['email'],
            phone:         $row['phone'],
            address:       $row['address'],
            city:          $row['city'],
            postalCode:    $row['postal_code'],
            country:       $row['country'],
            contactPerson: $row['contact_person'],
            website:       $row['website'],
            notes:         $row['notes'],
            isActive:      (bool) $row['is_active'],
            createdAt:     $row['created_at'],
            updatedAt:     $row['updated_at'],
        );
    }
}
