<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Handles persistence for the Article model.
 */
class ArticleRepository
{
    /** Return all active articles with supplier name, ordered by article number. */
    public function all(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT a.id, a.article_number, a.name, a.description, a.unit,
                    a.purchase_price, a.selling_price, a.vat_rate, a.category,
                    a.supplier_id, s.name AS supplier_name, a.is_active,
                    a.created_at, a.updated_at
             FROM articles a
             LEFT JOIN suppliers s ON s.id = a.supplier_id AND s.is_deleted = 0
             WHERE a.is_deleted = 0
             ORDER BY a.article_number ASC'
        );
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    /** Find an article by ID, or return null if not found. */
    public function find(int $id): ?Article
    {
        $stmt = Database::pdo()->prepare(
            'SELECT a.id, a.article_number, a.name, a.description, a.unit,
                    a.purchase_price, a.selling_price, a.vat_rate, a.category,
                    a.supplier_id, s.name AS supplier_name, a.is_active,
                    a.created_at, a.updated_at
             FROM articles a
             LEFT JOIN suppliers s ON s.id = a.supplier_id AND s.is_deleted = 0
             WHERE a.id = ? AND a.is_deleted = 0
             LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $this->hydrate($row) : null;
    }

    /**
     * Create a new article.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Article
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO articles (article_number, name, description, unit, purchase_price,
                                   selling_price, vat_rate, category, supplier_id, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['article_number'],
            $data['name'],
            $data['description'] ?: null,
            $data['unit'],
            $data['purchase_price'] !== '' ? (float) $data['purchase_price'] : null,
            (float) $data['selling_price'],
            (float) $data['vat_rate'],
            $data['category'] ?: null,
            $data['supplier_id'] !== '' ? (int) $data['supplier_id'] : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
        ]);

        $id = (int) Database::pdo()->lastInsertId();
        return $this->find($id) ?? throw new \RuntimeException('Failed to retrieve article after insertion.');
    }

    /**
     * Update an existing article.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE articles
             SET article_number = ?, name = ?, description = ?, unit = ?, purchase_price = ?,
                 selling_price = ?, vat_rate = ?, category = ?, supplier_id = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['article_number'],
            $data['name'],
            $data['description'] ?: null,
            $data['unit'],
            $data['purchase_price'] !== '' ? (float) $data['purchase_price'] : null,
            (float) $data['selling_price'],
            (float) $data['vat_rate'],
            $data['category'] ?: null,
            $data['supplier_id'] !== '' ? (int) $data['supplier_id'] : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id,
        ]);
    }

    /** Soft-delete an article by ID. */
    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE articles SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Check whether an article number already exists (optionally excluding a given ID). */
    public function articleNumberExists(string $number, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare(
                'SELECT COUNT(*) FROM articles WHERE article_number = ? AND id != ? AND is_deleted = 0'
            );
            $stmt->execute([$number, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare(
                'SELECT COUNT(*) FROM articles WHERE article_number = ? AND is_deleted = 0'
            );
            $stmt->execute([$number]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Return active suppliers for dropdown (id, name). */
    public function allSuppliers(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT id, name FROM suppliers WHERE is_deleted = 0 AND is_active = 1 ORDER BY name ASC'
        );
        return $stmt->fetchAll();
    }

    /** @param array<string, mixed> $row */
    public function hydrate(array $row): Article
    {
        return new Article(
            id:            (int) $row['id'],
            articleNumber: $row['article_number'],
            name:          $row['name'],
            description:   $row['description'],
            unit:          $row['unit'],
            purchasePrice: $row['purchase_price'] !== null ? (float) $row['purchase_price'] : null,
            sellingPrice:  (float) $row['selling_price'],
            vatRate:       (float) $row['vat_rate'],
            category:      $row['category'],
            supplierId:    $row['supplier_id'] !== null ? (int) $row['supplier_id'] : null,
            supplierName:  $row['supplier_name'] ?? null,
            isActive:      (bool) $row['is_active'],
            createdAt:     $row['created_at'],
            updatedAt:     $row['updated_at'],
        );
    }

    /** Return all articles as plain arrays (for dropdowns/views) */
    public function allAsArray(): array
    {
        $stmt = \App\Core\Database::pdo()->query(
            'SELECT id, article_number, name, description, unit, purchase_price, selling_price, vat_rate, category
             FROM articles WHERE is_deleted = 0 ORDER BY article_number ASC'
        );
        return $stmt->fetchAll();
    }

    /** Find article as array */
    public function findAsArray(int $id): ?array
    {
        $stmt = \App\Core\Database::pdo()->prepare(
            'SELECT id, article_number, name, description, unit, purchase_price, selling_price, vat_rate, category
             FROM articles WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
