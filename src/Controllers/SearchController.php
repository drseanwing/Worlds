<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use Worlds\Config\EntityTypes;
use Worlds\Repositories\EntityRepository;

/**
 * SearchController class
 *
 * Handles global search functionality across entities using FTS5 full-text search.
 * Supports filtering by entity type and pagination of results.
 */
class SearchController
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
     * @var int Items per page for search results
     */
    private const PER_PAGE = 20;

    /**
     * Constructor
     *
     * Initializes repository and view instances.
     */
    public function __construct()
    {
        $this->repository = new EntityRepository();
        $this->view = new View();

        // Load entity type schemas
        EntityTypes::load();
    }

    /**
     * Display search results
     *
     * GET /search?q={query}&type={optional_type}&page={page}
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

        // Get search query from query params
        $query = trim($request->getQueryParam('q', ''));

        // Get optional entity type filter
        $typeFilter = $request->getQueryParam('type', '');

        // Get page number
        $page = max(1, (int) $request->getQueryParam('page', 1));

        // Initialize results
        $results = [];
        $totalResults = 0;
        $highlightedQuery = htmlspecialchars($query);

        // Perform search if query is not empty
        if (!empty($query)) {
            // Search entities
            $searchResults = $this->repository->search(
                $query,
                $campaignId,
                $page,
                self::PER_PAGE
            );

            // Filter by type if specified
            if (!empty($typeFilter) && EntityTypes::typeExists($typeFilter)) {
                $searchResults = array_filter($searchResults, function ($entity) use ($typeFilter) {
                    return $entity['entity_type'] === $typeFilter;
                });
            }

            $results = $searchResults;

            // Get total count for pagination (simplified - actual count would need separate query)
            $totalResults = count($results);
        }

        // Calculate pagination
        $totalPages = $totalResults > 0 ? (int) ceil($totalResults / self::PER_PAGE) : 0;

        // Get all available entity types for filter dropdown
        $availableTypes = EntityTypes::getAll();

        // Render search results view
        $html = $this->view->render('search/results', [
            'query' => $query,
            'typeFilter' => $typeFilter,
            'results' => $results,
            'totalResults' => $totalResults,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => self::PER_PAGE,
            'availableTypes' => $availableTypes,
            'highlightedQuery' => $highlightedQuery
        ]);

        return Response::html($html);
    }
}
