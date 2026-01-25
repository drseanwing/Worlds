<?php

namespace Worlds\Config;

/**
 * Auth class
 *
 * Manages user authentication and session handling with secure password
 * hashing, session management, and authentication state checking.
 */
class Auth
{
    /**
     * @var string Session key for user ID
     */
    private const SESSION_USER_ID = 'auth_user_id';

    /**
     * @var string Session key for user data
     */
    private const SESSION_USER_DATA = 'auth_user_data';

    /**
     * @var bool Whether session has been started
     */
    private static bool $sessionStarted = false;

    /**
     * Private constructor to prevent instantiation
     *
     * This class uses static methods only.
     */
    private function __construct()
    {
        // Static-only class
    }

    /**
     * Start session with secure settings
     *
     * Configures and initializes the PHP session with security best practices.
     * Only starts session once per request.
     *
     * @return void
     */
    public static function startSession(): void
    {
        if (self::$sessionStarted || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Configure secure session settings
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_samesite', 'Strict');

        // Enable secure cookies only if using HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', '1');
        }

        session_start();
        self::$sessionStarted = true;
    }

    /**
     * Hash a password using BCRYPT algorithm
     *
     * Uses PHP's password_hash function with BCRYPT for secure password storage.
     *
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify a password against a hash
     *
     * Uses PHP's password_verify function to check if password matches hash.
     *
     * @param string $password Plain text password
     * @param string $hash Stored password hash
     * @return bool True if password matches, false otherwise
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Attempt to log in a user
     *
     * Validates username and password, then stores user information in session.
     *
     * @param string $username Username to authenticate
     * @param string $password Plain text password
     * @return bool True if login successful, false otherwise
     */
    public static function attempt(string $username, string $password): bool
    {
        self::startSession();

        // Fetch user from database
        $user = self::getUserByUsername($username);

        // Check if user exists
        if ($user === null) {
            return false;
        }

        // Verify password
        if (!self::verifyPassword($password, $user['password_hash'])) {
            return false;
        }

        // Store user information in session
        $_SESSION[self::SESSION_USER_ID] = $user['id'];
        $_SESSION[self::SESSION_USER_DATA] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'] ?? null,
            'display_name' => $user['display_name'] ?? null,
            'is_admin' => (bool) ($user['is_admin'] ?? false),
        ];

        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        return true;
    }

    /**
     * Log out the current user
     *
     * Destroys session data and regenerates session ID.
     *
     * @return void
     */
    public static function logout(): void
    {
        self::startSession();

        // Unset session variables
        unset($_SESSION[self::SESSION_USER_ID]);
        unset($_SESSION[self::SESSION_USER_DATA]);

        // Destroy all session data
        $_SESSION = [];

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy the session
        session_destroy();

        // Regenerate session ID for fresh start
        session_start();
        session_regenerate_id(true);

        self::$sessionStarted = true;
    }

    /**
     * Check if a user is logged in
     *
     * @return bool True if user is logged in, false otherwise
     */
    public static function check(): bool
    {
        self::startSession();

        return isset($_SESSION[self::SESSION_USER_ID]) &&
               is_numeric($_SESSION[self::SESSION_USER_ID]);
    }

    /**
     * Get the current logged-in user
     *
     * Returns user data array or null if not logged in.
     *
     * @return array<string, mixed>|null User data or null if not logged in
     */
    public static function user(): ?array
    {
        self::startSession();

        if (!self::check()) {
            return null;
        }

        // Return cached user data from session
        if (isset($_SESSION[self::SESSION_USER_DATA])) {
            return $_SESSION[self::SESSION_USER_DATA];
        }

        // Fallback: Fetch fresh user data from database
        $userId = $_SESSION[self::SESSION_USER_ID];
        $user = self::getUserById((int) $userId);

        if ($user === null) {
            // User no longer exists in database, log out
            self::logout();
            return null;
        }

        // Cache in session
        $_SESSION[self::SESSION_USER_DATA] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'] ?? null,
            'display_name' => $user['display_name'] ?? null,
            'is_admin' => (bool) ($user['is_admin'] ?? false),
        ];

        return $_SESSION[self::SESSION_USER_DATA];
    }

    /**
     * Get the ID of the current logged-in user
     *
     * @return int|null User ID or null if not logged in
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }

    /**
     * Check if current user is an admin
     *
     * @return bool True if user is admin, false otherwise
     */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user['is_admin'] ?? false;
    }

    /**
     * Require authentication (middleware)
     *
     * Checks if user is logged in. If not, redirects to login page.
     *
     * @param string $loginUrl URL to redirect to if not authenticated (default '/login')
     * @return void
     */
    public static function requireAuth(string $loginUrl = '/login'): void
    {
        if (!self::check()) {
            // Store intended URL for redirect after login
            self::startSession();
            $_SESSION['auth_intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';

            // Redirect to login page
            Response::redirect($loginUrl)->send();
            exit;
        }
    }

    /**
     * Require admin authentication (middleware)
     *
     * Checks if user is logged in and is an admin. If not, returns 403 Forbidden.
     *
     * @return void
     */
    public static function requireAdmin(): void
    {
        if (!self::check()) {
            Response::redirect('/login')->send();
            exit;
        }

        if (!self::isAdmin()) {
            Response::error(403, 'Forbidden: Admin access required')->send();
            exit;
        }
    }

    /**
     * Get the intended URL after login
     *
     * Retrieves and clears the stored intended URL from session.
     *
     * @param string $default Default URL if no intended URL is stored
     * @return string Intended URL or default
     */
    public static function intended(string $default = '/'): string
    {
        self::startSession();

        $intended = $_SESSION['auth_intended_url'] ?? $default;
        unset($_SESSION['auth_intended_url']);

        return $intended;
    }

    /**
     * Get a user by username from database
     *
     * @param string $username Username to look up
     * @return array<string, mixed>|null User data or null if not found
     */
    private static function getUserByUsername(string $username): ?array
    {
        try {
            $stmt = Database::query(
                'SELECT id, username, email, password_hash, display_name, is_admin, created_at, updated_at
                 FROM users
                 WHERE username = ?
                 LIMIT 1',
                [$username]
            );

            $user = $stmt->fetch();
            return $user ?: null;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Auth error: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Get a user by ID from database
     *
     * @param int $userId User ID to look up
     * @return array<string, mixed>|null User data or null if not found
     */
    private static function getUserById(int $userId): ?array
    {
        try {
            $stmt = Database::query(
                'SELECT id, username, email, password_hash, display_name, is_admin, created_at, updated_at
                 FROM users
                 WHERE id = ?
                 LIMIT 1',
                [$userId]
            );

            $user = $stmt->fetch();
            return $user ?: null;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Auth error: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Create a new user
     *
     * Hashes password and inserts user into database.
     *
     * @param string $username Username
     * @param string $password Plain text password
     * @param string|null $email Email address (optional)
     * @param string|null $displayName Display name (optional)
     * @param bool $isAdmin Whether user is admin (default false)
     * @return int|false User ID on success, false on failure
     */
    public static function createUser(
        string $username,
        string $password,
        ?string $email = null,
        ?string $displayName = null,
        bool $isAdmin = false
    ): int|false {
        try {
            $passwordHash = self::hashPassword($password);

            $stmt = Database::query(
                'INSERT INTO users (username, email, password_hash, display_name, is_admin)
                 VALUES (?, ?, ?, ?, ?)',
                [$username, $email, $passwordHash, $displayName, $isAdmin ? 1 : 0]
            );

            return (int) Database::getInstance()->lastInsertId();

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Auth error: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Update user password
     *
     * @param int $userId User ID
     * @param string $newPassword New plain text password
     * @return bool True on success, false on failure
     */
    public static function updatePassword(int $userId, string $newPassword): bool
    {
        try {
            $passwordHash = self::hashPassword($newPassword);

            $stmt = Database::query(
                'UPDATE users SET password_hash = ? WHERE id = ?',
                [$passwordHash, $userId]
            );

            return $stmt->rowCount() > 0;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Auth error: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Delete a user
     *
     * @param int $userId User ID to delete
     * @return bool True on success, false on failure
     */
    public static function deleteUser(int $userId): bool
    {
        try {
            $stmt = Database::query('DELETE FROM users WHERE id = ?', [$userId]);
            return $stmt->rowCount() > 0;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Auth error: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Check if a username already exists
     *
     * @param string $username Username to check
     * @param int|null $excludeUserId User ID to exclude from check (for updates)
     * @return bool True if username exists, false otherwise
     */
    public static function usernameExists(string $username, ?int $excludeUserId = null): bool
    {
        try {
            if ($excludeUserId !== null) {
                $stmt = Database::query(
                    'SELECT COUNT(*) FROM users WHERE username = ? AND id != ?',
                    [$username, $excludeUserId]
                );
            } else {
                $stmt = Database::query(
                    'SELECT COUNT(*) FROM users WHERE username = ?',
                    [$username]
                );
            }

            return (int) $stmt->fetchColumn() > 0;

        } catch (\PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Auth error: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Login as a specific user (for testing/admin purposes)
     *
     * WARNING: This bypasses password verification. Use with caution.
     *
     * @param int $userId User ID to log in as
     * @return bool True on success, false if user doesn't exist
     */
    public static function loginAs(int $userId): bool
    {
        self::startSession();

        $user = self::getUserById($userId);

        if ($user === null) {
            return false;
        }

        $_SESSION[self::SESSION_USER_ID] = $user['id'];
        $_SESSION[self::SESSION_USER_DATA] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'] ?? null,
            'display_name' => $user['display_name'] ?? null,
            'is_admin' => (bool) ($user['is_admin'] ?? false),
        ];

        session_regenerate_id(true);

        return true;
    }

    /**
     * Reset session state (mainly for testing)
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$sessionStarted = false;

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}
