<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Application bootstrap.
 *
 * 1. Loads .env
 * 2. Registers the error handler
 * 3. Configures PHP runtime settings from env
 * 4. Registers routes
 * 5. Dispatches the request and sends the response
 */
class App
{
    private Router       $router;
    private ErrorHandler $errorHandler;

    public function __construct(private readonly string $basePath)
    {
        $this->loadEnvironment();
        $this->configureRuntime();
        $this->errorHandler = new ErrorHandler(
            logDir: $this->basePath . '/storage/logs',
            debug:  (bool) Config::env('APP_DEBUG', false)
        );
        $this->errorHandler->register();
        $this->router = new Router();
    }

    /** Expose the router so routes can be registered in public/index.php. */
    public function router(): Router
    {
        return $this->router;
    }

    /** Boot the application: dispatch request and send response. */
    public function run(): void
    {
        $request  = new Request();
        $response = $this->router->dispatch($request);
        $response->send();
    }

    private function loadEnvironment(): void
    {
        $envFile = $this->basePath . '/.env';
        require_once $this->basePath . '/app/Core/EnvLoader.php';
        loadEnv($envFile);
    }

    private function configureRuntime(): void
    {
        $debug = filter_var(Config::env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);

        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('display_startup_errors', $debug ? '1' : '0');
        error_reporting($debug ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT);
    }
}
