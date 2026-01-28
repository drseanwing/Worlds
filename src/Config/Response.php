<?php

namespace Worlds\Config;

/**
 * Response class
 * 
 * Builds and sends HTTP responses with proper headers, status codes,
 * and body content for various response types (HTML, JSON, redirects, files).
 */
class Response
{
    /**
     * @var int HTTP status code
     */
    private int $statusCode = 200;

    /**
     * @var array<string, string> Response headers
     */
    private array $headers = [];

    /**
     * @var string Response body content
     */
    private string $body = '';

    /**
     * @var bool Whether headers have been sent
     */
    private bool $headersSent = false;

    /**
     * Common HTTP status codes and their messages
     */
    private const STATUS_MESSAGES = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    /**
     * Create a new Response instance
     * 
     * @param string $body Response body content
     * @param int $statusCode HTTP status code
     * @param array<string, string> $headers Response headers
     */
    public function __construct(string $body = '', int $statusCode = 200, array $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set the HTTP status code
     * 
     * @param int $code HTTP status code
     * @return self For method chaining
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get the HTTP status code
     * 
     * @return int HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set a response header
     * 
     * @param string $name Header name
     * @param string $value Header value
     * @return self For method chaining
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Get a response header
     * 
     * @param string $name Header name
     * @return string|null Header value or null if not set
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Get all response headers
     * 
     * @return array<string, string> All headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Remove a response header
     * 
     * @param string $name Header name
     * @return self For method chaining
     */
    public function removeHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Set the response body content
     * 
     * @param string $body Body content
     * @return self For method chaining
     */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get the response body content
     * 
     * @return string Body content
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Append content to the response body
     * 
     * @param string $content Content to append
     * @return self For method chaining
     */
    public function appendBody(string $content): self
    {
        $this->body .= $content;
        return $this;
    }

    /**
     * Create an HTML response
     * 
     * Sets the Content-Type header to text/html and sets the body content.
     * 
     * @param string $html HTML content
     * @param int $statusCode HTTP status code (default 200)
     * @return self New Response instance
     */
    public static function html(string $html, int $statusCode = 200): self
    {
        $response = new self($html, $statusCode);
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        return $response;
    }

    /**
     * Create a JSON response
     * 
     * Sets the Content-Type header to application/json and encodes the data.
     * 
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code (default 200)
     * @param int $options JSON encoding options (default JSON_UNESCAPED_UNICODE)
     * @return self New Response instance
     */
    public static function json(mixed $data, int $statusCode = 200, int $options = JSON_UNESCAPED_UNICODE): self
    {
        $json = json_encode($data, $options);
        if ($json === false) {
            // Use hardcoded JSON string to avoid potential recursive failure
            $json = '{"error":"Failed to encode response"}';
        }
        
        $response = new self($json, $statusCode);
        $response->setHeader('Content-Type', 'application/json; charset=utf-8');
        return $response;
    }

    /**
     * Create a redirect response
     * 
     * Sets the Location header and appropriate status code for redirects.
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default 302 for temporary redirect)
     * @return self New Response instance
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->setHeader('Location', $url);
        return $response;
    }

    /**
     * Create a file download response
     *
     * Sets appropriate headers for file downloads. Can accept either a file path
     * or direct content with filename and MIME type.
     *
     * @param string $contentOrPath File path or content string
     * @param string $downloadName Filename for download (required if passing content)
     * @param string|null $mimeType MIME type (auto-detected for files, required for content)
     * @return self New Response instance
     * @throws \RuntimeException If file cannot be read
     */
    public static function download(string $contentOrPath, string $downloadName, ?string $mimeType = null): self
    {
        // Check if first parameter is a file path
        if (file_exists($contentOrPath) && is_file($contentOrPath)) {
            // File path mode
            if (!is_readable($contentOrPath)) {
                throw new \RuntimeException("File not found or not readable");
            }

            $content = file_get_contents($contentOrPath);
            if ($content === false) {
                throw new \RuntimeException("Failed to read file");
            }

            // Determine MIME type
            if ($mimeType === null) {
                $mimeType = self::getMimeType($contentOrPath);
            }
        } else {
            // Direct content mode
            $content = $contentOrPath;

            // MIME type is required for direct content
            if ($mimeType === null) {
                $mimeType = 'application/octet-stream';
            }
        }

        // Sanitize filename
        $filename = self::sanitizeFilename($downloadName);

        $response = new self($content, 200);
        $response->setHeader('Content-Type', $mimeType);
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setHeader('Content-Length', (string) strlen($content));
        $response->setHeader('Cache-Control', 'private, no-cache, no-store, must-revalidate');

        return $response;
    }

    /**
     * Create a file inline response (display in browser)
     * 
     * Sets appropriate headers for inline file display.
     * 
     * @param string $filePath Path to the file
     * @param string|null $mimeType MIME type (auto-detected if null)
     * @return self New Response instance
     * @throws \RuntimeException If file cannot be read
     */
    public static function file(string $filePath, ?string $mimeType = null): self
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException("File not found or not readable");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Failed to read file");
        }

