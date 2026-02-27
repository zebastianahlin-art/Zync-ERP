<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'name'  => Config::env('APP_NAME', 'ZYNC ERP'),
    'env'   => Config::env('APP_ENV', 'production'),
    'debug' => Config::env('APP_DEBUG', false),
    'url'   => Config::env('APP_URL', 'http://localhost'),
];
