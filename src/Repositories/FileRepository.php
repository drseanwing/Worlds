<?php

namespace Worlds\Repositories;

use PDO;
use PDOException;
use Worlds\Config\Database;
use Worlds\Config\Config;

/**
 * File Repository
 *
 * Manages database operations for file uploads including CRUD operations,
 * file validation, thumbnail generation, and file storage management.
 */
class FileRepository
{
    /**
     * @var PDO Database connection instance
     */
    private PDO $pdo;

    /**
     * @var string Upload directory path
     */
    private string $uploadDir;

    /**
     * @var int Maximum file size in bytes (10MB default)
     */
    private const MAX_FILE_SIZE = 10485760;

    /**
     * @var array<string> Allowed MIME types for uploads
     */
    private const ALLOWED_TYPES = [
        // Images
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'text/markdown',
    ];

    /**
     * @var array<string> Image MIME types for thumbnail generation
     */
    private const IMAGE_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Constructor
     *
     * Initializes repository with database connection and upload directory.
     */
    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->uploadDir = Config::get('UPLOAD_DIR', 'data/uploads');

        // Ensure upload directory exists and is writable
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Find file by ID
     *
     * @param int $id File ID
     * @return array|null File data array or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM files WHERE id = ?
        ');
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Find all files for an entity
     *
     * @param int $entityId Entity ID
     * @return array<array> Array of file records
     */
    public function findByEntity(int $entityId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM files
            WHERE entity_id = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$entityId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new file record
     *
     * @param array<string, mixed> $data File data (entity_id, filename, path, mime_type, size, etc.)
     * @return int Created file ID
     * @throws PDOException If database operation fails
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO files (entity_id, filename, path, mime_type, size, description, is_private)
            VALUES (:entity_id, :filename, :path, :mime_type, :size, :description, :is_private)
        ');

        $stmt->execute([
            'entity_id' => $data['entity_id'],
            'filename' => $data['filename'],
            'path' => $data['path'],
            'mime_type' => $data['mime_type'],
            'size' => $data['size'],
            'description' => $data['description'] ?? null,
            'is_private' => $data['is_private'] ?? 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Delete a file record and physical file
     *
     * @param int $id File ID
     * @return bool True if deleted successfully
     */
    public function delete(int $id): bool
    {
        // First, retrieve the file record to get the path
        $file = $this->findById($id);
        if (!$file) {
            return false;
        }

        // Delete the physical file
        $fullPath = $this->getFullPath($file['path']);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete thumbnail if it exists
        $thumbPath = $this->getThumbnailPath($file['path']);
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        // Delete the database record
        $stmt = $this->pdo->prepare('DELETE FROM files WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Handle file upload
     *
     * @param array<string, mixed> $file Uploaded file from $_FILES
     * @param int $entityId Entity ID to associate with
     * @param string|null $description Optional file description
     * @return int Created file ID
     * @throws \RuntimeException If upload or validation fails
     */
    public function upload(array $file, int $entityId, ?string $description = null): int
    {
        // Validate upload error
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException($this->getUploadErrorMessage($file['error'] ?? UPLOAD_ERR_NO_FILE));
        }

        // Validate file type
        $mimeType = $file['type'] ?? mime_content_type($file['tmp_name']);
        if (!$this->validateType($mimeType)) {
            throw new \RuntimeException('File type not allowed. Allowed types: images (jpg, png, gif, webp, svg) and documents (pdf, doc, docx, txt, md)');
        }

        // Validate file size
        $size = $file['size'] ?? filesize($file['tmp_name']);
        if (!$this->validateSize($size)) {
            throw new \RuntimeException('File size exceeds maximum allowed size of ' . $this->formatBytes(self::MAX_FILE_SIZE));
        }

        // Generate unique filename and storage path
        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = $this->generateUniqueFilename($extension);

        // Organize uploads by entity ID
        $relativePath = $entityId . '/' . $filename;
        $fullPath = $this->getFullPath($relativePath);

        // Ensure entity directory exists
        $entityDir = dirname($fullPath);
        if (!is_dir($entityDir)) {
            mkdir($entityDir, 0755, true);
        }

        // Move uploaded file to destination
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new \RuntimeException('Failed to move uploaded file');
        }

        // Generate thumbnail for images
        if ($this->isImageType($mimeType)) {
            try {
                $this->generateThumbnail($fullPath);
            } catch (\Exception $e) {
                // Thumbnail generation is non-critical, log and continue
                error_log('Failed to generate thumbnail: ' . $e->getMessage());
            }
        }

        // Create database record
        $fileId = $this->create([
            'entity_id' => $entityId,
            'filename' => $originalName,
            'path' => $relativePath,
            'mime_type' => $mimeType,
            'size' => $size,
            'description' => $description,
        ]);

        return $fileId;
    }

    /**
     * Generate a thumbnail for an image file
     *
     * @param string $filePath Full path to the image file
     * @return bool True if thumbnail created successfully
     */
    public function generateThumbnail(string $filePath): bool
    {
        // Check if GD library is available
        if (!function_exists('imagecreatefromjpeg')) {
            return false;
        }

        // Determine image type
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }

        $mimeType = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Skip SVG (vector format, no thumbnail needed)
        if ($mimeType === 'image/svg+xml') {
            return false;
        }

        // Create image resource from file
        $source = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($filePath),
            'image/png' => imagecreatefrompng($filePath),
            'image/gif' => imagecreatefromgif($filePath),
            'image/webp' => imagecreatefromwebp($filePath),
            default => false,
        };

        if ($source === false) {
            return false;
        }

        // Calculate thumbnail dimensions (max 200px on longest side, maintain aspect ratio)
        $maxSize = 200;
        if ($width > $height) {
            $thumbWidth = $maxSize;
            $thumbHeight = (int) ($height * ($maxSize / $width));
        } else {
            $thumbHeight = $maxSize;
            $thumbWidth = (int) ($width * ($maxSize / $height));
        }

        // Create thumbnail image
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        if ($thumb === false) {
            imagedestroy($source);
            return false;
        }

        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        // Resize image
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        // Save thumbnail
        $thumbPath = $this->getThumbnailPath($filePath);
        $thumbDir = dirname($thumbPath);
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $success = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagejpeg($thumb, $thumbPath, 85),
            'image/png' => imagepng($thumb, $thumbPath, 8),
            'image/gif' => imagegif($thumb, $thumbPath),
            'image/webp' => imagewebp($thumb, $thumbPath, 85),
            default => false,
        };

        // Clean up
        imagedestroy($source);
        imagedestroy($thumb);

        return $success;
    }

