<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();
foreach (['base_pay' => 'DECIMAL(12,2) NOT NULL DEFAULT 0', 'ob_amount' => 'DECIMAL(12,2) NOT NULL DEFAULT 0', 'overtime_amount' => 'DECIMAL(12,2) NOT NULL DEFAULT 0', 'tax_amount' => 'DECIMAL(12,2) NOT NULL DEFAULT 0'] as $col => $def) {
    $r = $pdo->query("SHOW COLUMNS FROM payroll_payslips LIKE '$col'")->fetchAll();
    if (empty($r)) {
        $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN $col $def");
    }
}
