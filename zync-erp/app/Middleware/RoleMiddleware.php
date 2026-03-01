<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * PSR-15 middleware for role-based access control.
 *
 * Access is granted when the user:
 *  - has a role slug of 'vd' or 'ceo' (always pass), OR
 *  - has a role slug listed in $allowedSlugs, OR
 *  - has a role level >= $minLevel.
 *
 * Must be used together with AuthMiddleware (which sets the 'user' request attribute).
 */
class RoleMiddleware implements MiddlewareInterface
{
    private int $minLevel;
    /** @var string[] */
    private array $allowedSlugs;

    /** @param string[] $allowedSlugs */
    public function __construct(int $minLevel = 1, array $allowedSlugs = [])
    {
        $this->minLevel     = $minLevel;
        $this->allowedSlugs = $allowedSlugs;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user) {
            return $this->forbidden($request);
        }

        $userLevel = (int) ($user['role_level'] ?? 0);
        $userSlug  = (string) ($user['role_slug'] ?? '');

        // VD/CEO always pass
        if (in_array($userSlug, ['vd', 'ceo'], true)) {
            return $handler->handle($request);
        }

        // Check by specific slugs
        if (!empty($this->allowedSlugs) && in_array($userSlug, $this->allowedSlugs, true)) {
            return $handler->handle($request);
        }

        // Check by minimum level
        if ($userLevel >= $this->minLevel) {
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
                    'code' => 'FORBIDDEN',
                    'message' => 'Åtkomst nekad — otillräcklig behörighet',
                ],
            ], JSON_UNESCAPED_UNICODE));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }

        // Web: set flash message and redirect to dashboard
        if (class_exists(\App\Core\Flash::class)) {
            \App\Core\Flash::set('error', 'Du har inte behörighet att visa den sidan.');
        }
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }
}
