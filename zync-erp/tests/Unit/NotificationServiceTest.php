<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\NotificationService;
use PHPUnit\Framework\TestCase;

class NotificationServiceTest extends TestCase
{
    private \PDO $pdo;
    private NotificationService $service;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("CREATE TABLE notifications (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            user_id    INTEGER NOT NULL,
            type       TEXT NOT NULL,
            title      TEXT NOT NULL,
            message    TEXT NULL,
            link       TEXT NULL,
            is_read    INTEGER NOT NULL DEFAULT 0,
            read_at    DATETIME NULL
        )");

        $this->service = new NotificationService($this->pdo);
    }

    public function testSendCreatesNotification(): void
    {
        $id = $this->service->send(1, 'info', 'Testnotis', 'Meddelandetext', '/test');

        $this->assertGreaterThan(0, $id);

        $stmt = $this->pdo->query("SELECT * FROM notifications WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($row);
        $this->assertSame('1', (string) $row['user_id']);
        $this->assertSame('info', $row['type']);
        $this->assertSame('Testnotis', $row['title']);
        $this->assertSame('Meddelandetext', $row['message']);
        $this->assertSame('/test', $row['link']);
        $this->assertSame('0', (string) $row['is_read']);
    }

    public function testSendWithNullMessageAndLink(): void
    {
        $id = $this->service->send(2, 'alert', 'Titel utan meddelande');

        $stmt = $this->pdo->query("SELECT * FROM notifications WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNull($row['message']);
        $this->assertNull($row['link']);
    }

    public function testUnreadCountReturnsCorrectCount(): void
    {
        $this->service->send(5, 'info', 'Notis 1');
        $this->service->send(5, 'info', 'Notis 2');
        $this->service->send(5, 'info', 'Notis 3');
        $this->service->send(6, 'info', 'Annan användares notis');

        $this->assertSame(3, $this->service->unreadCount(5));
        $this->assertSame(1, $this->service->unreadCount(6));
        $this->assertSame(0, $this->service->unreadCount(99));
    }

    public function testForUserReturnsNotificationsForUser(): void
    {
        $this->service->send(10, 'info', 'A');
        $this->service->send(10, 'info', 'B');
        $this->service->send(11, 'info', 'C');

        $notifications = $this->service->forUser(10);
        $this->assertCount(2, $notifications);
        $this->assertSame('10', (string) $notifications[0]['user_id']);
    }

    public function testForUserRespectsLimit(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->service->send(20, 'info', "Notis {$i}");
        }

        $notifications = $this->service->forUser(20, 3);
        $this->assertCount(3, $notifications);
    }

    public function testMarkReadMarksSingleNotification(): void
    {
        $id = $this->service->send(30, 'info', 'Läs mig');

        $this->assertSame(1, $this->service->unreadCount(30));

        $this->service->markRead($id);

        $this->assertSame(0, $this->service->unreadCount(30));

        $stmt = $this->pdo->query("SELECT is_read, read_at FROM notifications WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertSame('1', (string) $row['is_read']);
        $this->assertNotNull($row['read_at']);
    }

    public function testMarkReadDoesNotAffectOtherNotifications(): void
    {
        $id1 = $this->service->send(40, 'info', 'Notis 1');
        $id2 = $this->service->send(40, 'info', 'Notis 2');

        $this->service->markRead($id1);

        $this->assertSame(1, $this->service->unreadCount(40));
    }

    public function testMarkAllReadClearsAllForUser(): void
    {
        $this->service->send(50, 'info', 'A');
        $this->service->send(50, 'info', 'B');
        $this->service->send(51, 'info', 'C');

        $this->service->markAllRead(50);

        $this->assertSame(0, $this->service->unreadCount(50));
        $this->assertSame(1, $this->service->unreadCount(51));
    }

    public function testMarkAllReadOnEmptyIsHarmless(): void
    {
        $this->service->markAllRead(999);
        $this->assertSame(0, $this->service->unreadCount(999));
    }

    public function testForUserReturnsEmptyArrayForUnknownUser(): void
    {
        $this->assertSame([], $this->service->forUser(9999));
    }
}
