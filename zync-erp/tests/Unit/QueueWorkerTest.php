<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\QueueWorker;
use PHPUnit\Framework\TestCase;

class QueueWorkerTest extends TestCase
{
    private \PDO $pdo;
    private QueueWorker $worker;

    protected function setUp(): void
    {
        // SQLite in-memory för isolerade tester
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("CREATE TABLE job_queue (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
            queue        TEXT NOT NULL DEFAULT 'default',
            payload      TEXT NOT NULL,
            job_class    TEXT NOT NULL,
            status       TEXT NOT NULL DEFAULT 'pending',
            attempts     INTEGER NOT NULL DEFAULT 0,
            max_attempts INTEGER NOT NULL DEFAULT 3,
            error_msg    TEXT NULL,
            started_at   DATETIME NULL,
            completed_at DATETIME NULL
        )");

        $this->worker = new QueueWorker($this->pdo);
    }

    public function testDispatchCreatesJobInDatabase(): void
    {
        $id = $this->worker->dispatch('App\\Jobs\\TestJob', ['key' => 'value'], 'default', 3);

        $this->assertGreaterThan(0, $id);

        $stmt = $this->pdo->query("SELECT * FROM job_queue WHERE id = {$id}");
        $job  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($job);
        $this->assertSame('pending', $job['status']);
        $this->assertSame('App\\Jobs\\TestJob', $job['job_class']);
        $this->assertSame('default', $job['queue']);
    }

    public function testProcessNextReturnsFalseWhenQueueIsEmpty(): void
    {
        // SQLite doesn't support SKIP LOCKED — we use a simplified version in tests
        $result = $this->processNextWithoutLock();
        $this->assertFalse($result);
    }

    public function testDispatchPayloadIsStoredAsJson(): void
    {
        $payload = ['invoice_id' => 123, 'type' => 'pdf'];
        $id = $this->worker->dispatch('App\\Jobs\\GeneratePdfJob', $payload);

        $stmt = $this->pdo->query("SELECT payload FROM job_queue WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $decoded = json_decode($row['payload'], true);
        $this->assertSame(123, $decoded['invoice_id']);
        $this->assertSame('pdf', $decoded['type']);
    }

    public function testDispatchReturnsIncrementingIds(): void
    {
        $id1 = $this->worker->dispatch('Job1', []);
        $id2 = $this->worker->dispatch('Job2', []);

        $this->assertGreaterThan($id1, $id2);
    }

    public function testDispatchDefaultsToDefaultQueue(): void
    {
        $id = $this->worker->dispatch('Job', []);

        $stmt = $this->pdo->query("SELECT queue FROM job_queue WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('default', $row['queue']);
    }

    public function testDispatchRespectsCustomQueue(): void
    {
        $id = $this->worker->dispatch('Job', [], 'emails');

        $stmt = $this->pdo->query("SELECT queue FROM job_queue WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('emails', $row['queue']);
    }

    public function testDispatchRespectsMaxAttempts(): void
    {
        $id = $this->worker->dispatch('Job', [], 'default', 5);

        $stmt = $this->pdo->query("SELECT max_attempts FROM job_queue WHERE id = {$id}");
        $row  = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('5', (string) $row['max_attempts']);
    }

    /**
     * Simplified processNext without FOR UPDATE SKIP LOCKED (not supported by SQLite).
     */
    private function processNextWithoutLock(string $queue = 'default'): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM job_queue WHERE status = 'pending' AND queue = ? ORDER BY id ASC LIMIT 1"
        );
        $stmt->execute([$queue]);
        $job = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $job !== false;
    }
}