    /**
     * Validate file MIME type
     *
     * @param string $mimeType MIME type to validate
     * @return bool True if type is allowed
     */
    public function validateType(string $mimeType): bool
    {
        return in_array($mimeType, self::ALLOWED_TYPES, true);
    }

    /**
     * Validate file size
     *
     * @param int $size File size in bytes
     * @return bool True if size is within limit
     */
    public function validateSize(int $size): bool
    {
        return $size > 0 && $size <= self::MAX_FILE_SIZE;
    }

    /**
     * Check if MIME type is an image
     *
     * @param string $mimeType MIME type to check
     * @return bool True if image type
     */
    private function isImageType(string $mimeType): bool
    {
        return in_array($mimeType, self::IMAGE_TYPES, true);
    }

    /**
     * Generate a unique filename to prevent overwrites
     *
     * @param string $extension File extension
     * @return string Unique filename
     */
    private function generateUniqueFilename(string $extension): string
    {
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get full filesystem path for a relative file path
     *
     * @param string $relativePath Relative path from uploads directory
     * @return string Full filesystem path
     */
    private function getFullPath(string $relativePath): string
    {
        return rtrim($this->uploadDir, '/') . '/' . ltrim($relativePath, '/');
    }

    /**
     * Get thumbnail path for a file
     *
     * @param string $filePath Original file path (full or relative)
     * @return string Thumbnail path
     */
    private function getThumbnailPath(string $filePath): string
    {
        $pathInfo = pathinfo($filePath);
        return $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
    }

    /**
     * Get user-friendly error message for upload error code
     *
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by PHP extension',
            default => 'Unknown upload error',
        };
    }

    /**
     * Format bytes into human-readable size
     *
     * @param int $bytes Size in bytes
     * @return string Formatted size (e.g., "10.5 MB")
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get public URL for a file
     *
     * @param int $fileId File ID
     * @return string File download URL
     */
    public function getFileUrl(int $fileId): string
    {
        return '/files/' . $fileId;
    }

    /**
     * Get thumbnail URL for a file
     *
     * @param int $fileId File ID
     * @return string|null Thumbnail URL or null if not available
     */
    public function getThumbnailUrl(int $fileId): ?string
    {
        $file = $this->findById($fileId);
        if (!$file || !$this->isImageType($file['mime_type'])) {
            return null;
        }

        $thumbPath = $this->getThumbnailPath($this->getFullPath($file['path']));
        if (!file_exists($thumbPath)) {
            return null;
        }

        return '/files/' . $fileId . '/thumb';
    }
}
