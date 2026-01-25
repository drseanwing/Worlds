<?php

namespace Worlds\Config;

/**
 * Markdown Processing Class
 *
 * Handles markdown to HTML conversion with custom @[entity:id] mention syntax.
 * Uses simple markdown parsing for common formatting: headers, bold, italic, links, lists.
 */
class Markdown
{
    /**
     * Parse markdown text to HTML
     *
     * Converts markdown syntax to HTML with support for:
     * - Headers (# ## ###)
     * - Bold (**text**)
     * - Italic (*text*)
     * - Links ([text](url))
     * - Unordered lists (- item)
     * - Ordered lists (1. item)
     * - Entity mentions (@[entity:id])
     * - Paragraphs
     *
     * @param string $markdown Markdown text
     * @param int|null $campaignId Optional campaign ID for entity link validation
     * @return string HTML output
     */
    public static function parse(string $markdown, ?int $campaignId = null): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Process entity mentions first (before other markdown processing)
        $html = self::processMentions($markdown, $campaignId);

        // Split into lines for processing
        $lines = explode("\n", $html);
        $output = [];
        $inList = false;
        $listType = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Empty lines end lists
            if (empty($trimmed)) {
                if ($inList) {
                    $output[] = $listType === 'ul' ? '</ul>' : '</ol>';
                    $inList = false;
                    $listType = null;
                }
                $output[] = '';
                continue;
            }

            // Headers
            if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmed, $matches)) {
                if ($inList) {
                    $output[] = $listType === 'ul' ? '</ul>' : '</ol>';
                    $inList = false;
                    $listType = null;
                }
                $level = strlen($matches[1]);
                $text = self::parseInline($matches[2]);
                $output[] = "<h{$level}>{$text}</h{$level}>";
                continue;
            }

            // Unordered lists
            if (preg_match('/^[\-\*]\s+(.+)$/', $trimmed, $matches)) {
                if (!$inList || $listType !== 'ul') {
                    if ($inList) {
                        $output[] = '</ol>';
                    }
                    $output[] = '<ul>';
                    $inList = true;
                    $listType = 'ul';
                }
                $text = self::parseInline($matches[1]);
                $output[] = "<li>{$text}</li>";
                continue;
            }

            // Ordered lists
            if (preg_match('/^\d+\.\s+(.+)$/', $trimmed, $matches)) {
                if (!$inList || $listType !== 'ol') {
                    if ($inList) {
                        $output[] = '</ul>';
                    }
                    $output[] = '<ol>';
                    $inList = true;
                    $listType = 'ol';
                }
                $text = self::parseInline($matches[1]);
                $output[] = "<li>{$text}</li>";
                continue;
            }

            // Regular paragraph
            if ($inList) {
                $output[] = $listType === 'ul' ? '</ul>' : '</ol>';
                $inList = false;
                $listType = null;
            }

            $text = self::parseInline($trimmed);
            $output[] = "<p>{$text}</p>";
        }

        // Close any open lists
        if ($inList) {
            $output[] = $listType === 'ul' ? '</ul>' : '</ol>';
        }

        return implode("\n", $output);
    }

    /**
     * Parse inline markdown elements
     *
     * Processes bold, italic, and links within text.
     *
     * @param string $text Text to process
     * @return string Processed text with HTML tags
     */
    private static function parseInline(string $text): string
    {
        // Bold: **text**
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Italic: *text* (but not already in bold)
        $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $text);

        // Links: [text](url)
        $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }

    /**
     * Process entity mentions in text
     *
     * Converts @[entity:123] syntax to HTML links with tooltips.
     * Validates entity existence and fetches name for link text.
     *
     * @param string $text Text containing mentions
     * @param int|null $campaignId Optional campaign ID for validation
     * @return string Text with mentions replaced by HTML links
     */
    private static function processMentions(string $text, ?int $campaignId = null): string
    {
        // Pattern: @[entity:123] or @[entity:123|Custom Label]
        $pattern = '/@\[entity:(\d+)(?:\|([^\]]+))?\]/';

        return preg_replace_callback($pattern, function ($matches) use ($campaignId) {
            $entityId = (int) $matches[1];
            $customLabel = $matches[2] ?? null;

            // Fetch entity data
            $entity = self::fetchEntity($entityId, $campaignId);

            if ($entity === null) {
                // Entity not found or not accessible
                return '<span class="mention-invalid" title="Entity not found">@[entity:' . $entityId . ']</span>';
            }

            // Use custom label or entity name
            $linkText = $customLabel ?? htmlspecialchars($entity['name']);
            $entityType = htmlspecialchars($entity['entity_type']);
            $entityName = htmlspecialchars($entity['name']);

            // Build tooltip content
            $tooltip = "{$entityName} ({$entityType})";

            // Generate link
            return sprintf(
                '<a href="/entities/%s/%d" class="mention-link" data-entity-id="%d" data-entity-type="%s" title="%s">%s</a>',
                urlencode($entity['entity_type']),
                $entityId,
                $entityId,
                $entityType,
                $tooltip,
                $linkText
            );
        }, $text);
    }

    /**
     * Fetch entity data from database
     *
     * @param int $entityId Entity ID
     * @param int|null $campaignId Optional campaign ID for access control
     * @return array|null Entity data or null if not found/accessible
     */
    private static function fetchEntity(int $entityId, ?int $campaignId = null): ?array
    {
        try {
            $pdo = Database::getInstance();

            $sql = 'SELECT id, campaign_id, entity_type, name FROM entities WHERE id = ?';
            $params = [$entityId];

            // Optionally filter by campaign for access control
            if ($campaignId !== null) {
                $sql .= ' AND campaign_id = ?';
                $params[] = $campaignId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (\PDOException $e) {
            error_log('Markdown entity fetch error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract entity mentions from text
     *
     * Returns array of entity IDs mentioned in the text.
     * Useful for building relation graphs or mention indexes.
     *
     * @param string $text Text to analyze
     * @return array<int> Array of entity IDs
     */
    public static function extractMentions(string $text): array
    {
        $pattern = '/@\[entity:(\d+)(?:\|[^\]]+)?\]/';
        preg_match_all($pattern, $text, $matches);

        // Return unique entity IDs as integers
        return array_unique(array_map('intval', $matches[1]));
    }

    /**
     * Validate mention syntax
     *
     * Checks if text contains valid mention syntax.
     *
     * @param string $text Text to validate
     * @return bool True if valid mentions found
     */
    public static function hasMentions(string $text): bool
    {
        return preg_match('/@\[entity:\d+(?:\|[^\]]+)?\]/', $text) === 1;
    }

    /**
     * Convert plain text to markdown-safe text
     *
     * Escapes markdown special characters.
     *
     * @param string $text Plain text
     * @return string Escaped text
     */
    public static function escape(string $text): string
    {
        $specialChars = ['\\', '`', '*', '_', '{', '}', '[', ']', '(', ')', '#', '+', '-', '.', '!'];

        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }

        return $text;
    }
}
