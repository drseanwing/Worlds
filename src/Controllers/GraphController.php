<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use Worlds\Repositories\RelationRepository;
use Worlds\Repositories\EntityRepository;

/**
 * GraphController class
 *
 * Handles graph visualization of entity relations using vis-network.
 * Provides both single entity and campaign-wide graph views.
 */
class GraphController
{
    /**
     * @var RelationRepository Relation repository instance
     */
    private RelationRepository $relationRepository;

    /**
     * @var EntityRepository Entity repository instance
     */
    private EntityRepository $entityRepository;

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
        $this->relationRepository = new RelationRepository();
        $this->entityRepository = new EntityRepository();
        $this->view = new View();
    }

    /**
     * Display graph page for a single entity
     *
     * GET /entities/{type}/{id}/graph
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID to display graph for
     * @return Response HTTP response
     */
    public function show(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch entity
        $entity = $this->entityRepository->findById($entityId);

        // Check if entity exists
        if ($entity === null) {
            flash('error', 'Entity not found');
            return Response::html(
                $this->view->render('errors/404', ['message' => 'Entity not found']),
                404
            );
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Render entity graph view
        $html = $this->view->render('graph/entity', [
            'entity' => $entity,
            'entityId' => $entityId
        ]);

        return Response::html($html);
    }

    /**
     * Display full campaign graph
     *
     * GET /graph
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function showCampaign(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            flash('error', 'No campaign selected');
            return redirect('/campaigns');
        }

        // Render campaign graph view
        $html = $this->view->render('graph/campaign', [
            'campaignId' => $campaignId
        ]);

        return Response::html($html);
    }

    /**
     * Get graph data for a single entity (JSON API)
     *
     * GET /api/entities/{id}/graph
     *
     * @param Request $request Request instance
     * @param int $entityId Entity ID
     * @return Response JSON response with nodes and edges
     */
    public function data(Request $request, int $entityId): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Verify entity exists and user has access
        $entity = $this->entityRepository->findById($entityId);

        if ($entity === null) {
            return Response::json([
                'success' => false,
                'error' => 'Entity not found'
            ], 404);
        }

        // Verify entity belongs to user's campaign
        $campaignId = session('campaign_id');
        if ($entity['campaign_id'] !== $campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
        }

        // Get depth parameter (default: 1, max: 3)
        $depth = min(3, max(1, (int) $request->getQueryParam('depth', 1)));

        // Get graph data
        $graphData = $this->relationRepository->getGraphData($entityId, $depth);

        return Response::json([
            'success' => true,
            'data' => $graphData
        ]);
    }

    /**
     * Get graph data for entire campaign (JSON API)
     *
     * GET /graph/data
     *
     * @param Request $request Request instance
     * @return Response JSON response with nodes and edges
     */
    public function campaignData(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Get current campaign from session
        $campaignId = session('campaign_id');
        if (!$campaignId) {
            return Response::json([
                'success' => false,
                'error' => 'No campaign selected'
            ], 400);
        }

        // Get graph data for campaign
        $graphData = $this->relationRepository->getCampaignGraphData($campaignId);

        return Response::json([
            'success' => true,
            'data' => $graphData
        ]);
    }
}
