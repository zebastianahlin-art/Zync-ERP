<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpForbiddenException;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\ErrorHandlerInterface;

/**
 * Custom Slim 4 error handler.
 *
 * - API routes (/api/*): always returns JSON
 * - Production: renders clean Swedish error views (404 / 403 / 500)
 * - Development: delegates to Slim's default behaviour (full stack trace)
 * - 500 errors are always logged via LoggerInterface
 */
class ErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private readonly CallableResolverInterface $callableResolver,
        private readonly ResponseFactoryInterface  $responseFactory,
        private readonly LoggerInterface           $logger,
    ) {}

    public function __invoke(
        ServerRequestInterface $request,
        \Throwable             $exception,
        bool                   $displayErrorDetails,
        bool                   $logErrors,
        bool                   $logErrorDetails
    ): ResponseInterface {
        $statusCode = $this->statusCode($exception);
        $path       = $request->getUri()->getPath();
        $isApi      = str_starts_with($path, '/api/');

        // Log 500 errors
        if ($statusCode === 500) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        // API: always JSON
        if ($isApi) {
            return $this->jsonResponse($statusCode, $exception->getMessage());
        }

        // Debug mode: rethrow so Slim shows full trace
        if ($displayErrorDetails) {
            return $this->debugResponse($statusCode, $exception);
        }

        // Production: clean Swedish error page
        return $this->htmlResponse($statusCode);
    }

    private function statusCode(\Throwable $e): int
    {
        if ($e instanceof HttpNotFoundException) {
            return 404;
        }
        if ($e instanceof HttpForbiddenException) {
            return 403;
        }
        if ($e instanceof \Slim\Exception\HttpException) {
            return $e->getCode();
        }
        return 500;
    }

    private function jsonResponse(int $status, string $message): ResponseInterface
    {
        $code = match ($status) {
            404     => 'NOT_FOUND',
            403     => 'FORBIDDEN',
            default => 'INTERNAL_ERROR',
        };

        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write((string) json_encode([
            'success' => false,
            'error'   => [
                'code'    => $code,
                'message' => $message,
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    private function htmlResponse(int $status): ResponseInterface
    {
        $viewFile = dirname(__DIR__, 2) . '/views/errors/' . $status . '.php';

        if (!is_file($viewFile)) {
            $viewFile = dirname(__DIR__, 2) . '/views/errors/500.php';
        }

        $view    = new View(dirname(__DIR__, 2) . '/views');
        $content = $view->render('errors/' . $status, ['title' => $this->pageTitle($status)]);

        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    private function debugResponse(int $status, \Throwable $e): ResponseInterface
    {
        $html = '<pre style="font-family:monospace;padding:1rem;background:#1e1e1e;color:#d4d4d4;overflow:auto;">';
        $html .= htmlspecialchars(
            get_class($e) . ': ' . $e->getMessage() . "\n\nin " . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString(),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
        $html .= '</pre>';

        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    private function pageTitle(int $status): string
    {
        return match ($status) {
            404     => '404 – Sidan hittades inte – ZYNC ERP',
            403     => '403 – Åtkomst nekad – ZYNC ERP',
            default => '500 – Serverfel – ZYNC ERP',
        };
    }
}
