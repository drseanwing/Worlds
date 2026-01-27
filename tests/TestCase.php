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
     *
     * Schema matches production migrations in database/ directory
     */
    protected static function runMigrations(): void
    {
        // Users table (matches 009_users.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT UNIQUE,
                password_hash TEXT NOT NULL,
                display_name TEXT,
                is_admin INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Campaigns table (matches 001_campaigns.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS campaigns (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                settings TEXT DEFAULT \'{}\',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Entities table (matches 002_entities.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS entities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                entity_type TEXT NOT NULL,
                name TEXT NOT NULL,
                type TEXT,
                entry TEXT,
                image_path TEXT,
                parent_id INTEGER,
                is_private INTEGER DEFAULT 0,
                data TEXT DEFAULT \'{}\',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
                FOREIGN KEY (parent_id) REFERENCES entities(id) ON DELETE SET NULL
            )
        ');

        // Tags table (matches 004_tags.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                colour TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');

        // Entity Tags junction table (matches 005_entity_tags.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS entity_tags (
                entity_id INTEGER NOT NULL,
                tag_id INTEGER NOT NULL,
                PRIMARY KEY (entity_id, tag_id),
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
            )
        ');

        // Relations table (matches 003_relations.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS relations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                campaign_id INTEGER NOT NULL,
                source_entity_id INTEGER NOT NULL,
                target_entity_id INTEGER NOT NULL,
                relation_type TEXT NOT NULL,
                description TEXT,
                is_bidirectional INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
                FOREIGN KEY (source_entity_id) REFERENCES entities(id) ON DELETE CASCADE,
                FOREIGN KEY (target_entity_id) REFERENCES entities(id) ON DELETE CASCADE
            )
        ');

        // Attributes table (matches 006_attributes.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS attributes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                value TEXT,
                attribute_type TEXT DEFAULT \'text\',
                is_private INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
            )
        ');

        // Posts table (matches 007_posts.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_id INTEGER,
                campaign_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                content TEXT,
                is_private INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');

        // Files table (matches 008_files.sql)
        self::$db->exec('
            CREATE TABLE IF NOT EXISTS files (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_id INTEGER,
                campaign_id INTEGER NOT NULL,
                filename TEXT NOT NULL,
                path TEXT NOT NULL,
                mime_type TEXT,
                size INTEGER,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE SET NULL,
                FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
            )
        ');

        // FTS5 virtual table for full-text search (matches 010_fts.sql)
        self::$db->exec('
            CREATE VIRTUAL TABLE IF NOT EXISTS entities_fts USING fts5(
                name,
                entry,
                content=\'entities\',
                content_rowid=\'id\'
            )
        ');
    }

    /**
     * Clean database between tests
     */
    protected static function cleanDatabase(): void
    {
        // Delete all data while preserving schema
        // Order matters due to foreign key constraints
        self::$db->exec('DELETE FROM entity_tags');
        self::$db->exec('DELETE FROM files');
        self::$db->exec('DELETE FROM posts');
        self::$db->exec('DELETE FROM attributes');
        self::$db->exec('DELETE FROM relations');
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
     *
     * @param string $username Username (unique identifier)
     * @param string $password Plain text password
     * @param string|null $email Email address (optional)
     * @param bool $isAdmin Whether user is admin
     * @return int User ID
     */
    protected function createUser(
        string $username = 'testuser',
        string $password = 'password',
        ?string $email = 'test@example.com',
        bool $isAdmin = false
    ): int {
        $stmt = self::$db->prepare('
            INSERT INTO users (username, email, password_hash, display_name, is_admin)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $username,
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            $username,
            $isAdmin ? 1 : 0
        ]);
        return (int) self::$db->lastInsertId();
    }

    /**
     * Insert a test campaign
     *
     * @param string $name Campaign name
     * @param string $description Campaign description
     * @return int Campaign ID
     */
    protected function createCampaign(string $name = 'Test Campaign', string $description = 'Test Description'): int
    {
        $stmt = self::$db->prepare('
            INSERT INTO campaigns (name, description)
            VALUES (?, ?)
        ');
        $stmt->execute([$name, $description]);
        return (int) self::$db->lastInsertId();
    }

    /**
     * Insert a test entity
     *
     * @param int $campaignId Campaign ID
     * @param string $entityType Entity type (character, location, item, etc.)
     * @param string $name Entity name
     * @param string $entry Entity description/entry content
     * @return int Entity ID
     */
    protected function createEntity(
        int $campaignId,
        string $entityType = 'character',
        string $name = 'Test Entity',
        string $entry = 'Test entry content'
    ): int {
        $stmt = self::$db->prepare('
            INSERT INTO entities (campaign_id, entity_type, name, entry)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$campaignId, $entityType, $name, $entry]);
        return (int) self::$db->lastInsertId();
    }

    /**
     * Insert a test tag
     *
     * @param int $campaignId Campaign ID
     * @param string $name Tag name
     * @param string $colour Tag colour (hex code)
     * @return int Tag ID
     */
    protected function createTag(int $campaignId, string $name = 'Test Tag', string $colour = '#ff0000'): int
    {
        $stmt = self::$db->prepare('
            INSERT INTO tags (campaign_id, name, colour)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$campaignId, $name, $colour]);
        return (int) self::$db->lastInsertId();
    }

    /**
     * Insert a test relation between entities
     *
     * @param int $campaignId Campaign ID
     * @param int $sourceEntityId Source entity ID
     * @param int $targetEntityId Target entity ID
     * @param string $relationType Type of relation
     * @return int Relation ID
     */
    protected function createRelation(
        int $campaignId,
        int $sourceEntityId,
        int $targetEntityId,
        string $relationType = 'related_to'
    ): int {
        $stmt = self::$db->prepare('
            INSERT INTO relations (campaign_id, source_entity_id, target_entity_id, relation_type)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$campaignId, $sourceEntityId, $targetEntityId, $relationType]);
        return (int) self::$db->lastInsertId();
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
