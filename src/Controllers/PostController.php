<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Repositories\PostRepository;
use Worlds\Repositories\EntityRepository;

/**
 * PostController class
 *
 * Handles HTTP requests for post (sub-entry) CRUD operations via AJAX endpoints.
 * All methods return JSON responses for client-side rendering.
 */
class PostController
{
    /**
     * @var PostRepository Post repository instance
     */
    private PostRepository $repository;

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
        $this->repository = new PostRepository();
        $this->entityRepository = new EntityRepository();
    }

    /**
     * Create a new post for an entity
     *
     * POST /api/entities/{entityId}/posts
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

        // Prepare post data (name is optional for posts)
        $postData = [
            'entity_id' => $entityId,
            'name' => isset($input['name']) ? trim($input['name']) : null,
            'entry' => $input['entry'] ?? null,
            'is_private' => isset($input['is_private']) ? 1 : 0,
            'position' => isset($input['position']) ? (int) $input['position'] : null
        ];

        try {
            // Create post
            $id = $this->repository->create($postData);

            // Fetch created post
            $post = $this->repository->findById($id);

            return Response::json([
                'success' => true,
                'message' => 'Post created successfully',
                'post' => $post
            ], 201);

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to create post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing post
     *
     * PUT /api/posts/{id}
     *
     * @param Request $request Request instance
     * @param int $id Post ID
     * @return Response JSON response
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch post to verify it exists
        $post = $this->repository->findById($id);

        if ($post === null) {
            return Response::json(['error' => 'Post not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($post['entity_id']);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        // Extract input data
        $input = $request->getPost();

        // Prepare update data
        $updateData = [];

        if (isset($input['name'])) {
            $updateData['name'] = trim($input['name']) !== '' ? trim($input['name']) : null;
        }

        if (array_key_exists('entry', $input)) {
            $updateData['entry'] = $input['entry'];
        }

        if (isset($input['is_private'])) {
            $updateData['is_private'] = $input['is_private'] ? 1 : 0;
        }

        if (isset($input['position'])) {
            $updateData['position'] = (int) $input['position'];
        }

        try {
            // Update post
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                // Fetch updated post
                $post = $this->repository->findById($id);

                return Response::json([
                    'success' => true,
                    'message' => 'Post updated successfully',
                    'post' => $post
                ]);
            } else {
                return Response::json(['error' => 'Failed to update post'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to update post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a post
     *
     * DELETE /api/posts/{id}
     *
     * @param Request $request Request instance
     * @param int $id Post ID
     * @return Response JSON response
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch post to verify it exists
        $post = $this->repository->findById($id);

        if ($post === null) {
            return Response::json(['error' => 'Post not found'], 404);
        }

        // Verify entity belongs to user's campaign
        $entity = $this->entityRepository->findById($post['entity_id']);

        if ($entity === null) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json(['error' => 'Access denied'], 403);
        }

        try {
            // Delete post
            $success = $this->repository->delete($id);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Post deleted successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to delete post'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to delete post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder posts for an entity
     *
     * POST /api/entities/{entityId}/posts/reorder
     *
     * Expected JSON body: { "positions": { "postId": position, ... } }
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response JSON response
     */
    public function reorder(Request $request, int $entityId): Response
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

        // Validate positions data
        if (!isset($input['positions']) || !is_array($input['positions'])) {
            return Response::json(['error' => 'Invalid positions data'], 400);
        }

        try {
            // Reorder posts
            $success = $this->repository->reorder($entityId, $input['positions']);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'Posts reordered successfully'
                ]);
            } else {
                return Response::json(['error' => 'Failed to reorder posts'], 500);
            }

        } catch (\PDOException $e) {
            return Response::json([
                'error' => 'Failed to reorder posts: ' . $e->getMessage()
            ], 500);
        }
    }
}
