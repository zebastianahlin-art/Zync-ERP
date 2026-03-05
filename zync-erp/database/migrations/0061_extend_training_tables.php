<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();

// Extend training_courses: add certificate_type_id for auto cert generation
$stmt = $pdo->prepare("SHOW COLUMNS FROM training_courses LIKE 'certificate_type_id'");
$stmt->execute();
if (empty($stmt->fetchAll())) {
    $pdo->exec("ALTER TABLE training_courses ADD COLUMN `certificate_type_id` BIGINT UNSIGNED NULL COMMENT 'Kopplad certifikattyp för automatisk certifikatgenerering'");
}

// Extend training_sessions: ensure capacity column exists (already may exist as max_participants)
$stmt2 = $pdo->prepare("SHOW COLUMNS FROM training_sessions LIKE 'notes'");
$stmt2->execute();
if (empty($stmt2->fetchAll())) {
    $pdo->exec("ALTER TABLE training_sessions ADD COLUMN `notes` TEXT NULL");
}

// Extend training_participants: add completion_date, certificate_id
$participantCols = [
    'completion_date' => "DATE NULL",
    'certificate_id'  => "BIGINT UNSIGNED NULL COMMENT 'Genererat certifikat'",
    'cancelled_at'    => "DATETIME NULL",
    'cancel_reason'   => "VARCHAR(500) NULL",
];
foreach ($participantCols as $col => $def) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM training_participants LIKE ?");
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        $pdo->exec("ALTER TABLE training_participants ADD COLUMN `{$col}` {$def}");
    }
}
