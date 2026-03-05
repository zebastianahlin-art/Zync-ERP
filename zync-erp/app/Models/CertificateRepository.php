<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CertificateRepository
{
    public function all(): array
    {
        try {
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
        } catch (\Exception $e) {
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
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
        } catch (\Exception $e) {
            return null;
        }
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
        try {
            return Database::pdo()->query(
                'SELECT id, name FROM certificate_types WHERE is_deleted = 0 ORDER BY name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function expiringCertificates(int $days = 30): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT c.*,
                        CONCAT(e.first_name, \' \', e.last_name) AS employee_name,
                        ct.name AS certificate_type_name
                 FROM certificates c
                 LEFT JOIN employees e ON c.employee_id = e.id
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                   AND c.is_deleted = 0
                 ORDER BY c.expiry_date ASC'
            );
            $stmt->execute(['days' => $days]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function expiredCertificates(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT c.*,
                        CONCAT(e.first_name, \' \', e.last_name) AS employee_name,
                        ct.name AS certificate_type_name
                 FROM certificates c
                 LEFT JOIN employees e ON c.employee_id = e.id
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.expiry_date < CURDATE() AND c.is_deleted = 0
                 ORDER BY c.expiry_date DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function certificatesByEmployee(int $employeeId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT c.*,
                        ct.name AS certificate_type_name
                 FROM certificates c
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.employee_id = :id AND c.is_deleted = 0
                 ORDER BY c.expiry_date ASC'
            );
            $stmt->execute(['id' => $employeeId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function expiringInDays(int $days): array
    {
        return $this->expiringCertificates($days);
    }

    public function renew(int $id, array $data): int
    {
        $original = $this->find($id);
        if ($original === null) {
            throw new \RuntimeException("Certificate {$id} not found");
        }

        $stmt = Database::pdo()->prepare(
            'INSERT INTO certificates
             (employee_id, certificate_type_id, issued_date, expiry_date, file_path, notes, created_by, renewed_from_id)
             VALUES (:employee_id, :certificate_type_id, :issued_date, :expiry_date, :file_path, :notes, :created_by, :renewed_from_id)'
        );

        // Check if renewed_from_id column exists, skip if not
        try {
            $stmt->execute([
                'employee_id'         => $original['employee_id'],
                'certificate_type_id' => $original['certificate_type_id'],
                'issued_date'         => $data['issued_date'] ?? date('Y-m-d'),
                'expiry_date'         => $data['expiry_date'] ?? null,
                'file_path'           => $data['file_path'] ?? $original['file_path'] ?? null,
                'notes'               => $data['notes'] ?? null,
                'created_by'          => $data['created_by'] ?? null,
                'renewed_from_id'     => $id,
            ]);
        } catch (\Exception $e) {
            // Fallback without renewed_from_id
            $stmt2 = Database::pdo()->prepare(
                'INSERT INTO certificates
                 (employee_id, certificate_type_id, issued_date, expiry_date, file_path, notes, created_by)
                 VALUES (:employee_id, :certificate_type_id, :issued_date, :expiry_date, :file_path, :notes, :created_by)'
            );
            $stmt2->execute([
                'employee_id'         => $original['employee_id'],
                'certificate_type_id' => $original['certificate_type_id'],
                'issued_date'         => $data['issued_date'] ?? date('Y-m-d'),
                'expiry_date'         => $data['expiry_date'] ?? null,
                'file_path'           => $data['file_path'] ?? $original['file_path'] ?? null,
                'notes'               => $data['notes'] ?? null,
                'created_by'          => $data['created_by'] ?? null,
            ]);
        }

        return (int) Database::pdo()->lastInsertId();
    }

    public function allCourses(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, name FROM training_courses WHERE is_deleted = 0 ORDER BY name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
}
