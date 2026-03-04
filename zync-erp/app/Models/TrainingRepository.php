<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TrainingRepository
{
    public function allCourses(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM training_courses WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createCourse(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO training_courses (name, description, duration_h, provider, category, is_mandatory, created_by)
             VALUES (:name, :description, :duration_h, :provider, :category, :is_mandatory, :created_by)'
        );
        $stmt->execute([
            'name'         => $data['name'],
            'description'  => $data['description'] ?: null,
            'duration_h'   => $data['duration_h'] ?: null,
            'provider'     => $data['provider'] ?: null,
            'category'     => $data['category'] ?: null,
            'is_mandatory' => $data['is_mandatory'] ?? 0,
            'created_by'   => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function allSessions(): array
    {
        return Database::pdo()->query(
            'SELECT s.*, c.name AS course_name
             FROM training_sessions s
             LEFT JOIN training_courses c ON s.course_id = c.id
             WHERE s.is_deleted = 0
             ORDER BY s.start_date DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allParticipants(): array
    {
        return Database::pdo()->query(
            'SELECT p.*, e.first_name, e.last_name, s.start_date, c.name AS course_name
             FROM training_participants p
             LEFT JOIN employees e ON p.employee_id = e.id
             LEFT JOIN training_sessions s ON p.session_id = s.id
             LEFT JOIN training_courses c ON s.course_id = c.id
             WHERE p.is_deleted = 0
             ORDER BY s.start_date DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
