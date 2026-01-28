<?php

namespace Worlds\Config;

use DateTime;
use DateTimeInterface;

/**
 * ApiAuth class
 *
 * Handles API token authentication, generation, validation, and rate limiting
 * for the REST API. Supports token expiration and usage tracking.
 */
class ApiAuth
{
    /**
     * @var int Rate limit: maximum requests per minute per token
     */
    private const RATE_LIMIT_PER_MINUTE = 100;

    /**
     * Authenticate API request via Bearer token
     *
     * Validates the Bearer token from the Authorization header and returns
     * the associated user data. Updates last_used_at timestamp.
     *
     * @param Request $request HTTP request object
     * @return array|null User data array or null if authentication fails
     */
    public static function authenticate(Request $request): ?array
    {
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        // Extract token from "Bearer <token>"
        $token = trim(substr($authHeader, 7));

        if (empty($token)) {
            return null;
        }

        // Hash the token to compare with stored hash
        $hashedToken = self::hashToken($token);

        try {
            $db = Database::getInstance();

            // Fetch token with user data
            $stmt = $db->prepare('
                SELECT
                    t.id as token_id,
                    t.user_id,
                    t.name as token_name,
                    t.last_used_at,
                    t.expires_at,
                    u.id,
                    u.username,
                    u.email,
                    u.display_name,
                    u.is_admin
                FROM api_tokens t
                INNER JOIN users u ON t.user_id = u.id
                WHERE t.token = :token
                LIMIT 1
            ');

            $stmt->execute(['token' => $hashedToken]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            // Check if token has expired
            if ($row['expires_at'] !== null) {
                $expiresAt = new DateTime($row['expires_at']);
                if ($expiresAt < new DateTime()) {
                    return null;
                }
            }

            // Check rate limiting
            if (!self::checkRateLimit($row['token_id'])) {
                return null;
            }

            // Update last_used_at timestamp
            $updateStmt = $db->prepare('
                UPDATE api_tokens
                SET last_used_at = CURRENT_TIMESTAMP
                WHERE id = :token_id
            ');
            $updateStmt->execute(['token_id' => $row['token_id']]);

            // Return user data
            return [
                'id' => $row['id'],
                'username' => $row['username'],
                'email' => $row['email'],
                'display_name' => $row['display_name'],
                'is_admin' => (bool) $row['is_admin'],
                'token_name' => $row['token_name']
            ];

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("API Auth error: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Generate a new API token for a user
     *
     * Creates a random token, hashes it for storage, and associates it with
     * a user. Returns the plain token (only shown once).
     *
     * @param int $userId User ID to associate token with
     * @param string $name User-friendly name for the token
     * @param DateTimeInterface|null $expiresAt Expiration date (null for never)
     * @return string The generated plain token (store this, it won't be retrievable later)
     */
    public static function generateToken(int $userId, string $name, ?DateTimeInterface $expiresAt = null): string
    {
        // Generate a cryptographically secure random token
        $plainToken = bin2hex(random_bytes(32)); // 64 character hex string

        // Hash the token for storage
        $hashedToken = self::hashToken($plainToken);

        try {
            $db = Database::getInstance();

            $stmt = $db->prepare('
                INSERT INTO api_tokens (user_id, token, name, expires_at)
                VALUES (:user_id, :token, :name, :expires_at)
            ');

            $stmt->execute([
                'user_id' => $userId,
                'token' => $hashedToken,
                'name' => $name,
                'expires_at' => $expiresAt ? $expiresAt->format('Y-m-d H:i:s') : null
            ]);

            return $plainToken;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("API token generation error: " . $e->getMessage());
            }
            throw new \RuntimeException('Failed to generate API token');
        }
    }

    /**
     * Revoke (delete) an API token
     *
     * @param string $token Plain or hashed token to revoke
     * @return bool True if token was revoked, false otherwise
     */
    public static function revokeToken(string $token): bool
    {
        // Accept both plain and hashed tokens for flexibility
        $hashedToken = strlen($token) === 64 ? self::hashToken($token) : $token;

        try {
            $db = Database::getInstance();

            $stmt = $db->prepare('DELETE FROM api_tokens WHERE token = :token');
            $stmt->execute(['token' => $hashedToken]);

            return $stmt->rowCount() > 0;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("API token revocation error: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Revoke an API token by its ID
     *
     * @param int $tokenId Token ID to revoke
     * @param int $userId User ID (for authorization check)
     * @return bool True if token was revoked, false otherwise
     */
    public static function revokeTokenById(int $tokenId, int $userId): bool
    {
        try {
            $db = Database::getInstance();

            // Only allow users to revoke their own tokens
            $stmt = $db->prepare('
                DELETE FROM api_tokens
                WHERE id = :token_id AND user_id = :user_id
            ');

            $stmt->execute([
                'token_id' => $tokenId,
                'user_id' => $userId
            ]);

            return $stmt->rowCount() > 0;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("API token revocation error: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Get all tokens for a user
     *
     * Returns token metadata (NOT the actual tokens, which are hashed).
     *
     * @param int $userId User ID
     * @return array Array of token info (id, name, last_used_at, expires_at, created_at)
     */
    public static function getUserTokens(int $userId): array
    {
        try {
            $db = Database::getInstance();

            $stmt = $db->prepare('
                SELECT
                    id,
                    name,
                    last_used_at,
                    expires_at,
                    created_at
                FROM api_tokens
                WHERE user_id = :user_id
                ORDER BY created_at DESC
            ');

            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("API token listing error: " . $e->getMessage());
            }
            return [];
        }
    }

    /**
     * Hash a token using SHA-256
     *
     * @param string $token Plain text token
     * @return string Hashed token
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Check rate limit for a token
     *
     * Simple rate limiting using a SQLite table to track requests per minute.
     *
     * @param int $tokenId Token ID to check
     * @return bool True if request is allowed, false if rate limit exceeded
     */
    private static function checkRateLimit(int $tokenId): bool
    {
        try {
            $db = Database::getInstance();

            // Create rate limit tracking table if it doesn't exist
            $db->exec('
                CREATE TABLE IF NOT EXISTS api_rate_limits (
                    token_id INTEGER NOT NULL,
                    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (token_id, timestamp)
                )
            ');

            // Clean up old entries (older than 1 minute)
            $db->exec("
                DELETE FROM api_rate_limits
                WHERE timestamp < datetime('now', '-1 minute')
            ");

            // Count requests in the last minute
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM api_rate_limits
                WHERE token_id = :token_id
                AND timestamp >= datetime('now', '-1 minute')
            ");

            $stmt->execute(['token_id' => $tokenId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            $count = (int) ($result['count'] ?? 0);

            if ($count >= self::RATE_LIMIT_PER_MINUTE) {
                return false;
            }

            // Log this request
            $insertStmt = $db->prepare('
                INSERT INTO api_rate_limits (token_id, timestamp)
                VALUES (:token_id, CURRENT_TIMESTAMP)
            ');

            $insertStmt->execute(['token_id' => $tokenId]);

            return true;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("API rate limit error: " . $e->getMessage());
            }
            // On error, allow the request (fail open)
            return true;
        }
    }
}
