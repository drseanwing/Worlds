<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use Worlds\Repositories\TagRepository;

/**
 * TagController class
 *
 * Handles HTTP requests for tag CRUD operations and entity-tag associations.
 */
class TagController
{
    /**
     * @var TagRepository Tag repository instance
     */
    private TagRepository $repository;

    /**
     * @var View View instance for rendering templates
     */
    private View $view;

    /**
     * Constructor
     *
     * Initializes repository and view instances.
     */
    public function __construct()
    {
        $this->repository = new TagRepository();
        $this->view = new View();
    }

    /**
     * Display tag list
     *
     * GET /tags
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function index(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            flash('error', 'No campaign selected');
            return redirect('/campaigns');
        }

        // Fetch tags
        $tags = $this->repository->findByCampaign($campaignId);

        // Render list view
        $html = $this->view->render('tags/index', [
            'tags' => $tags
        ]);

        return Response::html($html);
    }

    /**
     * Save new tag
     *
     * POST /tags
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function store(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            return Response::json(['error' => 'No campaign selected'], 400);
        }

        // Extract input data
        $input = $request->getPost();

        // Validate input
        $errors = $this->validateTag($input);

        if (!empty($errors)) {
            return Response::json(['errors' => $errors], 422);
        }

        // Prepare tag data
        $tagData = [
            'campaign_id' => $campaignId,
            'name' => trim($input['name']),
            'colour' => $input['colour'] ?? '#666666',
            'description' => $input['description'] ?? null
        ];

        try {
            // Create tag
            $id = $this->repository->create($tagData);
            $tag = $this->repository->findById($id);

            return Response::json([
                'success' => true,
                'tag' => $tag,
                'message' => 'Tag created successfully'
            ], 201);

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to create tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tag
     *
     * PUT /tags/{id}
     *
     * @param Request $request Request instance
     * @param int $id Tag ID
     * @return Response HTTP response
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Fetch tag to verify it exists and user has access
        $tag = $this->repository->findById($id);

        if ($tag === null) {
            return Response::json(['error' => 'Tag not found'], 404);
        }

        // Verify tag belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($tag['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract input data
        $input = $request->getPost();

        // Validate input
        $errors = $this->validateTag($input);

        if (!empty($errors)) {
            return Response::json(['errors' => $errors], 422);
        }

        // Prepare update data
        $updateData = [
            'name' => trim($input['name']),
            'colour' => $input['colour'] ?? '#666666',
            'description' => $input['description'] ?? null
        ];

        try {
            // Update tag
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                $updatedTag = $this->repository->findById($id);
                return Response::json([
                    'success' => true,
                    'tag' => $updatedTag,
                    'message' => 'Tag updated successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to update tag'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to update tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete tag
     *
     * DELETE /tags/{id}
     *
     * @param Request $request Request instance
     * @param int $id Tag ID
     * @return Response HTTP response
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Fetch tag to verify it exists and user has access
        $tag = $this->repository->findById($id);

        if ($tag === null) {
            return Response::json(['error' => 'Tag not found'], 404);
        }

        // Verify tag belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($tag['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        try {
            // Delete tag
            $success = $this->repository->delete($id);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Tag deleted successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to delete tag'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to delete tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach tags to entity
     *
     * POST /api/entities/{id}/tags
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response HTTP response
     */
    public function attach(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        // Get tag IDs from request
        $tagIds = $request->getPostParam('tag_ids', []);

        if (!is_array($tagIds) || empty($tagIds)) {
            return Response::json(['error' => 'No tags specified'], 400);
        }

        try {
            $attached = 0;
            $alreadyAttached = 0;

            foreach ($tagIds as $tagId) {
                $success = $this->repository->attachToEntity((int) $tagId, $entityId);
                if ($success) {
                    $attached++;
                } else {
                    $alreadyAttached++;
                }
            }

            return Response::json([
                'success' => true,
                'attached' => $attached,
                'already_attached' => $alreadyAttached,
                'message' => "Attached {$attached} tag(s) to entity"
            ]);

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to attach tags: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detach tag from entity
     *
     * DELETE /api/entities/{id}/tags/{tagId}
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @param int $tagId Tag ID
     * @return Response HTTP response
     */
    public function detach(Request $request, int $entityId, int $tagId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        try {
            // Detach tag from entity
            $this->repository->detachFromEntity($tagId, $entityId);

            return Response::json([
                'success' => true,
                'message' => 'Tag detached successfully'
            ]);

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to detach tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate tag data
     *
     * @param array<string, mixed> $input Input data from request
     * @return array<string> List of validation error messages
     */
    private function validateTag(array $input): array
    {
        $errors = [];

        // Validate name (required, max 100 chars)
        if (empty($input['name']) || trim($input['name']) === '') {
            $errors[] = 'Name is required';
        } elseif (strlen($input['name']) > 100) {
            $errors[] = 'Name must not exceed 100 characters';
        }

        // Validate colour (optional, must be valid hex code if provided)
        if (isset($input['colour']) && !empty($input['colour'])) {
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $input['colour'])) {
                $errors[] = 'Colour must be a valid hex code (e.g., #3498db)';
            }
        }

        return $errors;
    }
}
