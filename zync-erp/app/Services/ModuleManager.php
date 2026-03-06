<?php

namespace App\Services;

class ModuleManager
{
    protected $modules = [];

    public function loadModules()
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

    public function getModules()
    {
        return $this->modules;
    }
}