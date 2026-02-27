<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router
 *
 * Supports GET and POST route registration.
 * Handlers may be a closure or a 'Controller@method' string.
 *
 * Example:
 *   $router->get('/', fn(Request $req) => ...);
 *   $router->get('/about', 'HomeController@about');
 */
class Router
{
    /** @var array<string, array<string, mixed>> */
    private array $routes = [];

    public function get(string $path, mixed $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, mixed $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    /**
     * Dispatch the current request to its registered handler.
     *
     * Returns a Response object ready to send.
     */
    public function dispatch(Request $request): Response
    {
        $method  = $request->method;
        $path    = '/' . trim($request->path, '/');

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            return $this->notFound();
        }

        return $this->call($handler, $request);
    }

    /** Resolve and call the handler. */
    private function call(mixed $handler, Request $request): Response
    {
        if (is_callable($handler)) {
            return $handler($request);
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);

            // Support short names (e.g. 'HomeController') or fully-qualified
            if (!str_contains($class, '\\')) {
                $class = 'App\\Controllers\\' . $class;
            }

            /** @var Controller $controller */
            $controller = new $class();
            return $controller->$method($request);
        }

        throw new \RuntimeException('Invalid route handler.');
    }

    private function notFound(): Response
    {
        $response = new Response();
        return $response->html('<h1>404 – Page Not Found</h1>', 404);
    }
}