        // Determine MIME type
        if ($mimeType === null) {
            $mimeType = self::getMimeType($filePath);
        }

        $response = new self($content, 200);
        $response->setHeader('Content-Type', $mimeType);
        $response->setHeader('Content-Length', (string) strlen($content));
        
        return $response;
    }

    /**
     * Get MIME type for a file
     * 
     * @param string $filePath Path to the file
     * @return string MIME type
     */
    private static function getMimeType(string $filePath): string
    {
        // Try to use finfo if available
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mimeType = finfo_file($finfo, $filePath);
                finfo_close($finfo);
                if ($mimeType !== false) {
                    return $mimeType;
                }
            }
        }

        // Fallback to extension-based detection
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Sanitize a filename for use in Content-Disposition header
     * 
     * Removes or escapes characters that could cause header injection vulnerabilities.
     * 
     * @param string $filename Original filename
     * @return string Sanitized filename safe for HTTP headers
     */
    private static function sanitizeFilename(string $filename): string
    {
        // Remove any path components
        $filename = basename($filename);
        
        // Remove control characters and newlines (prevent header injection)
        $filename = preg_replace('/[\x00-\x1F\x7F\r\n]/', '', $filename);
        
        // Remove backslashes and forward slashes (prevent path traversal)
        $filename = str_replace(['\\', '/'], '', $filename);
        
        // Escape double quotes (prevent header injection)
        $filename = str_replace('"', '\\"', $filename);
        
        // Ensure the filename is not empty after sanitization
        if (empty($filename)) {
            $filename = 'download';
        }
        
        return $filename;
    }

    /**
     * Create a "No Content" response (204)
     * 
     * @return self New Response instance
     */
    public static function noContent(): self
    {
        return new self('', 204);
    }

    /**
     * Create an error response
     * 
     * @param int $statusCode HTTP error status code
     * @param string $message Error message
     * @return self New Response instance
     */
    public static function error(int $statusCode, string $message = ''): self
    {
        $response = new self($message, $statusCode);
        $response->setHeader('Content-Type', 'text/plain; charset=utf-8');
        return $response;
    }

    /**
     * Send the response headers
     * 
     * @return void
     */
    private function sendHeaders(): void
    {
        if ($this->headersSent || headers_sent()) {
            return;
        }

        // Send status line
        $statusMessage = self::STATUS_MESSAGES[$this->statusCode] ?? 'Unknown';
        header("HTTP/1.1 {$this->statusCode} {$statusMessage}", true, $this->statusCode);

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true);
        }

        $this->headersSent = true;
    }

    /**
     * Send the response to the client
     * 
     * Outputs headers and body content.
     * 
     * @return void
     */
    public function send(): void
    {
        $this->sendHeaders();
        echo $this->body;
    }

    /**
     * Get the response as a string (body only, without sending)
     * 
     * @return string Response body
     */
    public function __toString(): string
    {
        return $this->body;
    }

    /**
     * Check if the response is a redirect
     * 
     * @return bool True if status code is a redirect (3xx)
     */
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Check if the response is successful
     * 
     * @return bool True if status code is 2xx
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if the response is a client error
     * 
     * @return bool True if status code is 4xx
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if the response is a server error
     * 
     * @return bool True if status code is 5xx
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Set cache control headers
     * 
     * @param int $maxAge Max age in seconds (0 for no-cache)
     * @param bool $public Whether the response can be cached publicly
     * @return self For method chaining
     */
    public function setCache(int $maxAge, bool $public = true): self
    {
        if ($maxAge <= 0) {
            $this->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
            $this->setHeader('Pragma', 'no-cache');
            $this->setHeader('Expires', '0');
        } else {
            $directive = $public ? 'public' : 'private';
            $this->setHeader('Cache-Control', "{$directive}, max-age={$maxAge}");
            $this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
        }
        
        return $this;
    }

    /**
     * Set CORS headers for cross-origin requests
     * 
     * @param string $origin Allowed origin (default '*')
     * @param array<string> $methods Allowed methods
     * @param array<string> $headers Allowed headers
     * @return self For method chaining
     */
    public function setCors(
        string $origin = '*',
        array $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        array $headers = ['Content-Type', 'Authorization']
    ): self {
        $this->setHeader('Access-Control-Allow-Origin', $origin);
        $this->setHeader('Access-Control-Allow-Methods', implode(', ', $methods));
        $this->setHeader('Access-Control-Allow-Headers', implode(', ', $headers));
        
        return $this;
    }
}
