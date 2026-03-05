<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();

$columns = [
    'social_security_amount' => "DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'Sociala avgifter'",
    'other_additions'        => "DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'Övriga tillägg'",
    'other_deductions'       => "DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'Övriga avdrag'",
];

foreach ($columns as $col => $def) {
    $stmt = $pdo->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_payslips' AND COLUMN_NAME = ?"
    );
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN `{$col}` {$def}");
    }
}
