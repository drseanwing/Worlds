<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Repositories\InventoryRepository;
use Worlds\Repositories\EntityRepository;

/**
 * InventoryController class
 *
 * Handles HTTP requests for inventory item CRUD operations via AJAX endpoints.
 * All methods return JSON responses for client-side rendering.
 */
class InventoryController
{
    /**
     * @var InventoryRepository Inventory repository instance
     */
    private InventoryRepository $repository;

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
        $this->repository = new InventoryRepository();
        $this->entityRepository = new EntityRepository();
    }

    /**
     * Get all inventory items for an entity
     *
     * GET /api/entities/{entityId}/inventory
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
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Fetch inventory items
        $items = $this->repository->findByEntity($entityId);

        return Response::json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Create a new inventory item for an entity
     *
     * POST /api/entities/{entityId}/inventory
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response JSON response
     */
    public function store(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

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
            return Response::json(['error' => 'Item name is required'], 400);
        }

        // Prepare inventory item data
        $itemData = [
            'entity_id' => $entityId,
            'name' => trim($input['name']),
            'quantity' => isset($input['quantity']) ? (int) $input['quantity'] : 1,
            'description' => $input['description'] ?? null,
            'item_entity_id' => isset($input['item_entity_id']) && $input['item_entity_id'] !== ''
                ? (int) $input['item_entity_id']
                : null,
            'position' => isset($input['position']) ? (int) $input['position'] : null,
            'is_equipped' => isset($input['is_equipped']) ? 1 : 0
        ];

        try {
            // Create inventory item
            $id = $this->repository->create($itemData);

            // Fetch created item
            $item = $this->repository->findById($id);

            return Response::json([
                'success' => true,
                'message' => 'Inventory item created successfully',
                'item' => $item
            ], 201);

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to create inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing inventory item
     *
     * PUT /api/inventory/{id}
     *
     * @param Request $request Request instance
     * @param int $id Inventory item ID
     * @return Response JSON response
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Fetch item to verify it exists
        $item = $this->repository->findById($id);

        if ($item === null) {
            return Response::json(['error' => 'Inventory item not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($item['entity_id']);

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
            return Response::json(['error' => 'Item name cannot be empty'], 400);
        }

        // Prepare update data
        $updateData = [];

        if (isset($input['name'])) {
            $updateData['name'] = trim($input['name']);
        }

        if (isset($input['quantity'])) {
            $updateData['quantity'] = (int) $input['quantity'];
        }

        if (array_key_exists('description', $input)) {
            $updateData['description'] = $input['description'];
        }

        if (array_key_exists('item_entity_id', $input)) {
            $updateData['item_entity_id'] = $input['item_entity_id'] !== ''
                ? (int) $input['item_entity_id']
                : null;
        }

        if (isset($input['position'])) {
            $updateData['position'] = (int) $input['position'];
        }

        if (isset($input['is_equipped'])) {
            $updateData['is_equipped'] = $input['is_equipped'] ? 1 : 0;
        }

        try {
            // Update item
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                // Fetch updated item
                $item = $this->repository->findById($id);

                return Response::json([
                    'success' => true,
                    'message' => 'Inventory item updated successfully',
                    'item' => $item
                ]);
            } else {
                return Response::json(['error' => 'Failed to update inventory item'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to update inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an inventory item
     *
     * DELETE /api/inventory/{id}
     *
     * @param Request $request Request instance
     * @param int $id Inventory item ID
     * @return Response JSON response
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Fetch item to verify it exists
        $item = $this->repository->findById($id);

        if ($item === null) {
            return Response::json(['error' => 'Inventory item not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($item['entity_id']);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        try {
            // Delete item
            $success = $this->repository->delete($id);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Inventory item deleted successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to delete inventory item'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to delete inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder inventory items for an entity
     *
     * POST /api/entities/{entityId}/inventory/reorder
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response JSON response
     */
    public function reorder(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Verify entity exists and user has access
        $entity = $this->entityRepository->findById($entityId);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract positions data
        $input = $request->getPost();

        if (!isset($input['positions']) || !is_array($input['positions'])) {
            return Response::json(['error' => 'Positions data is required'], 400);
        }

        try {
            // Reorder items
            $success = $this->repository->reorder($entityId, $input['positions']);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Inventory items reordered successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to reorder inventory items'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to reorder inventory items: ' . $e->getMessage()
            ], 500);
        }
    }
}
