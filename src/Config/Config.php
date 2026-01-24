<?php

namespace Worlds\Config;

/**
 * Configuration loader class
 * 
 * Reads environment variables from .env file and provides
 * getter methods with default fallback values.
 */
class Config
{
    /**
     * @var array<string, string> Loaded configuration values
     */
    private static array $config = [];

    /**
     * @var bool Whether config has been loaded
     */
    private static bool $loaded = false;

    /**
     * Load configuration from .env file
     * 
     * @param string|null $envPath Path to .env file (defaults to project root)
     * @return void
     */
    public static function load(?string $envPath = null): void
    {
        if (self::$loaded) {
            return;
        }

        $envPath = $envPath ?? dirname(__DIR__, 2) . '/.env';

        if (!file_exists($envPath)) {
            self::$loaded = true;
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            self::$loaded = true;
            return;
        }

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                [$key, $value] = array_map('trim', explode('=', $line, 2));
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                self::$config[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Get configuration value
     * 
     * @param string $key Configuration key
     * @param string|null $default Default value if key not found
     * @return string Configuration value or default
     */
    public static function get(string $key, ?string $default = null): string
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$config[$key] ?? $default ?? '';
    }

    /**
     * Get configuration value as boolean
     * 
     * @param string $key Configuration key
     * @param bool $default Default value if key not found
     * @return bool Configuration value as boolean
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        
        if ($value === '') {
            return $default;
        }

        return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
    }

    /**
     * Get database path
     * 
     * @return string Path to SQLite database file
     */
    public static function getDatabasePath(): string
    {
        return self::get('DATABASE_PATH', 'data/campaign.db');
    }

    /**
     * Check if debug mode is enabled
     * 
     * @return bool True if debug mode is enabled
     */
    public static function isDebugMode(): bool
    {
        return self::getBool('DEBUG_MODE', false);
    }

    /**
     * Get upload directory path
     * 
     * @return string Path to upload directory
     */
    public static function getUploadDir(): string
    {
        return self::get('UPLOAD_DIR', 'data/uploads');
    }

    /**
     * Get application name
     * 
     * @return string Application name
     */
    public static function getAppName(): string
    {
        return self::get('APP_NAME', 'Worlds');
    }

    /**
     * Get application URL
     * 
     * @return string Application URL
     */
    public static function getAppUrl(): string
    {
        return self::get('APP_URL', 'http://localhost:8080');
    }

    /**
     * Reset configuration (mainly for testing)
     * 
     * @return void
     */
    public static function reset(): void
    {
        self::$config = [];
        self::$loaded = false;
    }

    /**
     * Get all configuration values
     * 
     * @return array<string, string> All configuration values
     */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$config;
    }
}
