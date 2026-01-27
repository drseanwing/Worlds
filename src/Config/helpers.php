<?php

/**
 * Helper Functions
 *
 * Global utility functions for use throughout the application,
 * especially in templates and views.
 */

use Worlds\Config\Config;

if (!function_exists('e')) {
    /**
     * Escape HTML special characters
     *
     * Prevents XSS attacks by converting special characters to HTML entities.
     *
     * @param string|null $value Value to escape
     * @return string Escaped string safe for HTML output
     */
    function e(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('escape')) {
    /**
     * Alias for e() - Escape HTML special characters
     *
     * @param string|null $value Value to escape
     * @return string Escaped string safe for HTML output
     */
    function escape(?string $value): string
    {
        return e($value);
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL for a given path
     *
     * Creates a full URL path with optional parameters.
     *
     * @param string $path URL path (e.g., '/entities/123')
     * @param array<string, mixed> $params Query parameters to append
     * @return string Full URL path
     */
    function url(string $path, array $params = []): string
    {
        // Ensure path starts with /
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        // Build query string if parameters provided
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $path .= '?' . $queryString;
        }

        return $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for an asset with cache-busting
     *
     * Creates a URL for static assets (CSS, JS, images) with a cache-busting
     * query parameter based on the file's last modification time.
     *
     * @param string $path Asset path relative to public directory (e.g., 'css/app.css')
     * @return string Asset URL with cache-busting parameter
     */
    function asset(string $path): string
    {
        // Remove leading slash if present
        $path = ltrim($path, '/');

        // Build full file path
        $publicPath = defined('PUBLIC_PATH') ? PUBLIC_PATH : dirname(__DIR__, 2) . '/public';
        $filePath = $publicPath . '/' . $path;

        // Get file modification time for cache busting
        $timestamp = file_exists($filePath) ? filemtime($filePath) : time();

        // Build URL with cache-busting query parameter
        $url = '/' . $path;

        if ($timestamp !== false) {
            $url .= '?v=' . $timestamp;
        }

        return $url;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get or generate CSRF token
     *
     * Creates a random CSRF token and stores it in the session.
     * Returns the existing token if already generated.
     *
     * @return string CSRF token
     */
    function csrf_token(): string
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generate token if not exists
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate hidden CSRF token input field
     *
     * Creates a hidden input field containing the CSRF token
     * for use in HTML forms.
     *
     * @return string HTML hidden input field
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify CSRF token from request
     *
     * Compares the submitted CSRF token with the session token
     * to prevent CSRF attacks.
     *
     * @param string|null $token Token to verify (defaults to $_POST['_csrf_token'])
     * @return bool True if token is valid
     */
    function verify_csrf_token(?string $token = null): bool
    {
        if ($token === null) {
            $token = $_POST['_csrf_token'] ?? '';
        }

        $sessionToken = csrf_token();

        return hash_equals($sessionToken, $token);
    }
}

if (!function_exists('old')) {
    /**
     * Get old form input value
     *
     * Retrieves previously submitted form input from the session,
     * useful for repopulating forms after validation errors.
     *
     * @param string $key Input field name
     * @param mixed $default Default value if no old input exists
     * @return mixed Old input value or default
     */
    function old(string $key, mixed $default = null): mixed
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $oldInput = $_SESSION['_old_input'] ?? [];

        return $oldInput[$key] ?? $default;
    }
}

if (!function_exists('flash_old_input')) {
    /**
     * Store current request input for next request
     *
     * Saves POST data to session so it can be retrieved via old()
     * after a redirect (typically after validation errors).
     *
     * @param array<string, mixed>|null $input Input data (defaults to $_POST)
     * @return void
     */
    function flash_old_input(?array $input = null): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['_old_input'] = $input ?? $_POST;
    }
}

if (!function_exists('clear_old_input')) {
    /**
     * Clear old input from session
     *
     * Removes flashed input data from session after it's been used.
     *
     * @return void
     */
    function clear_old_input(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['_old_input']);
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value
     *
     * Retrieves a configuration value from the Config class.
     *
     * @param string $key Configuration key
     * @param string|null $default Default value if not found
     * @return string Configuration value or default
     */
    function config(string $key, ?string $default = null): string
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     *
     * Dumps variables in a readable format and terminates execution.
     * Useful for debugging during development.
     *
     * @param mixed ...$vars Variables to dump
     * @return never
     */
    function dd(mixed ...$vars): never
    {
        echo '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 1rem; border-radius: 0.5rem; overflow: auto; font-family: monospace; font-size: 14px;">';

        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }

        echo '</pre>';

        // Add stack trace in debug mode
        if (Config::isDebugMode()) {
            echo '<pre style="background: #2d2d2d; color: #d4d4d4; padding: 1rem; border-radius: 0.5rem; overflow: auto; font-family: monospace; font-size: 12px; margin-top: 1rem;">';
            echo '<strong>Stack Trace:</strong>' . "\n";
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            echo '</pre>';
        }

        exit(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variables without terminating
     *
     * Similar to dd() but doesn't stop execution.
     *
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    function dump(mixed ...$vars): void
    {
        echo '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 1rem; border-radius: 0.5rem; overflow: auto; font-family: monospace; font-size: 14px; margin: 0.5rem 0;">';

        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }

        echo '</pre>';
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response
     *
     * Convenience function to redirect to another URL.
     *
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default 302)
     * @return \Worlds\Config\Response Redirect response
     */
    function redirect(string $url, int $statusCode = 302): \Worlds\Config\Response
    {
        return \Worlds\Config\Response::redirect($url, $statusCode);
    }
}

if (!function_exists('json_response')) {
    /**
     * Create a JSON response
     *
     * Convenience function to return JSON data.
     *
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code (default 200)
     * @return \Worlds\Config\Response JSON response
     */
    function json_response(mixed $data, int $statusCode = 200): \Worlds\Config\Response
    {
        return \Worlds\Config\Response::json($data, $statusCode);
    }
}

if (!function_exists('view')) {
    /**
     * Render a view file
     *
     * Loads a PHP template file and extracts variables into scope.
     *
     * @param string $viewPath Path to view file (relative to views directory)
     * @param array<string, mixed> $data Variables to extract into view scope
     * @return string Rendered view content
     */
    function view(string $viewPath, array $data = []): string
    {
        $viewsPath = defined('BASE_PATH') ? BASE_PATH . '/views' : dirname(__DIR__, 2) . '/views';

        // Add .php extension if not present
        if (!str_ends_with($viewPath, '.php')) {
            $viewPath .= '.php';
        }

        $fullPath = $viewsPath . '/' . $viewPath;

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("View not found: {$viewPath}");
        }

        // Extract variables and render
        extract($data, EXTR_SKIP);

        ob_start();
        require $fullPath;
        return ob_get_clean();
    }
}

