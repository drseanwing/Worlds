<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Repositories\AttributeRepository;
use Worlds\Repositories\EntityRepository;

/**
 * AttributeController class
 *
 * Handles HTTP requests for attribute CRUD operations via AJAX endpoints.
 * All methods return JSON responses for client-side rendering.
 */
class AttributeController
{
    /**
     * @var AttributeRepository Attribute repository instance
     */
    private AttributeRepository $repository;

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
        $this->repository = new AttributeRepository();
        $this->entityRepository = new EntityRepository();
    }

    /**
     * Create a new attribute for an entity
     *
     * POST /api/entities/{entityId}/attributes
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response JSON response
     */
    public function store(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Verify entity exists and user has access
        $entity = $this->entityRepository->findById($entityId);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract input data
        $input = $request->getPost();

        // Validate required fields
        if (empty($input['name']) || trim($input['name']) === '') {
            return Response::json(['error' => 'Attribute name is required'], 400);
        }

        // Prepare attribute data
        $attributeData = [
            'entity_id' => $entityId,
            'name' => trim($input['name']),
            'value' => $input['value'] ?? null,
            'is_private' => isset($input['is_private']) ? 1 : 0,
            'position' => isset($input['position']) ? (int) $input['position'] : null
        ];

        try {
            // Create attribute
            $id = $this->repository->create($attributeData);

            // Fetch created attribute
            $attribute = $this->repository->findById($id);

            return Response::json([
                'success' => true,
                'message' => 'Attribute created successfully',
                'attribute' => $attribute
            ], 201);

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to create attribute: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing attribute
     *
     * PUT /api/attributes/{id}
     *
     * @param Request $request Request instance
     * @param int $id Attribute ID
     * @return Response JSON response
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch attribute to verify it exists
        $attribute = $this->repository->findById($id);

        if ($attribute === null) {
            return Response::json(['error' => 'Attribute not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($attribute['entity_id']);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract input data
        $input = $request->getPost();

        // Validate name if provided
        if (isset($input['name']) && (empty($input['name']) || trim($input['name']) === '')) {
            return Response::json(['error' => 'Attribute name cannot be empty'], 400);
        }

        // Prepare update data
        $updateData = [];

        if (isset($input['name'])) {
            $updateData['name'] = trim($input['name']);
        }

        if (array_key_exists('value', $input)) {
            $updateData['value'] = $input['value'];
        }

        if (isset($input['is_private'])) {
            $updateData['is_private'] = $input['is_private'] ? 1 : 0;
        }

        if (isset($input['position'])) {
            $updateData['position'] = (int) $input['position'];
        }

        try {
            // Update attribute
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                // Fetch updated attribute
                $attribute = $this->repository->findById($id);

                return Response::json([
                    'success' => true,
                    'message' => 'Attribute updated successfully',
                    'attribute' => $attribute
                ]);
            } else {
                return Response::json(['error' => 'Failed to update attribute'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to update attribute: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an attribute
     *
     * DELETE /api/attributes/{id}
     *
     * @param Request $request Request instance
     * @param int $id Attribute ID
     * @return Response JSON response
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch attribute to verify it exists
        $attribute = $this->repository->findById($id);

        if ($attribute === null) {
            return Response::json(['error' => 'Attribute not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($attribute['entity_id']);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        try {
            // Delete attribute
            $success = $this->repository->delete($id);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Attribute deleted successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to delete attribute'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to delete attribute: ' . $e->getMessage()
            ], 500);
        }
    }
}
