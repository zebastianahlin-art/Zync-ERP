<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Controller
 *
 * Provides convenience methods for building responses.
 */
abstract class Controller
{
    protected View     $view;
    protected Response $response;

    public function __construct()
    {
        $viewsDir       = dirname(__DIR__, 2) . '/views';
        $this->view     = new View($viewsDir);
        $this->response = new Response();
    }

    /**
     * Render a view and return an HTML Response.
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = [], ?string $layout = 'main'): Response
    {
        $html = $this->view->render($view, $data, $layout);
        return $this->response->html($html);
    }

    /** Return a JSON Response. */
    protected function json(mixed $data, int $status = 200): Response
    {
        return $this->response->json($data, $status);
    }

    /** Redirect to another URL. */
    protected function redirect(string $url, int $status = 302): Response
    {
        return $this->response->redirect($url, $status);
    }
}
