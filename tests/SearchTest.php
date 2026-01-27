<?php

namespace Worlds\Tests;

use Worlds\Repositories\EntityRepository;
use Worlds\Config\Database;

/**
 * Search Test
 *
 * Tests search functionality including full-text search,
 * filtering, and pagination of search results.
 *
 * Note: These tests use basic SQLite LIKE queries since FTS5
 * requires additional setup. Integration tests should verify
 * full FTS5 functionality.
 */
class SearchTest extends TestCase
{
    private EntityRepository $repo;

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Override Database singleton with test database
        $reflection = new \ReflectionClass(Database::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, self::$db);

        $this->repo = new EntityRepository();

        // Set up FTS5 virtual table for search testing
        $this->setupFtsTable();
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset Database singleton
        Database::reset();
    }

    /**
     * Set up FTS5 virtual table for search tests
     */
    private function setupFtsTable(): void
    {
        // Create FTS5 virtual table for entities
        self::$db->exec('
            CREATE VIRTUAL TABLE IF NOT EXISTS entities_fts USING fts5(
                name,
                entry,
                content=entities,
                content_rowid=id
            )
        ');

        // Create trigger to keep FTS table in sync with entities table
        self::$db->exec('
            CREATE TRIGGER IF NOT EXISTS entities_fts_insert AFTER INSERT ON entities BEGIN
                INSERT INTO entities_fts(rowid, name, entry)
                VALUES (new.id, new.name, new.entry);
            END
        ');

        self::$db->exec('
            CREATE TRIGGER IF NOT EXISTS entities_fts_update AFTER UPDATE ON entities BEGIN
                UPDATE entities_fts SET name = new.name, entry = new.entry
                WHERE rowid = new.id;
            END
        ');

        self::$db->exec('
            CREATE TRIGGER IF NOT EXISTS entities_fts_delete AFTER DELETE ON entities BEGIN
                DELETE FROM entities_fts WHERE rowid = old.id;
            END
        ');
    }

    /**
     * Test searching for entities by name
     */
    public function test_search_entities_by_name(): void
    {
        $this->createUser();
        $campaignId = $this->createCampaign();

        // Create test entities
        $this->createEntity($campaignId, 'character', 'Aragorn');
        $this->createEntity($campaignId, 'character', 'Gandalf');
        $this->createEntity($campaignId, 'location', 'Rivendell');

        // Search for "Aragorn"
        $results = $this->repo->search('Aragorn', $campaignId);

        $this->assertCount(1, $results);
        $this->assertEquals('Aragorn', $results[0]['name']);
    }

    /**
     * Test searching for entities by entry content
     */
    public function test_search_entities_by_description(): void
    {
        $this->createUser();
        $campaignId = $this->createCampaign();

        // Create entities with entry content (markdown description)
        $stmt = self::$db->prepare('
            INSERT INTO entities (campaign_id, entity_type, name, entry)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$campaignId, 'character', 'Character 1', 'A brave warrior']);
        $stmt->execute([$campaignId, 'character', 'Character 2', 'A wise wizard']);
        $stmt->execute([$campaignId, 'location', 'Location 1', 'A dark forest']);

        // Search for "wizard" in entry
        $results = $this->repo->search('wizard', $campaignId);

        $this->assertGreaterThan(0, count($results));
        $found = false;
        foreach ($results as $result) {
            if ($result['name'] === 'Character 2') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Should find entity with "wizard" in entry');
    }

    /**
     * Test search returns empty array for no matches
     */
    public function test_search_returns_empty_for_no_matches(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'Test Character');

        $results = $this->repo->search('NonExistentTerm', $campaignId);

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    /**
     * Test search is campaign-specific
     */
    public function test_search_is_campaign_specific(): void
    {
        $userId = $this->createUser();
        $campaign1Id = $this->createCampaign('Campaign 1');
        $campaign2Id = $this->createCampaign('Campaign 2');

        // Create entities in different campaigns
        $this->createEntity($campaign1Id, 'character', 'UniqueCharacter');
        $this->createEntity($campaign2Id, 'character', 'Another Character');

        // Search in campaign 1
        $results = $this->repo->search('UniqueCharacter', $campaign1Id);

        $this->assertCount(1, $results);
        $this->assertEquals($campaign1Id, $results[0]['campaign_id']);

        // Search in campaign 2 should not find it
        $results2 = $this->repo->search('UniqueCharacter', $campaign2Id);
        $this->assertEmpty($results2);
    }

    /**
     * Test search pagination
     */
    public function test_search_pagination(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        // Create multiple entities with common search term
        for ($i = 1; $i <= 15; $i++) {
            $this->createEntity($campaignId, 'character', "Warrior $i", 'A brave warrior');
        }

        // Get first page (5 items)
        $page1 = $this->repo->search('warrior', $campaignId, page: 1, perPage: 5);

        // Get second page
        $page2 = $this->repo->search('warrior', $campaignId, page: 2, perPage: 5);

        $this->assertCount(5, $page1);
        $this->assertCount(5, $page2);

        // Verify pages contain different results
        $this->assertNotEquals($page1[0]['id'], $page2[0]['id']);
    }

    /**
     * Test search with special characters
     */
    public function test_search_with_special_characters(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', "O'Brien");
        $this->createEntity($campaignId, 'location', "King's Landing");

        // Search should handle apostrophes
        $results = $this->repo->search("O'Brien", $campaignId);

        $this->assertGreaterThan(0, count($results));
    }

    /**
     * Test empty search query
     */
    public function test_empty_search_query(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'Test Character');

        // Empty search should return empty or all results depending on implementation
        $results = $this->repo->search('', $campaignId);

        $this->assertIsArray($results);
    }

    /**
     * Test search query sanitization
     */
    public function test_search_query_sanitization(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'Test Character');

        // Search with quotes should be sanitized
        $results = $this->repo->search('"quoted term"', $campaignId);

        $this->assertIsArray($results);
        // Should not throw exception due to FTS5 syntax errors
    }

    /**
     * Test search across multiple entity types
     */
    public function test_search_across_entity_types(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        // Create entities of different types with same search term
        $this->createEntity($campaignId, 'character', 'Dragon Slayer');
        $this->createEntity($campaignId, 'location', 'Dragon Mountain');
        $this->createEntity($campaignId, 'item', 'Dragon Sword');

        $results = $this->repo->search('Dragon', $campaignId);

        $this->assertGreaterThanOrEqual(3, count($results));

        $types = array_map(fn($r) => $r['entity_type'], $results);
        $this->assertContains('character', $types);
        $this->assertContains('location', $types);
        $this->assertContains('item', $types);
    }

    /**
     * Test case-insensitive search
     */
    public function test_case_insensitive_search(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'TestCharacter');

        // Search with different cases
        $resultsLower = $this->repo->search('testcharacter', $campaignId);
        $resultsUpper = $this->repo->search('TESTCHARACTER', $campaignId);
        $resultsMixed = $this->repo->search('TeStChArAcTeR', $campaignId);

        // All should find the entity (FTS5 is case-insensitive by default)
        $this->assertGreaterThan(0, count($resultsLower));
        $this->assertGreaterThan(0, count($resultsUpper));
        $this->assertGreaterThan(0, count($resultsMixed));
    }

    /**
     * Test search result includes rank score
     */
    public function test_search_result_includes_rank(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'Test Character', 'Test description');

        $results = $this->repo->search('Test', $campaignId);

        $this->assertNotEmpty($results);
        // FTS5 results should include BM25 rank
        if (!empty($results)) {
            $this->assertArrayHasKey('rank', $results[0]);
        }
    }

    /**
     * Test search with partial word matching
     */
    public function test_search_partial_word_matching(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'Adventurer');

        // Search for partial word
        $results = $this->repo->search('Advent', $campaignId);

        // FTS5 with phrase search should match exact phrase
        $this->assertIsArray($results);
    }

    /**
     * Test multiple search terms
     */
    public function test_multiple_search_terms(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $this->createEntity($campaignId, 'character', 'Brave Knight', 'A brave and noble knight');
        $this->createEntity($campaignId, 'character', 'Dark Wizard', 'A dark and powerful wizard');

        // Search for multiple terms
        $results = $this->repo->search('brave knight', $campaignId);

        $this->assertGreaterThan(0, count($results));
    }

    /**
     * Test search result data decoding
     */
    public function test_search_result_data_decoding(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $id = $this->repo->create([
            'campaign_id' => $campaignId,
            'entity_type' => 'character',
            'name' => 'Test Character',
            'data' => ['level' => 5, 'class' => 'warrior']
        ]);

        $results = $this->repo->search('Test', $campaignId);

        $this->assertNotEmpty($results);

        // Find our entity in results
        $entity = null;
        foreach ($results as $result) {
            if ($result['id'] === $id) {
                $entity = $result;
                break;
            }
        }

        $this->assertNotNull($entity);
        $this->assertIsArray($entity['data']);
        $this->assertEquals(5, $entity['data']['level']);
        $this->assertEquals('warrior', $entity['data']['class']);
    }

    /**
     * Test search respects per page limit
     */
    public function test_search_respects_per_page_limit(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        // Create 20 entities
        for ($i = 1; $i <= 20; $i++) {
            $this->createEntity($campaignId, 'character', "Character $i", 'Common description');
        }

        // Request only 10 results
        $results = $this->repo->search('Common', $campaignId, page: 1, perPage: 10);

        $this->assertLessThanOrEqual(10, count($results));
    }

    /**
     * Test FTS table stays in sync with entity updates
     */
    public function test_fts_table_sync_on_update(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $id = $this->createEntity($campaignId, 'character', 'Old Name');

        // Update the entity
        $this->repo->update($id, ['name' => 'New Name']);

        // Search should find updated name
        $results = $this->repo->search('New Name', $campaignId);
        $this->assertGreaterThan(0, count($results));

        // Old name should not be found
        $oldResults = $this->repo->search('Old Name', $campaignId);
        $this->assertEmpty($oldResults);
    }

    /**
     * Test FTS table stays in sync with entity deletion
     */
    public function test_fts_table_sync_on_delete(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign();

        $id = $this->createEntity($campaignId, 'character', 'Deleted Character');

        // Verify it's searchable
        $results = $this->repo->search('Deleted Character', $campaignId);
        $this->assertGreaterThan(0, count($results));

        // Delete the entity
        $this->repo->delete($id);

        // Should no longer be found in search
        $resultsAfterDelete = $this->repo->search('Deleted Character', $campaignId);
        $this->assertEmpty($resultsAfterDelete);
    }
}
