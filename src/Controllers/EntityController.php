<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use Worlds\Config\EntityTypes;
use Worlds\Repositories\EntityRepository;

/**
 * EntityController class
 *
 * Handles HTTP requests for entity CRUD operations including listing,
 * viewing, creating, updating, and deleting entities with validation.
 */
class EntityController
{
    /**
     * @var EntityRepository Entity repository instance
     */
    private EntityRepository $repository;

    /**
     * @var View View instance for rendering templates
     */
    private View $view;

    /**
     * @var int Items per page for pagination
     */
    private const PER_PAGE = 50;

    /**
     * Constructor
     *
     * Initializes repository and view instances, ensures EntityTypes are loaded.
     */
    public function __construct()
    {
        $this->repository = new EntityRepository();
        $this->view = new View();

        // Load entity type schemas
        EntityTypes::load();
    }

    /**
     * Display entity list with pagination
     *
     * GET /entities/{type}?page=1
     *
     * @param Request $request Request instance
     * @param string $type Entity type (character, location, etc.)
     * @return Response HTTP response
     */
    public function index(Request $request, string $type): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            flash('error', 'No campaign selected');
            return redirect('/campaigns');
        }

        // Get page number from query params
        $page = max(1, (int) $request->getQueryParam('page', 1));

        // Fetch entities
        $entities = $this->repository->findByType($type, $campaignId, $page, self::PER_PAGE);

        // Get pagination info
        $pagination = $this->repository->getPaginationInfo($campaignId, $type, null, self::PER_PAGE);

        // Render list view
        $html = $this->view->render('entities/index', [
            'type' => $type,
            'entities' => $entities,
            'pagination' => $pagination,
            'currentPage' => $page,
            'entityTypeLabel' => EntityTypes::getLabel($type),
            'entityTypePluralLabel' => EntityTypes::getPluralLabel($type)
        ]);

        return Response::html($html);
    }

    /**
     * Display single entity
     *
     * GET /entities/{type}/{id}
     *
     * @param Request $request Request instance
     * @param string $type Entity type
     * @param int $id Entity ID
     * @return Response HTTP response
     */
    public function show(Request $request, string $type, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Fetch entity
        $entity = $this->repository->findById($id);

        // Check if entity exists
        if ($entity === null) {
            flash('error', 'Entity not found');
            return Response::html(
                $this->view->render('errors/404', ['message' => 'Entity not found']),
                404
            );
        }

        // Verify entity type matches
        if ($entity['entity_type'] !== $type) {
            flash('error', 'Entity type mismatch');
            return redirect("/entities/{$type}");
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Fetch related entities based on entity type
        $relatedData = $this->fetchRelatedEntities($entity);

        // Render show view
        $html = $this->view->render('entities/show', [
            'entity' => $entity,
            'type' => $type,
            'entityTypeLabel' => EntityTypes::getLabel($type),
            'childEntities' => $relatedData['children'] ?? [],
            'relatedCharacters' => $relatedData['characters'] ?? [],
            'relatedLocations' => $relatedData['locations'] ?? [],
            'relatedOrganisations' => $relatedData['organisations'] ?? [],
            'relatedFamilies' => $relatedData['families'] ?? [],
        ]);

        return Response::html($html);
    }

    /**
     * Display creation form
     *
     * GET /entities/{type}/create
     *
     * @param Request $request Request instance
     * @param string $type Entity type
     * @return Response HTTP response
     */
    public function create(Request $request, string $type): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            flash('error', 'No campaign selected');
            return redirect('/campaigns');
        }

        // Load entity type schema
        $schema = EntityTypes::getSchema($type);
        $fieldInfo = EntityTypes::getFieldInfo($type);
        $defaults = EntityTypes::getDefaults($type);

        // Render create form
        $html = $this->view->render('entities/create', [
            'type' => $type,
            'schema' => $schema,
            'fieldInfo' => $fieldInfo,
            'defaults' => $defaults,
            'entityTypeLabel' => EntityTypes::getLabel($type),
            'errors' => session('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input and errors after rendering
        clear_old_input();
        unset($_SESSION['errors']);

        return Response::html($html);
    }

    /**
     * Save new entity
     *
     * POST /entities/{type}
     *
     * @param Request $request Request instance
     * @param string $type Entity type
     * @return Response HTTP response
     */
    public function store(Request $request, string $type): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            flash('error', 'Invalid CSRF token');
            return redirect("/entities/{$type}/create");
        }

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            flash('error', 'No campaign selected');
            return redirect('/campaigns');
        }

        // Extract input data
        $input = $request->getPost();

        // Validate input
        $errors = $this->validateEntity($type, $input);

        if (!empty($errors)) {
            // Store errors and old input for next request
            flash_old_input($input);
            $_SESSION['errors'] = $errors;
            flash('error', 'Validation failed. Please check the form.');
            return redirect("/entities/{$type}/create");
        }

        // Prepare entity data
        $entityData = [
            'campaign_id' => $campaignId,
            'entity_type' => $type,
            'name' => trim($input['name']),
            'type' => $input['type'] ?? null,
            'entry' => $input['entry'] ?? null,
            'image_path' => $input['image_path'] ?? null,
            'parent_id' => !empty($input['parent_id']) ? (int) $input['parent_id'] : null,
            'is_private' => isset($input['is_private']) ? 1 : 0,
            'data' => $this->extractTypeSpecificData($input)
        ];

        try {
            // Create entity
            $id = $this->repository->create($entityData);

            flash('success', EntityTypes::getLabel($type) . ' created successfully');
            return redirect("/entities/{$type}/{$id}");

        } catch (\PDOException $e) {
            flash('error', 'Failed to create entity: ' . $e->getMessage());
            flash_old_input($input);
            return redirect("/entities/{$type}/create");
        }
    }

    /**
     * Display edit form
     *
     * GET /entities/{type}/{id}/edit
     *
     * @param Request $request Request instance
     * @param string $type Entity type
     * @param int $id Entity ID
     * @return Response HTTP response
     */
    public function edit(Request $request, string $type, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Fetch entity
        $entity = $this->repository->findById($id);

        // Check if entity exists
        if ($entity === null) {
            flash('error', 'Entity not found');
            return redirect("/entities/{$type}");
        }

        // Verify entity type matches
        if ($entity['entity_type'] !== $type) {
            flash('error', 'Entity type mismatch');
            return redirect("/entities/{$type}");
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Load entity type schema
        $schema = EntityTypes::getSchema($type);
        $fieldInfo = EntityTypes::getFieldInfo($type);

        // Merge entity data with type-specific data for form population
        $formData = array_merge(
            $entity,
            $entity['data'] ?? []
        );

        // Render edit form
        $html = $this->view->render('entities/edit', [
            'entity' => $entity,
            'type' => $type,
            'schema' => $schema,
            'fieldInfo' => $fieldInfo,
            'formData' => $formData,
            'entityTypeLabel' => EntityTypes::getLabel($type),
            'errors' => session('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input and errors after rendering
        clear_old_input();
        unset($_SESSION['errors']);

        return Response::html($html);
    }

    /**
     * Save entity changes
     *
     * PUT /entities/{type}/{id}
     *
     * @param Request $request Request instance
     * @param string $type Entity type
     * @param int $id Entity ID
     * @return Response HTTP response
     */
    public function update(Request $request, string $type, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            flash('error', 'Invalid CSRF token');
            return redirect("/entities/{$type}/{$id}/edit");
        }

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Fetch entity to verify it exists and user has access
        $entity = $this->repository->findById($id);

        if ($entity === null) {
            flash('error', 'Entity not found');
            return redirect("/entities/{$type}");
        }

        // Verify entity type matches
        if ($entity['entity_type'] !== $type) {
            flash('error', 'Entity type mismatch');
            return redirect("/entities/{$type}");
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Extract input data
        $input = $request->getPost();

        // Validate input
        $errors = $this->validateEntity($type, $input);

        if (!empty($errors)) {
            // Store errors and old input for next request
            flash_old_input($input);
            $_SESSION['errors'] = $errors;
            flash('error', 'Validation failed. Please check the form.');
            return redirect("/entities/{$type}/{$id}/edit");
        }

        // Prepare update data
        $updateData = [
            'name' => trim($input['name']),
            'type' => $input['type'] ?? null,
            'entry' => $input['entry'] ?? null,
            'image_path' => $input['image_path'] ?? null,
            'parent_id' => !empty($input['parent_id']) ? (int) $input['parent_id'] : null,
            'is_private' => isset($input['is_private']) ? 1 : 0,
            'data' => $this->extractTypeSpecificData($input)
        ];

        try {
            // Update entity
            $success = $this->repository->update($id, $updateData);

            if ($success) {
                flash('success', EntityTypes::getLabel($type) . ' updated successfully');
                return redirect("/entities/{$type}/{$id}");
            } else {
                flash('error', 'Failed to update entity');
                flash_old_input($input);
                return redirect("/entities/{$type}/{$id}/edit");
            }

        } catch (\PDOException $e) {
            flash('error', 'Failed to update entity: ' . $e->getMessage());
            flash_old_input($input);
            return redirect("/entities/{$type}/{$id}/edit");
        }
    }

    /**
     * Delete entity
     *
     * DELETE /entities/{type}/{id}
     *
     * @param Request $request Request instance
     * @param string $type Entity type
     * @param int $id Entity ID
     * @return Response HTTP response
     */
    public function destroy(Request $request, string $type, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        if (!verify_csrf_token($request->getPostParam('_csrf_token'))) {
            flash('error', 'Invalid CSRF token');
            return redirect("/entities/{$type}");
        }

        // Validate entity type
        if (!EntityTypes::typeExists($type)) {
            flash('error', 'Invalid entity type');
            return redirect('/dashboard');
        }

        // Fetch entity to verify it exists and user has access
        $entity = $this->repository->findById($id);

        if ($entity === null) {
            flash('error', 'Entity not found');
            return redirect("/entities/{$type}");
        }

        // Verify entity type matches
        if ($entity['entity_type'] !== $type) {
            flash('error', 'Entity type mismatch');
            return redirect("/entities/{$type}");
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        try {
            // Delete entity
            $success = $this->repository->delete($id);

            if ($success) {
                flash('success', EntityTypes::getLabel($type) . ' deleted successfully');
            } else {
                flash('error', 'Failed to delete entity');
            }

            return redirect("/entities/{$type}");

        } catch (\PDOException $e) {
            flash('error', 'Failed to delete entity: ' . $e->getMessage());
            return redirect("/entities/{$type}");
        }
    }

    /**
     * Validate entity data against schema
     *
     * @param string $type Entity type
     * @param array<string, mixed> $input Input data from request
     * @return array<string> List of validation error messages
     */
    private function validateEntity(string $type, array $input): array
    {
        $errors = [];

        // Validate name (required, max 255 chars)
        if (empty($input['name']) || trim($input['name']) === '') {
            $errors[] = 'Name is required';
        } elseif (strlen($input['name']) > 255) {
            $errors[] = 'Name must not exceed 255 characters';
        }

        // Extract type-specific data for schema validation
        $typeSpecificData = $this->extractTypeSpecificData($input);

        // Validate against entity type schema
        $schemaErrors = EntityTypes::validate($type, $typeSpecificData);
        $errors = array_merge($errors, $schemaErrors);

        return $errors;
    }

    /**
     * Extract type-specific data from input
     *
     * Filters out standard entity fields and returns only custom data
     * that should be stored in the JSON data column.
     *
     * @param array<string, mixed> $input Request input data
     * @return array<string, mixed> Type-specific data for JSON storage
     */
    private function extractTypeSpecificData(array $input): array
    {
        // Standard entity fields that should NOT go in the data JSON
        $standardFields = [
            'name',
            'type',
            'entry',
            'image_path',
            'parent_id',
            'is_private',
            'campaign_id',
            'entity_type',
            '_csrf_token',
            '_method'
        ];

        // Filter out standard fields
        $typeSpecificData = [];
        foreach ($input as $key => $value) {
            if (!in_array($key, $standardFields, true)) {
                $typeSpecificData[$key] = $value;
            }
        }

        return $typeSpecificData;
    }

    /**
     * Fetch related entities for display on entity detail page
     *
     * @param array<string, mixed> $entity The main entity
     * @return array<string, array> Related entities grouped by type
     */
    private function fetchRelatedEntities(array $entity): array
    {
        $result = [
            'children' => [],
            'characters' => [],
            'locations' => [],
            'organisations' => [],
            'families' => [],
        ];

        $campaignId = $entity['campaign_id'];
        $entityId = $entity['id'];
        $entityType = $entity['entity_type'];
        $data = $entity['data'] ?? [];

        // Decode JSON data if it's a string
        if (is_string($data)) {
            $data = json_decode($data, true) ?? [];
        }

        // Fetch child entities (entities where parent_id = this entity)
        $result['children'] = $this->repository->findByParent($entityId, $campaignId);

        // Type-specific related entity fetching
        switch ($entityType) {
            case 'location':
                // Characters at this location
                if (!empty($entityId)) {
                    $result['characters'] = $this->repository->findByTypeWithData(
                        'character', $campaignId, 'location_id', $entityId
                    );
                }
                break;

            case 'character':
                // Fetch linked race, location, families, organisations by their IDs
                if (!empty($data['race_id'])) {
                    $race = $this->repository->findById((int)$data['race_id']);
                    if ($race) $result['race'] = $race;
                }
                if (!empty($data['location_id'])) {
                    $location = $this->repository->findById((int)$data['location_id']);
                    if ($location) $result['location'] = $location;
                }
                if (!empty($data['family_ids']) && is_array($data['family_ids'])) {
                    foreach ($data['family_ids'] as $familyId) {
                        $family = $this->repository->findById((int)$familyId);
                        if ($family) $result['families'][] = $family;
                    }
                }
                if (!empty($data['organisation_ids']) && is_array($data['organisation_ids'])) {
                    foreach ($data['organisation_ids'] as $orgId) {
                        $org = $this->repository->findById((int)$orgId);
                        if ($org) $result['organisations'][] = $org;
                    }
                }
                break;

            case 'organisation':
                // Headquarters location
                if (!empty($data['headquarters_id'])) {
                    $hq = $this->repository->findById((int)$data['headquarters_id']);
                    if ($hq) $result['headquarters'] = $hq;
                }
                // Leader character
                if (!empty($data['leader_id'])) {
                    $leader = $this->repository->findById((int)$data['leader_id']);
                    if ($leader) $result['leader'] = $leader;
                }
                break;

            case 'family':
                // Family seat location
                if (!empty($data['seat_location_id'])) {
                    $seat = $this->repository->findById((int)$data['seat_location_id']);
                    if ($seat) $result['seat'] = $seat;
                }
                break;

            case 'quest':
                // Quest giver
                if (!empty($data['giver_id'])) {
                    $giver = $this->repository->findById((int)$data['giver_id']);
                    if ($giver) $result['giver'] = $giver;
                }
                // Quest locations
                if (!empty($data['location_ids']) && is_array($data['location_ids'])) {
                    foreach ($data['location_ids'] as $locId) {
                        $loc = $this->repository->findById((int)$locId);
                        if ($loc) $result['locations'][] = $loc;
                    }
                }
                // Quest characters
                if (!empty($data['character_ids']) && is_array($data['character_ids'])) {
                    foreach ($data['character_ids'] as $charId) {
                        $char = $this->repository->findById((int)$charId);
                        if ($char) $result['characters'][] = $char;
                    }
                }
                break;
        }

        return $result;
    }
}
