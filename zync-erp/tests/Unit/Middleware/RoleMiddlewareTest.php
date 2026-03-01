<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\RoleMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class RoleMiddlewareTest extends TestCase
{
    private function makeRequest(string $path = '/dashboard'): ServerRequestInterface
    {
        return (new RequestFactory())->createRequest('GET', 'http://localhost' . $path);
    }

    private function makeHandler(int $status = 200): RequestHandlerInterface
    {
        return new class ($status) implements RequestHandlerInterface {
            public function __construct(private int $status) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseFactory())->createResponse($this->status);
            }
        };
    }

    public function testVdAlwaysPasses(): void
    {
        $middleware = new RoleMiddleware(minLevel: 10);
        $request    = $this->makeRequest()->withAttribute('user', [
            'role_slug'  => 'vd',
            'role_level' => 10,
        ]);

        $response = $middleware->process($request, $this->makeHandler(200));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCeoAlwaysPasses(): void
    {
        $middleware = new RoleMiddleware(minLevel: 10);
        $request    = $this->makeRequest()->withAttribute('user', [
            'role_slug'  => 'ceo',
            'role_level' => 9,
        ]);

        $response = $middleware->process($request, $this->makeHandler(200));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testUserWithSufficientLevelPasses(): void
    {
        $middleware = new RoleMiddleware(minLevel: 7);
        $request    = $this->makeRequest()->withAttribute('user', [
            'role_slug'  => 'chef',
            'role_level' => 7,
        ]);

        $response = $middleware->process($request, $this->makeHandler(200));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testUserWithInsufficientLevelIsForbidden(): void
    {
        $middleware = new RoleMiddleware(minLevel: 7);
        $request    = $this->makeRequest()->withAttribute('user', [
            'role_slug'  => 'arbetare',
            'role_level' => 2,
        ]);

        $response = $middleware->process($request, $this->makeHandler(200));
        // Forbidden redirects (302) or returns 403 for API routes
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function testMissingUserIsForbidden(): void
    {
        $middleware = new RoleMiddleware(minLevel: 1);
        $request    = $this->makeRequest();

        $response = $middleware->process($request, $this->makeHandler(200));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function testApiRouteForbiddenReturns403Json(): void
    {
        $middleware = new RoleMiddleware(minLevel: 7);
        $request    = $this->makeRequest('/api/admin')->withAttribute('user', [
            'role_slug'  => 'arbetare',
            'role_level' => 2,
        ]);

        $response = $middleware->process($request, $this->makeHandler(200));
        $this->assertSame(403, $response->getStatusCode());

        $body = (string) $response->getBody();
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertFalse($json['success']);
        $this->assertSame('FORBIDDEN', $json['error']['code']);
    }

    public function testAllowedSlugPasses(): void
    {
        $middleware = new RoleMiddleware(minLevel: 99, allowedSlugs: ['teamchef']);
        $request    = $this->makeRequest()->withAttribute('user', [
            'role_slug'  => 'teamchef',
            'role_level' => 5,
        ]);

        $response = $middleware->process($request, $this->makeHandler(200));
        $this->assertSame(200, $response->getStatusCode());
    }
}
