<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Core\Csrf;

/**
 * PSR-15 middleware for CSRF protection.
 *
 * GET, HEAD, and OPTIONS requests pass through without any token check.
 * POST, PUT, DELETE, and PATCH requests must supply a valid CSRF token
 * in the parsed body under the key '_token' (matching Csrf::field()).
 *
 * On failure:
 *  - API paths (/api/*) receive a 403 JSON response.
 *  - Web paths are redirected back to the referrer (or /) with a flash error.
 */
class CsrfMiddleware implements MiddlewareInterface
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), self::SAFE_METHODS, true)) {
            return $handler->handle($request);
        }

        $body  = (array) ($request->getParsedBody() ?? []);
        $token = (string) ($body['_token'] ?? '');

        if (Csrf::verify($token)) {
            return $handler->handle($request);
        }

        return $this->forbidden($request);
    }

    private function forbidden(ServerRequestInterface $request): ResponseInterface
    {
        $path     = $request->getUri()->getPath();
        $response = new \Slim\Psr7\Response();

        if (str_starts_with($path, '/api/')) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'CSRF_TOKEN_MISMATCH',
                    'message' => 'Ogiltig eller saknad CSRF-token',
                ],
            ], JSON_UNESCAPED_UNICODE));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }

        // Web: flash error and redirect back
        if (class_exists(\App\Core\Flash::class)) {
            \App\Core\Flash::set('error', 'Säkerhetstoken saknas eller är ogiltig. Försök igen.');
        }
        $referrer = $request->getHeaderLine('Referer') ?: '/';
        return $response
            ->withHeader('Location', $referrer)
            ->withStatus(302);
    }
}
