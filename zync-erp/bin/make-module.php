<?php

if ($argc < 2) {
    echo "Usage: php bin/make-module.php ModuleName\n";
    exit;
}

$module = $argv[1];

$base = __DIR__ . '/../app/Modules/' . $module;

$folders = [
    $base,
    "$base/Controllers",
    "$base/Services",
    "$base/Models",
    "$base/Routes",
    "$base/Views"
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
        echo "Created: $folder\n";
    }
}