<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Post Repository
 *
 * Manages database operations for entity posts (sub-entries) including
 * CRUD operations, ordering, and reordering.
 */
class PostRepository
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
     * Find post by ID
     *
     * @param int $id Post ID
     * @return array|null Post data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM posts WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodePost($result);
    }

    /**
     * Find all posts for an entity
     *
     * @param int $entityId Entity ID
     * @return array Array of posts ordered by position
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM posts
            WHERE entity_id = ?
            ORDER BY position ASC, created_at ASC
        ');
        $stmt->execute([$entityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodePost'], $results);
    }

    /**
     * Create a new post
     *
     * @param array $data Post data (entity_id required, name optional)
     * @return int New post ID
     * @throws PDOException If required fields are missing or insert fails
     */
    public function create(array $data): int
    {
        // Validate required fields
        if (!isset($data['entity_id'])) {
            throw new PDOException("Missing required field: entity_id");
        }

        // Get next position if not provided
        if (!isset($data['position'])) {
            $data['position'] = $this->getNextPosition($data['entity_id']);
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO posts (
                entity_id,
                name,
                entry,
                is_private,
                position,
                created_at
            ) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $data['entity_id'],
            $data['name'] ?? null,
            $data['entry'] ?? null,
            $data['is_private'] ?? 0,
            $data['position']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing post
     *
     * @param int $id Post ID to update
     * @param array $data Updated post data
     * @return bool True on success, false if post not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if post exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = ['name', 'entry', 'is_private', 'position'];

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

        $sql = 'UPDATE posts SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete a post
     *
     * @param int $id Post ID to delete
     * @return bool True on success, false if post not found
     */
    public function delete(int $id): bool
    {
        // Check if post exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Reorder posts for an entity
     *
     * @param int $entityId Entity ID
     * @param array $positions Array mapping post IDs to new positions: [postId => position]
     * @return bool True on success
     * @throws PDOException On database error
     */
    public function reorder(int $entityId, array $positions): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('
                UPDATE posts
                SET position = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND entity_id = ?
            ');

            foreach ($positions as $postId => $position) {
                $stmt->execute([(int) $position, (int) $postId, $entityId]);
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
            SELECT MAX(position) as max_pos FROM posts WHERE entity_id = ?
        ');
        $stmt->execute([$entityId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result['max_pos'] ?? -1) + 1;
    }

    /**
     * Decode post data from database format
     *
     * Ensures proper types for numeric fields.
     *
     * @param array $post Raw post data from database
     * @return array Decoded post data
     */
    private function decodePost(array $post): array
    {
        // Convert numeric strings to integers
        $post['id'] = (int) $post['id'];
        $post['entity_id'] = (int) $post['entity_id'];
        $post['is_private'] = (int) $post['is_private'];
        $post['position'] = (int) $post['position'];

        return $post;
    }
}
