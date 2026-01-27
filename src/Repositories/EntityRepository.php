<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Entity Repository
 *
 * Manages database operations for entities including CRUD operations,
 * searching, filtering, and pagination.
 */
class EntityRepository
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
     * Find entity by ID
     *
     * @param int $id Entity ID
     * @return array|null Entity data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM entities WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeEntity($result);
    }

    /**
     * Find entities by type
     *
     * @param string $entityType Entity type (character, location, etc.)
     * @param int $campaignId Campaign ID to filter by
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page
     * @return array Array of entities
     */
    public function findByType(
        string $entityType,
        int $campaignId,
        int $page = 1,
        int $perPage = 50
    ): array {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare('
            SELECT * FROM entities
            WHERE entity_type = ? AND campaign_id = ?
            ORDER BY name ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$entityType, $campaignId, $perPage, $offset]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeEntity'], $results);
    }

    /**
     * Find entities by campaign
     *
     * @param int $campaignId Campaign ID
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page
     * @return array Array of entities
     */
    public function findByCampaign(
        int $campaignId,
        int $page = 1,
        int $perPage = 50
    ): array {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare('
            SELECT * FROM entities
            WHERE campaign_id = ?
            ORDER BY entity_type ASC, name ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$campaignId, $perPage, $offset]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeEntity'], $results);
    }

    /**
     * Find child entities by parent ID
     *
     * @param int $parentId Parent entity ID
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page
     * @return array Array of child entities
     */
    public function findByParent(
        int $parentId,
        int $page = 1,
        int $perPage = 50
    ): array {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare('
            SELECT * FROM entities
            WHERE parent_id = ?
            ORDER BY name ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$parentId, $perPage, $offset]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeEntity'], $results);
    }

    /**
     * Create a new entity
     *
     * @param array $data Entity data (campaign_id, entity_type, name required)
     * @return int New entity ID
     * @throws PDOException If required fields are missing or insert fails
     */
    public function create(array $data): int
    {
        // Validate required fields
        $required = ['campaign_id', 'entity_type', 'name'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new PDOException("Missing required field: {$field}");
            }
        }

        // Encode data array to JSON if present
        if (isset($data['data']) && is_array($data['data'])) {
            $data['data'] = json_encode($data['data'], JSON_THROW_ON_ERROR);
        } elseif (!isset($data['data'])) {
            $data['data'] = '{}';
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO entities (
                campaign_id,
                entity_type,
                name,
                type,
                entry,
                image_path,
                parent_id,
                is_private,
                data,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $data['campaign_id'],
            $data['entity_type'],
            $data['name'],
            $data['type'] ?? null,
            $data['entry'] ?? null,
            $data['image_path'] ?? null,
            $data['parent_id'] ?? null,
            $data['is_private'] ?? 0,
            $data['data']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing entity
     *
     * @param int $id Entity ID to update
     * @param array $data Updated entity data
     * @return bool True on success, false if entity not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if entity exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Encode data array to JSON if present
        if (isset($data['data']) && is_array($data['data'])) {
            $data['data'] = json_encode($data['data'], JSON_THROW_ON_ERROR);
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = [
            'campaign_id',
            'entity_type',
            'name',
            'type',
            'entry',
            'image_path',
            'parent_id',
            'is_private',
            'data'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }

        // Always update updated_at (handled by trigger, but explicit for clarity)
        $fields[] = 'updated_at = CURRENT_TIMESTAMP';

        if (empty($fields)) {
            return true; // No fields to update
        }

        $sql = 'UPDATE entities SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete an entity and its related records
     *
     * Removes entity and cascades to delete related records:
     * - FTS entries (via trigger)
     * - Relations (via CASCADE)
     * - Entity tags (via CASCADE)
     * - Attributes (via CASCADE)
     * - Files (via CASCADE)
     *
     * @param int $id Entity ID to delete
     * @return bool True on success, false if entity not found
     */
    public function delete(int $id): bool
    {
        // Check if entity exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM entities WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Search entities using full-text search
     *
     * Uses SQLite FTS5 MATCH for fast text search across name and entry fields.
     * Returns results ranked by relevance.
     *
     * @param string $query Search query
     * @param int $campaignId Campaign ID to filter results
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page
     * @return array Array of matching entities with rank scores
     */
    public function search(
        string $query,
        int $campaignId,
        int $page = 1,
        int $perPage = 50
    ): array {
        $offset = ($page - 1) * $perPage;

        // Sanitize query for FTS5 - escape special characters
        $ftsQuery = $this->sanitizeFtsQuery($query);

        $stmt = $this->pdo->prepare('
            SELECT
                e.*,
                bm25(entities_fts) as rank
            FROM entities e
            INNER JOIN entities_fts ON e.id = entities_fts.rowid
            WHERE entities_fts MATCH ? AND e.campaign_id = ?
            ORDER BY rank
            LIMIT ? OFFSET ?
        ');

        $stmt->execute([$ftsQuery, $campaignId, $perPage, $offset]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeEntity'], $results);
    }

    /**
     * Get pagination metadata
     *
     * @param int $campaignId Campaign ID
     * @param string|null $entityType Optional entity type filter
     * @param int|null $parentId Optional parent ID filter
     * @param int $perPage Items per page
     * @return array Pagination info: total_items, total_pages, per_page
     */
    public function getPaginationInfo(
        int $campaignId,
        ?string $entityType = null,
        ?int $parentId = null,
        int $perPage = 50
    ): array {
        $sql = 'SELECT COUNT(*) as total FROM entities WHERE campaign_id = ?';
        $params = [$campaignId];

        if ($entityType !== null) {
            $sql .= ' AND entity_type = ?';
            $params[] = $entityType;
        }

        if ($parentId !== null) {
            $sql .= ' AND parent_id = ?';
            $params[] = $parentId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalItems = (int) $result['total'];
        $totalPages = (int) ceil($totalItems / $perPage);

        return [
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'per_page' => $perPage
        ];
    }

    /**
     * Decode entity data from database format
     *
     * Converts JSON data field to array and ensures proper types.
     *
     * @param array $entity Raw entity data from database
     * @return array Decoded entity data
     */
    private function decodeEntity(array $entity): array
    {
        // Decode JSON data field
        if (isset($entity['data']) && is_string($entity['data'])) {
            $decoded = json_decode($entity['data'], true);
            $entity['data'] = $decoded ?? [];
        }

        // Convert numeric strings to integers
        $entity['id'] = (int) $entity['id'];
        $entity['campaign_id'] = (int) $entity['campaign_id'];
        $entity['is_private'] = (int) $entity['is_private'];

        if ($entity['parent_id'] !== null) {
            $entity['parent_id'] = (int) $entity['parent_id'];
        }

        return $entity;
    }

    /**
     * Sanitize query string for FTS5 MATCH
     *
     * Escapes special FTS5 characters to prevent syntax errors.
     *
     * @param string $query User query string
     * @return string Sanitized query safe for FTS5 MATCH
     */
    private function sanitizeFtsQuery(string $query): string
    {
        // Remove special FTS5 operators and quotes for basic search
        $query = trim($query);

        // If query contains only special characters, return empty phrase
        if (empty($query)) {
            return '""';
        }

        // Escape double quotes
        $query = str_replace('"', '""', $query);

        // Wrap in quotes for phrase search (safer than allowing raw operators)
        return '"' . $query . '"';
    }
}
