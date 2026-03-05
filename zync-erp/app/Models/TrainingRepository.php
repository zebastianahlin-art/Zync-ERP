<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TrainingRepository
{
    public function allCourses(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT * FROM training_courses WHERE is_deleted = 0 ORDER BY name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
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
        try {
            return Database::pdo()->query(
                'SELECT s.*, c.name AS course_name
                 FROM training_sessions s
                 LEFT JOIN training_courses c ON s.course_id = c.id
                 WHERE s.is_deleted = 0
                 ORDER BY s.start_date DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function allParticipants(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT p.*, e.first_name, e.last_name, s.start_date, c.name AS course_name
                 FROM training_participants p
                 LEFT JOIN employees e ON p.employee_id = e.id
                 LEFT JOIN training_sessions s ON p.session_id = s.id
                 LEFT JOIN training_courses c ON s.course_id = c.id
                 WHERE p.is_deleted = 0
                 ORDER BY s.start_date DESC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function findCourse(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT tc.*,
                 (SELECT COUNT(*) FROM training_sessions s WHERE s.course_id = tc.id AND s.is_deleted = 0) AS session_count
                 FROM training_courses tc WHERE tc.id = ? AND tc.is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateCourse(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE training_courses SET name=:name, description=:description, duration_h=:duration_h,
             provider=:provider, category=:category, is_mandatory=:is_mandatory
             WHERE id=:id AND is_deleted=0'
        );
        $stmt->execute([
            'name'         => $data['name'],
            'description'  => $data['description'] ?: null,
            'duration_h'   => $data['duration_h'] ?: null,
            'provider'     => $data['provider'] ?: null,
            'category'     => $data['category'] ?: null,
            'is_mandatory' => $data['is_mandatory'] ?? 0,
            'id'           => $id,
        ]);
    }

    public function deleteCourse(int $id): void
    {
        Database::pdo()->prepare('UPDATE training_courses SET is_deleted=1 WHERE id=?')->execute([$id]);
    }

    public function createSession(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO training_sessions (course_id, start_date, end_date, location, trainer, max_participants, status, created_by)
             VALUES (:course_id, :start_date, :end_date, :location, :trainer, :max_participants, :status, :created_by)'
        );
        $stmt->execute([
            'course_id'       => $data['course_id'],
            'start_date'      => $data['start_date'] ?: null,
            'end_date'        => $data['end_date'] ?: null,
            'location'        => $data['location'] ?: null,
            'trainer'         => $data['trainer'] ?: null,
            'max_participants'=> $data['max_participants'] ?: null,
            'status'          => $data['status'] ?? 'planned',
            'created_by'      => $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function findSession(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT ts.*, tc.name AS course_name,
                 (SELECT COUNT(*) FROM training_participants tp WHERE tp.session_id = ts.id AND tp.is_deleted = 0) AS participant_count
                 FROM training_sessions ts
                 LEFT JOIN training_courses tc ON ts.course_id = tc.id
                 WHERE ts.id = ? AND ts.is_deleted = 0'
            );
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateSession(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE training_sessions SET course_id=:course_id, start_date=:start_date, end_date=:end_date,
             location=:location, trainer=:trainer, max_participants=:max_participants, status=:status
             WHERE id=:id AND is_deleted=0'
        );
        $stmt->execute([
            'course_id'       => $data['course_id'] ?: null,
            'start_date'      => $data['start_date'] ?: null,
            'end_date'        => $data['end_date'] ?: null,
            'location'        => $data['location'] ?: null,
            'trainer'         => $data['trainer'] ?: null,
            'max_participants'=> $data['max_participants'] ?: null,
            'status'          => $data['status'] ?? 'planned',
            'id'              => $id,
        ]);
    }

    public function deleteSession(int $id): void
    {
        Database::pdo()->prepare('UPDATE training_sessions SET is_deleted=1 WHERE id=?')->execute([$id]);
    }

    public function sessionParticipants(int $sessionId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT tp.*, e.first_name, e.last_name
                 FROM training_participants tp
                 LEFT JOIN employees e ON tp.employee_id = e.id
                 WHERE tp.session_id = :id AND tp.is_deleted = 0
                 ORDER BY e.last_name ASC'
            );
            $stmt->execute(['id' => $sessionId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function addParticipant(int $sessionId, int $employeeId): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO training_participants (session_id, employee_id, status) VALUES (:session_id, :employee_id, :status)'
        );
        $stmt->execute(['session_id'=>$sessionId,'employee_id'=>$employeeId,'status'=>'registered']);
        return (int) Database::pdo()->lastInsertId();
    }

    public function removeParticipant(int $participantId): void
    {
        Database::pdo()->prepare('UPDATE training_participants SET is_deleted=1 WHERE id=?')->execute([$participantId]);
    }

    public function updateParticipantStatus(int $participantId, string $status): void
    {
        $stmt = Database::pdo()->prepare('UPDATE training_participants SET status=:status WHERE id=:id');
        $stmt->execute(['status'=>$status,'id'=>$participantId]);
    }

    public function upcomingSessions(): array
    {
        try {
            return Database::pdo()->query(
                "SELECT ts.*, tc.name AS course_name
                 FROM training_sessions ts
                 LEFT JOIN training_courses tc ON ts.course_id = tc.id
                 WHERE ts.is_deleted = 0 AND ts.status = 'planned' AND ts.start_date >= CURDATE()
                 ORDER BY ts.start_date ASC LIMIT 20"
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function availableSlots(int $sessionId): int
    {
        try {
            $session = $this->findSession($sessionId);
            if ($session === null) {
                return 0;
            }
            $max = (int) ($session['max_participants'] ?? 0);
            if ($max <= 0) {
                return 999; // No limit set
            }
            $count = (int) ($session['participant_count'] ?? 0);
            return max(0, $max - $count);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function bookParticipant(int $sessionId, int $employeeId, string $status = 'registered'): int
    {
        // Check if already booked
        $stmt = Database::pdo()->prepare(
            'SELECT id FROM training_participants WHERE session_id = :sid AND employee_id = :eid AND is_deleted = 0 LIMIT 1'
        );
        $stmt->execute(['sid' => $sessionId, 'eid' => $employeeId]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            return (int) $existing;
        }

        return $this->addParticipant($sessionId, $employeeId);
    }

    public function cancelParticipant(int $participantId, string $reason = ''): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE training_participants SET status = :status, cancelled_at = NOW(), cancel_reason = :reason WHERE id = :id'
        );
        $stmt->execute(['status' => 'cancelled', 'reason' => $reason ?: null, 'id' => $participantId]);
    }

    public function courseCategories(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT DISTINCT category FROM training_courses WHERE is_deleted = 0 AND category IS NOT NULL ORDER BY category ASC'
            )->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function allCoursesFiltered(?string $category = null, ?string $search = null): array
    {
        try {
            $where = ['tc.is_deleted = 0'];
            $params = [];
            if ($category !== null && $category !== '') {
                $where[] = 'tc.category = :category';
                $params['category'] = $category;
            }
            if ($search !== null && $search !== '') {
                $where[] = '(tc.name LIKE :search OR tc.description LIKE :search2)';
                $params['search']  = '%' . $search . '%';
                $params['search2'] = '%' . $search . '%';
            }
            $sql  = 'SELECT tc.*,
                     (SELECT COUNT(*) FROM training_sessions s WHERE s.course_id = tc.id AND s.is_deleted = 0) AS session_count
                     FROM training_courses tc
                     WHERE ' . implode(' AND ', $where) . '
                     ORDER BY tc.name ASC';
            $stmt = Database::pdo()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function allEmployees(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, first_name, last_name FROM employees WHERE is_deleted = 0 ORDER BY last_name ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
}
