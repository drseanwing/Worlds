<?php

namespace Worlds\Controllers;

use Worlds\Config\ApiAuth;
use Worlds\Config\Request;
use Worlds\Config\Response;

/**
 * ApiController class
 *
 * Base controller for all API endpoints. Provides common methods for
 * API authentication, JSON responses, and error handling.
 */
abstract class ApiController
{
    /**
     * @var array|null Authenticated user data
     */
    protected ?array $user = null;

    /**
     * Require API authentication via Bearer token
     *
     * Validates the API token and sets the authenticated user.
     * Throws 401 Unauthorized if authentication fails.
     *
     * @param Request $request HTTP request object
     * @return array Authenticated user data
     */
    protected function requireApiAuth(Request $request): array
    {
        $user = ApiAuth::authenticate($request);

        if ($user === null) {
            $this->errorResponse('Unauthorized: Invalid or missing API token', 401)->send();
            exit;
        }

        $this->user = $user;
        return $user;
    }

    /**
     * Create a JSON success response
     *
     * @param array $data Response data
     * @param int $status HTTP status code (default 200)
     * @return Response JSON response
     */
    protected function jsonResponse(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * Create a JSON error response
     *
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param string|null $code Optional error code
     * @return Response JSON error response
     */
    protected function errorResponse(string $message, int $status, ?string $code = null): Response
    {
        $error = ['message' => $message];

        if ($code !== null) {
            $error['code'] = $code;
        }

        return Response::json(['error' => $error], $status);
    }

    /**
     * Create a successful JSON response with data
     *
     * Wraps data in standard API format with optional metadata.
     *
     * @param mixed $data Response data
     * @param array|null $meta Optional metadata (pagination, etc.)
     * @param int $status HTTP status code (default 200)
     * @return Response JSON response
     */
    protected function successResponse($data, ?array $meta = null, int $status = 200): Response
    {
        $response = ['data' => $data];

        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return Response::json($response, $status);
    }

    /**
     * Validate required fields in request data
     *
     * @param array $data Input data to validate
     * @param array $required Required field names
     * @return array|null Array of missing fields, or null if all present
     */
    protected function validateRequired(array $data, array $required): ?array
    {
        $missing = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $missing[] = $field;
            }
        }

        return empty($missing) ? null : $missing;
    }

    /**
     * Send a validation error response
     *
     * @param array $errors Array of validation error messages
     * @return Response JSON error response with 422 status
     */
    protected function validationError(array $errors): Response
    {
        return Response::json([
            'error' => [
                'message' => 'Validation failed',
                'code' => 'VALIDATION_ERROR',
                'errors' => $errors
            ]
        ], 422);
    }

    /**
     * Handle rate limit exceeded
     *
     * @return Response JSON error response with 429 status
     */
    protected function rateLimitExceeded(): Response
    {
        return $this->errorResponse(
            'Rate limit exceeded. Please try again later.',
            429,
            'RATE_LIMIT_EXCEEDED'
        );
    }

    /**
     * Extract pagination parameters from request
     *
     * @param Request $request HTTP request
     * @param int $defaultPerPage Default items per page
     * @param int $maxPerPage Maximum items per page
     * @return array ['page' => int, 'per_page' => int]
     */
    protected function getPagination(Request $request, int $defaultPerPage = 50, int $maxPerPage = 100): array
    {
        $page = max(1, (int) $request->getQueryParam('page', 1));
        $perPage = min($maxPerPage, max(1, (int) $request->getQueryParam('per_page', $defaultPerPage)));

        return [
            'page' => $page,
            'per_page' => $perPage
        ];
    }
}
