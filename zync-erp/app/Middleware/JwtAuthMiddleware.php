<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * PSR-15 middleware that validates Bearer JWT tokens for API routes.
 *
 * On success: attaches the decoded payload as the 'jwt_user' request attribute.
 * On failure: returns a 401 JSON response.
 */
class JwtAuthMiddleware implements MiddlewareInterface
{
    private JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (str_starts_with($authHeader, 'Bearer ')) {
            $token   = substr($authHeader, 7);
            $payload = $this->jwt->validateToken($token);

            if ($payload !== null) {
                $request = $request->withAttribute('jwt_user', $payload);
                return $handler->handle($request);
            }
        }

        return $this->unauthorized();
    }

    private function unauthorized(): ResponseInterface
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write((string) json_encode([
            'error'   => 'Unauthorized',
            'message' => 'Ogiltigt eller saknat token',
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}
