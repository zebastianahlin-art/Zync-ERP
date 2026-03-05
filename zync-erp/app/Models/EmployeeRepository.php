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
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT e.*, d.name AS department_name
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.id
                 WHERE e.id = ? AND e.is_deleted = 0'
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

    public function findEmployee(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT e.*, d.name AS department_name,
                        m.first_name AS manager_first_name, m.last_name AS manager_last_name
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.id
                 LEFT JOIN employees m ON e.manager_id = m.id
                 WHERE e.id = :id AND e.is_deleted = 0'
            );
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateEmployee(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE employees SET
             first_name = :first_name, last_name = :last_name,
             employee_number = :employee_number, department_id = :department_id,
             position = :position, phone = :phone, email = :email,
             hire_date = :hire_date, end_date = :end_date,
             employment_type = :employment_type, status = :status,
             salary = :salary, notes = :notes,
             manager_id = :manager_id,
             emergency_contact_name = :emergency_contact_name,
             emergency_contact_phone = :emergency_contact_phone
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'first_name'              => $data['first_name'],
            'last_name'               => $data['last_name'],
            'employee_number'         => $data['employee_number'] ?: null,
            'department_id'           => $data['department_id'] ?: null,
            'position'                => $data['position'] ?: null,
            'phone'                   => $data['phone'] ?: null,
            'email'                   => $data['email'] ?: null,
            'hire_date'               => $data['hire_date'] ?: null,
            'end_date'                => $data['end_date'] ?: null,
            'employment_type'         => $data['employment_type'] ?: null,
            'status'                  => $data['status'] ?: 'active',
            'salary'                  => $data['salary'] !== '' ? $data['salary'] : null,
            'notes'                   => $data['notes'] ?: null,
            'manager_id'              => $data['manager_id'] ?: null,
            'emergency_contact_name'  => $data['emergency_contact_name'] ?: null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?: null,
            'id'                      => $id,
        ]);
    }

    public function deleteEmployee(int $id): void
    {
        $this->delete($id);
    }

    public function employeeCertificates(int $id): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT c.*, ct.name AS type_name
                 FROM certificates c
                 LEFT JOIN certificate_types ct ON c.certificate_type_id = ct.id
                 WHERE c.employee_id = :id AND c.is_deleted = 0
                 ORDER BY c.expiry_date ASC'
            );
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function employeeTraining(int $id): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT tp.*, tc.name AS course_name, ts.start_date
                 FROM training_participants tp
                 LEFT JOIN training_sessions ts ON tp.session_id = ts.id
                 LEFT JOIN training_courses tc ON ts.course_id = tc.id
                 WHERE tp.employee_id = :id AND tp.is_deleted = 0
                 ORDER BY ts.start_date DESC'
            );
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function employeeAttendance(int $id): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT * FROM attendance WHERE employee_id = :id AND is_deleted = 0 ORDER BY date DESC LIMIT 30'
            );
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function allManagers(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, first_name, last_name FROM employees WHERE is_deleted = 0 ORDER BY last_name ASC, first_name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

}