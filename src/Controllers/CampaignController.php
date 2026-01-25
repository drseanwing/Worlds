<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use Worlds\Repositories\CampaignRepository;

/**
 * CampaignController class
 *
 * Handles HTTP requests for campaign CRUD operations including listing,
 * creating, switching between campaigns, and managing user campaigns.
 */
class CampaignController
{
    /**
     * @var CampaignRepository Campaign repository instance
     */
    private CampaignRepository $repository;

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
        $this->repository = new CampaignRepository();
        $this->view = new View();
    }

    /**
     * Display list of user's campaigns
     *
     * Shows all campaigns belonging to the current user and indicates
     * which campaign is currently active.
     *
     * GET /campaigns
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
        $campaigns = $this->repository->findByUser($userId);

        // Render list view
        $html = $this->view->render('campaigns/index', [
            'campaigns' => $campaigns,
            'activeCampaignId' => $activeCampaignId
        ]);

        return Response::html($html);
    }

    /**
     * Display campaign creation form
     *
     * GET /campaigns/create
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function create(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Render create form
        $html = $this->view->render('campaigns/create', [
            'errors' => get_flash('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input after displaying
        clear_old_input();

        return Response::html($html);
    }

    /**
     * Save new campaign
     *
     * Accepts name and description, creates the campaign, and sets it as
     * the active campaign in the session.
     *
     * POST /campaigns
     *
     * @param Request $request Request instance
     * @return Response HTTP response
     */
    public function store(Request $request): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        $token = $request->input('_csrf_token');
        if (!verify_csrf_token($token)) {
            flash('errors', ['CSRF token validation failed']);
            flash_old_input($request->getPost());
            return redirect('/campaigns/create');
        }

        $name = $request->input('name', '');
        $description = $request->input('description', '');

        // Validation
        $errors = [];

        if (empty($name) || trim($name) === '') {
            $errors[] = 'Campaign name is required';
        } elseif (strlen($name) > 255) {
            $errors[] = 'Campaign name must not exceed 255 characters';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash_old_input($request->getPost());
            return redirect('/campaigns/create');
        }

        try {
            // Create campaign
            $campaignId = $this->repository->create([
                'name' => trim($name),
                'description' => trim($description),
                'user_id' => Auth::id()
            ]);

            // Set as active campaign
            Auth::setActiveCampaignId($campaignId);

            flash('success', 'Campaign created successfully and set as active');
            return redirect('/campaigns');

        } catch (\PDOException $e) {
            flash('errors', ['Failed to create campaign: ' . $e->getMessage()]);
            flash_old_input($request->getPost());
            return redirect('/campaigns/create');
        }
    }

    /**
     * Display single campaign details
     *
     * GET /campaigns/{id}
     *
     * @param Request $request Request instance
     * @param int $id Campaign ID
     * @return Response HTTP response
     */
    public function show(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch campaign
        $campaign = $this->repository->findById($id);

        // Check if campaign exists
        if ($campaign === null) {
            flash('error', 'Campaign not found');
            return Response::html(
                $this->view->render('errors/404', ['message' => 'Campaign not found']),
                404
            );
        }

        // Verify campaign belongs to user
        if ($campaign['user_id'] !== Auth::id()) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Render show view
        $html = $this->view->render('campaigns/show', [
            'campaign' => $campaign,
            'isActive' => Auth::getActiveCampaignId() === $id
        ]);

        return Response::html($html);
    }

    /**
     * Display campaign edit form
     *
     * GET /campaigns/{id}/edit
     *
     * @param Request $request Request instance
     * @param int $id Campaign ID
     * @return Response HTTP response
     */
    public function edit(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Fetch campaign
        $campaign = $this->repository->findById($id);

        // Check if campaign exists
        if ($campaign === null) {
            flash('error', 'Campaign not found');
            return redirect('/campaigns');
        }

        // Verify campaign belongs to user
        if ($campaign['user_id'] !== Auth::id()) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Render edit form
        $html = $this->view->render('campaigns/edit', [
            'campaign' => $campaign,
            'errors' => get_flash('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input after displaying
        clear_old_input();

        return Response::html($html);
    }

    /**
     * Update campaign
     *
     * PUT /campaigns/{id}
     *
     * @param Request $request Request instance
     * @param int $id Campaign ID
     * @return Response HTTP response
     */
    public function update(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        $token = $request->input('_csrf_token');
        if (!verify_csrf_token($token)) {
            flash('errors', ['CSRF token validation failed']);
            flash_old_input($request->getPost());
            return redirect("/campaigns/{$id}/edit");
        }

        // Fetch campaign to verify it exists and user has access
        $campaign = $this->repository->findById($id);

        if ($campaign === null) {
            flash('error', 'Campaign not found');
            return redirect('/campaigns');
        }

        // Verify campaign belongs to user
        if ($campaign['user_id'] !== Auth::id()) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        $name = $request->input('name', '');
        $description = $request->input('description', '');

        // Validation
        $errors = [];

        if (empty($name) || trim($name) === '') {
            $errors[] = 'Campaign name is required';
        } elseif (strlen($name) > 255) {
            $errors[] = 'Campaign name must not exceed 255 characters';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash_old_input($request->getPost());
            return redirect("/campaigns/{$id}/edit");
        }

        try {
            // Update campaign
            $success = $this->repository->update($id, [
                'name' => trim($name),
                'description' => trim($description)
            ]);

            if ($success) {
                flash('success', 'Campaign updated successfully');
                return redirect("/campaigns/{$id}");
            } else {
                flash('error', 'Failed to update campaign');
                flash_old_input($request->getPost());
                return redirect("/campaigns/{$id}/edit");
            }

        } catch (\PDOException $e) {
            flash('errors', ['Failed to update campaign: ' . $e->getMessage()]);
            flash_old_input($request->getPost());
            return redirect("/campaigns/{$id}/edit");
        }
    }

    /**
     * Delete campaign
     *
     * DELETE /campaigns/{id}
     *
     * @param Request $request Request instance
     * @param int $id Campaign ID
     * @return Response HTTP response
     */
    public function destroy(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        $token = $request->input('_csrf_token');
        if (!verify_csrf_token($token)) {
            flash('error', 'Invalid CSRF token');
            return redirect('/campaigns');
        }

        // Fetch campaign to verify it exists and user has access
        $campaign = $this->repository->findById($id);

        if ($campaign === null) {
            flash('error', 'Campaign not found');
            return redirect('/campaigns');
        }

        // Verify campaign belongs to user
        if ($campaign['user_id'] !== Auth::id()) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        try {
            // Delete campaign
            $success = $this->repository->delete($id);

            if ($success) {
                // If this was the active campaign, clear it from session
                if (Auth::getActiveCampaignId() === $id) {
                    Auth::clearActiveCampaignId();
                }

                flash('success', 'Campaign deleted successfully');
            } else {
                flash('error', 'Failed to delete campaign');
            }

            return redirect('/campaigns');

        } catch (\PDOException $e) {
            flash('error', 'Failed to delete campaign: ' . $e->getMessage());
            return redirect('/campaigns');
        }
    }

    /**
     * Switch active campaign
     *
     * Changes the active campaign in the session.
     *
     * POST /campaigns/{id}/switch
     *
     * @param Request $request Request instance
     * @param int $id Campaign ID to switch to
     * @return Response HTTP response
     */
    public function switchCampaign(Request $request, int $id): Response
    {
        // Verify user is logged in
        Auth::requireAuth();

        // Validate CSRF token
        $token = $request->input('_csrf_token');
        if (!verify_csrf_token($token)) {
            flash('error', 'Invalid CSRF token');
            return redirect('/campaigns');
        }

        // Fetch campaign to verify it exists and user has access
        $campaign = $this->repository->findById($id);

        if ($campaign === null) {
            flash('error', 'Campaign not found');
            return redirect('/campaigns');
        }

        // Verify campaign belongs to user
        if ($campaign['user_id'] !== Auth::id()) {
            flash('error', 'Access denied');
            return Response::error(403, 'Forbidden');
        }

        // Set as active campaign
        Auth::setActiveCampaignId($id);

        flash('success', "Switched to campaign: {$campaign['name']}");
        return redirect('/campaigns');
    }
}
