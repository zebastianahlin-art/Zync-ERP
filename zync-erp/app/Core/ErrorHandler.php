<?php

declare(strict_types=1);

namespace App\Core;

/**
 * ErrorHandler
 *
 * Registers PHP error/exception handlers and writes to storage/logs/.
 * In production, generic messages are shown; in debug mode, full details.
 */
class ErrorHandler
{
    private string $logDir;
    private bool   $debug;

    public function __construct(string $logDir, bool $debug = false)
    {
        $this->logDir = rtrim($logDir, '/\\');
        $this->debug  = $debug;
    }

    /** Register all handlers. */
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /** Convert PHP errors to exceptions (for uniform handling). */
    public function handleError(
        int    $errno,
        string $errstr,
        string $errfile = '',
        int    $errline = 0
    ): bool {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /** Handle uncaught exceptions. */
    public function handleException(\Throwable $e): void
    {
        $this->log($e);
        $this->respond($e);
    }

    /** Catch fatal errors that bypass set_error_handler. */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $e = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            $this->log($e);
            $this->respond($e);
        }
    }

    /** Write the exception to a daily log file. */
    private function log(\Throwable $e): void
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }

        $file    = $this->logDir . '/' . date('Y-m-d') . '.log';
        $message = sprintf(
            "[%s] %s: %s in %s on line %d\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        @file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
    }

    /** Send an HTTP error response to the browser. */
    private function respond(\Throwable $e): void
    {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
        }

        if ($this->debug) {
            echo '<pre style="font-family:monospace;padding:1rem;">';
            echo htmlspecialchars(
                get_class($e) . ': ' . $e->getMessage() . "\n\n" . $e->getTraceAsString(),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
            echo '</pre>';
        } else {
            echo '<h1>500 – Internal Server Error</h1>';
            echo '<p>Something went wrong. Please try again later.</p>';
        }
    }
}
