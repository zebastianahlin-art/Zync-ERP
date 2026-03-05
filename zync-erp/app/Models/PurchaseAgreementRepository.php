<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PurchaseAgreementRepository
{
    public function all(): array
    {
        $sql = "SELECT pa.*, s.name AS supplier_name, u.full_name AS responsible_name
                FROM purchase_agreements pa
                LEFT JOIN suppliers s ON pa.supplier_id = s.id
                LEFT JOIN users u ON pa.responsible_id = u.id
                WHERE pa.is_deleted = 0
                ORDER BY pa.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT pa.*, s.name AS supplier_name, u.full_name AS responsible_name,
                    u2.full_name AS created_by_name
             FROM purchase_agreements pa
             LEFT JOIN suppliers s ON pa.supplier_id = s.id
             LEFT JOIN users u ON pa.responsible_id = u.id
             LEFT JOIN users u2 ON pa.created_by = u2.id
             WHERE pa.id = ? AND pa.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->generateNumber();
        $stmt = Database::pdo()->prepare(
            "INSERT INTO purchase_agreements 
             (agreement_number, title, supplier_id, agreement_type, status,
              start_date, end_date, value, currency, responsible_id, description, terms, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['title'],
            $data['supplier_id'],
            $data['agreement_type'] ?? 'standard',
            $data['status'] ?? 'draft',
            $data['start_date'],
            $data['end_date'] ?: null,
            $data['value'] ?: null,
            $data['currency'] ?? 'SEK',
            $data['responsible_id'] ?: null,
            $data['description'] ?? null,
            $data['terms'] ?? null,
            $data['created_by'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE purchase_agreements 
             SET title = ?, supplier_id = ?, agreement_type = ?, status = ?,
                 start_date = ?, end_date = ?, value = ?, currency = ?,
                 responsible_id = ?, description = ?, terms = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['title'],
            $data['supplier_id'],
            $data['agreement_type'] ?? 'standard',
            $data['status'] ?? 'draft',
            $data['start_date'],
            $data['end_date'] ?: null,
            $data['value'] ?: null,
            $data['currency'] ?? 'SEK',
            $data['responsible_id'] ?: null,
            $data['description'] ?? null,
            $data['terms'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE purchase_agreements SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getExpiring(int $days = 30): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT pa.*, s.name AS supplier_name
             FROM purchase_agreements pa
             LEFT JOIN suppliers s ON pa.supplier_id = s.id
             WHERE pa.is_deleted = 0 AND pa.status = 'active'
               AND pa.end_date IS NOT NULL
               AND pa.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY pa.end_date ASC"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function uploadFile(int $id, string $filePath): void
    {
        $stmt = Database::pdo()->prepare("UPDATE purchase_agreements SET file_path = ? WHERE id = ?");
        $stmt->execute([$filePath, $id]);
    }

    public function history(): array
    {
        $sql = "SELECT pa.*, s.name AS supplier_name, u.full_name AS responsible_name
                FROM purchase_agreements pa
                LEFT JOIN suppliers s ON pa.supplier_id = s.id
                LEFT JOIN users u ON pa.responsible_id = u.id
                WHERE pa.is_deleted = 0
                  AND (pa.status IN ('expired', 'terminated')
                       OR (pa.end_date IS NOT NULL AND pa.end_date < NOW()))
                ORDER BY pa.created_at DESC";
        return Database::pdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function generateNumber(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM purchase_agreements WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return "AVT-{$year}-" . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
