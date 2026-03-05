<?php

declare(strict_types=1);

namespace App\Core;

/**
 * NotificationService — Hanterar in-app-notifikationer.
 */
class NotificationService
{
    public function __construct(private readonly \PDO $pdo) {}

    /**
     * Skapa och skicka en notifikation till en användare.
     */
    public function send(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $link = null
    ): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notifications (user_id, type, title, message, link)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $type, $title, $message, $link]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Antal olästa notifikationer för en användare.
     */
    public function unreadCount(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hämta de senaste notifikationerna för en användare.
     *
     * @return array<int, array<string, mixed>>
     */
    public function forUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM notifications
              WHERE user_id = ?
              ORDER BY created_at DESC
              LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Markera en notifikation som läst.
     */
    public function markRead(int $id): void
    {
        $this->pdo->prepare(
            "UPDATE notifications SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE id = ? AND is_read = 0"
        )->execute([$id]);
    }

    /**
     * Markera alla notifikationer som lästa för en användare.
     */
    public function markAllRead(int $userId): void
    {
        $this->pdo->prepare(
            "UPDATE notifications SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE user_id = ? AND is_read = 0"
        )->execute([$userId]);
    }
}
