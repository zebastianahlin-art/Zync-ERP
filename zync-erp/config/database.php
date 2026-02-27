<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'host'    => Config::env('DB_HOST', 'localhost'),
    'port'    => (int) Config::env('DB_PORT', 3306),
    'name'    => Config::env('DB_NAME', ''),
    'user'    => Config::env('DB_USER', ''),
    'pass'    => Config::env('DB_PASS', ''),
    'charset' => Config::env('DB_CHARSET', 'utf8mb4'),
];
