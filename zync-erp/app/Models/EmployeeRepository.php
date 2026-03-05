<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EmployeeRepository
{
    public function all(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT e.*, d.name AS department_name
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.id
                 WHERE e.is_deleted = 0
                 ORDER BY e.last_name ASC, e.first_name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT e.*, d.name AS department_name
             FROM employees e
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE e.id = ? AND e.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO employees
             (first_name, last_name, employee_number, department_id, position,
              phone, email, hire_date, created_by)
             VALUES (:first_name, :last_name, :employee_number, :department_id, :position,
                     :phone, :email, :hire_date, :created_by)'
        );
        $stmt->execute([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'employee_number'  => $data['employee_number'] ?: null,
            'department_id'    => $data['department_id'] ?: null,
            'position'         => $data['position'] ?: null,
            'phone'            => $data['phone'] ?: null,
            'email'            => $data['email'] ?: null,
            'hire_date'        => $data['hire_date'] ?: null,
            'created_by'       => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE employees SET
             first_name = :first_name, last_name = :last_name,
             employee_number = :employee_number, department_id = :department_id,
             position = :position, phone = :phone, email = :email, hire_date = :hire_date
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'employee_number' => $data['employee_number'] ?: null,
            'department_id'   => $data['department_id'] ?: null,
            'position'        => $data['position'] ?: null,
            'phone'           => $data['phone'] ?: null,
            'email'           => $data['email'] ?: null,
            'hire_date'       => $data['hire_date'] ?: null,
            'id'              => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE employees SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
