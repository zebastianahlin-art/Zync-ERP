<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

class TrainingRepository
{
    // ─── Courses ──────────────────────────────────────────────

    public function allCourses(): array
    {
        $stmt = Database::pdo()->query('SELECT * FROM training_courses WHERE is_deleted = 0 ORDER BY name ASC');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findCourse(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM training_courses WHERE id = ? AND is_deleted = 0');
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createCourse(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO training_courses (name, description, provider, duration_hours, category, is_recurring, recurrence_months)
             VALUES (:name, :description, :provider, :duration_hours, :category, :is_recurring, :recurrence_months)'
        );
        $stmt->execute([
            'name'               => $data['name'],
            'description'        => $data['description'] ?? null,
            'provider'           => $data['provider'] ?? null,
            'duration_hours'     => $data['duration_hours'] ?: null,
            'category'           => $data['category'] ?? null,
            'is_recurring'       => isset($data['is_recurring']) ? 1 : 0,
            'recurrence_months'  => $data['recurrence_months'] ?: null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updateCourse(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE training_courses SET name = :name, description = :description, provider = :provider,
             duration_hours = :duration_hours, category = :category, is_recurring = :is_recurring,
             recurrence_months = :recurrence_months WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'              => $data['name'],
            'description'       => $data['description'] ?? null,
            'provider'          => $data['provider'] ?? null,
            'duration_hours'    => $data['duration_hours'] ?: null,
            'category'          => $data['category'] ?? null,
            'is_recurring'      => isset($data['is_recurring']) ? 1 : 0,
            'recurrence_months' => $data['recurrence_months'] ?: null,
            'id'                => $id,
        ]);
    }

    public function deleteCourse(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE training_courses SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    // ─── Sessions ─────────────────────────────────────────────

    public function allSessions(): array
    {
        $stmt = Database::pdo()->query(
            'SELECT ts.*, tc.name AS course_name
             FROM training_sessions ts
             JOIN training_courses tc ON tc.id = ts.course_id
             WHERE ts.is_deleted = 0
             ORDER BY ts.scheduled_date DESC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findSession(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT ts.*, tc.name AS course_name
             FROM training_sessions ts
             JOIN training_courses tc ON tc.id = ts.course_id
             WHERE ts.id = ? AND ts.is_deleted = 0'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createSession(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO training_sessions (course_id, scheduled_date, location, instructor, max_participants, status, notes)
             VALUES (:course_id, :scheduled_date, :location, :instructor, :max_participants, :status, :notes)'
        );
        $stmt->execute([
            'course_id'        => $data['course_id'],
            'scheduled_date'   => $data['scheduled_date'],
            'location'         => $data['location'] ?? null,
            'instructor'       => $data['instructor'] ?? null,
            'max_participants' => $data['max_participants'] ?: null,
            'status'           => $data['status'] ?? 'planned',
            'notes'            => $data['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }
}
