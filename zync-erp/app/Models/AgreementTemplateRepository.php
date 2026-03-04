<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class AgreementTemplateRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            "SELECT pat.*, s.name AS supplier_name
             FROM purchase_agreement_templates pat
             LEFT JOIN suppliers s ON pat.supplier_id = s.id
             WHERE pat.is_deleted = 0
             ORDER BY pat.name ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT pat.*, s.name AS supplier_name
             FROM purchase_agreement_templates pat
             LEFT JOIN suppliers s ON pat.supplier_id = s.id
             WHERE pat.id = ? AND pat.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO purchase_agreement_templates
                (name, description, supplier_id, default_terms,
                 default_payment_terms, default_delivery_terms, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['description']             ?? null,
            $data['supplier_id']             ?? null,
            $data['default_terms']           ?? null,
            $data['default_payment_terms']   ?? null,
            $data['default_delivery_terms']  ?? null,
            $data['is_active']               ?? 1,
            $data['created_by']              ?? null,
        ]);

        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        Database::pdo()->prepare(
            "UPDATE purchase_agreement_templates
             SET name                   = ?,
                 description            = ?,
                 supplier_id            = ?,
                 default_terms          = ?,
                 default_payment_terms  = ?,
                 default_delivery_terms = ?,
                 is_active              = ?
             WHERE id = ?"
        )->execute([
            $data['name'],
            $data['description']             ?? null,
            $data['supplier_id']             ?? null,
            $data['default_terms']           ?? null,
            $data['default_payment_terms']   ?? null,
            $data['default_delivery_terms']  ?? null,
            $data['is_active']               ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE purchase_agreement_templates SET is_deleted = 1 WHERE id = ?"
        )->execute([$id]);
    }

    public function createAgreementFromTemplate(int $templateId, array $overrides): int
    {
        $pdo      = Database::pdo();
        $template = $this->find($templateId);

        if (!$template) {
            throw new \InvalidArgumentException("Template #{$templateId} not found.");
        }

        $stmt = $pdo->prepare(
            "INSERT INTO purchase_agreements
                (agreement_number, title, supplier_id, payment_terms, delivery_terms, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            '',  // temporary; updated after we have the id
            $overrides['title']          ?? $template['name'],
            $overrides['supplier_id']    ?? $template['supplier_id'],
            $overrides['payment_terms']  ?? $template['default_payment_terms'],
            $overrides['delivery_terms'] ?? $template['default_delivery_terms'],
            $overrides['notes']          ?? $template['default_terms'],
            $overrides['created_by']     ?? null,
        ]);

        $newId  = (int) $pdo->lastInsertId();
        $number = 'AGR-' . date('Y') . '-' . str_pad((string) $newId, 4, '0', STR_PAD_LEFT);

        $pdo->prepare(
            "UPDATE purchase_agreements SET agreement_number = ? WHERE id = ?"
        )->execute([$number, $newId]);

        return $newId;
    }
}
