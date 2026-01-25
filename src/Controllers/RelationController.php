<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Repositories\RelationRepository;
use Worlds\Repositories\EntityRepository;

/**
 * RelationController class
 *
 * Handles HTTP API requests for entity relation operations.
 * All responses are JSON format for frontend consumption.
 */
class RelationController
{
    /**
     * @var RelationRepository Relation repository instance
     */
    private RelationRepository $repository;

    /**
     * @var EntityRepository Entity repository instance
     */
    private EntityRepository $entityRepository;

    /**
     * Constructor
     *
     * Initializes repository instances.
     */
    public function __construct()
    {
        $this->repository = new RelationRepository();
        $this->entityRepository = new EntityRepository();
    }

    /**
     * List all relations for an entity
     *
     * GET /api/entities/{id}/relations
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response JSON response
     */
    public function index(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Verify entity exists and user has access
        $entity = $this->entityRepository->findById($entityId);

        if ($entity === null) {
            return Response::json([
                'success' => false,
                'error' => 'Entity not found'
            ], 404);
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
        }

        // Fetch relations
        $relations = $this->repository->findByEntity($entityId);

        return Response::json([
            'success' => true,
            'data' => $relations,
            'count' => count($relations)
        ]);
    }

    /**
     * Create a new relation
     *
     * POST /api/entities/{id}/relations
     *
     * Expected JSON body:
     * {
     *   "target_id": 123,
     *   "relation_type": "ally",
     *   "mirror_relation": "ally",
     *   "attitude": 50,
     *   "is_private": false
     * }
     *
     * @param Request $request Request instance
     * @param int $entityId Source entity ID
     * @return Response JSON response
     */
    public function store(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Verify source entity exists and user has access
        $sourceEntity = $this->entityRepository->findById($entityId);

        if ($sourceEntity === null) {
            return Response::json([
                'success' => false,
                'error' => 'Source entity not found'
            ], 404);
        }

        // Verify source entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($sourceEntity['campaign_id'] !== $campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
        }

        // Parse JSON input
        $input = $request->getJson();

        if ($input === null) {
            return Response::json([
                'success' => false,
                'error' => 'Invalid JSON input'
            ], 400);
        }

        // Validate required fields
        $errors = $this->validateRelation($input);

