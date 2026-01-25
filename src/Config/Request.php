<?php

namespace Worlds\Config;

/**
 * Request class
 * 
 * Wraps HTTP request data providing convenient methods to access
 * request method, path, query parameters, POST data, and uploaded files.
 */
class Request
{
    /**
     * @var string HTTP request method
     */
    private string $method;

    /**
     * @var string Request path (without query string)
     */
    private string $path;

    /**
     * @var string Raw query string
     */
    private string $queryString;

    /**
     * @var array<string, mixed> Parsed query parameters
     */
    private array $query;

    /**
     * @var array<string, mixed> POST body data
     */
    private array $post;

    /**
     * @var array<string, mixed>|null Parsed JSON body
     */
    private ?array $json = null;

    /**
     * @var array<string, array<string, mixed>> Uploaded files
     */
    private array $files;

    /**
     * @var array<string, string> Request headers
     */
    private array $headers;

    /**
     * @var string Raw request body
     */
    private string $rawBody;

    /**
     * @var int Maximum allowed body size in bytes (10MB default)
     */
    private const MAX_BODY_SIZE = 10485760;

    /**
     * Create a new Request instance
     * 
     * @param string $method HTTP method
     * @param string $path Request path
     * @param array<string, mixed> $query Query parameters
     * @param array<string, mixed> $post POST data
     * @param array<string, array<string, mixed>> $files Uploaded files
     * @param array<string, string> $headers Request headers
     * @param string $rawBody Raw request body
     */
    public function __construct(
        string $method = 'GET',
        string $path = '/',
        array $query = [],
        array $post = [],
        array $files = [],
        array $headers = [],
        string $rawBody = ''
    ) {
        $this->method = strtoupper($method);
        $this->path = $this->normalizePath($path);
        $this->queryString = http_build_query($query);
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->headers = $headers;
        $this->rawBody = $rawBody;
    }

    /**
     * Create a Request from PHP superglobals
     * 
     * @param int|null $maxBodySize Maximum body size in bytes (null for default)
     * @return self Request instance populated from current HTTP request
     */
    public static function createFromGlobals(?int $maxBodySize = null): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Parse URI to get path without query string
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        
        // Parse query parameters
        $query = [];
        if (!empty($queryString)) {
            parse_str($queryString, $query);
        }

        // Get raw body with size limit to prevent memory exhaustion
        $rawBody = self::readBodyWithLimit($maxBodySize ?? self::MAX_BODY_SIZE);

        // Extract headers from $_SERVER
        $headers = self::getHeadersFromServer();

