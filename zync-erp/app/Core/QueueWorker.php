<?php

declare(strict_types=1);

namespace App\Core;

/**
 * QueueWorker — Bearbetar asynkrona jobb från job_queue-tabellen.
 *
 * Jobb-klasser ska implementera metoden handle(array $payload): void.
 * Kör via bin/queue-worker.php.
 */
class QueueWorker
{
    public function __construct(private readonly \PDO $pdo) {}

    /**
     * Hämtar och bearbetar nästa väntande jobb i angiven kö.
     *
     * @return bool true om ett jobb bearbetades, false om kön är tom
     */
    public function processNext(string $queue = 'default'): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM job_queue
                  WHERE status = 'pending' AND queue = ?
                  ORDER BY id ASC
                  LIMIT 1
                  FOR UPDATE SKIP LOCKED"
            );
            $stmt->execute([$queue]);
            $job = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($job === false) {
                $this->pdo->rollBack();
                return false;
            }

            // Markera som "processing"
            $this->pdo->prepare(
                "UPDATE job_queue SET status = 'processing', started_at = NOW(), attempts = attempts + 1 WHERE id = ?"
            )->execute([$job['id']]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        // Kör jobbet utanför transaktionen
        $payload = json_decode((string) $job['payload'], true) ?? [];

        try {
            $jobClass = (string) $job['job_class'];

            if (!class_exists($jobClass)) {
                throw new \RuntimeException("Jobbklass saknas: {$jobClass}");
            }

            $instance = new $jobClass();
            $instance->handle($payload);

            $this->pdo->prepare(
                "UPDATE job_queue SET status = 'completed', completed_at = NOW() WHERE id = ?"
            )->execute([$job['id']]);
        } catch (\Throwable $e) {
            $attempts    = (int) $job['attempts'];
            $maxAttempts = (int) $job['max_attempts'];

            $newStatus = $attempts >= $maxAttempts ? 'failed' : 'pending';

            $this->pdo->prepare(
                "UPDATE job_queue SET status = ?, error_msg = ?, completed_at = IF(? = 'failed', NOW(), NULL) WHERE id = ?"
            )->execute([$newStatus, $e->getMessage(), $newStatus, $job['id']]);
        }

        return true;
    }

    /**
     * Lägg till ett nytt jobb i kön.
     *
     * @param array<string, mixed> $payload
     */
    public function dispatch(
        string $jobClass,
        array $payload,
        string $queue = 'default',
        int $maxAttempts = 3
    ): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO job_queue (job_class, payload, queue, max_attempts)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$jobClass, json_encode($payload, JSON_UNESCAPED_UNICODE), $queue, $maxAttempts]);
        return (int) $this->pdo->lastInsertId();
    }
}
