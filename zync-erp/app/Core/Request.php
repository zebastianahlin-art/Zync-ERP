<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Request wrapper.
 */
class Request
{
    public readonly string $method;
    public readonly string $uri;
    public readonly string $path;

    /** @var array<string, string> */
    public readonly array $query;

    /** @var array<string, string> */
    public readonly array $body;

    /** @var array<string, string> */
    public readonly array $headers;

    /** @var array<string, string> Route parameters extracted from dynamic path segments. */
    public array $params = [];

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri     = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path    = strtok($this->uri, '?') ?: '/';
        $this->query   = $_GET;
        $this->body    = $_POST;
        $this->headers = $this->parseHeaders();
    }

    /** @return array<string, string> */
    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name           = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }
}
