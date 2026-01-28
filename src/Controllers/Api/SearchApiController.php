<?php

namespace Worlds\Controllers\Api;

use Worlds\Controllers\ApiController;
use Worlds\Config\Request;
use Worlds\Config\Response;
use Worlds\Config\Database;

/**
 * SearchApiController class
 *
 * REST API endpoint for full-text search across entities.
 * Uses SQLite FTS5 for fast and accurate search results.
 */
class SearchApiController extends ApiController
{
    /**
     * Search entities by query
     *
     * GET /api/v1/search?q=dragon&campaign_id=1&type=character&page=1&per_page=50
     *
     * @param Request $request HTTP request
     * @return Response JSON response with search results
     */
    public function search(Request $request): Response
    {
        $user = $this->requireApiAuth($request);

        // Get search query
        $query = $request->getQueryParam('q');

        if (!$query || trim($query) === '') {
            return $this->errorResponse('Search query (q) is required', 400, 'MISSING_QUERY');
        }

        // Get campaign_id filter (optional)
        $campaignId = $request->getQueryParam('campaign_id');

        // Get entity type filter (optional)
        $type = $request->getQueryParam('type');

        // Get pagination params
        $pagination = $this->getPagination($request);
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        $offset = ($page - 1) * $perPage;

        try {
            $db = Database::getInstance();

            // Build WHERE clause for filters
            $whereClauses = ['entities_fts.name MATCH :query OR entities_fts.entry MATCH :query'];
            $params = ['query' => $query];

            if ($campaignId) {
                $whereClauses[] = 'e.campaign_id = :campaign_id';
                $params['campaign_id'] = (int) $campaignId;
            }

            if ($type) {
                $whereClauses[] = 'e.entity_type = :type';
                $params['type'] = $type;
            }

            $whereClause = 'WHERE ' . implode(' AND ', $whereClauses);

            // Count total results
            $countSql = "
                SELECT COUNT(DISTINCT e.id) as total
                FROM entities_fts
                INNER JOIN entities e ON entities_fts.rowid = e.id
                $whereClause
            ";

            $countStmt = $db->prepare($countSql);
            $countStmt->execute($params);
            $totalResult = $countStmt->fetch(\PDO::FETCH_ASSOC);
            $total = (int) ($totalResult['total'] ?? 0);

            // Fetch paginated results
            $sql = "
                SELECT DISTINCT
                    e.id,
                    e.campaign_id,
                    e.entity_type,
                    e.name,
                    e.type,
                    e.entry,
                    e.image_path,
                    e.parent_id,
                    e.is_private,
                    e.data,
                    e.created_at,
                    e.updated_at
                FROM entities_fts
                INNER JOIN entities e ON entities_fts.rowid = e.id
                $whereClause
                ORDER BY e.updated_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $db->prepare($sql);

            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Calculate total pages
            $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 0;

            return $this->successResponse(
                $results,
                [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'query' => $query
                ]
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage(), 500, 'SEARCH_FAILED');
        }
    }
}
