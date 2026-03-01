<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router
 *
 * Supports GET and POST route registration.
 * Handlers may be a closure or a 'Controller@method' string.
 * Dynamic segments are declared with curly braces: {param}.
 *
 * Example:
 *   $router->get('/', fn(Request $req) => ...);
 *   $router->get('/about', 'HomeController@about');
 *   $router->get('/customers/{id}/edit', 'CustomerController@edit');
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
     * HEAD requests are resolved using the GET route table (RFC 7231 §4.3.2).
     * Dynamic segments ({param}) are matched after exact routes.
     * Returns a Response object ready to send.
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->method === 'HEAD' ? 'GET' : $request->method;
        $path   = '/' . trim($request->path, '/');

        [$handler, $params] = $this->match($method, $path);

        if ($handler === null) {
            $response = $this->notFound();
            if ($request->method === 'HEAD') {
                $response->suppressBody();
            }
            return $response;
        }

        // Inject matched route params into the request
        $request->setParams($params);

        // CSRF check for state-changing requests
        if ($method === 'POST') {
            $token = (string) ($request->body['_token'] ?? '');
            if (!Csrf::verify($token)) {
                $response = new Response();
                $response->html('<h1>419 – CSRF Token Mismatch</h1>', 419);
                if ($request->method === 'HEAD') {
                    $response->suppressBody();
                }
                return $response;
            }
        }

        $response = $this->call($handler, $request);
        if ($request->method === 'HEAD') {
            $response->suppressBody();
        }
        return $response;
    }

    /**
     * Match a method + path against registered routes.
     * Tries exact match first, then parameterised patterns.
     *
     * @return array{0: mixed, 1: array<string, string>}
     */
    private function match(string $method, string $path): array
    {
        // 1. Exact match (fast path)
        if (isset($this->routes[$method][$path])) {
            return [$this->routes[$method][$path], []];
        }

        // 2. Parameterised match
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if (!str_contains($route, '{')) {
                continue;
            }

            $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                // Keep only named captures (string keys)
                $params = array_filter(
                    $matches,
                    fn($k) => is_string($k),
                    ARRAY_FILTER_USE_KEY
                );
                return [$handler, $params];
            }
        }

        return [null, []];
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
