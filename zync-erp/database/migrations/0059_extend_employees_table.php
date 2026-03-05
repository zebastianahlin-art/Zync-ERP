<?php
declare(strict_types=1);
use App\Core\Database;
$pdo = Database::pdo();

$columns = [
    'personal_number'  => "VARCHAR(20) NULL COMMENT 'Personnummer/ID'",
    'birth_date'       => "DATE NULL",
    'gender'           => "VARCHAR(20) NULL",
    'nationality'      => "VARCHAR(100) NULL",
    'civil_status'     => "VARCHAR(30) NULL",
    'address_street'   => "VARCHAR(200) NULL",
    'address_zip'      => "VARCHAR(20) NULL",
    'address_city'     => "VARCHAR(100) NULL",
    'private_email'    => "VARCHAR(200) NULL",
    'private_phone'    => "VARCHAR(50) NULL",
    'ice_name'         => "VARCHAR(200) NULL COMMENT 'ICE-kontakt namn'",
    'ice_phone'        => "VARCHAR(50) NULL COMMENT 'ICE-kontakt telefon'",
    'employment_category' => "VARCHAR(30) NULL COMMENT 'tillsvidare/visstid/provanstallning/timme'",
    'pay_type'         => "VARCHAR(20) NULL COMMENT 'monthly/hourly'",
    'work_percentage'  => "DECIMAL(5,2) NULL DEFAULT 100",
    'profile_image_url'=> "VARCHAR(500) NULL",
];

foreach ($columns as $col => $def) {
    $stmt = $pdo->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'employees' AND COLUMN_NAME = ?"
    );
    $stmt->execute([$col]);
    if (empty($stmt->fetchAll())) {
        $pdo->exec("ALTER TABLE employees ADD COLUMN `{$col}` {$def}");
    }
}
