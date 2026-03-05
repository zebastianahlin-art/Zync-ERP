#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Queue Worker CLI — Bearbetar jobb från job_queue-tabellen.
 *
 * Användning:
 *   php bin/queue-worker.php [queue] [--sleep=N]
 *
 * Argument:
 *   queue     Könamn att bearbeta (standard: 'default')
 *   --sleep=N Sekunder att sova mellan tomma körningar (standard: 5)
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\QueueWorker;

// Läs argument
$queueName  = 'default';
$sleepSecs  = 5;

foreach ($argv as $i => $arg) {
    if ($i === 0) {
        continue;
    }
    if (str_starts_with($arg, '--sleep=')) {
        $sleepSecs = max(1, (int) substr($arg, 8));
    } elseif (!str_starts_with($arg, '--')) {
        $queueName = $arg;
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Queue worker startar. Kö: {$queueName}\n";

$pdo    = Database::pdo();
$worker = new QueueWorker($pdo);

while (true) {
    try {
        $processed = $worker->processNext($queueName);
        if ($processed) {
            echo "[" . date('Y-m-d H:i:s') . "] Jobb bearbetades.\n";
        } else {
            sleep($sleepSecs);
        }
    } catch (\Throwable $e) {
        echo "[" . date('Y-m-d H:i:s') . "] FEL: " . $e->getMessage() . "\n";
        sleep($sleepSecs);
    }
}
