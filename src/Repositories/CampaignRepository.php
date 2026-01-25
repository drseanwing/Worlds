<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;

/**
 * Campaign Repository
 *
 * Manages database operations for campaigns including CRUD operations
 * and filtering campaigns by user access.
 */
class CampaignRepository
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
     * Find campaign by ID
     *
     * @param int $id Campaign ID
     * @return array|null Campaign data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM campaigns WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $this->decodeCampaign($result);
    }

    /**
     * Find campaigns by user ID
     *
     * Returns all campaigns accessible by the specified user.
     *
     * @param int $userId User ID
     * @return array Array of campaign data
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM campaigns
            WHERE user_id = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'decodeCampaign'], $results);
    }

    /**
     * Create a new campaign
     *
     * @param array $data Campaign data (name, user_id required)
     * @return int New campaign ID
     * @throws PDOException If required fields are missing or insert fails
     */
    public function create(array $data): int
    {
        // Validate required fields
        $required = ['name', 'user_id'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new PDOException("Missing required field: {$field}");
            }
        }

        // Encode settings array to JSON if present
        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings'], JSON_THROW_ON_ERROR);
        } elseif (!isset($data['settings'])) {
            $data['settings'] = '{}';
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO campaigns (
                name,
                description,
                settings,
                user_id,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ');

        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['settings'],
            $data['user_id']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing campaign
     *
     * @param int $id Campaign ID to update
     * @param array $data Updated campaign data
     * @return bool True on success, false if campaign not found
     * @throws PDOException On database error
     */
    public function update(int $id, array $data): bool
    {
        // Check if campaign exists
        if ($this->findById($id) === null) {
            return false;
        }

        // Encode settings array to JSON if present
        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings'], JSON_THROW_ON_ERROR);
        }

        // Build dynamic update query based on provided fields
        $fields = [];
        $values = [];

        $allowedFields = [
            'name',
            'description',
            'settings'
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

        $sql = 'UPDATE campaigns SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Delete a campaign and its related records
     *
     * Removes campaign and cascades to delete related entities.
     *
     * @param int $id Campaign ID to delete
     * @return bool True on success, false if campaign not found
     */
    public function delete(int $id): bool
    {
        // Check if campaign exists
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM campaigns WHERE id = ?');
        $stmt->execute([$id]);

        return true;
    }

    /**
     * Decode campaign data from database format
     *
     * Converts JSON settings field to array and ensures proper types.
     *
     * @param array $campaign Raw campaign data from database
     * @return array Decoded campaign data
     */
    private function decodeCampaign(array $campaign): array
    {
        // Decode JSON settings field
        if (isset($campaign['settings']) && is_string($campaign['settings'])) {
            $decoded = json_decode($campaign['settings'], true);
            $campaign['settings'] = $decoded ?? [];
        }

        // Convert numeric strings to integers
        $campaign['id'] = (int) $campaign['id'];
        $campaign['user_id'] = (int) $campaign['user_id'];

        return $campaign;
    }
}
