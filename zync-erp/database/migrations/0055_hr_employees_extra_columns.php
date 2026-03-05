<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();
$cols = $pdo->query("SHOW COLUMNS FROM employees LIKE 'manager_id'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN manager_id BIGINT UNSIGNED NULL");
}
$cols = $pdo->query("SHOW COLUMNS FROM employees LIKE 'emergency_contact_name'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN emergency_contact_name VARCHAR(200) NULL");
}
$cols = $pdo->query("SHOW COLUMNS FROM employees LIKE 'emergency_contact_phone'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE employees ADD COLUMN emergency_contact_phone VARCHAR(50) NULL");
}
