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
 * ImportController class
 *
 * Handles HTTP requests for importing campaign data from JSON files,
 * including Kanka format compatibility and conflict resolution.
 */
class ImportController
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
     * Kanka to Worlds entity type mapping
     */
    private const KANKA_TYPE_MAP = [
        'character' => 'character',
        'location' => 'location',
        'organisation' => 'organisation',
        'organization' => 'organisation',
        'family' => 'family',
        'quest' => 'quest',
        'journal' => 'post',
        'note' => 'post',
        'event' => 'timeline',
        'timeline' => 'timeline',
        'calendar' => 'calendar',
        'race' => 'race',
        'item' => 'item',
        'creature' => 'creature',
        'ability' => 'ability',
        'tag' => 'tag'
    ];

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
     * Display import form
     *
     * GET /import
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function showForm(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        $userId = Auth::id();
        $activeCampaignId = Auth::getActiveCampaignId();

        // Fetch user's campaigns for import target selection
        $campaigns = $this->campaignRepository->findByUser($userId);

        // Render import form
        $html = $this->view->render('import/form', [
            'campaigns' => $campaigns,
            'activeCampaignId' => $activeCampaignId,
            'errors' => get_flash('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input after displaying
        clear_old_input();

        return Response::html($html);
    }

    /**
     * Preview import data and show mapping options
     *
     * POST /import/preview
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function preview(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->input('_csrf_token'))) {
            flash('errors', ['CSRF token validation failed']);
            return redirect('/import');
        }

        // Check if file was uploaded
        $files = $request->getFiles();
        if (!isset($files['import_file']) || $files['import_file']['error'] !== UPLOAD_ERR_OK) {
            flash('errors', ['No file uploaded or upload error occurred']);
            return redirect('/import');
        }

        $file = $files['import_file'];

        // Validate file type (JSON only)
        if ($file['type'] !== 'application/json' && !str_ends_with($file['name'], '.json')) {
            flash('errors', ['Only JSON files are supported']);
            return redirect('/import');
        }

        // Read file content
        $jsonContent = file_get_contents($file['tmp_name']);
        if ($jsonContent === false) {
            flash('errors', ['Failed to read uploaded file']);
            return redirect('/import');
        }

        // Parse JSON
        $importData = json_decode($jsonContent, true);
        if ($importData === null) {
            flash('errors', ['Invalid JSON format: ' . json_last_error_msg()]);
            return redirect('/import');
        }

        // Detect format (Worlds or Kanka)
        $format = $this->detectFormat($importData);
        $sourceFormat = $request->input('source_format', 'auto');

        // Parse based on format
        if ($format === 'kanka' || $sourceFormat === 'kanka') {
            $parsedData = $this->parseKankaJson($importData);
        } else {
            $parsedData = $this->parseWorldsJson($importData);
        }

        if (isset($parsedData['error'])) {
            flash('errors', [$parsedData['error']]);
            return redirect('/import');
        }

        // Store import data in session for processing
        $_SESSION['import_data'] = $parsedData;
        $_SESSION['import_format'] = $format;
        $_SESSION['import_target_campaign'] = (int) $request->input('target_campaign', 0);
        $_SESSION['import_create_new_campaign'] = (bool) $request->input('create_new_campaign', false);
        $_SESSION['import_new_campaign_name'] = $request->input('new_campaign_name', '');

        // Detect conflicts
        $conflicts = $this->detectConflicts($parsedData, $_SESSION['import_target_campaign']);

        // Render preview view
        $html = $this->view->render('import/preview', [
            'data' => $parsedData,
            'format' => $format,
            'conflicts' => $conflicts,
            'targetCampaignId' => $_SESSION['import_target_campaign'],
            'createNewCampaign' => $_SESSION['import_create_new_campaign'],
            'newCampaignName' => $_SESSION['import_new_campaign_name']
        ]);

        return Response::html($html);
    }

    /**
     * Process and execute import
     *
     * POST /import/process
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function process(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->input('_csrf_token'))) {
            flash('errors', ['CSRF token validation failed']);
            return redirect('/import');
        }

        // Retrieve import data from session
        if (!isset($_SESSION['import_data'])) {
            flash('errors', ['No import data found. Please upload a file first.']);
            return redirect('/import');
        }

        $importData = $_SESSION['import_data'];
        $createNewCampaign = $_SESSION['import_create_new_campaign'] ?? false;
        $targetCampaignId = $_SESSION['import_target_campaign'] ?? 0;
        $newCampaignName = $_SESSION['import_new_campaign_name'] ?? '';

        // Get conflict resolution choices
        $conflictResolution = $request->input('conflict_resolution', 'skip'); // skip, overwrite, keep_both

        try {
            $this->pdo->beginTransaction();

            // Create new campaign if requested
            if ($createNewCampaign) {
                $campaignName = !empty($newCampaignName) ? $newCampaignName : ($importData['campaign']['name'] ?? 'Imported Campaign');

                $targetCampaignId = $this->campaignRepository->create([
                    'name' => $campaignName,
                    'description' => $importData['campaign']['description'] ?? '',
                    'user_id' => Auth::id()
                ]);
            }

            // Verify target campaign exists and user has access
            if ($targetCampaignId === 0) {
                throw new \Exception('No target campaign specified');
            }

            $campaign = $this->campaignRepository->findById($targetCampaignId);
            if ($campaign === null || $campaign['user_id'] !== Auth::id()) {
                throw new \Exception('Invalid target campaign');
            }

            // Import data
            $stats = $this->executeImport($importData, $targetCampaignId, $conflictResolution);

            $this->pdo->commit();

            // Clear import session data
            unset($_SESSION['import_data']);
            unset($_SESSION['import_format']);
            unset($_SESSION['import_target_campaign']);
            unset($_SESSION['import_create_new_campaign']);
            unset($_SESSION['import_new_campaign_name']);

            // Set imported campaign as active
            Auth::setActiveCampaignId($targetCampaignId);

            // Flash success message with statistics
            flash('success', sprintf(
                'Import completed successfully! Imported: %d entities, %d relations, %d tags, %d attributes, %d posts',
                $stats['entities'],
                $stats['relations'],
                $stats['tags'],
                $stats['attributes'],
                $stats['posts']
            ));

            return redirect('/campaigns/' . $targetCampaignId);

        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            flash('errors', ['Import failed: ' . $e->getMessage()]);
            return redirect('/import');
        }
    }

    /**
     * Parse Kanka export JSON format
     *
     * @param array $data Raw Kanka export data
     * @return array Normalized data or error
     */
    public function parseKankaJson(array $data): array
    {
        try {
            $normalized = [
                'campaign' => [
                    'name' => $data['campaign']['name'] ?? 'Imported Kanka Campaign',
                    'description' => $data['campaign']['description'] ?? '',
                    'settings' => []
                ],
                'entities' => [],
                'relations' => [],
                'tags' => [],
                'entity_tags' => [],
                'attributes' => [],
                'posts' => []
            ];

            // Map to track old IDs to new IDs
            $idMap = [];

            // Parse entities
            if (isset($data['entities']) && is_array($data['entities'])) {
                foreach ($data['entities'] as $kankaEntity) {
                    $oldId = $kankaEntity['id'] ?? null;

                    // Map entity type
                    $kankaType = strtolower($kankaEntity['type'] ?? 'character');
                    $worldsType = self::KANKA_TYPE_MAP[$kankaType] ?? 'character';

                    $entity = [
                        'entity_type' => $worldsType,
                        'name' => $kankaEntity['name'] ?? 'Unnamed',
                        'type' => $kankaEntity['subtype'] ?? null,
                        'entry' => $kankaEntity['entry'] ?? $kankaEntity['description'] ?? null,
                        'image_path' => $kankaEntity['image'] ?? null,
                        'is_private' => (int) ($kankaEntity['is_private'] ?? 0),
                        'data' => $this->extractKankaCustomData($kankaEntity)
                    ];

                    $normalized['entities'][] = $entity;

                    if ($oldId) {
                        $idMap['entity_' . $oldId] = count($normalized['entities']) - 1;
                    }
                }
            }

            // Parse tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $kankaTag) {
                    $normalized['tags'][] = [
                        'name' => $kankaTag['name'] ?? 'Unnamed Tag',
                        'colour' => $kankaTag['colour'] ?? $kankaTag['color'] ?? null,
                        'description' => $kankaTag['description'] ?? null
                    ];
                }
            }

            // Parse relations (connections in Kanka)
            if (isset($data['relations']) && is_array($data['relations'])) {
                foreach ($data['relations'] as $kankaRelation) {
                    $normalized['relations'][] = [
                        'source_id' => $kankaRelation['source_id'] ?? null,
                        'target_id' => $kankaRelation['target_id'] ?? null,
                        'relation' => $kankaRelation['relation'] ?? 'related to',
                        'mirror_relation' => $kankaRelation['mirror_relation'] ?? null,
                        'description' => $kankaRelation['description'] ?? null,
                        'is_private' => (int) ($kankaRelation['is_private'] ?? 0)
                    ];
                }
            }

            // Parse attributes
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                foreach ($data['attributes'] as $kankaAttr) {
                    $normalized['attributes'][] = [
                        'entity_id' => $kankaAttr['entity_id'] ?? null,
                        'name' => $kankaAttr['name'] ?? 'Unnamed',
                        'value' => $kankaAttr['value'] ?? '',
                        'is_private' => (int) ($kankaAttr['is_private'] ?? 0),
                        'position' => $kankaAttr['position'] ?? 0
                    ];
                }
            }

            return $normalized;

        } catch (\Exception $e) {
            return ['error' => 'Failed to parse Kanka JSON: ' . $e->getMessage()];
        }
    }

    /**
     * Parse Worlds native JSON format
     *
     * @param array $data Raw Worlds export data
     * @return array Normalized data or error
     */
    private function parseWorldsJson(array $data): array
    {
        // Worlds format is already normalized, just validate structure
        if (!isset($data['entities']) || !is_array($data['entities'])) {
            return ['error' => 'Invalid Worlds export format: missing entities'];
        }

        return [
            'campaign' => $data['campaign'] ?? ['name' => 'Imported Campaign', 'description' => '', 'settings' => []],
            'entities' => $data['entities'],
            'relations' => $data['relations'] ?? [],
            'tags' => $data['tags'] ?? [],
            'entity_tags' => $data['entity_tags'] ?? [],
            'attributes' => $data['attributes'] ?? [],
            'posts' => $data['posts'] ?? []
        ];
    }

    /**
     * Detect import format (Worlds or Kanka)
     *
     * @param array $data Parsed JSON data
     * @return string Format identifier ('worlds' or 'kanka')
     */
    private function detectFormat(array $data): string
    {
        // Check for Worlds-specific markers
        if (isset($data['format']) && $data['format'] === 'worlds') {
            return 'worlds';
        }

        if (isset($data['version']) && isset($data['entities']) && is_array($data['entities'])) {
            // Check first entity structure
            if (!empty($data['entities'])) {
                $firstEntity = $data['entities'][0];

                // Worlds entities have 'entity_type' field
                if (isset($firstEntity['entity_type'])) {
                    return 'worlds';
                }
            }
        }

        // Assume Kanka format if not clearly Worlds
        return 'kanka';
    }

    /**
     * Extract custom data from Kanka entity
     *
     * @param array $kankaEntity Kanka entity data
     * @return array Custom data for Worlds data JSON field
     */
    private function extractKankaCustomData(array $kankaEntity): array
    {
        $data = [];

        // Extract known Kanka fields
        $knownFields = [
            'title', 'age', 'sex', 'pronouns', 'type', 'location',
            'race', 'family', 'families', 'organisations', 'organizations'
        ];

        foreach ($knownFields as $field) {
            if (isset($kankaEntity[$field]) && $kankaEntity[$field] !== null) {
                $data[$field] = $kankaEntity[$field];
            }
        }

        return $data;
    }

    /**
     * Detect conflicts with existing data
     *
     * @param array $importData Import data
     * @param int $targetCampaignId Target campaign ID
     * @return array Conflict information
     */
    private function detectConflicts(array $importData, int $targetCampaignId): array
    {
        $conflicts = [
            'entity_names' => [],
            'tag_names' => []
        ];

        if ($targetCampaignId === 0) {
            return $conflicts; // No conflicts if creating new campaign
        }

        // Check for entity name conflicts
        foreach ($importData['entities'] as $entity) {
            $stmt = $this->pdo->prepare('
                SELECT id, name FROM entities
                WHERE campaign_id = ? AND name = ? AND entity_type = ?
            ');
            $stmt->execute([
                $targetCampaignId,
                $entity['name'],
                $entity['entity_type']
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $conflicts['entity_names'][] = [
                    'name' => $entity['name'],
                    'type' => $entity['entity_type'],
                    'existing_id' => $existing['id']
                ];
            }
        }

        // Check for tag name conflicts
        foreach ($importData['tags'] as $tag) {
            $stmt = $this->pdo->prepare('
                SELECT id, name FROM tags
                WHERE campaign_id = ? AND name = ?
            ');
            $stmt->execute([$targetCampaignId, $tag['name']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $conflicts['tag_names'][] = [
                    'name' => $tag['name'],
                    'existing_id' => $existing['id']
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Execute import with conflict resolution
     *
     * @param array $importData Import data
     * @param int $targetCampaignId Target campaign ID
     * @param string $conflictResolution Conflict resolution strategy
     * @return array Import statistics
     */
    private function executeImport(array $importData, int $targetCampaignId, string $conflictResolution): array
    {
        $stats = [
            'entities' => 0,
            'relations' => 0,
            'tags' => 0,
            'attributes' => 0,
            'posts' => 0
        ];

        // Map old IDs to new IDs
        $entityIdMap = [];
        $tagIdMap = [];

        // Import tags first
        foreach ($importData['tags'] as $tag) {
            $oldTagId = $tag['id'] ?? null;

            // Check for existing tag
            $stmt = $this->pdo->prepare('SELECT id FROM tags WHERE campaign_id = ? AND name = ?');
            $stmt->execute([$targetCampaignId, $tag['name']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if ($conflictResolution === 'skip') {
                    $tagIdMap[$oldTagId] = $existing['id'];
                    continue;
                } elseif ($conflictResolution === 'keep_both') {
                    $tag['name'] = $tag['name'] . ' (imported)';
                }
                // overwrite: continue to insert new tag
            }

            $stmt = $this->pdo->prepare('
                INSERT INTO tags (campaign_id, name, colour, description)
                VALUES (?, ?, ?, ?)
            ');
            $stmt->execute([
                $targetCampaignId,
                $tag['name'],
                $tag['colour'] ?? null,
                $tag['description'] ?? null
            ]);

            $newTagId = (int) $this->pdo->lastInsertId();
            if ($oldTagId) {
                $tagIdMap[$oldTagId] = $newTagId;
            }
            $stats['tags']++;
        }

        // Import entities
        foreach ($importData['entities'] as $entity) {
            $oldEntityId = $entity['id'] ?? null;

            // Check for existing entity
            $stmt = $this->pdo->prepare('
                SELECT id FROM entities
                WHERE campaign_id = ? AND name = ? AND entity_type = ?
            ');
            $stmt->execute([
                $targetCampaignId,
                $entity['name'],
                $entity['entity_type']
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if ($conflictResolution === 'skip') {
                    $entityIdMap[$oldEntityId] = $existing['id'];
                    continue;
                } elseif ($conflictResolution === 'keep_both') {
                    $entity['name'] = $entity['name'] . ' (imported)';
                } elseif ($conflictResolution === 'overwrite') {
                    // Delete existing entity (CASCADE will handle relations)
                    $deleteStmt = $this->pdo->prepare('DELETE FROM entities WHERE id = ?');
                    $deleteStmt->execute([$existing['id']]);
                }
            }

            $newEntityId = $this->entityRepository->create([
                'campaign_id' => $targetCampaignId,
                'entity_type' => $entity['entity_type'],
                'name' => $entity['name'],
                'type' => $entity['type'] ?? null,
                'entry' => $entity['entry'] ?? null,
                'image_path' => $entity['image_path'] ?? null,
                'parent_id' => null, // Will update in second pass
                'is_private' => $entity['is_private'] ?? 0,
                'data' => $entity['data'] ?? []
            ]);

            if ($oldEntityId) {
                $entityIdMap[$oldEntityId] = $newEntityId;
            }
            $stats['entities']++;
        }

        // Update parent_id references
        foreach ($importData['entities'] as $entity) {
            if (isset($entity['parent_id']) && $entity['parent_id'] !== null) {
                $oldEntityId = $entity['id'] ?? null;
                $oldParentId = $entity['parent_id'];

                if (isset($entityIdMap[$oldEntityId]) && isset($entityIdMap[$oldParentId])) {
                    $stmt = $this->pdo->prepare('UPDATE entities SET parent_id = ? WHERE id = ?');
                    $stmt->execute([$entityIdMap[$oldParentId], $entityIdMap[$oldEntityId]]);
                }
            }
        }

        // Import relations
        foreach ($importData['relations'] as $relation) {
            $oldSourceId = $relation['source_id'];
            $oldTargetId = $relation['target_id'];

            if (!isset($entityIdMap[$oldSourceId]) || !isset($entityIdMap[$oldTargetId])) {
                continue; // Skip if entities not imported
            }

            $stmt = $this->pdo->prepare('
                INSERT INTO relations (source_id, target_id, relation, mirror_relation, description, is_private)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $entityIdMap[$oldSourceId],
                $entityIdMap[$oldTargetId],
                $relation['relation'] ?? null,
                $relation['mirror_relation'] ?? null,
                $relation['description'] ?? null,
                $relation['is_private'] ?? 0
            ]);
            $stats['relations']++;
        }

        // Import entity-tag associations
        foreach ($importData['entity_tags'] as $entityTag) {
            $oldEntityId = $entityTag['entity_id'];
            $oldTagId = $entityTag['tag_id'];

            if (!isset($entityIdMap[$oldEntityId]) || !isset($tagIdMap[$oldTagId])) {
                continue;
            }

            $stmt = $this->pdo->prepare('
                INSERT OR IGNORE INTO entity_tags (entity_id, tag_id)
                VALUES (?, ?)
            ');
            $stmt->execute([$entityIdMap[$oldEntityId], $tagIdMap[$oldTagId]]);
        }

        // Import attributes
        foreach ($importData['attributes'] as $attribute) {
            $oldEntityId = $attribute['entity_id'];

            if (!isset($entityIdMap[$oldEntityId])) {
                continue;
            }

            $stmt = $this->pdo->prepare('
                INSERT INTO attributes (entity_id, name, value, is_private, position)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $entityIdMap[$oldEntityId],
                $attribute['name'],
                $attribute['value'] ?? '',
                $attribute['is_private'] ?? 0,
                $attribute['position'] ?? 0
            ]);
            $stats['attributes']++;
        }

        // Import posts
        foreach ($importData['posts'] as $post) {
            $oldEntityId = $post['entity_id'];

            if (!isset($entityIdMap[$oldEntityId])) {
                continue;
            }

            $stmt = $this->pdo->prepare('
                INSERT INTO posts (entity_id, name, entry, is_private, position)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $entityIdMap[$oldEntityId],
                $post['name'] ?? null,
                $post['entry'] ?? null,
                $post['is_private'] ?? 0,
                $post['position'] ?? 0
            ]);
            $stats['posts']++;
        }

        return $stats;
    }
}
