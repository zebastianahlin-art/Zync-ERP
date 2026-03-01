<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Handles persistence for the Customer model.
 */
class CustomerRepository
{
    /** Return all customers ordered by name. */
    public function all(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, name, org_number, email, phone, address, created_at, updated_at FROM customers ORDER BY name ASC'
        );
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    /** Find a customer by ID, or return null if not found. */
    public function find(int $id): ?Customer
    {
        $stmt = Database::pdo()->prepare(
            'SELECT id, name, org_number, email, phone, address, created_at, updated_at FROM customers WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $this->hydrate($row) : null;
    }

    /**
     * Create a new customer.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Customer
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO customers (name, org_number, email, phone, address) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['org_number'],
            $data['email'],
            $data['phone'] ?: null,
            $data['address'] ?: null,
        ]);

        $id = (int) Database::pdo()->lastInsertId();
        return $this->find($id) ?? throw new \RuntimeException('Failed to retrieve customer after insertion.');
    }

    /**
     * Update an existing customer.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE customers SET name = ?, org_number = ?, email = ?, phone = ?, address = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['name'],
            $data['org_number'],
            $data['email'],
            $data['phone'] ?: null,
            $data['address'] ?: null,
            $id,
        ]);
    }

    /** Delete a customer by ID. */
    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM customers WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Customer
    {
        return new Customer(
            id:        (int) $row['id'],
            name:      $row['name'],
            orgNumber: $row['org_number'],
            email:     $row['email'],
            phone:     $row['phone'],
            address:   $row['address'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at'],
        );
    }
}
