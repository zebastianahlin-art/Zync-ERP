<?php

declare(strict_types=1);

namespace App\Services;

use Slim\App;

class ModuleManager
{
    protected array $modules = [];

    public function loadModules(): void
    {
        $path = __DIR__ . '/../Modules';

        if (!is_dir($path)) {
            return;
        }

        $dirs = scandir($path);

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $modulePath = $path . '/' . $dir;

            if (is_dir($modulePath)) {
                $this->modules[] = $dir;
            }
        }
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function loadRoutes(App $app): void
    {
        foreach ($this->modules as $module) {
            $routeFile = __DIR__ . '/../Modules/' . $module . '/Routes/routes.php';

            if (!file_exists($routeFile)) {
                continue;
            }

            $registerRoutes = require $routeFile;

            if (is_callable($registerRoutes)) {
                $registerRoutes($app);
            }
        }
    }
}