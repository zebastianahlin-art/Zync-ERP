<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CertificateRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            'SELECT c.*,
                    CONCAT(e.first_name, \' \', e.last_name) AS employee_name,
                    ct.name AS certificate_type_name
             FROM certificates c
             LEFT JOIN employees e ON c.employee_id = e.id
             LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
             WHERE c.is_deleted = 0
             ORDER BY c.expiry_date ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT c.*,
                    CONCAT(e.first_name, \' \', e.last_name) AS employee_name,
                    ct.name AS certificate_type_name
             FROM certificates c
             LEFT JOIN employees e ON c.employee_id = e.id
             LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
             WHERE c.id = ? AND c.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO certificates
             (employee_id, certificate_type_id, issued_date, expiry_date, file_path, notes, created_by)
             VALUES (:employee_id, :certificate_type_id, :issued_date, :expiry_date, :file_path, :notes, :created_by)'
        );
        $stmt->execute([
            'employee_id'          => $data['employee_id'] ?: null,
            'certificate_type_id'  => $data['certificate_type_id'] ?: null,
            'issued_date'          => $data['issued_date'] ?: null,
            'expiry_date'          => $data['expiry_date'] ?: null,
            'file_path'            => $data['file_path'] ?: null,
            'notes'                => $data['notes'] ?: null,
            'created_by'           => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE certificates SET
             employee_id = :employee_id, certificate_type_id = :certificate_type_id,
             issued_date = :issued_date, expiry_date = :expiry_date,
             file_path = :file_path, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'employee_id'         => $data['employee_id'] ?: null,
            'certificate_type_id' => $data['certificate_type_id'] ?: null,
            'issued_date'         => $data['issued_date'] ?: null,
            'expiry_date'         => $data['expiry_date'] ?: null,
            'file_path'           => $data['file_path'] ?: null,
            'notes'               => $data['notes'] ?: null,
            'id'                  => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE certificates SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function allTypes(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM certificate_types WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
