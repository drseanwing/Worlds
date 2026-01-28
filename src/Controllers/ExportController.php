<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use Worlds\Config\Database;
use Worlds\Repositories\CampaignRepository;
use Worlds\Repositories\EntityRepository;
use PDO;

/**
 * ExportController class
 *
 * Handles HTTP requests for exporting campaign data, entities,
 * and database backups in JSON and SQLite formats.
 */
class ExportController
{
    /**
     * @var CampaignRepository Campaign repository instance
     */
    private CampaignRepository $campaignRepository;

    /**
     * @var EntityRepository Entity repository instance
     */
    private EntityRepository $entityRepository;

    /**
     * @var View View instance for rendering templates
     */
    private View $view;

    /**
     * @var PDO Database connection
     */
    private PDO $pdo;

    /**
     * Constructor
     *
     * Initializes repository and view instances.
     */
    public function __construct()
    {
        $this->campaignRepository = new CampaignRepository();
        $this->entityRepository = new EntityRepository();
        $this->view = new View();
        $this->pdo = Database::getInstance();
    }

    /**
     * Display export options page
     *
     * GET /export
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function index(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        $userId = Auth::id();
        $activeCampaignId = Auth::getActiveCampaignId();

        // Fetch user's campaigns
        $campaigns = $this->campaignRepository->findByUser($userId);

        // Get recent backups
        $backups = Database::listBackups();

        // Render export options view
        $html = $this->view->render('export/index', [
            'campaigns' => $campaigns,
            'activeCampaignId' => $activeCampaignId,
            'backups' => $backups
        ]);

        return Response::html($html);
    }

    /**
     * Export entire campaign as JSON
     *
     * GET /export/campaign/{id}
     *
     * @param Request $request Request instance
     * @param int $id Campaign ID
     * @return Response HTTP response with JSON download
     */
    public function campaign(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch campaign
        $campaign = $this->campaignRepository->findById($id);

        if ($campaign === null) {
            flash('error', 'Campaign not found');
            return redirect('/export');
        }

        // Verify campaign belongs to user
        if ($campaign['user_id'] !== Auth::id()) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Gather all campaign data
        $exportData = $this->buildCampaignExport($id);

        // Generate filename
        $filename = $this->sanitizeFilename($campaign['name']) . '_export_' . date('Y-m-d_H-i-s') . '.json';

        // Return JSON download
        return $this->jsonDownload($exportData, $filename);
    }

