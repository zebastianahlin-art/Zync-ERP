<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseInterface;

/**
 * Base Controller
 *
 * Provides convenience methods for building PSR-7 responses.
 */
abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $viewsDir   = dirname(__DIR__, 2) . '/views';
        $this->view = new View($viewsDir);
    }

    /**
     * Render a view and write HTML to the PSR-7 response body.
     *
     * @param array<string, mixed> $data
     */
    protected function render(ResponseInterface $response, string $view, array $data = [], ?string $layout = 'main'): ResponseInterface
    {
        $html = $this->view->render($view, $data, $layout);
        $response->getBody()->write($html);
        return $response;
    }

    /** Write JSON to the PSR-7 response. */
    protected function json(ResponseInterface $response, mixed $data, int $status = 200): ResponseInterface
    {
        $response->getBody()->write((string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=UTF-8')
            ->withStatus($status);
    }

    /** Return a redirect PSR-7 response. */
    protected function redirect(ResponseInterface $response, string $url, int $status = 302): ResponseInterface
    {
        return $response
            ->withHeader('Location', $url)
            ->withStatus($status);
    }

    /**
     * Require an authenticated session.
     *
     * Returns null when authenticated so callers can continue normally:
     *
     *   if ($guard = $this->requireAuth($response)) return $guard;
     *
     * @deprecated Use AuthMiddleware instead of calling this in controllers.
     */
    protected function requireAuth(ResponseInterface $response): ?ResponseInterface
    {
        if (Auth::check()) {
            return null;
        }

        Flash::set('error', 'Please log in to continue.');
        return $this->redirect($response, '/login');
    }
}
