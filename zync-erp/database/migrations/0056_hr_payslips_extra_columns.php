<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();
$allowed = [
    'base_pay'        => 'DECIMAL(12,2) NOT NULL DEFAULT 0',
    'ob_amount'       => 'DECIMAL(12,2) NOT NULL DEFAULT 0',
    'overtime_amount' => 'DECIMAL(12,2) NOT NULL DEFAULT 0',
    'tax_amount'      => 'DECIMAL(12,2) NOT NULL DEFAULT 0',
];
foreach ($allowed as $col => $def) {
    $stmt = $pdo->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_payslips' AND COLUMN_NAME = ?"
    );
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        // $col and $def are from a fixed whitelist above — safe to interpolate
        $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN `{$col}` {$def}");
    }
}
