<?php

namespace Worlds\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Worlds\Config\Database;
use PDO;

/**
 * Base test case for Worlds application tests
 *
 * Sets up in-memory SQLite database and provides
 * common test utilities for all test classes
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Database instance for testing
     */
    protected static PDO $db;

    /**
     * Set up test environment before running tests
     */
    public static function setUpBeforeClass(): void
    {
        // Initialize in-memory SQLite database
        self::$db = new PDO('sqlite::memory:');
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Run migrations
        self::runMigrations();
    }

    /**
     * Clean up database after each test
     */
    protected function tearDown(): void
    {
        // Clear all tables
        self::cleanDatabase();
    }

    /**
     * Run database migrations
     */
    protected static function runMigrations(): void
    {
        // Users table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Campaigns table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS campaigns (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');

        // Entities table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS entities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                type VARCHAR(50) NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');

        // Tags table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                name VARCHAR(100) NOT NULL,
                color VARCHAR(7),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');

        // Entity Tags junction table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS entity_tags (
                entity_id INTEGER NOT NULL,
                tag_id INTEGER NOT NULL,
                PRIMARY KEY (entity_id, tag_id),
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
            )
        ');

        // Relations table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS relations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                source_entity_id INTEGER NOT NULL,
                target_entity_id INTEGER NOT NULL,
                type VARCHAR(100) NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
                FOREIGN KEY (source_entity_id) REFERENCES entities(id) ON DELETE CASCADE,
                FOREIGN KEY (target_entity_id) REFERENCES entities(id) ON DELETE CASCADE
            )
        ');

        // Attributes table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS attributes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_id INTEGER NOT NULL,
                key VARCHAR(100) NOT NULL,
                value TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
            )
        ');

        // Posts table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');

        // Files table
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS files (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_id INTEGER,
                campaign_id INTEGER NOT NULL,
                filename VARCHAR(255) NOT NULL,
                filepath VARCHAR(500) NOT NULL,
                mime_type VARCHAR(100),
                size INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE SET NULL,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');
    }

    /**
     * Clean database between tests
     */
    protected static function cleanDatabase(): void
    {
        // Delete all data while preserving schema
        self::$db->exec('DELETE FROM entity_tags');
        self::$db->exec('DELETE FROM files');
        self::$db->exec('DELETE FROM posts');
        self::$db->exec('DELETE FROM attributes');
        self::$db->exec('DELETE FROM relations');
        self::$db->exec('DELETE FROM entity_tags');
        self::$db->exec('DELETE FROM tags');
        self::$db->exec('DELETE FROM entities');
        self::$db->exec('DELETE FROM campaigns');
        self::$db->exec('DELETE FROM users');
    }

    /**
     * Get the test database instance
     */
    protected function getDatabase(): PDO
    {
        return self::$db;
    }

    /**
     * Insert a test user
     */
    protected function createUser(string $email = 'test@example.com', string $password = 'password'): int
    {
        $stmt = self::$db->prepare('
            INSERT INTO users (email, password)
            VALUES (?, ?)
        ');
        $stmt->execute([$email, password_hash($password, PASSWORD_BCRYPT)]);
        return self::$db->lastInsertId();
    }

    /**
     * Insert a test campaign
     */
    protected function createCampaign(int $userId, string $title = 'Test Campaign', string $description = 'Test Description'): int
    {
        $stmt = self::$db->prepare('
            INSERT INTO campaigns (user_id, title, description)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$userId, $title, $description]);
        return self::$db->lastInsertId();
    }

    /**
     * Insert a test entity
     */
    protected function createEntity(int $campaignId, string $type = 'character', string $name = 'Test Entity', string $description = 'Test Description'): int
    {
        $stmt = self::$db->prepare('
            INSERT INTO entities (campaign_id, type, name, description)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$campaignId, $type, $name, $description]);
        return self::$db->lastInsertId();
    }

    /**
     * Assert database record exists
     */
    protected function assertDatabaseHas(string $table, array $data): void
    {
        $conditions = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $stmt = self::$db->prepare("SELECT COUNT(*) as count FROM $table WHERE $conditions");
        $stmt->execute(array_values($data));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertGreaterThan(0, $result['count'], "Record not found in $table with " . json_encode($data));
    }

    /**
     * Assert database record does not exist
     */
    protected function assertDatabaseMissing(string $table, array $data): void
    {
        $conditions = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $stmt = self::$db->prepare("SELECT COUNT(*) as count FROM $table WHERE $conditions");
        $stmt->execute(array_values($data));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(0, $result['count'], "Record found in $table with " . json_encode($data));
    }
}
