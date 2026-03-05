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
    $stmt = $pdo->prepare("SHOW COLUMNS FROM payroll_payslips LIKE ?");
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN `{$col}` {$def}");
    }
}