    /**
     * Export single entity as JSON
     *
     * GET /export/entity/{id}
     *
     * @param Request $request Request instance
     * @param int $id Entity ID
     * @return Response HTTP response with JSON download
     */
    public function entity(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch entity
        $entity = $this->entityRepository->findById($id);

        if ($entity === null) {
            flash('error', 'Entity not found');
            return redirect('/dashboard');
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Build export data for single entity
        $exportData = $this->buildEntityExport($entity);

        // Generate filename
        $filename = $this->sanitizeFilename($entity['name']) . '_export_' . date('Y-m-d_H-i-s') . '.json';

        // Return JSON download
        return $this->jsonDownload($exportData, $filename);
    }

    /**
     * Download SQLite database backup
     *
     * GET /export/backup
     *
     * @param Request $request Request instance
     * @return Response HTTP response with database file download
     */
    public function backup(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Only allow admin users to backup database
        if (!Auth::isAdmin()) {
            flash('error', 'Admin access required to backup database');
            return redirect('/export');
        }

        // Create database backup
        $backupPath = Database::backup();

        if ($backupPath === false) {
            flash('error', 'Failed to create database backup');
            return redirect('/export');
        }

        // Read backup file
        if (!file_exists($backupPath)) {
            flash('error', 'Backup file not found');
            return redirect('/export');
        }

        $backupContent = file_get_contents($backupPath);
        if ($backupContent === false) {
            flash('error', 'Failed to read backup file');
            return redirect('/export');
        }

        // Generate filename
        $filename = basename($backupPath);

        // Return database file download
        return Response::download($backupContent, $filename, 'application/x-sqlite3');
    }

    /**
     * Build complete campaign export data
     *
     * @param int $campaignId Campaign ID
     * @return array Export data structure
     */
    private function buildCampaignExport(int $campaignId): array
    {
        // Fetch campaign data
        $campaign = $this->campaignRepository->findById($campaignId);

        // Fetch all entities
        $stmt = $this->pdo->prepare('SELECT * FROM entities WHERE campaign_id = ? ORDER BY entity_type, name');
        $stmt->execute([$campaignId]);
        $entities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode entity data fields
        $entities = array_map(function ($entity) {
            if (isset($entity['data']) && is_string($entity['data'])) {
                $entity['data'] = json_decode($entity['data'], true) ?? [];
            }
            return $entity;
        }, $entities);

        // Fetch all relations for these entities
        $entityIds = array_column($entities, 'id');
        $relations = [];
        if (!empty($entityIds)) {
            $placeholders = implode(',', array_fill(0, count($entityIds), '?'));
            $stmt = $this->pdo->prepare("
                SELECT * FROM relations
                WHERE source_id IN ($placeholders) OR target_id IN ($placeholders)
            ");
            $stmt->execute(array_merge($entityIds, $entityIds));
            $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch all tags
        $stmt = $this->pdo->prepare('SELECT * FROM tags WHERE campaign_id = ?');
        $stmt->execute([$campaignId]);
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch entity-tag associations
        $entityTags = [];
        if (!empty($entityIds)) {
            $placeholders = implode(',', array_fill(0, count($entityIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM entity_tags WHERE entity_id IN ($placeholders)");
            $stmt->execute($entityIds);
            $entityTags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch all attributes
        $attributes = [];
        if (!empty($entityIds)) {
            $placeholders = implode(',', array_fill(0, count($entityIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM attributes WHERE entity_id IN ($placeholders)");
            $stmt->execute($entityIds);
            $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch all posts
        $posts = [];
        if (!empty($entityIds)) {
            $placeholders = implode(',', array_fill(0, count($entityIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE entity_id IN ($placeholders)");
            $stmt->execute($entityIds);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Build export structure
        return [
            'version' => '1.0',
            'format' => 'worlds',
            'exported_at' => date('c'),
            'campaign' => [
                'name' => $campaign['name'],
                'description' => $campaign['description'],
                'settings' => json_decode($campaign['settings'] ?? '{}', true)
            ],
            'entities' => $entities,
            'relations' => $relations,
            'tags' => $tags,
            'entity_tags' => $entityTags,
            'attributes' => $attributes,
            'posts' => $posts
        ];
    }

    /**
     * Build single entity export data
     *
     * @param array $entity Entity data
     * @return array Export data structure
     */
    private function buildEntityExport(array $entity): array
    {
        $entityId = $entity['id'];

        // Fetch relations
        $stmt = $this->pdo->prepare('
            SELECT * FROM relations
            WHERE source_id = ? OR target_id = ?
        ');
        $stmt->execute([$entityId, $entityId]);
        $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch tags
        $stmt = $this->pdo->prepare('
            SELECT t.* FROM tags t
            INNER JOIN entity_tags et ON t.id = et.tag_id
            WHERE et.entity_id = ?
        ');
        $stmt->execute([$entityId]);
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch entity-tag associations
        $stmt = $this->pdo->prepare('SELECT * FROM entity_tags WHERE entity_id = ?');
        $stmt->execute([$entityId]);
        $entityTags = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch attributes
        $stmt = $this->pdo->prepare('SELECT * FROM attributes WHERE entity_id = ?');
        $stmt->execute([$entityId]);
        $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch posts
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE entity_id = ?');
        $stmt->execute([$entityId]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build export structure
        return [
            'version' => '1.0',
            'format' => 'worlds',
            'exported_at' => date('c'),
            'entity' => $entity,
            'relations' => $relations,
            'tags' => $tags,
            'entity_tags' => $entityTags,
            'attributes' => $attributes,
            'posts' => $posts
        ];
    }

    /**
     * Sanitize filename for safe download
     *
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove special characters and replace spaces with underscores
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);
        // Remove multiple consecutive underscores
        $filename = preg_replace('/_+/', '_', $filename);
        // Trim underscores from ends
        return trim($filename, '_');
    }

    /**
     * Return JSON data as downloadable response
     *
     * @param array $data Data to export
     * @param string $filename Download filename
     * @return Response HTTP response
     */
    private function jsonDownload(array $data, string $filename): Response
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return Response::download($json, $filename, 'application/json');
    }
}
