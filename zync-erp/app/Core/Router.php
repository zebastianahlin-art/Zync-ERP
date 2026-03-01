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
     * HEAD requests are resolved using the GET route table (RFC 7231 §4.3.2).
     * Returns a Response object ready to send.
     */
    public function dispatch(Request $request): Response
    {
        $method  = $request->method === 'HEAD' ? 'GET' : $request->method;
        $path    = '/' . trim($request->path, '/');

        // Try exact match first.
        $handler = $this->routes[$method][$path] ?? null;
        $params  = [];

        // Fall back to dynamic segment matching.
        if ($handler === null) {
            foreach ($this->routes[$method] ?? [] as $pattern => $h) {
                if (!str_contains($pattern, '{')) {
                    continue;
                }
                $regex = $this->patternToRegex($pattern);
                if (preg_match($regex, $path, $matches)) {
                    $handler = $h;
                    // Collect named captures as params.
                    foreach ($matches as $k => $v) {
                        if (is_string($k)) {
                            $params[$k] = $v;
                        }
                    }
                    break;
                }
            }
        }

        if ($handler === null) {
            $response = $this->notFound();
            if ($request->method === 'HEAD') {
                $response->suppressBody();
            }
            return $response;
        }

        $request->params = $params;
        $response = $this->call($handler, $request);
        if ($request->method === 'HEAD') {
            $response->suppressBody();
        }
        return $response;
    }

    /** Convert a route pattern like /customers/{id}/edit into a named-capture regex. */
    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
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