        if (!empty($errors)) {
            return Response::json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        // Verify target entity exists and belongs to same campaign
        $targetEntity = $this->entityRepository->findById((int) $input['target_id']);

        if ($targetEntity === null) {
            return Response::json([
                'success' => false,
                'error' => 'Target entity not found'
            ], 404);
        }

        if ($targetEntity['campaign_id'] !== $campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'Target entity not in same campaign'
            ], 403);
        }

        // Check for duplicate relation
        if ($this->repository->exists($entityId, (int) $input['target_id'], $input['relation_type'])) {
            return Response::json([
                'success' => false,
                'error' => 'Relation already exists'
            ], 409);
        }

        // Prepare relation data
        $relationData = [
            'source_id' => $entityId,
            'target_id' => (int) $input['target_id'],
            'relation_type' => trim($input['relation_type']),
            'mirror_relation' => !empty($input['mirror_relation']) ? trim($input['mirror_relation']) : null,
            'attitude' => isset($input['attitude']) ? (int) $input['attitude'] : 0,
            'is_private' => !empty($input['is_private']) ? 1 : 0
        ];

        try {
            // Create relation (with mirror)
            $id = $this->repository->create($relationData);

            // Fetch created relation for response
            $relation = $this->repository->findById($id);

            return Response::json([
                'success' => true,
                'message' => 'Relation created successfully',
                'data' => $relation
            ], 201);

        } catch (\PDOException $e) {
            return Response::json([
                'success' => false,
                'error' => 'Failed to create relation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a relation
     *
     * PUT /api/relations/{id}
     *
     * Expected JSON body:
     * {
     *   "relation_type": "enemy",
     *   "mirror_relation": "enemy",
     *   "attitude": -50,
     *   "is_private": true
     * }
     *
     * @param Request $request Request instance
     * @param int $id Relation ID
     * @return Response JSON response
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch relation to verify it exists
        $relation = $this->repository->findById($id);

        if ($relation === null) {
            return Response::json([
                'success' => false,
                'error' => 'Relation not found'
            ], 404);
        }

        // Verify source entity belongs to user's campaign
        $sourceEntity = $this->entityRepository->findById($relation['source_id']);

        if ($sourceEntity === null) {
            return Response::json([
                'success' => false,
                'error' => 'Source entity not found'
            ], 404);
        }

        $campaignId = session('campaign_id');
        if ($sourceEntity['campaign_id'] !== $campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
        }

        // Parse JSON input
        $input = $request->getJson();

        if ($input === null) {
            return Response::json([
                'success' => false,
                'error' => 'Invalid JSON input'
            ], 400);
        }

        // Validate input (partial validation for updates)
        $errors = $this->validateRelation($input, true);

        if (!empty($errors)) {
            return Response::json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        // Prepare update data
        $updateData = [];

        if (isset($input['relation_type'])) {
            $updateData['relation_type'] = trim($input['relation_type']);
        }

        if (isset($input['mirror_relation'])) {
            $updateData['mirror_relation'] = !empty($input['mirror_relation']) ? trim($input['mirror_relation']) : null;
        }

        if (isset($input['attitude'])) {
            $updateData['attitude'] = (int) $input['attitude'];
        }

        if (isset($input['is_private'])) {
            $updateData['is_private'] = !empty($input['is_private']) ? 1 : 0;
        }

        if (empty($updateData)) {
            return Response::json([
                'success' => false,
                'error' => 'No fields to update'
            ], 400);
        }

        try {
            // Update relation (and mirror)
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                // Fetch updated relation for response
                $updated = $this->repository->findById($id);

                return Response::json([
                    'success' => true,
                    'message' => 'Relation updated successfully',
                    'data' => $updated
                ]);
            } else {
                return Response::json([
                    'success' => false,
                    'error' => 'Failed to update relation'
                ], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'success' => false,
                'error' => 'Failed to update relation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a relation
     *
     * DELETE /api/relations/{id}
     *
     * @param Request $request Request instance
     * @param int $id Relation ID
     * @return Response JSON response
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch relation to verify it exists
        $relation = $this->repository->findById($id);

        if ($relation === null) {
            return Response::json([
                'success' => false,
                'error' => 'Relation not found'
            ], 404);
        }

        // Verify source entity belongs to user's campaign
        $sourceEntity = $this->entityRepository->findById($relation['source_id']);

        if ($sourceEntity === null) {
            return Response::json([
                'success' => false,
                'error' => 'Source entity not found'
            ], 404);
        }

        $campaignId = session('campaign_id');
        if ($sourceEntity['campaign_id'] !== $campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
        }

        try {
            // Delete relation (and mirror)
            $success = $this->repository->delete($id);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Relation deleted successfully'
                ]);
            } else {
                return Response::json([
                    'success' => false,
                    'error' => 'Failed to delete relation'
                ], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'success' => false,
                'error' => 'Failed to delete relation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate relation data
     *
     * @param array $input Input data
     * @param bool $isUpdate Whether this is an update (partial validation)
     * @return array List of validation errors
     */
    private function validateRelation(array $input, bool $isUpdate = false): array
    {
        $errors = [];

        // Required fields for creation
        if (!$isUpdate) {
            if (empty($input['target_id'])) {
                $errors[] = 'Target entity ID is required';
            } elseif (!is_numeric($input['target_id']) || (int) $input['target_id'] <= 0) {
                $errors[] = 'Invalid target entity ID';
            }

            if (empty($input['relation_type'])) {
                $errors[] = 'Relation type is required';
            }
        }

        // Validate relation_type if provided
        if (isset($input['relation_type'])) {
            if (!is_string($input['relation_type']) || trim($input['relation_type']) === '') {
                $errors[] = 'Relation type must be a non-empty string';
            } elseif (strlen($input['relation_type']) > 100) {
                $errors[] = 'Relation type must not exceed 100 characters';
            }
        }

        // Validate mirror_relation if provided
        if (isset($input['mirror_relation']) && !empty($input['mirror_relation'])) {
            if (!is_string($input['mirror_relation'])) {
                $errors[] = 'Mirror relation must be a string';
            } elseif (strlen($input['mirror_relation']) > 100) {
                $errors[] = 'Mirror relation must not exceed 100 characters';
            }
        }

        // Validate attitude if provided
        if (isset($input['attitude'])) {
            if (!is_numeric($input['attitude'])) {
                $errors[] = 'Attitude must be a number';
            } else {
                $attitude = (int) $input['attitude'];
                if ($attitude < -100 || $attitude > 100) {
                    $errors[] = 'Attitude must be between -100 and 100';
                }
            }
        }

        return $errors;
    }
}
