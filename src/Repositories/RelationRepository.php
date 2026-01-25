<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Relation Repository
 *
 * Manages database operations for entity relations including bidirectional
 * relationship creation, updates, and deletions with automatic mirroring.
 */
class RelationRepository
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
     * Find all relations for an entity
     *
     * Returns relations where the entity is the source, including details
     * about the target entity for each relation.
     *
     * @param int $entityId Entity ID to find relations for
     * @return array Array of relations with target entity data
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                r.id,
                r.source_id,
                r.target_id,
                r.relation_type,
                r.mirror_relation,
                r.attitude,
                r.is_private,
                e.name as target_name,
                e.entity_type as target_entity_type,
                e.image_path as target_image_path
            FROM relations r
            INNER JOIN entities e ON r.target_id = e.id
            WHERE r.source_id = ?
            ORDER BY r.relation_type ASC, e.name ASC
        ');
        $stmt->execute([$entityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeRelation'], $results);
    }

    /**
     * Find relation by ID
     *
     * @param int $id Relation ID
     * @return array|null Relation data or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                r.*,
                e.name as target_name,
                e.entity_type as target_entity_type
            FROM relations r
            INNER JOIN entities e ON r.target_id = e.id
            WHERE r.id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeRelation($result);
    }

    /**
     * Create a new relation with automatic mirror relation
     *
     * Creates both the primary relation (source -> target) and the mirror
     * relation (target -> source) in a transaction to ensure consistency.
     *
     * @param array $data Relation data (source_id, target_id, relation_type required)
     * @return int New relation ID (primary relation)
     * @throws PDOException If required fields missing or creation fails
     */
    public function create(array $data): int
    {
        // Validate required fields
        $required = ['source_id', 'target_id', 'relation_type'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new PDOException("Missing required field: {$field}");
            }
        }

        // Prevent self-relations
        if ($data['source_id'] === $data['target_id']) {
            throw new PDOException("Cannot create relation: source and target cannot be the same entity");
        }

        $this->pdo->beginTransaction();

        try {
            // Insert primary relation
            $stmt = $this->pdo->prepare('
                INSERT INTO relations (
                    source_id,
                    target_id,
                    relation_type,
                    mirror_relation,
                    attitude,
                    is_private
                ) VALUES (?, ?, ?, ?, ?, ?)
            ');

            $stmt->execute([
                $data['source_id'],
                $data['target_id'],
                $data['relation_type'],
                $data['mirror_relation'] ?? null,
                $data['attitude'] ?? 0,
                $data['is_private'] ?? 0
            ]);

            $primaryId = (int) $this->pdo->lastInsertId();

            // Create mirror relation if mirror_relation is specified
            if (!empty($data['mirror_relation'])) {
                $mirrorStmt = $this->pdo->prepare('
                    INSERT INTO relations (
                        source_id,
                        target_id,
                        relation_type,
                        mirror_relation,
                        attitude,
                        is_private
                    ) VALUES (?, ?, ?, ?, ?, ?)
                ');

                $mirrorStmt->execute([
                    $data['target_id'],          // Swap source and target
                    $data['source_id'],
                    $data['mirror_relation'],    // Use mirror as relation type
                    $data['relation_type'],      // Use original as mirror
                    $data['attitude'] ?? 0,      // Same attitude
                    $data['is_private'] ?? 0     // Same privacy
                ]);
            }

            $this->pdo->commit();

            return $primaryId;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Update a relation
     *
     * Updates the relation and its mirror (if exists) to maintain bidirectional
     * consistency. Changes to relation_type and mirror_relation will update
     * both directions.
     *
     * @param int $id Relation ID to update
     * @param array $data Updated relation data
     * @return bool True on success, false if relation not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if relation exists
        $existing = $this->findById($id);
        if ($existing === null) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            // Build dynamic update query
            $fields = [];
            $values = [];

            $allowedFields = ['relation_type', 'mirror_relation', 'attitude', 'is_private'];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $fields[] = "{$field} = ?";
                    $values[] = $data[$field];
                }
            }

            if (empty($fields)) {
                $this->pdo->commit();
                return true; // No fields to update
            }

            // Update primary relation
            $sql = 'UPDATE relations SET ' . implode(', ', $fields) . ' WHERE id = ?';
            $values[] = $id;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);

            // Find and update mirror relation if it exists
            if (!empty($existing['mirror_relation'])) {
                $mirrorStmt = $this->pdo->prepare('
                    SELECT id FROM relations
                    WHERE source_id = ? AND target_id = ? AND relation_type = ?
                    LIMIT 1
                ');
                $mirrorStmt->execute([
                    $existing['target_id'],
                    $existing['source_id'],
                    $existing['mirror_relation']
                ]);

                $mirror = $mirrorStmt->fetch(PDO::FETCH_ASSOC);

                if ($mirror) {
                    // Update mirror with swapped relation types
                    $mirrorFields = [];
                    $mirrorValues = [];

                    if (isset($data['relation_type'])) {
                        $mirrorFields[] = 'mirror_relation = ?';
                        $mirrorValues[] = $data['relation_type'];
                    }

                    if (isset($data['mirror_relation'])) {
                        $mirrorFields[] = 'relation_type = ?';
                        $mirrorValues[] = $data['mirror_relation'];
                    }

                    if (isset($data['attitude'])) {
                        $mirrorFields[] = 'attitude = ?';
                        $mirrorValues[] = $data['attitude'];
                    }

                    if (isset($data['is_private'])) {
                        $mirrorFields[] = 'is_private = ?';
                        $mirrorValues[] = $data['is_private'];
                    }

                    if (!empty($mirrorFields)) {
                        $mirrorSql = 'UPDATE relations SET ' . implode(', ', $mirrorFields) . ' WHERE id = ?';
                        $mirrorValues[] = $mirror['id'];

                        $updateMirrorStmt = $this->pdo->prepare($mirrorSql);
                        $updateMirrorStmt->execute($mirrorValues);
                    }
                }
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Delete a relation and its mirror
     *
     * Removes both the primary relation and its corresponding mirror relation
     * to maintain bidirectional consistency.
     *
     * @param int $id Relation ID to delete
     * @return bool True on success, false if relation not found
     * @throws PDOException On database error
     */
    public function delete(int $id): bool
    {
        // Check if relation exists
        $existing = $this->findById($id);
        if ($existing === null) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            // Delete primary relation
            $stmt = $this->pdo->prepare('DELETE FROM relations WHERE id = ?');
            $stmt->execute([$id]);

            // Find and delete mirror relation if it exists
            if (!empty($existing['mirror_relation'])) {
                $mirrorStmt = $this->pdo->prepare('
                    DELETE FROM relations
                    WHERE source_id = ? AND target_id = ? AND relation_type = ?
                ');
                $mirrorStmt->execute([
                    $existing['target_id'],
                    $existing['source_id'],
                    $existing['mirror_relation']
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Get relation count for an entity
     *
     * @param int $entityId Entity ID
     * @return int Number of relations (as source)
     */
    public function countByEntity(int $entityId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) as count FROM relations WHERE source_id = ?
        ');
        $stmt->execute([$entityId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    /**
     * Find relations by type
     *
     * @param int $entityId Entity ID (as source)
     * @param string $relationType Relation type to filter by
     * @return array Array of matching relations
     */
    public function findByType(int $entityId, string $relationType): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                r.*,
                e.name as target_name,
                e.entity_type as target_entity_type
            FROM relations r
            INNER JOIN entities e ON r.target_id = e.id
            WHERE r.source_id = ? AND r.relation_type = ?
            ORDER BY e.name ASC
        ');
        $stmt->execute([$entityId, $relationType]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeRelation'], $results);
    }

    /**
     * Check if relation exists between two entities
     *
     * @param int $sourceId Source entity ID
     * @param int $targetId Target entity ID
     * @param string|null $relationType Optional relation type filter
     * @return bool True if relation exists
     */
    public function exists(int $sourceId, int $targetId, ?string $relationType = null): bool
    {
        $sql = 'SELECT COUNT(*) as count FROM relations WHERE source_id = ? AND target_id = ?';
        $params = [$sourceId, $targetId];

        if ($relationType !== null) {
            $sql .= ' AND relation_type = ?';
            $params[] = $relationType;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'] > 0;
    }

    /**
     * Decode relation data from database format
     *
     * Ensures proper types for numeric fields.
     *
     * @param array $relation Raw relation data from database
     * @return array Decoded relation data
     */
    private function decodeRelation(array $relation): array
    {
        // Convert numeric strings to integers
        $relation['id'] = (int) $relation['id'];
        $relation['source_id'] = (int) $relation['source_id'];
        $relation['target_id'] = (int) $relation['target_id'];
        $relation['attitude'] = (int) $relation['attitude'];
        $relation['is_private'] = (int) $relation['is_private'];

        return $relation;
    }
}
