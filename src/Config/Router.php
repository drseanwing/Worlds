<?php

namespace Worlds\Config;

/**
 * Router class
 * 
 * Handles HTTP request routing, matching URLs to controller callbacks,
 * extracting route parameters, and dispatching requests.
 */
class Router
{
    /**
     * @var array<string, array<array{pattern: string, regex: string, callback: callable, params: array<string>}>> Routes grouped by HTTP method
     */
    private array $routes = [];

    /**
     * @var callable|null Handler for 404 (not found) responses
     */
    private $notFoundHandler = null;

    /**
     * @var Request|null Current request object
     */
    private ?Request $request = null;

    /**
     * Register a GET route
     * 
     * @param string $pattern URL pattern (e.g., '/entity/{id}')
     * @param callable $callback Controller callback to execute
     * @return self For method chaining
     */
    public function get(string $pattern, callable $callback): self
    {
        return $this->addRoute('GET', $pattern, $callback);
    }

    /**
     * Register a POST route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Controller callback to execute
     * @return self For method chaining
     */
    public function post(string $pattern, callable $callback): self
    {
        return $this->addRoute('POST', $pattern, $callback);
    }

    /**
     * Register a PUT route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Controller callback to execute
     * @return self For method chaining
     */
    public function put(string $pattern, callable $callback): self
    {
        return $this->addRoute('PUT', $pattern, $callback);
    }

    /**
     * Register a DELETE route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Controller callback to execute
     * @return self For method chaining
     */
    public function delete(string $pattern, callable $callback): self
    {
        return $this->addRoute('DELETE', $pattern, $callback);
    }

    /**
     * Add a route for a specific HTTP method
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $pattern URL pattern
     * @param callable $callback Controller callback
     * @return self For method chaining
     */
    private function addRoute(string $method, string $pattern, callable $callback): self
    {
        $method = strtoupper($method);
        
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        // Convert URL pattern to regex and extract parameter names
        $params = [];
        $regex = $this->patternToRegex($pattern, $params);

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'regex' => $regex,
            'callback' => $callback,
            'params' => $params
        ];

        return $this;
    }

    /**
     * Convert URL pattern to regex
     * 
     * Converts patterns like '/entity/{id}' to regex patterns
     * and extracts parameter names.
     * 
     * @param string $pattern URL pattern
     * @param array<string> $params Reference to store parameter names
     * @return string Regex pattern
     * @throws \InvalidArgumentException If pattern contains duplicate parameter names
     */
    private function patternToRegex(string $pattern, array &$params): string
    {
        // Validate pattern - must start with /
        if (!str_starts_with($pattern, '/')) {
            $pattern = '/' . $pattern;
        }

        // Escape regex special characters (except curly braces used for parameters)
        $regex = preg_replace('/([.+*?^$[\]\\(){}|])/', '\\\\$1', $pattern);
        
        // But we need to unescape our parameter syntax {name}
        $regex = str_replace(['\\{', '\\}'], ['{', '}'], $regex);
        
        // Replace {paramName} with named capture groups
        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function ($matches) use (&$params) {
                $paramName = $matches[1];
                
                // Check for duplicate parameter names
                if (in_array($paramName, $params, true)) {
                    throw new \InvalidArgumentException(
                        "Duplicate parameter name '{$paramName}' in route pattern"
                    );
                }
                
                $params[] = $paramName;
                // Match any characters except forward slash
                return '(?P<' . $paramName . '>[^/]+)';
            },
            $regex
        );

        return '#^' . $regex . '$#';
    }

    /**
     * Extract URL parameters from a matched route
     * 
     * @param string $uri Request URI
     * @param string $regex Route regex pattern
     * @param array<string> $paramNames Parameter names from route pattern
     * @return array<string, string>|null Extracted parameters or null if no match
     */
    private function extractParams(string $uri, string $regex, array $paramNames): ?array
    {
        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        $params = [];
        foreach ($paramNames as $name) {
            if (isset($matches[$name])) {
                $params[$name] = urldecode($matches[$name]);
            }
        }

        return $params;
    }

    /**
     * Parse query string from URL
     * 
     * Extracts key-value pairs from the query string portion of a URL.
     * 
     * @param string $queryString Query string (without leading ?)
     * @return array<string, string|array<string>> Parsed query parameters
     */
    public function parseQueryString(string $queryString): array
    {
        $params = [];
        
        if (empty($queryString)) {
            return $params;
        }

        parse_str($queryString, $params);
        
        return $params;
    }

    /**
     * Set the 404 not found handler
     * 
     * @param callable $handler Handler function receiving Request object
     * @return self For method chaining
     */
    public function setNotFoundHandler(callable $handler): self
    {
        $this->notFoundHandler = $handler;
        return $this;
    }

    /**
     * Dispatch a request to the matching route handler
     * 
     * Matches the request URI and method to a registered route,
     * extracts parameters, and calls the handler.
     * 
     * @param Request|null $request Request object (creates one if not provided)
     * @return mixed Result from the route handler
     */
    public function dispatch(?Request $request = null): mixed
    {
        $this->request = $request ?? Request::createFromGlobals();
        
        $method = $this->request->getMethod();
        $uri = $this->request->getPath();

        // Try to find a matching route
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route) {
                $params = $this->extractParams($uri, $route['regex'], $route['params']);
                
                if ($params !== null) {
                    // Route matched - call the handler
                    return $this->callHandler($route['callback'], $params);
                }
            }
        }

        // No matching route found - return 404
        return $this->handleNotFound();
    }

    /**
     * Call a route handler with parameters
     * 
     * @param callable $callback Route handler callback
     * @param array<string, string> $params Extracted route parameters
     * @return mixed Result from handler
     */
    private function callHandler(callable $callback, array $params): mixed
    {
        return call_user_func($callback, $this->request, $params);
    }

    /**
     * Handle 404 not found response
     * 
     * Calls the custom 404 handler if set, otherwise sends a default response.
     * 
     * @return mixed Result from 404 handler
     */
    private function handleNotFound(): mixed
    {
        http_response_code(404);

        if ($this->notFoundHandler !== null) {
            return call_user_func($this->notFoundHandler, $this->request);
        }

        // Default 404 response
        header('Content-Type: text/html; charset=utf-8');
        echo $this->getDefault404Page();
        return null;
    }

    /**
     * Get default 404 error page HTML
     * 
     * @return string HTML content for 404 page
     */
    private function getDefault404Page(): string
    {
        $appName = Config::getAppName();
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - {$appName}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        h1 {
            font-size: 6rem;
            margin: 0;
            color: #6b7280;
        }
        h2 {
            font-size: 1.5rem;
            margin: 0.5rem 0 1rem;
        }
        p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        a {
            color: #3b82f6;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="/">← Return Home</a>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get all registered routes
     * 
     * @return array<string, array<array{pattern: string, regex: string, callback: callable, params: array<string>}>> All routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Check if a route is registered for a method and pattern
     * 
     * @param string $method HTTP method
     * @param string $pattern URL pattern
     * @return bool True if route exists
     */
    public function hasRoute(string $method, string $pattern): bool
    {
        $method = strtoupper($method);
        
        if (!isset($this->routes[$method])) {
            return false;
        }

        foreach ($this->routes[$method] as $route) {
            if ($route['pattern'] === $pattern) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clear all registered routes
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->routes = [];
        $this->notFoundHandler = null;
        $this->request = null;
    }

    /**
     * Get the current request object
     * 
     * @return Request|null Current request or null if not dispatching
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
