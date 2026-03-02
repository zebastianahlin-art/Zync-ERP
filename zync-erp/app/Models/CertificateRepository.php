<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CertificateRepository
{
    public function all(?int $employeeId = null, ?string $status = null, ?string $type = null): array
    {
        $sql = '
            SELECT c.*, CONCAT(e.first_name, " ", e.last_name) AS employee_name, e.employee_number
            FROM certificates c
            JOIN employees e ON c.employee_id = e.id AND e.is_deleted = 0
            WHERE c.is_deleted = 0
        ';
        $params = [];

        if ($employeeId) {
            $sql .= ' AND c.employee_id = ?';
            $params[] = $employeeId;
        }
        if ($status && $status !== '') {
            $sql .= ' AND c.status = ?';
            $params[] = $status;
        }
        if ($type && $type !== '') {
            $sql .= ' AND c.type = ?';
            $params[] = $type;
        }

        $sql .= ' ORDER BY c.expiry_date ASC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('
            SELECT c.*, CONCAT(e.first_name, " ", e.last_name) AS employee_name, e.employee_number
            FROM certificates c
            JOIN employees e ON c.employee_id = e.id
            WHERE c.id = ? AND c.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare('
            INSERT INTO certificates
                (employee_id, name, type, issuer, certificate_number, issued_date, expiry_date,
                 file_path, file_name, notes, status, created_by)
            VALUES
                (:employee_id, :name, :type, :issuer, :certificate_number, :issued_date, :expiry_date,
                 :file_path, :file_name, :notes, :status, :created_by)
        ');
        $stmt->execute($this->bind($data));
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare('
            UPDATE certificates SET
                employee_id = :employee_id, name = :name, type = :type, issuer = :issuer,
                certificate_number = :certificate_number, issued_date = :issued_date,
                expiry_date = :expiry_date, file_path = :file_path, file_name = :file_name,
                notes = :notes, status = :status
            WHERE id = :id AND is_deleted = 0
        ');
        $params = $this->bind($data);
        unset($params['created_by']);
        $params['id'] = $id;
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE certificates SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Beräkna och uppdatera status baserat på expiry_date. */
    public function refreshStatuses(): int
    {
        $pdo = Database::pdo();
        // Expired
        $pdo->exec("UPDATE certificates SET status = 'expired' WHERE is_deleted = 0 AND expiry_date IS NOT NULL AND expiry_date < CURDATE() AND status != 'revoked'");
        // Expiring within 60 days
        $pdo->exec("UPDATE certificates SET status = 'expiring' WHERE is_deleted = 0 AND expiry_date IS NOT NULL AND expiry_date >= CURDATE() AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY) AND status NOT IN ('revoked','expired')");
        // Active
        $pdo->exec("UPDATE certificates SET status = 'active' WHERE is_deleted = 0 AND expiry_date IS NOT NULL AND expiry_date > DATE_ADD(CURDATE(), INTERVAL 60 DAY) AND status NOT IN ('revoked')");
        // Active if no expiry
        $pdo->exec("UPDATE certificates SET status = 'active' WHERE is_deleted = 0 AND expiry_date IS NULL AND status NOT IN ('revoked')");
        return 1;
    }

    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'total'    => (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_deleted = 0")->fetchColumn(),
            'active'   => (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_deleted = 0 AND status = 'active'")->fetchColumn(),
            'expiring' => (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_deleted = 0 AND status = 'expiring'")->fetchColumn(),
            'expired'  => (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_deleted = 0 AND status = 'expired'")->fetchColumn(),
        ];
    }

    /** Utgående inom X dagar. */
    public function expiringWithin(int $days = 60): array
    {
        $stmt = Database::pdo()->prepare('
            SELECT c.*, CONCAT(e.first_name, " ", e.last_name) AS employee_name, e.employee_number
            FROM certificates c
            JOIN employees e ON c.employee_id = e.id AND e.is_deleted = 0
            WHERE c.is_deleted = 0 AND c.expiry_date IS NOT NULL
              AND c.expiry_date >= CURDATE() AND c.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
            ORDER BY c.expiry_date ASC
        ');
        $stmt->execute([$days]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allTypes(): array
    {
        return Database::pdo()->query('SELECT * FROM certificate_types WHERE is_deleted = 0 ORDER BY name')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allEmployees(): array
    {
        return Database::pdo()->query("SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, employee_number FROM employees WHERE is_deleted = 0 AND status = 'active' ORDER BY last_name, first_name")->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Certifikat per anställd (för profil-vy). */
    public function byEmployee(int $employeeId): array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM certificates WHERE employee_id = ? AND is_deleted = 0 ORDER BY expiry_date ASC');
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function bind(array $data): array
    {
        return [
            'employee_id'        => $data['employee_id'],
            'name'               => $data['name'],
            'type'               => $data['type'] ?: null,
            'issuer'             => $data['issuer'] ?: null,
            'certificate_number' => $data['certificate_number'] ?: null,
            'issued_date'        => $data['issued_date'] ?: null,
            'expiry_date'        => $data['expiry_date'] ?: null,
            'file_path'          => $data['file_path'] ?? null,
            'file_name'          => $data['file_name'] ?? null,
            'notes'              => $data['notes'] ?: null,
            'status'             => $data['status'] ?: 'active',
            'created_by'         => $data['created_by'] ?? null,
        ];
    }
}
