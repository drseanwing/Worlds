<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Inventory Repository
 *
 * Manages database operations for inventory items including
 * CRUD operations, ordering, and item linking.
 */
class InventoryRepository
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
     * Find inventory item by ID
     *
     * @param int $id Inventory item ID
     * @return array|null Inventory item data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM inventory_items WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeInventoryItem($result);
    }

    /**
     * Find all inventory items for an entity
     *
     * @param int $entityId Entity ID
     * @return array Array of inventory items ordered by position
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM inventory_items
            WHERE entity_id = ?
            ORDER BY position ASC, created_at ASC
        ');
        $stmt->execute([$entityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeInventoryItem'], $results);
    }

    /**
     * Create a new inventory item
     *
     * @param array $data Inventory item data (entity_id, name required)
     * @return int New inventory item ID
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
            INSERT INTO inventory_items (
                entity_id,
                name,
                quantity,
                description,
                item_entity_id,
                position,
                is_equipped,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $data['entity_id'],
            $data['name'],
            $data['quantity'] ?? 1,
            $data['description'] ?? null,
            $data['item_entity_id'] ?? null,
            $data['position'],
            $data['is_equipped'] ?? 0
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing inventory item
     *
     * @param int $id Inventory item ID to update
     * @param array $data Updated inventory item data
     * @return bool True on success, false if item not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if item exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = ['name', 'quantity', 'description', 'item_entity_id', 'position', 'is_equipped'];

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

        $sql = 'UPDATE inventory_items SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete an inventory item
     *
     * @param int $id Inventory item ID to delete
     * @return bool True on success, false if item not found
     */
    public function delete(int $id): bool
    {
        // Check if item exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM inventory_items WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Reorder inventory items for an entity
     *
     * Updates the position values for multiple items at once.
     *
     * @param int $entityId Entity ID
     * @param array $positions Array mapping item ID to new position (e.g., [1 => 0, 2 => 1, 3 => 2])
     * @return bool True on success
     * @throws PDOException On database error
     */
    public function reorder(int $entityId, array $positions): bool
    {
        // Start transaction for atomic updates
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('
                UPDATE inventory_items
                SET position = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND entity_id = ?
            ');

            foreach ($positions as $itemId => $position) {
                $stmt->execute([$position, $itemId, $entityId]);
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
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
            SELECT MAX(position) as max_pos FROM inventory_items WHERE entity_id = ?
        ');
        $stmt->execute([$entityId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result['max_pos'] ?? -1) + 1;
    }

    /**
     * Decode inventory item data from database format
     *
     * Ensures proper types for numeric fields.
     *
     * @param array $item Raw inventory item data from database
     * @return array Decoded inventory item data
     */
    private function decodeInventoryItem(array $item): array
    {
        // Convert numeric strings to integers
        $item['id'] = (int) $item['id'];
        $item['entity_id'] = (int) $item['entity_id'];
        $item['quantity'] = (int) $item['quantity'];
        $item['position'] = (int) $item['position'];
        $item['is_equipped'] = (int) $item['is_equipped'];

        // Handle nullable item_entity_id
        if ($item['item_entity_id'] !== null) {
            $item['item_entity_id'] = (int) $item['item_entity_id'];
        }

        return $item;
    }
}