if (!function_exists('session')) {
    /**
     * Get or set session values
     *
     * If called with just a key, retrieves the value.
     * If called with key and value, sets the value.
     * If called with no arguments, returns all session data.
     *
     * @param string|null $key Session key
     * @param mixed $value Value to set (if setting)
     * @return mixed Session value(s)
     */
    function session(?string $key = null, mixed $value = null): mixed
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get all session data
        if ($key === null) {
            return $_SESSION;
        }

        // Set session value
        if ($value !== null) {
            $_SESSION[$key] = $value;
            return $value;
        }

        // Get session value
        return $_SESSION[$key] ?? null;
    }
}

if (!function_exists('flash')) {
    /**
     * Flash a message to the session
     *
     * Stores a message in the session for one-time display
     * (typically after redirects). Supports both string messages
     * and arrays (for validation errors, etc.).
     *
     * @param string $key Flash message key (e.g., 'success', 'error', 'errors')
     * @param string|array<string, mixed> $message Message content or array of messages
     * @return void
     */
    function flash(string $key, string|array $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['_flash'][$key] = $message;
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get and remove a flash message
     *
     * Retrieves a flash message and removes it from the session.
     * Returns string, array, or null depending on what was stored.
     *
     * @param string $key Flash message key
     * @return string|array<string, mixed>|null Flash message or null if not found
     */
    function get_flash(string $key): string|array|null
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = $_SESSION['_flash'][$key] ?? null;

        if ($message !== null) {
            unset($_SESSION['_flash'][$key]);
        }

        return $message;
    }
}

if (!function_exists('app_name')) {
    /**
     * Get application name
     *
     * @return string Application name
     */
    function app_name(): string
    {
        return Config::getAppName();
    }
}

if (!function_exists('app_url')) {
    /**
     * Get application URL
     *
     * @return string Application URL
     */
    function app_url(): string
    {
        return Config::getAppUrl();
    }
}

if (!function_exists('is_debug')) {
    /**
     * Check if debug mode is enabled
     *
     * @return bool True if debug mode is enabled
     */
    function is_debug(): bool
    {
        return Config::isDebugMode();
    }
}
