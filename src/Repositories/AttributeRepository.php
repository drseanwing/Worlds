<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Attribute Repository
 *
 * Manages database operations for custom entity attributes including
 * CRUD operations and ordering.
 */
class AttributeRepository
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
     * Find attribute by ID
     *
     * @param int $id Attribute ID
     * @return array|null Attribute data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM attributes WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeAttribute($result);
    }

    /**
     * Find all attributes for an entity
     *
     * @param int $entityId Entity ID
     * @return array Array of attributes ordered by position
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM attributes
            WHERE entity_id = ?
            ORDER BY position ASC, created_at ASC
        ');
        $stmt->execute([$entityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeAttribute'], $results);
    }

    /**
     * Create a new attribute
     *
     * @param array $data Attribute data (entity_id, name required)
     * @return int New attribute ID
     * @throws PDOException If required fields are missing or insert fails
     */
    public function create(array $data): int
    {
        // Validate required fields
        $required = ['entity_id', 'name'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new PDOException("Missing required field: {$field}");
            }
        }

        // Get next position if not provided
        if (!isset($data['position'])) {
            $data['position'] = $this->getNextPosition($data['entity_id']);
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO attributes (
                entity_id,
                name,
                value,
                is_private,
                position,
                created_at
            ) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $data['entity_id'],
            $data['name'],
            $data['value'] ?? null,
            $data['is_private'] ?? 0,
            $data['position']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing attribute
     *
     * @param int $id Attribute ID to update
     * @param array $data Updated attribute data
     * @return bool True on success, false if attribute not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if attribute exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = ['name', 'value', 'is_private', 'position'];

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

        $sql = 'UPDATE attributes SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete an attribute
     *
     * @param int $id Attribute ID to delete
     * @return bool True on success, false if attribute not found
     */
    public function delete(int $id): bool
    {
        // Check if attribute exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM attributes WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Get next position value for an entity
     *
     * @param int $entityId Entity ID
     * @return int Next position value
     */
    private function getNextPosition(int $entityId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT MAX(position) as max_pos FROM attributes WHERE entity_id = ?
        ');
        $stmt->execute([$entityId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result['max_pos'] ?? -1) + 1;
    }

    /**
     * Decode attribute data from database format
     *
     * Ensures proper types for numeric fields.
     *
     * @param array $attribute Raw attribute data from database
     * @return array Decoded attribute data
     */
    private function decodeAttribute(array $attribute): array
    {
        // Convert numeric strings to integers
        $attribute['id'] = (int) $attribute['id'];
        $attribute['entity_id'] = (int) $attribute['entity_id'];
        $attribute['is_private'] = (int) $attribute['is_private'];
        $attribute['position'] = (int) $attribute['position'];

        return $attribute;
    }
}
