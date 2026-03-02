<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Core\Auth;

/**
 * PSR-15 middleware that verifies the user is authenticated.
 *
 * On failure:
 *  - API paths (/api/*) receive a 401 JSON response.
 *  - Web paths are redirected to /login.
 *
 * When 2FA verification is pending, web paths are redirected to /2fa/verify.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Auth::check()) {
            // If 2FA is pending, the user must complete verification first
            if (Auth::is2faPending()) {
                $path = $request->getUri()->getPath();
                if (!str_starts_with($path, '/2fa/')) {
                    $response = new \Slim\Psr7\Response();
                    return $response
                        ->withHeader('Location', '/2fa/verify')
                        ->withStatus(302);
                }
            }

            $user = Auth::user();
            if ($user) {
                $request = $request->withAttribute('user', $user);
                $request = $request->withAttribute('user_id', (int) $user['id']);
                return $handler->handle($request);
            }
        }

        // Not authenticated — differentiate API vs Web
        $path = $request->getUri()->getPath();
        if (str_starts_with($path, '/api/')) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Autentisering krävs',
                ],
            ], JSON_UNESCAPED_UNICODE));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        // Web: redirect to login
        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}
