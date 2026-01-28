<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Ability Attachment Repository
 *
 * Manages database operations for entity-ability associations including
 * attaching/detaching abilities and tracking usage.
 */
class AbilityAttachmentRepository
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
     * Find ability attachment by ID
     *
     * @param int $id Attachment ID
     * @return array|null Attachment data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM entity_abilities WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeAttachment($result);
    }

    /**
     * Find all abilities attached to an entity
     *
     * @param int $entityId Entity ID
     * @return array Array of ability attachments with ability details
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                ea.*,
                e.name as ability_name,
                e.type as ability_type,
                e.entry as ability_entry,
                e.data as ability_data
            FROM entity_abilities ea
            INNER JOIN entities e ON ea.ability_entity_id = e.id
            WHERE ea.entity_id = ?
            ORDER BY e.name ASC
        ');
        $stmt->execute([$entityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeAttachment'], $results);
    }

    /**
     * Find all entities that have a specific ability
     *
     * @param int $abilityEntityId Ability entity ID
     * @return array Array of entity attachments
     */
    public function findByAbility(int $abilityEntityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                ea.*,
                e.name as entity_name,
                e.entity_type,
                e.type as entity_subtype
            FROM entity_abilities ea
            INNER JOIN entities e ON ea.entity_id = e.id
            WHERE ea.ability_entity_id = ?
            ORDER BY e.name ASC
        ');
        $stmt->execute([$abilityEntityId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeAttachment'], $results);
    }

    /**
     * Attach an ability to an entity
     *
     * @param int $entityId Entity ID
     * @param int $abilityEntityId Ability entity ID
     * @param int|null $chargesUsed Initial charges used (default 0)
     * @param string|null $notes Optional notes
     * @return int New attachment ID
     * @throws PDOException If attachment already exists or insert fails
     */
    public function attach(
        int $entityId,
        int $abilityEntityId,
        ?int $chargesUsed = 0,
        ?string $notes = null
    ): int {
        $stmt = $this->pdo->prepare('
            INSERT INTO entity_abilities (
                entity_id,
                ability_entity_id,
                charges_used,
                notes,
                created_at
            ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $entityId,
            $abilityEntityId,
            $chargesUsed ?? 0,
            $notes
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Detach an ability from an entity
     *
     * @param int $entityId Entity ID
     * @param int $abilityEntityId Ability entity ID
     * @return bool True on success, false if attachment not found
     */
    public function detach(int $entityId, int $abilityEntityId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM entity_abilities
            WHERE entity_id = ? AND ability_entity_id = ?
        ');
        $stmt->execute([$entityId, $abilityEntityId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Update an ability attachment
     *
     * @param int $id Attachment ID to update
     * @param array $data Updated attachment data
     * @return bool True on success, false if attachment not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if attachment exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = ['charges_used', 'notes'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($values)) {
            return true; // No fields to update
        }

        $sql = 'UPDATE entity_abilities SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete an ability attachment
     *
     * @param int $id Attachment ID to delete
     * @return bool True on success, false if attachment not found
     */
    public function delete(int $id): bool
    {
        // Check if attachment exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM entity_abilities WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Decode attachment data from database format
     *
     * Ensures proper types for numeric fields and decodes JSON data.
     *
     * @param array $attachment Raw attachment data from database
     * @return array Decoded attachment data
     */
    private function decodeAttachment(array $attachment): array
    {
        // Convert numeric strings to integers
        $attachment['id'] = (int) $attachment['id'];
        $attachment['entity_id'] = (int) $attachment['entity_id'];
        $attachment['ability_entity_id'] = (int) $attachment['ability_entity_id'];
        $attachment['charges_used'] = (int) $attachment['charges_used'];

        // Decode ability_data JSON if present
        if (isset($attachment['ability_data']) && is_string($attachment['ability_data'])) {
            $decoded = json_decode($attachment['ability_data'], true);
            $attachment['ability_data'] = $decoded ?? [];
        }

        return $attachment;
    }
}
