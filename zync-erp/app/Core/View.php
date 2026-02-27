<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple PHP-template view renderer.
 *
 * Views live in /views/**\/*.php.
 * Layouts live in /views/layouts/*.php and receive $content.
 */
class View
{
    private string $viewsDir;

    public function __construct(string $viewsDir)
    {
        $this->viewsDir = rtrim($viewsDir, '/\\');
    }

    /**
     * Render a view and return the output as a string.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data = [], ?string $layout = 'main'): string
    {
        $viewFile = $this->viewsDir . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        // Render inner view
        $content = $this->include($viewFile, $data);

        // Wrap in layout if requested
        if ($layout !== null) {
            $layoutFile = $this->viewsDir . '/layouts/' . $layout . '.php';

            if (!is_file($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }

            $content = $this->include($layoutFile, array_merge($data, ['content' => $content]));
        }

        return $content;
    }

    /**
     * Include a PHP file with extracted variables and capture its output.
     *
     * @param array<string, mixed> $data
     */
    private function include(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
