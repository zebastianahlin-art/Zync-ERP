<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class CustomerServiceRepository
{
    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'open'             => (int) $pdo->query("SELECT COUNT(*) FROM cs_tickets WHERE status = 'open'       AND is_deleted = 0")->fetchColumn(),
            'in_progress'      => (int) $pdo->query("SELECT COUNT(*) FROM cs_tickets WHERE status = 'in_progress' AND is_deleted = 0")->fetchColumn(),
            'resolved'         => (int) $pdo->query("SELECT COUNT(*) FROM cs_tickets WHERE status = 'resolved'   AND is_deleted = 0")->fetchColumn(),
            'closed'           => (int) $pdo->query("SELECT COUNT(*) FROM cs_tickets WHERE status = 'closed'     AND is_deleted = 0")->fetchColumn(),
        ];
    }

    public function allTickets(): array
    {
        return Database::pdo()->query(
            'SELECT t.*, c.name AS customer_name, u.name AS assigned_name
             FROM cs_tickets t
             LEFT JOIN customers c ON t.customer_id = c.id
             LEFT JOIN users u     ON t.assigned_to  = u.id
             WHERE t.is_deleted = 0
             ORDER BY t.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function myTickets(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT t.*, c.name AS customer_name, u.name AS assigned_name
             FROM cs_tickets t
             LEFT JOIN customers c ON t.customer_id = c.id
             LEFT JOIN users u     ON t.assigned_to  = u.id
             WHERE t.is_deleted = 0 AND t.assigned_to = ?
             ORDER BY t.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findTicket(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT t.*, c.name AS customer_name, u.name AS assigned_name
             FROM cs_tickets t
             LEFT JOIN customers c ON t.customer_id = c.id
             LEFT JOIN users u     ON t.assigned_to  = u.id
             WHERE t.id = ? AND t.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createTicket(array $data): int
    {
        $pdo   = Database::pdo();
        $year  = date('Y');
        $count = (int) $pdo->query('SELECT COUNT(*) FROM cs_tickets')->fetchColumn() + 1;
        $number = 'CS-' . $year . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare(
            'INSERT INTO cs_tickets
             (ticket_number, title, description, customer_id, contact_person, contact_email,
              contact_phone, category, priority, status, assigned_to, resolution, created_by)
             VALUES
             (:ticket_number, :title, :description, :customer_id, :contact_person, :contact_email,
              :contact_phone, :category, :priority, :status, :assigned_to, :resolution, :created_by)'
        );
        $stmt->execute([
            'ticket_number'  => $number,
            'title'          => $data['title'],
            'description'    => $data['description'] ?: null,
            'customer_id'    => $data['customer_id']   ?: null,
            'contact_person' => $data['contact_person'] ?: null,
            'contact_email'  => $data['contact_email']  ?: null,
            'contact_phone'  => $data['contact_phone']  ?: null,
            'category'       => $data['category']   ?? 'inquiry',
            'priority'       => $data['priority']   ?? 'normal',
            'status'         => $data['status']     ?? 'open',
            'assigned_to'    => $data['assigned_to']  ?: null,
            'resolution'     => $data['resolution']   ?: null,
            'created_by'     => $data['created_by']   ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updateTicket(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE cs_tickets SET
             title = :title, description = :description, customer_id = :customer_id,
             contact_person = :contact_person, contact_email = :contact_email,
             contact_phone = :contact_phone, category = :category, priority = :priority,
             status = :status, assigned_to = :assigned_to, resolution = :resolution
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'title'          => $data['title'],
            'description'    => $data['description']    ?: null,
            'customer_id'    => $data['customer_id']    ?: null,
            'contact_person' => $data['contact_person'] ?: null,
            'contact_email'  => $data['contact_email']  ?: null,
            'contact_phone'  => $data['contact_phone']  ?: null,
            'category'       => $data['category']   ?? 'inquiry',
            'priority'       => $data['priority']   ?? 'normal',
            'status'         => $data['status']     ?? 'open',
            'assigned_to'    => $data['assigned_to']  ?: null,
            'resolution'     => $data['resolution']   ?: null,
            'id'             => $id,
        ]);
    }

    public function deleteTicket(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE cs_tickets SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function updateTicketStatus(int $id, string $status): void
    {
        $now  = date('Y-m-d H:i:s');
        $extra = '';
        if ($status === 'resolved') {
            $extra = ', resolved_at = :ts';
        } elseif ($status === 'closed') {
            $extra = ', closed_at = :ts';
        }

        $sql  = "UPDATE cs_tickets SET status = :status{$extra} WHERE id = :id AND is_deleted = 0";
        $stmt = Database::pdo()->prepare($sql);
        $params = ['status' => $status, 'id' => $id];
        if ($extra !== '') {
            $params['ts'] = $now;
        }
        $stmt->execute($params);
    }

    public function addComment(int $ticketId, int $userId, string $comment, bool $isInternal = false): int
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO cs_ticket_comments (ticket_id, user_id, comment, is_internal)
             VALUES (:ticket_id, :user_id, :comment, :is_internal)'
        );
        $stmt->execute([
            'ticket_id'   => $ticketId,
            'user_id'     => $userId,
            'comment'     => $comment,
            'is_internal' => $isInternal ? 1 : 0,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function ticketComments(int $ticketId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT tc.*, u.name AS user_name
             FROM cs_ticket_comments tc
             LEFT JOIN users u ON tc.user_id = u.id
             WHERE tc.ticket_id = ?
             ORDER BY tc.created_at ASC'
        );
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allUsers(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM users WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allCustomers(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
}