        return new self(
            $method,
            $path,
            $query,
            $_POST,
            $_FILES,
            $headers,
            $rawBody
        );
    }

    /**
     * Extract HTTP headers from $_SERVER
     * 
     * @return array<string, string> Headers
     */
    private static function getHeadersFromServer(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                // Convert HTTP_CONTENT_TYPE to Content-Type
                $headerName = str_replace('_', '-', substr($key, 5));
                $headerName = ucwords(strtolower($headerName), '-');
                $headers[$headerName] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $headerName = str_replace('_', '-', $key);
                $headerName = ucwords(strtolower($headerName), '-');
                $headers[$headerName] = $value;
            }
        }

        return $headers;
    }

    /**
     * Read request body with size limit
     * 
     * Reads the request body in chunks up to the maximum size to prevent
     * memory exhaustion attacks from large payloads.
     * 
     * @param int $maxSize Maximum bytes to read
     * @return string Request body content (truncated if over limit)
     */
    private static function readBodyWithLimit(int $maxSize): string
    {
        $input = fopen('php://input', 'rb');
        if ($input === false) {
            return '';
        }

        $body = '';
        $chunkSize = 8192; // 8KB chunks

        while (!feof($input) && strlen($body) < $maxSize) {
            $remaining = $maxSize - strlen($body);
            $readSize = min($chunkSize, $remaining);
            $chunk = fread($input, $readSize);
            
            if ($chunk === false) {
                break;
            }
            
            $body .= $chunk;
        }

        fclose($input);
        
        return $body;
    }

    /**
     * Normalize a path (ensure leading slash, remove trailing slash except for root)
     * 
     * @param string $path Path to normalize
     * @return string Normalized path
     */
    private function normalizePath(string $path): string
    {
        // Ensure leading slash
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        // Remove trailing slash (except for root)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    /**
     * Get the HTTP request method
     * 
     * @return string HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the request path (without query string)
     * 
     * @return string Request path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the full URI (path with query string)
     * 
     * @return string Full URI
     */
    public function getUri(): string
    {
        if (empty($this->queryString)) {
            return $this->path;
        }
        return $this->path . '?' . $this->queryString;
    }

    /**
     * Get all query parameters
     * 
     * @return array<string, mixed> Query parameters
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Get a specific query parameter
     * 
     * @param string $key Parameter name
     * @param mixed $default Default value if not found
     * @return mixed Parameter value or default
     */
    public function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Check if a query parameter exists
     * 
     * @param string $key Parameter name
     * @return bool True if parameter exists
     */
    public function hasQueryParam(string $key): bool
    {
        return array_key_exists($key, $this->query);
    }

    /**
     * Get the raw query string
     * 
     * @return string Query string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Get all POST body data
     * 
     * @return array<string, mixed> POST data
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * Get a specific POST parameter
     * 
     * @param string $key Parameter name
     * @param mixed $default Default value if not found
     * @return mixed Parameter value or default
     */
    public function getPostParam(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Check if a POST parameter exists
     * 
     * @param string $key Parameter name
     * @return bool True if parameter exists
     */
    public function hasPostParam(string $key): bool
    {
        return array_key_exists($key, $this->post);
    }

    /**
     * Get parsed JSON body
     * 
     * Parses the request body as JSON and caches the result.
     * 
     * @return array<string, mixed>|null Parsed JSON or null if invalid/empty
     */
    public function getJson(): ?array
    {
        if ($this->json === null && !empty($this->rawBody)) {
            $decoded = json_decode($this->rawBody, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->json = $decoded;
            }
        }

        return $this->json;
    }

    /**
     * Get a value from the JSON body
     * 
     * @param string $key Key to retrieve
     * @param mixed $default Default value if not found
     * @return mixed Value or default
     */
    public function getJsonParam(string $key, mixed $default = null): mixed
    {
        $json = $this->getJson();
        return $json[$key] ?? $default;
    }

    /**
     * Get the raw request body
     * 
     * @return string Raw body content
     */
    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    /**
     * Get all uploaded files
     * 
     * @return array<string, array<string, mixed>> Uploaded files array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get a specific uploaded file
     * 
     * @param string $key File input name
     * @return array<string, mixed>|null File info or null if not found
     */
    public function getFile(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if a file was uploaded
     * 
     * @param string $key File input name
     * @return bool True if file exists and was uploaded successfully
     */
    public function hasFile(string $key): bool
    {
        if (!isset($this->files[$key])) {
            return false;
        }

        $file = $this->files[$key];
        return isset($file['error']) && $file['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Get all request headers
     * 
     * @return array<string, string> Headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header
     * 
     * @param string $name Header name (case-insensitive)
     * @param string|null $default Default value if not found
     * @return string|null Header value or default
     */
    public function getHeader(string $name, ?string $default = null): ?string
    {
        // Headers are case-insensitive - normalize for lookup
        $normalized = ucwords(strtolower($name), '-');
        return $this->headers[$normalized] ?? $default;
    }

    /**
     * Check if a header exists
     * 
     * @param string $name Header name
     * @return bool True if header exists
     */
    public function hasHeader(string $name): bool
    {
        $normalized = ucwords(strtolower($name), '-');
        return isset($this->headers[$normalized]);
    }

    /**
     * Check if the request is an AJAX request
     * 
     * @return bool True if request has X-Requested-With: XMLHttpRequest
     */
    public function isAjax(): bool
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Check if the request expects a JSON response
     * 
     * @return bool True if Accept header includes application/json
     */
    public function wantsJson(): bool
    {
        $accept = $this->getHeader('Accept', '');
        return str_contains($accept, 'application/json');
    }

    /**
     * Check if the request content type is JSON
     * 
     * @return bool True if Content-Type is application/json
     */
    public function isJson(): bool
    {
        $contentType = $this->getHeader('Content-Type', '');
        return str_contains($contentType, 'application/json');
    }

    /**
     * Get input from POST, JSON, or query (in that order)
     * 
     * Convenience method to get input regardless of source.
     * 
     * @param string $key Input key
     * @param mixed $default Default value
     * @return mixed Input value or default
     */
    public function input(string $key, mixed $default = null): mixed
    {
        // Check POST first
        if ($this->hasPostParam($key)) {
            return $this->getPostParam($key);
        }

        // Check JSON body
        $json = $this->getJson();
        if ($json !== null && array_key_exists($key, $json)) {
            return $json[$key];
        }

        // Check query params
        if ($this->hasQueryParam($key)) {
            return $this->getQueryParam($key);
        }

        return $default;
    }

    /**
     * Get all input data (merged from query, JSON, and POST)
     * 
     * @return array<string, mixed> All input data
     */
    public function all(): array
    {
        $json = $this->getJson() ?? [];
        return array_merge($this->query, $json, $this->post);
    }

    /**
     * Check if the request method matches
     * 
     * @param string $method Method to check
     * @return bool True if method matches
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->method;
    }

    /**
     * Check if this is a GET request
     * 
     * @return bool True if GET request
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Check if this is a POST request
     * 
     * @return bool True if POST request
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Check if this is a PUT request
     * 
     * @return bool True if PUT request
     */
    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    /**
     * Check if this is a DELETE request
     * 
     * @return bool True if DELETE request
     */
    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }
}
