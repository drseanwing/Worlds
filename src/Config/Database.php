<?php

namespace Worlds\Config;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database singleton class
 * 
 * Manages SQLite database connections with singleton pattern,
 * migration support, query logging, and backup functionality.
 */
class Database
{
    /**
     * @var PDO|null Singleton PDO instance
     */
    private static ?PDO $instance = null;

    /**
     * @var array<int, array{query: string, time: float}> Query log entries
     */
    private static array $queryLog = [];

    /**
     * @var bool Whether query logging is enabled
     */
    private static bool $loggingEnabled = false;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        // Singleton pattern - use getInstance()
    }

    /**
     * Get singleton PDO instance
     * 
     * Returns a single PDO connection, creating one if it doesn't exist.
     * Automatically creates the database file and directory if missing.
     * 
     * @return PDO The PDO database connection
     * @throws PDOException If connection fails
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Create a new PDO connection
     * 
     * @return PDO Configured PDO instance
     * @throws PDOException If connection fails
     */
    private static function createConnection(): PDO
    {
        $dbPath = self::getDatabasePath();
        
        // Ensure database directory exists
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            if (!mkdir($dbDir, 0755, true)) {
                throw new PDOException("Failed to create database directory: {$dbDir}");
            }
        }

        // Create PDO connection with SQLite driver
        $pdo = new PDO("sqlite:{$dbPath}");

        // Configure PDO to throw exceptions on errors
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Return associative arrays by default
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Emulate prepared statements for better compatibility
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        // Enable foreign key enforcement for SQLite
        $pdo->exec('PRAGMA foreign_keys = ON');

        // Enable query logging when DEBUG_MODE environment variable is true
        self::$loggingEnabled = Config::isDebugMode();

        return $pdo;
    }

    /**
     * Get the absolute path to the database file
     * 
     * @return string Absolute path to SQLite database
     */
    public static function getDatabasePath(): string
    {
        $relativePath = Config::getDatabasePath();
        $basePath = dirname(__DIR__, 2);
        
        return $basePath . DIRECTORY_SEPARATOR . $relativePath;
    }

    /**
     * Run database migrations
     * 
     * Reads SQL migration files from the database directory and executes
     * them in numeric order. Tracks completed migrations to prevent duplicates.
     * 
     * @return array{executed: array<string>, skipped: array<string>, errors: array<string>} Migration results
     */
    public static function runMigrations(): array
    {
        $pdo = self::getInstance();
        $basePath = dirname(__DIR__, 2);
        $migrationsDir = $basePath . DIRECTORY_SEPARATOR . 'database';

        $result = [
            'executed' => [],
            'skipped' => [],
            'errors' => []
        ];

        // Create migrations tracking table if it doesn't exist
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS _migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                filename TEXT NOT NULL UNIQUE,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Get list of already executed migrations
        $stmt = $pdo->query('SELECT filename FROM _migrations');
        $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Get SQL files from database directory
        $files = glob($migrationsDir . DIRECTORY_SEPARATOR . '*.sql');
        if ($files === false) {
            $result['errors'][] = 'Failed to read migrations directory';
            return $result;
        }

        // Sort files numerically (001_, 002_, etc.)
        usort($files, function ($a, $b) {
            return basename($a) <=> basename($b);
        });

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip master migration script (handled separately)
            if ($filename === 'migrate.sql') {
                continue;
            }

            // Skip already executed migrations
            if (in_array($filename, $executedMigrations, true)) {
                $result['skipped'][] = $filename;
                continue;
            }

            try {
                $sql = file_get_contents($file);
                if ($sql === false) {
                    $result['errors'][] = "Failed to read: {$filename}";
                    continue;
                }

                // Execute migration within transaction
                $pdo->beginTransaction();
                $pdo->exec($sql);
                
                // Record successful migration
                $insertStmt = $pdo->prepare('INSERT INTO _migrations (filename) VALUES (?)');
                $insertStmt->execute([$filename]);
                
                $pdo->commit();
                $result['executed'][] = $filename;

            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $result['errors'][] = "{$filename}: " . $e->getMessage();
            }
        }

        return $result;
    }

    /**
     * Run the master migration script
     * 
     * Executes the consolidated migrate.sql file which contains all schema
     * definitions. Uses CREATE TABLE IF NOT EXISTS for idempotent execution.
     * 
     * @return bool True on success, false on failure
     * @throws PDOException On execution failure if not caught
     */
    public static function runMasterMigration(): bool
    {
        $pdo = self::getInstance();
        $basePath = dirname(__DIR__, 2);
        $migrationFile = $basePath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrate.sql';

        if (!file_exists($migrationFile)) {
            return false;
        }

        $sql = file_get_contents($migrationFile);
        if ($sql === false) {
            return false;
        }

        try {
            $pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            if (Config::isDebugMode()) {
                error_log("Migration error: " . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Execute a query with timing and logging
     * 
     * Wraps PDO query execution to add timing and logging when debug mode is enabled.
     * 
     * @param string $sql SQL query string
     * @param array<mixed> $params Query parameters
     * @return PDOStatement Executed statement
     * @throws PDOException On query failure
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = self::getInstance();
        $startTime = microtime(true);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $executionTime = microtime(true) - $startTime;

        // Log query if debug mode is enabled
        if (self::$loggingEnabled) {
            self::logQuery($sql, $executionTime, $params);
        }

        return $stmt;
    }

    /**
     * Log a query for debugging
     * 
     * @param string $sql SQL query string
     * @param float $executionTime Time in seconds
     * @param array<mixed> $params Query parameters
     * @return void
     */
    private static function logQuery(string $sql, float $executionTime, array $params = []): void
    {
        $entry = [
            'query' => $sql,
            'params' => $params,
            'time' => round($executionTime * 1000, 2), // Convert to milliseconds
            'timestamp' => date('Y-m-d H:i:s')
        ];

        self::$queryLog[] = $entry;

        // Also write to error log for real-time debugging
        $paramStr = empty($params) ? '' : ' [params: ' . json_encode($params) . ']';
        error_log(sprintf(
            "DB Query (%.2fms): %s%s",
            $entry['time'],
            $sql,
            $paramStr
        ));
    }

    /**
     * Get the query log
     * 
     * Returns all logged queries with their execution times.
     * Only populated when DEBUG_MODE environment variable is enabled.
     * 
     * @return array<int, array{query: string, time: float}> Query log entries
     */
    public static function getQueryLog(): array
    {
        return self::$queryLog;
    }

    /**
     * Clear the query log
     * 
     * @return void
     */
    public static function clearQueryLog(): void
    {
        self::$queryLog = [];
    }

    /**
     * Create a backup of the database
     * 
     * Copies the SQLite database file to a timestamped backup in the
     * data/backups directory.
     * 
     * @param string|null $backupName Optional custom backup name (without extension)
     * @return string|false Path to backup file on success, false on failure
     */
    public static function backup(?string $backupName = null): string|false
    {
        $dbPath = self::getDatabasePath();
        
        // Check if database exists
        if (!file_exists($dbPath)) {
            return false;
        }

        // Create backups directory
        $basePath = dirname(__DIR__, 2);
        $backupDir = $basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'backups';
        
        if (!is_dir($backupDir)) {
            if (!mkdir($backupDir, 0755, true)) {
                if (Config::isDebugMode()) {
                    error_log("Failed to create backup directory: {$backupDir}");
                }
                return false;
            }
        }

        // Generate backup filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $backupFilename = $backupName ?? "backup_{$timestamp}";
        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $backupFilename . '.db';

        // Ensure unique filename if backup with same name exists
        $counter = 1;
        $originalPath = $backupPath;
        while (file_exists($backupPath)) {
            $backupPath = str_replace('.db', "_{$counter}.db", $originalPath);
            $counter++;
        }

        // Copy database file
        if (!copy($dbPath, $backupPath)) {
            if (Config::isDebugMode()) {
                error_log("Failed to copy database to: {$backupPath}");
            }
            return false;
        }

        return $backupPath;
    }

    /**
     * List available backups
     * 
     * @return array<array{filename: string, path: string, created: string, size: int}> Backup information
     */
    public static function listBackups(): array
    {
        $basePath = dirname(__DIR__, 2);
        $backupDir = $basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'backups';
        
        if (!is_dir($backupDir)) {
            return [];
        }

        $backups = [];
        $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.db');
        
        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'created' => date('Y-m-d H:i:s', filemtime($file)),
                'size' => filesize($file)
            ];
        }

        // Sort by creation date, newest first
        usort($backups, function ($a, $b) {
            return strtotime($b['created']) <=> strtotime($a['created']);
        });

        return $backups;
    }

    /**
     * Restore database from a backup
     * 
     * @param string $backupPath Path to backup file
     * @return bool True on success, false on failure
     */
    public static function restore(string $backupPath): bool
    {
        if (!file_exists($backupPath)) {
            return false;
        }

        $dbPath = self::getDatabasePath();
        
        // Close existing connection
        self::close();

        // Create a backup of current database before restore
        if (file_exists($dbPath)) {
            $preRestoreBackup = $dbPath . '.pre-restore';
            if (!copy($dbPath, $preRestoreBackup)) {
                return false;
            }
        }

        // Restore from backup
        if (!copy($backupPath, $dbPath)) {
            return false;
        }

        return true;
    }

    /**
     * Close the database connection
     * 
     * @return void
     */
    public static function close(): void
    {
        self::$instance = null;
    }

    /**
     * Reset the database connection (mainly for testing)
     * 
     * Closes the connection and clears all state.
     * 
     * @return void
     */
    public static function reset(): void
    {
        self::close();
        self::$queryLog = [];
        self::$loggingEnabled = false;
    }

    /**
     * Check if database file exists
     * 
     * @return bool True if database file exists
     */
    public static function exists(): bool
    {
        return file_exists(self::getDatabasePath());
    }

    /**
     * Get database file size
     * 
     * @return int|false File size in bytes or false if file doesn't exist
     */
    public static function getFileSize(): int|false
    {
        $path = self::getDatabasePath();
        return file_exists($path) ? filesize($path) : false;
    }

    /**
     * Check if connection is active
     * 
     * @return bool True if connected
     */
    public static function isConnected(): bool
    {
        return self::$instance !== null;
    }
}
