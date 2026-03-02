<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EmployeeRepository
{
    /** All employees with department and manager info. */
    public function all(?string $status = null, ?int $departmentId = null): array
    {
        $sql = '
            SELECT e.*,
                   d.name AS department_name,
                   CONCAT(m.first_name, " ", m.last_name) AS manager_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id AND d.is_deleted = 0
            LEFT JOIN employees m ON e.manager_id = m.id AND m.is_deleted = 0
            WHERE e.is_deleted = 0
        ';
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= ' AND e.status = ?';
            $params[] = $status;
        }
        if ($departmentId !== null && $departmentId > 0) {
            $sql .= ' AND e.department_id = ?';
            $params[] = $departmentId;
        }

        $sql .= ' ORDER BY e.last_name ASC, e.first_name ASC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single employee by ID. */
    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('
            SELECT e.*,
                   d.name AS department_name,
                   CONCAT(m.first_name, " ", m.last_name) AS manager_name,
                   u.username AS linked_username
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id AND d.is_deleted = 0
            LEFT JOIN employees m ON e.manager_id = m.id AND m.is_deleted = 0
            LEFT JOIN users u ON e.user_id = u.id AND u.is_deleted = 0
            WHERE e.id = ? AND e.is_deleted = 0
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Insert a new employee. */
    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare('
            INSERT INTO employees
                (employee_number, first_name, last_name, email, phone, title,
                 department_id, manager_id, user_id, hire_date, birth_date,
                 address, city, postal_code, country, employment_type, status, salary, notes, created_by)
            VALUES
                (:employee_number, :first_name, :last_name, :email, :phone, :title,
                 :department_id, :manager_id, :user_id, :hire_date, :birth_date,
                 :address, :city, :postal_code, :country, :employment_type, :status, :salary, :notes, :created_by)
        ');
        $stmt->execute($this->bindParams($data));
        return (int) Database::pdo()->lastInsertId();
    }

    /** Update an employee. */
    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare('
            UPDATE employees SET
                employee_number = :employee_number, first_name = :first_name, last_name = :last_name,
                email = :email, phone = :phone, title = :title,
                department_id = :department_id, manager_id = :manager_id, user_id = :user_id,
                hire_date = :hire_date, birth_date = :birth_date,
                address = :address, city = :city, postal_code = :postal_code, country = :country,
                employment_type = :employment_type, status = :status, salary = :salary, notes = :notes
            WHERE id = :id AND is_deleted = 0
        ');
        $params = $this->bindParams($data);
        $params['id'] = $id;
        unset($params['created_by']);
        $stmt->execute($params);
    }

    /** Soft-delete. */
    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE employees SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Check if employee_number is taken. */
    public function numberExists(string $number, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM employees WHERE employee_number = ? AND id != ? AND is_deleted = 0');
            $stmt->execute([$number, $excludeId]);
        } else {
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM employees WHERE employee_number = ? AND is_deleted = 0');
            $stmt->execute([$number]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Next suggested employee number. */
    public function nextNumber(): string
    {
        $max = Database::pdo()->query("SELECT MAX(employee_number) FROM employees WHERE employee_number REGEXP '^EMP[0-9]+$'")->fetchColumn();
        if ($max) {
            $num = (int) substr($max, 3) + 1;
        } else {
            $num = 1;
        }
        return 'EMP' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    /** All employees for manager dropdown (optionally excluding one). */
    public function allForManagerDropdown(?int $excludeId = null): array
    {
        $sql = "SELECT id, first_name, last_name, employee_number FROM employees WHERE is_deleted = 0 AND status = 'active'";
        $params = [];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY last_name ASC, first_name ASC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** All departments for dropdown. */
    public function allDepartments(): array
    {
        return Database::pdo()->query('SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name ASC')->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** All active users not yet linked to an employee (for dropdown). */
    public function availableUsers(?int $currentUserId = null): array
    {
        $sql = 'SELECT u.id, u.username, u.full_name FROM users u
                WHERE u.is_deleted = 0 AND u.is_active = 1
                AND u.id NOT IN (SELECT user_id FROM employees WHERE user_id IS NOT NULL AND is_deleted = 0';
        $params = [];
        if ($currentUserId !== null) {
            $sql .= ' AND user_id != ?';
            $params[] = $currentUserId;
        }
        $sql .= ') ORDER BY u.username ASC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Stats for dashboard. */
    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'total'       => (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE is_deleted = 0")->fetchColumn(),
            'active'      => (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE is_deleted = 0 AND status = 'active'")->fetchColumn(),
            'on_leave'    => (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE is_deleted = 0 AND status = 'on_leave'")->fetchColumn(),
            'terminated'  => (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE is_deleted = 0 AND status = 'terminated'")->fetchColumn(),
        ];
    }

    /** @return array<string, mixed> */
    private function bindParams(array $data): array
    {
        return [
            'employee_number' => $data['employee_number'],
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'] ?: null,
            'phone'           => $data['phone'] ?: null,
            'title'           => $data['title'] ?: null,
            'department_id'   => $data['department_id'] ?: null,
            'manager_id'      => $data['manager_id'] ?: null,
            'user_id'         => $data['user_id'] ?: null,
            'hire_date'       => $data['hire_date'] ?: null,
            'birth_date'      => $data['birth_date'] ?: null,
            'address'         => $data['address'] ?: null,
            'city'            => $data['city'] ?: null,
            'postal_code'     => $data['postal_code'] ?: null,
            'country'         => $data['country'] ?: 'Sverige',
            'employment_type' => $data['employment_type'] ?: 'full_time',
            'status'          => $data['status'] ?: 'active',
            'salary'          => $data['salary'] !== '' ? $data['salary'] : null,
            'notes'           => $data['notes'] ?: null,
            'created_by'      => $data['created_by'] ?? null,
        ];
    }
}
