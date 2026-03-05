<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();

$applicantCols = [
    'pipeline_step' => "VARCHAR(30) NULL DEFAULT 'new' COMMENT 'Ny/Granskning/Intervju/Erbjudande/Anst&#228;lld/Avvisad'",
    'rating'        => "TINYINT UNSIGNED NULL DEFAULT 0 COMMENT 'Betyg 1-5'",
    'cv_url'        => "VARCHAR(500) NULL COMMENT 'CV-fil URL/filnamn'",
    'cover_letter'  => "TEXT NULL",
    'salary_expectation' => "DECIMAL(12,2) NULL",
    'converted_employee_id' => "BIGINT UNSIGNED NULL COMMENT 'Om konverterad till anställd'",
];

foreach ($applicantCols as $col => $def) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM recruitment_applicants LIKE ?");
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        $pdo->exec("ALTER TABLE recruitment_applicants ADD COLUMN `{$col}` {$def}");
    }
}

// Extend recruitment_positions with offer/contact details
$positionCols = [
    'contact_person' => "VARCHAR(200) NULL",
    'contact_email'  => "VARCHAR(200) NULL",
    'salary_range'   => "VARCHAR(100) NULL",
    'benefits'       => "TEXT NULL",
    'location'       => "VARCHAR(200) NULL",
];
foreach ($positionCols as $col => $def) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM recruitment_positions LIKE ?");
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        $pdo->exec("ALTER TABLE recruitment_positions ADD COLUMN `{$col}` {$def}");
    }
}
