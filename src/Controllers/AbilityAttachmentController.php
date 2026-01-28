<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Repositories\AbilityAttachmentRepository;
use Worlds\Repositories\EntityRepository;

/**
 * Ability Attachment Controller
 *
 * Handles HTTP requests for entity-ability associations including
 * listing, attaching, updating, and detaching abilities.
 */
class AbilityAttachmentController
{
    /**
     * @var AbilityAttachmentRepository Ability attachment repository instance
     */
    private AbilityAttachmentRepository $repository;

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
        $this->repository = new AbilityAttachmentRepository();
        $this->entityRepository = new EntityRepository();
    }

    /**
     * List all abilities attached to an entity
     *
     * GET /api/entities/{id}/abilities
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response HTTP response with JSON data
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

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Fetch abilities
        $abilities = $this->repository->findByEntity($entityId);

        return Response::json([
            'success' => true,
            'abilities' => $abilities
        ]);
    }

    /**
     * Attach an ability to an entity
     *
     * POST /api/entities/{id}/abilities
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response HTTP response with JSON data
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

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract input data
        $abilityEntityId = (int) $request->getPostParam('ability_entity_id');
        $chargesUsed = $request->getPostParam('charges_used');
        $notes = $request->getPostParam('notes');

        // Validate ability entity exists
        $abilityEntity = $this->entityRepository->findById($abilityEntityId);

        if ($abilityEntity === null) {
            return Response::json(['error' => 'Ability not found'], 404);
        }

        // Verify ability entity is of type 'ability'
        if ($abilityEntity['entity_type'] !== 'ability') {
            return Response::json(['error' => 'Referenced entity is not an ability'], 400);
        }

        // Verify ability belongs to same campaign
        if ($abilityEntity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Ability not in this campaign'], 403);
        }

        try {
            // Attach ability
            $id = $this->repository->attach(
                $entityId,
                $abilityEntityId,
                $chargesUsed !== null ? (int) $chargesUsed : 0,
                $notes
            );

            return Response::json([
                'success' => true,
                'message' => 'Ability attached successfully',
                'id' => $id
            ], 201);

        } catch (\PDOException $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                return Response::json(['error' => 'Ability already attached to this entity'], 409);
            }

            return Response::json(['error' => 'Failed to attach ability: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update an ability attachment
     *
     * PUT /api/ability-attachments/{id}
     *
     * @param Request $request Request instance
     * @param int $id Attachment ID
     * @return Response HTTP response with JSON data
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Find attachment
        $attachment = $this->repository->findById($id);

        if ($attachment === null) {
            return Response::json(['error' => 'Attachment not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($attachment['entity_id']);
        $campaignId = session('campaign_id');

        if ($entity === null || $entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract update data
        $updateData = [];

        if ($request->hasPostParam('charges_used')) {
            $updateData['charges_used'] = (int) $request->getPostParam('charges_used');
        }

        if ($request->hasPostParam('notes')) {
            $updateData['notes'] = $request->getPostParam('notes');
        }

        try {
            // Update attachment
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Attachment updated successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to update attachment'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json(['error' => 'Failed to update attachment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Detach an ability from an entity
     *
     * DELETE /api/ability-attachments/{id}
     *
     * @param Request $request Request instance
     * @param int $id Attachment ID
     * @return Response HTTP response with JSON data
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Find attachment
        $attachment = $this->repository->findById($id);

        if ($attachment === null) {
            return Response::json(['error' => 'Attachment not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($attachment['entity_id']);
        $campaignId = session('campaign_id');

        if ($entity === null || $entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        try {
            // Delete attachment
            $success = $this->repository->delete($id);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Ability detached successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to detach ability'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json(['error' => 'Failed to detach ability: ' . $e->getMessage()], 500);
        }
    }
}
