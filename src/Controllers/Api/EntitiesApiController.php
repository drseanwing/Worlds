<?php

namespace Worlds\Controllers\Api;

use Worlds\Controllers\ApiController;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\Auth;
use Worlds\Repositories\EntityRepository;
use Worlds\Repositories\CampaignRepository;
use Worlds\Config\EntityTypes;

/**
 * EntitiesApiController class
 *
 * REST API endpoints for entity CRUD operations. Provides JSON responses
 * for listing, viewing, creating, updating, and deleting entities.
 */
class EntitiesApiController extends ApiController
{
    /**
     * @var EntityRepository Entity repository instance
     */
    private EntityRepository $repository;

    /**
     * @var CampaignRepository Campaign repository instance
     */
    private CampaignRepository $campaignRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->repository = new EntityRepository();
        $this->campaignRepository = new CampaignRepository();
        EntityTypes::load();
    }

    /**
     * List entities with pagination
     *
     * GET /api/v1/entities?campaign_id=1&type=character&page=1&per_page=50
     *
     * @param Request $request HTTP request
     * @return Response JSON response with entities list
     */
    public function index(Request $request): Response
    {
        $user = $this->requireApiAuth($request);

        // Get campaign_id from query param
        $campaignId = $request->getQueryParam('campaign_id');

        if (!$campaignId) {
            return $this->errorResponse('campaign_id is required', 400, 'MISSING_CAMPAIGN_ID');
        }

        // Verify campaign ownership
        $campaign = $this->campaignRepository->findById((int) $campaignId);
        if (!$campaign || $campaign['user_id'] !== $user['id']) {
            return $this->errorResponse('Access denied', 403, 'FORBIDDEN');
        }

        // Get optional entity type filter
        $type = $request->getQueryParam('type');

        if ($type && !EntityTypes::typeExists($type)) {
            return $this->errorResponse('Invalid entity type', 400, 'INVALID_TYPE');
        }

        // Get pagination params
        $pagination = $this->getPagination($request);

        // Fetch entities
        if ($type) {
            $result = $this->repository->findByType($type, (int) $campaignId, $pagination['page'], $pagination['per_page']);
            $paginationInfo = $this->repository->getPaginationInfo((int) $campaignId, $type, null, $pagination['per_page']);
        } else {
            $result = $this->repository->findByCampaign((int) $campaignId, $pagination['page'], $pagination['per_page']);
            $paginationInfo = $this->repository->getPaginationInfo((int) $campaignId, null, null, $pagination['per_page']);
        }

        // Format response
        return $this->successResponse(
            $result['data'] ?? [],
            [
                'page' => $pagination['page'],
                'per_page' => $pagination['per_page'],
                'total' => $paginationInfo['total'] ?? 0,
                'total_pages' => $paginationInfo['totalPages'] ?? 0
            ]
        );
    }

    /**
     * Get a single entity by ID
     *
     * GET /api/v1/entities/{id}
     *
     * @param Request $request HTTP request
     * @param int $id Entity ID
     * @return Response JSON response with entity data
     */
    public function show(Request $request, int $id): Response
    {
        $user = $this->requireApiAuth($request);

        $entity = $this->repository->findById($id);

        if (!$entity) {
            return $this->errorResponse('Entity not found', 404, 'NOT_FOUND');
        }

        // Verify campaign ownership
        $campaign = $this->campaignRepository->findById($entity['campaign_id']);
        if (!$campaign || $campaign['user_id'] !== $user['id']) {
            return $this->errorResponse('Access denied', 403, 'FORBIDDEN');
        }

        return $this->successResponse($entity);
    }

    /**
     * Create a new entity
     *
     * POST /api/v1/entities
     *
     * Required fields: campaign_id, entity_type, name
     * Optional fields: type, entry, image_path, parent_id, is_private, data
     *
     * @param Request $request HTTP request
     * @return Response JSON response with created entity
     */
    public function store(Request $request): Response
    {
        $user = $this->requireApiAuth($request);

        // Get input data (supports both JSON and form data)
        $data = $request->isJson() ? $request->getJson() : $request->getPost();

        // Validate required fields
        $missing = $this->validateRequired($data, ['campaign_id', 'entity_type', 'name']);

        if ($missing) {
            return $this->validationError(['Missing required fields: ' . implode(', ', $missing)]);
        }

        // Validate entity type
        if (!EntityTypes::typeExists($data['entity_type'])) {
            return $this->errorResponse('Invalid entity_type', 400, 'INVALID_TYPE');
        }

        // Verify campaign ownership
        $campaign = $this->campaignRepository->findById((int) $data['campaign_id']);
        if (!$campaign || $campaign['user_id'] !== $user['id']) {
            return $this->errorResponse('Access denied', 403, 'FORBIDDEN');
        }

        // Prepare entity data
        $entityData = [
            'campaign_id' => (int) $data['campaign_id'],
            'entity_type' => $data['entity_type'],
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'entry' => $data['entry'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'parent_id' => isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            'is_private' => isset($data['is_private']) ? (int) $data['is_private'] : 0,
            'data' => isset($data['data']) ? (is_string($data['data']) ? $data['data'] : json_encode($data['data'])) : '{}'
        ];

        try {
            $entityId = $this->repository->create($entityData);

            // Fetch the created entity
            $entity = $this->repository->findById($entityId);

            return $this->successResponse($entity, null, 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create entity: ' . $e->getMessage(), 500, 'CREATE_FAILED');
        }
    }

    /**
     * Update an existing entity
     *
     * PUT /api/v1/entities/{id}
     *
     * @param Request $request HTTP request
     * @param int $id Entity ID
     * @return Response JSON response with updated entity
     */
    public function update(Request $request, int $id): Response
    {
        $user = $this->requireApiAuth($request);

        // Check if entity exists
        $entity = $this->repository->findById($id);

        if (!$entity) {
            return $this->errorResponse('Entity not found', 404, 'NOT_FOUND');
        }

        // Verify campaign ownership
        $campaign = $this->campaignRepository->findById($entity['campaign_id']);
        if (!$campaign || $campaign['user_id'] !== $user['id']) {
            return $this->errorResponse('Access denied', 403, 'FORBIDDEN');
        }

        // Get input data
        $data = $request->isJson() ? $request->getJson() : $request->getPost();

        // Prepare update data (only include provided fields)
        $updateData = [];

        $allowedFields = ['name', 'type', 'entry', 'image_path', 'parent_id', 'is_private', 'data'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'data' && !is_string($data[$field])) {
                    $updateData[$field] = json_encode($data[$field]);
                } elseif ($field === 'parent_id' || $field === 'is_private') {
                    $updateData[$field] = $data[$field] !== null ? (int) $data[$field] : null;
                } else {
                    $updateData[$field] = $data[$field];
                }
            }
        }

        if (empty($updateData)) {
            return $this->errorResponse('No valid fields to update', 400, 'NO_FIELDS');
        }

        try {
            $this->repository->update($id, $updateData);

            // Fetch the updated entity
            $updatedEntity = $this->repository->findById($id);

            return $this->successResponse($updatedEntity);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update entity: ' . $e->getMessage(), 500, 'UPDATE_FAILED');
        }
    }

    /**
     * Delete an entity
     *
     * DELETE /api/v1/entities/{id}
     *
     * @param Request $request HTTP request
     * @param int $id Entity ID
     * @return Response JSON response confirming deletion
     */
    public function destroy(Request $request, int $id): Response
    {
        $user = $this->requireApiAuth($request);

        // Check if entity exists
        $entity = $this->repository->findById($id);

        if (!$entity) {
            return $this->errorResponse('Entity not found', 404, 'NOT_FOUND');
        }

        // Verify campaign ownership
        $campaign = $this->campaignRepository->findById($entity['campaign_id']);
        if (!$campaign || $campaign['user_id'] !== $user['id']) {
            return $this->errorResponse('Access denied', 403, 'FORBIDDEN');
        }

        try {
            $this->repository->delete($id);

            return $this->successResponse([
                'message' => 'Entity deleted successfully',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete entity: ' . $e->getMessage(), 500, 'DELETE_FAILED');
        }
    }
}
