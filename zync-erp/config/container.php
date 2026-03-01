<?php

declare(strict_types=1);

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Psr\Log\LoggerInterface;

return [
    // Logger
    LoggerInterface::class => function () {
        $logger = new Logger('zync');
        $logger->pushHandler(new RotatingFileHandler(
            dirname(__DIR__) . '/storage/logs/app.log',
            14,
            Logger::DEBUG
        ));
        return $logger;
    },

    // View renderer
    'view' => function () {
        return new \App\Core\View(dirname(__DIR__) . '/views');
    },
];
