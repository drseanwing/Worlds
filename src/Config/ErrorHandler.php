<?php

namespace Worlds\Config;

use Throwable;

/**
 * ErrorHandler class
 *
 * Sets up global exception and error handling with logging support.
 * Differentiates between debug and production modes for error display.
 */
class ErrorHandler
{
    /**
     * @var string Directory for log files
     */
    private static string $logDir;

    /**
     * @var bool Debug mode flag
     */
    private static bool $debugMode;

    /**
     * Register global exception and error handlers
     *
     * Sets up exception handler, error handler, and shutdown function
     * to catch all errors and exceptions throughout the application.
     *
     * @return void
     */
    public static function register(): void
    {
        // Initialize log directory
        self::$logDir = BASE_PATH . '/data/logs';
        self::ensureLogDirectory();

        // Get debug mode from config
        self::$debugMode = Config::isDebugMode();

        // Register exception handler
        set_exception_handler([self::class, 'handleException']);

        // Register error handler
        set_error_handler([self::class, 'handleError']);

        // Register shutdown function to catch fatal errors
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions
     *
     * Logs the exception and displays appropriate error page based on debug mode.
     *
     * @param Throwable $exception The uncaught exception
     * @return void
     */
    public static function handleException(Throwable $exception): void
    {
        // Log the exception
        self::logException($exception);

        // Send 500 status code
        http_response_code(500);

        // Display error page
        if (self::$debugMode) {
            self::displayDebugError($exception);
        } else {
            self::displayProductionError();
        }

        exit(1);
    }

    /**
     * Handle PHP errors
     *
     * Converts PHP errors to exceptions for consistent handling.
     *
     * @param int $errno Error level
     * @param string $errstr Error message
     * @param string $errfile File where error occurred
     * @param int $errline Line number where error occurred
     * @return bool Always returns false to allow normal error handling to continue
     * @throws \ErrorException
     */
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool {
        // Don't throw exception if error reporting is disabled
        if (!(error_reporting() & $errno)) {
            return false;
        }

        // Throw ErrorException for proper handling
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Handle shutdown to catch fatal errors
     *
     * Called at script shutdown to catch fatal errors that can't be caught otherwise.
     *
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        // Check if this was a fatal error
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // Create exception from error
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );

            // Handle as exception
            self::handleException($exception);
        }
    }

    /**
     * Log exception to file
     *
     * Writes exception details to daily log file in data/logs/.
     *
     * @param Throwable $exception Exception to log
     * @return void
     */
    private static function logException(Throwable $exception): void
    {
        // Create log entry
        $logEntry = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        // Log to daily file
        $logFile = self::$logDir . '/error-' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        // Also log to PHP error log
        error_log($exception->getMessage());
    }

    /**
     * Display debug error page with stack trace
     *
     * Shows detailed error information for debugging purposes.
     *
     * @param Throwable $exception Exception to display
     * @return void
     */
    private static function displayDebugError(Throwable $exception): void
    {
        $view = new View();

        try {
            echo $view->render('errors/500', [
                'exception' => $exception,
                'debugMode' => true,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            // Fallback if view rendering fails
            self::displayFallbackError($exception);
        }
    }

    /**
     * Display production error page without technical details
     *
     * Shows friendly error message for production environment.
     *
     * @return void
     */
    private static function displayProductionError(): void
    {
        $view = new View();

        try {
            echo $view->render('errors/500', [
                'debugMode' => false
            ]);
        } catch (\Exception $e) {
            // Fallback if view rendering fails
            self::displayFallbackError();
        }
    }

    /**
     * Display fallback error page
     *
     * Simple HTML error page used when view system fails.
     *
     * @param Throwable|null $exception Optional exception for debug output
     * @return void
     */
    private static function displayFallbackError(?Throwable $exception = null): void
    {
        $appName = Config::getAppName();

        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error - {$appName}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #18181b;
            color: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            max-width: 600px;
            text-align: center;
        }
        h1 {
            font-size: 4rem;
            color: #ef4444;
            margin: 0;
        }
        h2 {
            font-size: 1.5rem;
            color: #a1a1aa;
            margin: 1rem 0;
        }
        p {
            color: #71717a;
            line-height: 1.6;
        }
        .details {
            background: #27272a;
            border: 1px solid #3f3f46;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: left;
            overflow-x: auto;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>500</h1>
        <h2>Internal Server Error</h2>
        <p>Something went wrong on our end. We've been notified and will look into it.</p>
HTML;

        if (self::$debugMode && $exception !== null) {
            $message = htmlspecialchars($exception->getMessage());
            $file = htmlspecialchars($exception->getFile());
            $line = $exception->getLine();
            $trace = htmlspecialchars($exception->getTraceAsString());

            echo <<<HTML
        <div class="details">
            <strong>Error:</strong> {$message}<br>
            <strong>File:</strong> {$file}<br>
            <strong>Line:</strong> {$line}<br>
            <br>
            <strong>Stack trace:</strong>
            <pre>{$trace}</pre>
        </div>
HTML;
        }

        echo <<<HTML
    </div>
</body>
</html>
HTML;
    }

    /**
     * Ensure log directory exists
     *
     * Creates the log directory if it doesn't exist.
     *
     * @return void
     */
    private static function ensureLogDirectory(): void
    {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }

    /**
     * Get path to log directory
     *
     * @return string Log directory path
     */
    public static function getLogDir(): string
    {
        return self::$logDir ?? BASE_PATH . '/data/logs';
    }
}
