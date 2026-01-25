<?php

namespace Worlds\Tests;

use Worlds\Repositories\EntityRepository;
use Worlds\Config\Database;

/**
 * EntityRepository Test
 *
 * Tests CRUD operations, search, and pagination functionality
 * for the EntityRepository class.
 */
class EntityRepositoryTest extends TestCase
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
     * Test creating a new entity
     */
    public function test_create_entity(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        $id = $this->repo->create([
            'campaign_id' => $campaignId,
            'entity_type' => 'character',
            'name' => 'Test Character'
        ]);

        $this->assertGreaterThan(0, $id);
        $this->assertDatabaseHas('entities', [
            'id' => $id,
            'name' => 'Test Character',
            'entity_type' => 'character'
        ]);
    }

    /**
     * Test creating entity with all fields
     */
    public function test_create_entity_with_all_fields(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        $id = $this->repo->create([
            'campaign_id' => $campaignId,
            'entity_type' => 'location',
            'name' => 'Test Location',
            'type' => 'city',
            'entry' => 'A bustling city',
            'image_path' => '/images/city.jpg',
            'is_private' => 1,
            'data' => ['population' => 10000, 'climate' => 'temperate']
        ]);

        $this->assertGreaterThan(0, $id);

        $entity = $this->repo->findById($id);
        $this->assertNotNull($entity);
        $this->assertEquals('Test Location', $entity['name']);
        $this->assertEquals('location', $entity['entity_type']);
        $this->assertEquals('city', $entity['type']);
        $this->assertEquals('A bustling city', $entity['entry']);
        $this->assertEquals('/images/city.jpg', $entity['image_path']);
        $this->assertEquals(1, $entity['is_private']);
        $this->assertIsArray($entity['data']);
        $this->assertEquals(10000, $entity['data']['population']);
        $this->assertEquals('temperate', $entity['data']['climate']);
    }

    /**
     * Test creating entity fails without required fields
     */
    public function test_create_entity_fails_without_required_fields(): void
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage('Missing required field: campaign_id');

        $this->repo->create([
            'name' => 'Test Character'
        ]);
    }

    /**
     * Test finding entity by ID
     */
    public function test_find_entity_by_id(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);
        $entityId = $this->createEntity($campaignId, 'character', 'Test Character');

        $entity = $this->repo->findById($entityId);

        $this->assertNotNull($entity);
        $this->assertEquals($entityId, $entity['id']);
        $this->assertEquals('Test Character', $entity['name']);
        $this->assertEquals('character', $entity['entity_type']);
    }

    /**
     * Test finding non-existent entity returns null
     */
    public function test_find_non_existent_entity_returns_null(): void
    {
        $entity = $this->repo->findById(99999);

        $this->assertNull($entity);
    }

    /**
     * Test updating an entity
     */
    public function test_update_entity(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);
        $entityId = $this->createEntity($campaignId, 'character', 'Old Name');

        $result = $this->repo->update($entityId, [
            'name' => 'New Name',
            'entry' => 'Updated description'
        ]);

        $this->assertTrue($result);

        $entity = $this->repo->findById($entityId);
        $this->assertEquals('New Name', $entity['name']);
        $this->assertEquals('Updated description', $entity['entry']);
    }

    /**
     * Test updating non-existent entity returns false
     */
    public function test_update_non_existent_entity_returns_false(): void
    {
        $result = $this->repo->update(99999, ['name' => 'Test']);

        $this->assertFalse($result);
    }

    /**
     * Test updating entity with JSON data
     */
    public function test_update_entity_with_json_data(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);
        $entityId = $this->createEntity($campaignId, 'character', 'Test Character');

        $result = $this->repo->update($entityId, [
            'data' => ['level' => 5, 'class' => 'warrior']
        ]);

        $this->assertTrue($result);

        $entity = $this->repo->findById($entityId);
        $this->assertIsArray($entity['data']);
        $this->assertEquals(5, $entity['data']['level']);
        $this->assertEquals('warrior', $entity['data']['class']);
    }

    /**
     * Test deleting an entity
     */
    public function test_delete_entity(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);
        $entityId = $this->createEntity($campaignId, 'character', 'Test Character');

        $result = $this->repo->delete($entityId);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('entities', ['id' => $entityId]);
    }

    /**
     * Test deleting non-existent entity returns false
     */
    public function test_delete_non_existent_entity_returns_false(): void
    {
        $result = $this->repo->delete(99999);

        $this->assertFalse($result);
    }

    /**
     * Test finding entities by type
     */
    public function test_find_entities_by_type(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        // Create entities of different types
        $this->createEntity($campaignId, 'character', 'Character 1');
        $this->createEntity($campaignId, 'character', 'Character 2');
        $this->createEntity($campaignId, 'location', 'Location 1');

        $characters = $this->repo->findByType('character', $campaignId);

        $this->assertCount(2, $characters);
        $this->assertEquals('character', $characters[0]['entity_type']);
        $this->assertEquals('character', $characters[1]['entity_type']);
    }

    /**
     * Test finding entities by campaign
     */
    public function test_find_entities_by_campaign(): void
    {
        $userId = $this->createUser();
        $campaign1Id = $this->createCampaign($userId, 'Campaign 1');
        $campaign2Id = $this->createCampaign($userId, 'Campaign 2');

        // Create entities in different campaigns
        $this->createEntity($campaign1Id, 'character', 'Character 1');
        $this->createEntity($campaign1Id, 'location', 'Location 1');
        $this->createEntity($campaign2Id, 'character', 'Character 2');

        $campaign1Entities = $this->repo->findByCampaign($campaign1Id);

        $this->assertCount(2, $campaign1Entities);
        $this->assertEquals($campaign1Id, $campaign1Entities[0]['campaign_id']);
        $this->assertEquals($campaign1Id, $campaign1Entities[1]['campaign_id']);
    }

    /**
     * Test pagination info calculation
     */
    public function test_get_pagination_info(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        // Create 15 entities
        for ($i = 1; $i <= 15; $i++) {
            $this->createEntity($campaignId, 'character', "Character $i");
        }

        $info = $this->repo->getPaginationInfo($campaignId, perPage: 10);

        $this->assertEquals(15, $info['total_items']);
        $this->assertEquals(2, $info['total_pages']);
        $this->assertEquals(10, $info['per_page']);
    }

    /**
     * Test pagination info with entity type filter
     */
    public function test_get_pagination_info_with_type_filter(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        // Create mixed entity types
        for ($i = 1; $i <= 5; $i++) {
            $this->createEntity($campaignId, 'character', "Character $i");
        }
        for ($i = 1; $i <= 3; $i++) {
            $this->createEntity($campaignId, 'location', "Location $i");
        }

        $info = $this->repo->getPaginationInfo($campaignId, entityType: 'character', perPage: 10);

        $this->assertEquals(5, $info['total_items']);
        $this->assertEquals(1, $info['total_pages']);
    }

    /**
     * Test entity data JSON encoding/decoding
     */
    public function test_entity_data_json_handling(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        $data = [
            'stats' => ['strength' => 10, 'dexterity' => 15],
            'inventory' => ['sword', 'shield', 'potion'],
            'active' => true
        ];

        $id = $this->repo->create([
            'campaign_id' => $campaignId,
            'entity_type' => 'character',
            'name' => 'Test Character',
            'data' => $data
        ]);

        $entity = $this->repo->findById($id);

        $this->assertIsArray($entity['data']);
        $this->assertEquals($data, $entity['data']);
    }

    /**
     * Test finding entity by parent
     */
    public function test_find_entities_by_parent(): void
    {
        $userId = $this->createUser();
        $campaignId = $this->createCampaign($userId);

        // Create parent entity
        $stmt = self::$db->prepare('
            INSERT INTO entities (campaign_id, type, name, parent_id)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$campaignId, 'location', 'Parent Location', null]);
        $parentId = (int) self::$db->lastInsertId();

        // Create child entities
        $stmt->execute([$campaignId, 'location', 'Child Location 1', $parentId]);
        $stmt->execute([$campaignId, 'location', 'Child Location 2', $parentId]);

        $children = $this->repo->findByParent($parentId);

        $this->assertCount(2, $children);
        $this->assertEquals($parentId, $children[0]['parent_id']);
        $this->assertEquals($parentId, $children[1]['parent_id']);
    }
}
