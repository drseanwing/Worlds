<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Repositories\FileRepository;
use Worlds\Repositories\EntityRepository;

/**
 * FileController class
 *
 * Handles HTTP requests for file uploads including uploading,
 * downloading, and deleting files associated with entities.
 */
class FileController
{
    /**
     * @var FileRepository File repository instance
     */
    private FileRepository $repository;

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
        $this->repository = new FileRepository();
        $this->entityRepository = new EntityRepository();
    }

    /**
     * Upload a file for an entity
     *
     * POST /api/entities/{id}/files
     *
     * @param Request $request HTTP request
     * @return Response JSON response with file ID or error
     */
    public function store(Request $request): Response
    {
        // Require authentication
        if (!Auth::check()) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        // Get entity ID from route parameters
        $entityId = (int) $request->input('entity_id');
        if ($entityId <= 0) {
            return Response::json(['error' => 'Invalid entity ID'], 400);
        }

        // Verify entity exists and user has access
        $entity = $this->entityRepository->findById($entityId);
        if (!$entity) {
            return Response::json(['error' => 'Entity not found'], 404);
        }

        // Check if user has access to this entity's campaign
        $activeCampaignId = Auth::getActiveCampaignId();
        if ($entity['campaign_id'] !== $activeCampaignId) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        // Check if file was uploaded
        if (!$request->hasFile('file')) {
            return Response::json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->getFile('file');
        $description = $request->input('description');

        try {
            // Upload file and create database record
            $fileId = $this->repository->upload($file, $entityId, $description);

            // Return success response with file info
            $fileRecord = $this->repository->findById($fileId);

            return Response::json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => [
                    'id' => $fileRecord['id'],
                    'filename' => $fileRecord['filename'],
                    'mime_type' => $fileRecord['mime_type'],
                    'size' => $fileRecord['size'],
                    'url' => $this->repository->getFileUrl($fileId),
                    'thumbnail_url' => $this->repository->getThumbnailUrl($fileId),
                    'created_at' => $fileRecord['created_at'],
                ],
            ], 201);
        } catch (\RuntimeException $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            error_log('File upload error: ' . $e->getMessage());
            return Response::json(['error' => 'Failed to upload file'], 500);
        }
    }

    /**
     * Delete a file
     *
     * DELETE /api/files/{id}
     *
     * @param Request $request HTTP request
     * @return Response JSON response with success or error
     */
    public function destroy(Request $request): Response
    {
        // Require authentication
        if (!Auth::check()) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        // Get file ID from route parameters
        $fileId = (int) $request->input('file_id');
        if ($fileId <= 0) {
            return Response::json(['error' => 'Invalid file ID'], 400);
        }

        // Get file record
        $file = $this->repository->findById($fileId);
        if (!$file) {
            return Response::json(['error' => 'File not found'], 404);
        }

        // Verify user has access to the file's entity
        $entity = $this->entityRepository->findById($file['entity_id']);
        if (!$entity) {
            return Response::json(['error' => 'Associated entity not found'], 404);
        }

        $activeCampaignId = Auth::getActiveCampaignId();
        if ($entity['campaign_id'] !== $activeCampaignId) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        // Delete file
        try {
            $success = $this->repository->delete($fileId);

            if ($success) {
                return Response::json([
                    'success' => true,
                    'message' => 'File deleted successfully',
                ]);
            } else {
                return Response::json(['error' => 'Failed to delete file'], 500);
            }
        } catch (\Exception $e) {
            error_log('File deletion error: ' . $e->getMessage());
            return Response::json(['error' => 'Failed to delete file'], 500);
        }
    }

    /**
     * Download a file
     *
     * GET /files/{id}
     *
     * @param Request $request HTTP request
     * @return Response File download response
     */
    public function download(Request $request): Response
    {
        // Authentication check (optional - files could be public)
        // For now, require authentication
        if (!Auth::check()) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        // Get file ID from route parameters
        $fileId = (int) $request->input('file_id');
        if ($fileId <= 0) {
            return Response::error(400, 'Invalid file ID');
        }

        // Get file record
        $file = $this->repository->findById($fileId);
        if (!$file) {
            return Response::error(404, 'File not found');
        }

        // Verify user has access to the file's entity
        $entity = $this->entityRepository->findById($file['entity_id']);
        if (!$entity) {
            return Response::error(404, 'Associated entity not found');
        }

        $activeCampaignId = Auth::getActiveCampaignId();
        if ($entity['campaign_id'] !== $activeCampaignId) {
            return Response::error(403, 'Forbidden');
        }

        // Check if requesting thumbnail
        $isThumbnail = $request->getQueryParam('thumb', false) ||
                       str_ends_with($request->getPath(), '/thumb');

        try {
            // Get upload directory from config
            $uploadDir = \Worlds\Config\Config::get('UPLOAD_DIR', 'data/uploads');
            $filePath = rtrim($uploadDir, '/') . '/' . ltrim($file['path'], '/');

            if ($isThumbnail) {
                // Serve thumbnail
                $thumbPath = $this->getThumbnailPath($filePath);
                if (!file_exists($thumbPath)) {
                    return Response::error(404, 'Thumbnail not found');
                }
                return Response::file($thumbPath, $file['mime_type']);
            } else {
                // Serve original file
                if (!file_exists($filePath)) {
                    return Response::error(404, 'File not found on disk');
                }
                return Response::download($filePath, $file['filename'], $file['mime_type']);
            }
        } catch (\RuntimeException $e) {
            error_log('File download error: ' . $e->getMessage());
            return Response::error(500, 'Failed to serve file');
        }
    }

    /**
     * Get thumbnail path for a file
     *
     * @param string $filePath Original file path
     * @return string Thumbnail path
     */
    private function getThumbnailPath(string $filePath): string
    {
        $pathInfo = pathinfo($filePath);
        return $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
    }
}
