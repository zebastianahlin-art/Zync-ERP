<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Response helper.
 */
class Response
{
    private int    $statusCode   = 200;
    private string $body         = '';
    private bool   $suppressBody = false;

    /** @var array<string, string> */
    private array $headers = [];

    public function setStatus(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function html(string $body, int $status = 200): static
    {
        return $this->setStatus($status)
                    ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                    ->setBody($body);
    }

    public function json(mixed $data, int $status = 200): static
    {
        return $this->setStatus($status)
                    ->setHeader('Content-Type', 'application/json; charset=UTF-8')
                    ->setBody((string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function redirect(string $url, int $status = 302): static
    {
        return $this->setStatus($status)->setHeader('Location', $url);
    }

    /**
     * Suppress the response body (e.g. for HEAD requests per RFC 7231 §4.3.2).
     * Status code and headers are still sent.
     */
    public function suppressBody(): static
    {
        $this->suppressBody = true;
        return $this;
    }

    public function send(): void
    {
        if (headers_sent()) {
            echo $this->body;
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // HEAD requests must not include a response body (RFC 7231 §4.3.2).
        if (!$this->suppressBody) {
            echo $this->body;
        }
    }
}
