<?php

namespace Worlds\Controllers;

use Worlds\Config\Auth;
use Worlds\Config\ApiAuth;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\View;
use DateTime;

/**
 * ApiTokenController class
 *
 * Handles API token management UI for users. Allows users to create,
 * view, and revoke their API tokens through a web interface.
 */
class ApiTokenController
{
    /**
     * Display list of user's API tokens
     *
     * GET /settings/api-tokens
     *
     * @param Request $request HTTP request
     * @return Response HTML response with token list
     */
    public function index(Request $request): Response
    {
        Auth::requireAuth();

        $userId = Auth::id();
        $tokens = ApiAuth::getUserTokens($userId);

        // Add human-readable info to tokens
        foreach ($tokens as &$token) {
            $token['is_expired'] = false;

            if ($token['expires_at']) {
                $expiresAt = new DateTime($token['expires_at']);
                $now = new DateTime();
                $token['is_expired'] = $expiresAt < $now;
            }
        }

        $view = new View();
        $html = $view->render('settings/api-tokens', [
            'tokens' => $tokens,
            'newToken' => get_flash('new_token'),
            'success' => get_flash('success'),
            'errors' => get_flash('errors')
        ]);

        return Response::html($html);
    }

    /**
     * Create a new API token
     *
     * POST /settings/api-tokens
     *
     * @param Request $request HTTP request
     * @return Response Redirect response
     */
    public function store(Request $request): Response
    {
        Auth::requireAuth();

        // Validate CSRF token
        $csrfToken = $request->input('_csrf_token');
        if (!verify_csrf_token($csrfToken)) {
            flash('errors', ['CSRF token validation failed']);
            return redirect('/settings/api-tokens');
        }

        $name = $request->input('name', '');
        $expiresIn = $request->input('expires_in', 'never');

        // Validation
        $errors = [];

        if (empty($name) || strlen($name) < 3) {
            $errors[] = 'Token name must be at least 3 characters';
        }

        if (!in_array($expiresIn, ['never', '30d', '90d', '1y'])) {
            $errors[] = 'Invalid expiration option';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            return redirect('/settings/api-tokens');
        }

        // Calculate expiration date
        $expiresAt = null;
        if ($expiresIn !== 'never') {
            $expiresAt = new DateTime();
            switch ($expiresIn) {
                case '30d':
                    $expiresAt->modify('+30 days');
                    break;
                case '90d':
                    $expiresAt->modify('+90 days');
                    break;
                case '1y':
                    $expiresAt->modify('+1 year');
                    break;
            }
        }

        try {
            $userId = Auth::id();
            $plainToken = ApiAuth::generateToken($userId, $name, $expiresAt);

            // Flash the token (only shown once!)
            flash('new_token', [
                'token' => $plainToken,
                'name' => $name
            ]);

            flash('success', 'API token created successfully. Copy it now - it won\'t be shown again!');

            return redirect('/settings/api-tokens');

        } catch (\Exception $e) {
            flash('errors', ['Failed to create API token: ' . $e->getMessage()]);
            return redirect('/settings/api-tokens');
        }
    }

    /**
     * Revoke (delete) an API token
     *
     * DELETE /settings/api-tokens/{id}
     *
     * @param Request $request HTTP request
     * @param int $id Token ID
     * @return Response Redirect response
     */
    public function destroy(Request $request, int $id): Response
    {
        Auth::requireAuth();

        $userId = Auth::id();

        // Revoke token (only if it belongs to this user)
        $success = ApiAuth::revokeTokenById($id, $userId);

        if ($success) {
            flash('success', 'API token revoked successfully');
        } else {
            flash('errors', ['Failed to revoke token. It may not exist or may not belong to you.']);
        }

        return redirect('/settings/api-tokens');
    }
}
