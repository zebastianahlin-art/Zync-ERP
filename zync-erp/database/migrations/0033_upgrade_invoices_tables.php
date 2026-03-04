<?php

declare(strict_types=1);

/**
 * Migration: Upgrade invoices_incoming (approval/rejection) and invoices_outgoing (credit notes, reminders)
 */
return function (\PDO $pdo): void
{
    // ─── invoices_incoming ────────────────────────────────────────────────
    $inCols = $pdo->query("SHOW COLUMNS FROM invoices_incoming")->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('approved_by', $inCols)) {
        $pdo->exec("ALTER TABLE invoices_incoming ADD COLUMN approved_by BIGINT UNSIGNED NULL AFTER status");
    }
    if (!in_array('approved_at', $inCols)) {
        $pdo->exec("ALTER TABLE invoices_incoming ADD COLUMN approved_at DATETIME NULL AFTER approved_by");
    }
    if (!in_array('rejected_by', $inCols)) {
        $pdo->exec("ALTER TABLE invoices_incoming ADD COLUMN rejected_by BIGINT UNSIGNED NULL AFTER approved_at");
    }
    if (!in_array('rejected_at', $inCols)) {
        $pdo->exec("ALTER TABLE invoices_incoming ADD COLUMN rejected_at DATETIME NULL AFTER rejected_by");
    }
    if (!in_array('rejection_reason', $inCols)) {
        $pdo->exec("ALTER TABLE invoices_incoming ADD COLUMN rejection_reason TEXT NULL AFTER rejected_at");
    }

    // ─── invoices_outgoing ────────────────────────────────────────────────
    $outCols = $pdo->query("SHOW COLUMNS FROM invoices_outgoing")->fetchAll(\PDO::FETCH_COLUMN);

    if (!in_array('credit_note_for', $outCols)) {
        $pdo->exec("ALTER TABLE invoices_outgoing ADD COLUMN credit_note_for BIGINT UNSIGNED NULL AFTER status");
    }
    if (!in_array('reminder_count', $outCols)) {
        $pdo->exec("ALTER TABLE invoices_outgoing ADD COLUMN reminder_count INT NOT NULL DEFAULT 0 AFTER credit_note_for");
    }
    if (!in_array('last_reminder_at', $outCols)) {
        $pdo->exec("ALTER TABLE invoices_outgoing ADD COLUMN last_reminder_at DATETIME NULL AFTER reminder_count");
    }
};
