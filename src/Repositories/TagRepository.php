<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Tag Repository
 *
 * Manages database operations for tags including CRUD operations
 * and entity-tag associations.
 */
class TagRepository
{
    /**
     * @var PDO Database connection instance
     */
    private PDO $pdo;

    /**
     * Constructor
     *
     * Initializes repository with database connection.
     */
    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Find tag by ID
     *
     * @param int $id Tag ID
     * @return array|null Tag data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM tags WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeTag($result);
    }

    /**
     * Find tags by campaign
     *
     * Returns all tags for a specific campaign.
     *
     * @param int $campaignId Campaign ID
     * @return array Array of tag data
     */
    public function findByCampaign(int $campaignId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM tags
            WHERE campaign_id = ?
            ORDER BY name ASC
        ');
        $stmt->execute([$campaignId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeTag'], $results);
    }

    /**
     * Create a new tag
     *
     * @param array $data Tag data (campaign_id, name required)
     * @return int New tag ID
     * @throws PDOException If required fields are missing or insert fails
     */
    public function create(array $data): int
    {
        // Validate required fields
        $required = ['campaign_id', 'name'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new PDOException("Missing required field: {$field}");
            }
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO tags (
                campaign_id,
                name,
                colour,
                description,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $data['campaign_id'],
            $data['name'],
            $data['colour'] ?? '#666666',
            $data['description'] ?? null
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing tag
     *
     * @param int $id Tag ID to update
     * @param array $data Updated tag data
     * @return bool True on success, false if tag not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if tag exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = [
            'name',
            'colour',
            'description'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }

        // Always update updated_at
        $fields[] = 'updated_at = CURRENT_TIMESTAMP';

        if (empty($values)) {
            return true; // No fields to update
        }

        $sql = 'UPDATE tags SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete a tag
     *
     * Removes tag and cascades to delete entity-tag associations.
     *
     * @param int $id Tag ID to delete
     * @return bool True on success, false if tag not found
     */
    public function delete(int $id): bool
    {
        // Check if tag exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM tags WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Attach tag to entity
     *
     * Creates a many-to-many relationship between a tag and an entity.
     * Prevents duplicates by checking if association already exists.
     *
     * @param int $tagId Tag ID
     * @param int $entityId Entity ID
     * @return bool True on success, false if already attached
     */
    public function attachToEntity(int $tagId, int $entityId): bool
    {
        // Check if association already exists
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM entity_tags
            WHERE entity_id = ? AND tag_id = ?
        ');
        $stmt->execute([$entityId, $tagId]);

        if ($stmt->fetchColumn() > 0) {
            return false; // Already attached
        }

        // Create association
        $stmt = $this->pdo->prepare('
            INSERT INTO entity_tags (entity_id, tag_id, created_at)
            VALUES (?, ?, CURRENT_TIMESTAMP)
        ');
        $stmt->execute([$entityId, $tagId]);

        return true;
    }

    /**
     * Detach tag from entity
     *
     * Removes the many-to-many relationship between a tag and an entity.
     *
     * @param int $tagId Tag ID
     * @param int $entityId Entity ID
     * @return bool True on success
     */
    public function detachFromEntity(int $tagId, int $entityId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM entity_tags
            WHERE entity_id = ? AND tag_id = ?
        ');
        $stmt->execute([$entityId, $tagId]);

        return true;
    }

    /**
     * Find all tags for an entity
     *
     * @param int $entityId Entity ID
     * @return array Array of tag data
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT t.* FROM tags t
            INNER JOIN entity_tags et ON t.id = et.tag_id
            WHERE et.entity_id = ?
            ORDER BY t.name ASC
        ');
        $stmt->execute([$entityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeTag'], $results);
    }

    /**
     * Find all entities with a specific tag
     *
     * @param int $tagId Tag ID
     * @return array Array of entity IDs
     */
    public function findEntitiesByTag(int $tagId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT entity_id FROM entity_tags
            WHERE tag_id = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$tagId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Decode tag data from database format
     *
     * Ensures proper types for tag data.
     *
     * @param array $tag Raw tag data from database
     * @return array Decoded tag data
     */
    private function decodeTag(array $tag): array
    {
        // Convert numeric strings to integers
        $tag['id'] = (int) $tag['id'];
        $tag['campaign_id'] = (int) $tag['campaign_id'];

        return $tag;
    }
}
