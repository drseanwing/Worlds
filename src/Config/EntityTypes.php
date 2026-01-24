<?php

namespace Worlds\Config;

/**
 * EntityTypes registry class
 * 
 * Loads and manages entity type JSON schemas, provides validation
 * for entity data against their respective schemas.
 */
class EntityTypes
{
    /**
     * @var array<string, array<string, mixed>> Loaded schemas indexed by type name
     */
    private static array $schemas = [];

    /**
     * @var bool Whether schemas have been loaded
     */
    private static bool $loaded = false;

    /**
     * @var string Path to schemas directory
     */
    private static string $schemasPath = '';

    /**
     * List of all available entity types
     * 
     * @var array<string>
     */
    private const ENTITY_TYPES = [
        'character',
        'location',
        'family',
        'organisation',
        'item',
        'note',
        'event',
        'calendar',
        'race',
        'quest',
        'journal',
        'map',
        'timeline',
        'ability',
        'creature'
    ];

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct()
    {
        // Static class - use static methods
    }

    /**
     * Load all entity type schemas
     * 
     * @param string|null $schemasPath Optional custom path to schemas directory
     * @return void
     */
    public static function load(?string $schemasPath = null): void
    {
        if (self::$loaded && $schemasPath === null) {
            return;
        }

        self::$schemasPath = $schemasPath ?? dirname(__FILE__) . DIRECTORY_SEPARATOR . 'schemas';
        self::$schemas = [];

        foreach (self::ENTITY_TYPES as $type) {
            $schemaFile = self::$schemasPath . DIRECTORY_SEPARATOR . $type . '.json';
            
            if (file_exists($schemaFile)) {
                $content = file_get_contents($schemaFile);
                if ($content !== false) {
                    $schema = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($schema)) {
                        self::$schemas[$type] = $schema;
                    }
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get list of available entity types
     * 
     * @return array<string> List of entity type names
     */
    public static function getTypes(): array
    {
        return self::ENTITY_TYPES;
    }

    /**
     * Get list of loaded entity types (with valid schemas)
     * 
     * @return array<string> List of entity type names with loaded schemas
     */
    public static function getLoadedTypes(): array
    {
        if (!self::$loaded) {
            self::load();
        }

        return array_keys(self::$schemas);
    }

    /**
     * Get schema for a specific entity type
     * 
     * @param string $type Entity type name
     * @return array<string, mixed>|null Schema array or null if not found
     */
    public static function getSchema(string $type): ?array
    {
        if (!self::$loaded) {
            self::load();
        }

        $type = strtolower($type);
        return self::$schemas[$type] ?? null;
    }

    /**
     * Check if an entity type exists
     * 
     * @param string $type Entity type name
     * @return bool True if type exists
     */
    public static function typeExists(string $type): bool
    {
        return in_array(strtolower($type), self::ENTITY_TYPES, true);
    }

    /**
     * Check if schema is loaded for a type
     * 
     * @param string $type Entity type name
     * @return bool True if schema is loaded
     */
    public static function hasSchema(string $type): bool
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$schemas[strtolower($type)]);
    }

    /**
     * Validate entity data against its schema
     * 
     * Checks required fields, validates field types, and returns
     * any validation errors found.
     * 
     * @param string $type Entity type name
     * @param array<string, mixed> $data Entity data to validate
     * @return array<string> List of validation error messages (empty if valid)
     */
    public static function validate(string $type, array $data): array
    {
        $errors = [];
        $type = strtolower($type);

        if (!self::typeExists($type)) {
            $errors[] = "Unknown entity type: {$type}";
            return $errors;
        }

        $schema = self::getSchema($type);
        if ($schema === null) {
            $errors[] = "Schema not found for type: {$type}";
            return $errors;
        }

        // Check if schema has properties defined
        if (!isset($schema['properties']) || !is_array($schema['properties'])) {
            return $errors; // No properties to validate
        }

        $properties = $schema['properties'];

        // Check required fields
        if (isset($schema['required']) && is_array($schema['required'])) {
            foreach ($schema['required'] as $requiredField) {
                if (!array_key_exists($requiredField, $data) || $data[$requiredField] === null) {
                    $errors[] = "Required field missing: {$requiredField}";
                }
            }
        }

        // Validate each provided field
        foreach ($data as $field => $value) {
            if (!isset($properties[$field])) {
                // Skip fields not defined in schema (additionalProperties allows them)
                continue;
            }

            $fieldSchema = $properties[$field];
            $fieldErrors = self::validateField($field, $value, $fieldSchema);
            $errors = array_merge($errors, $fieldErrors);
        }

        return $errors;
    }

    /**
     * Validate a single field against its schema definition
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array<string, mixed> $fieldSchema Field schema definition
     * @return array<string> List of validation error messages
     */
    private static function validateField(string $field, mixed $value, array $fieldSchema): array
    {
        $errors = [];

        // Handle null values
        if ($value === null) {
            // Check if null is allowed
            if (isset($fieldSchema['type'])) {
                $allowedTypes = (array) $fieldSchema['type'];
                if (!in_array('null', $allowedTypes, true)) {
                    $errors[] = "Field '{$field}' cannot be null";
                }
            }
            return $errors;
        }

        // Validate type
        if (isset($fieldSchema['type'])) {
            $allowedTypes = (array) $fieldSchema['type'];
            $actualType = self::getJsonType($value);
            
            $typeValid = false;
            foreach ($allowedTypes as $allowedType) {
                if ($allowedType === 'null' && $value === null) {
                    $typeValid = true;
                    break;
                }
                if ($allowedType === $actualType) {
                    $typeValid = true;
                    break;
                }
                // Allow integer for number type
                if ($allowedType === 'number' && $actualType === 'integer') {
                    $typeValid = true;
                    break;
                }
            }

            if (!$typeValid) {
                $expected = implode(' or ', array_filter($allowedTypes, fn($t) => $t !== 'null'));
                $errors[] = "Field '{$field}' must be of type {$expected}, got {$actualType}";
            }
        }

        // Validate enum values
        if (isset($fieldSchema['enum']) && is_array($fieldSchema['enum'])) {
            if (!in_array($value, $fieldSchema['enum'], true)) {
                $allowed = implode(', ', $fieldSchema['enum']);
                $errors[] = "Field '{$field}' must be one of: {$allowed}";
            }
        }

        // Validate array items
        if (isset($fieldSchema['items']) && is_array($value)) {
            $itemSchema = $fieldSchema['items'];
            foreach ($value as $index => $item) {
                $itemErrors = self::validateField("{$field}[{$index}]", $item, $itemSchema);
                $errors = array_merge($errors, $itemErrors);
            }
        }

        // Validate object properties
        if (isset($fieldSchema['properties']) && is_array($value)) {
            foreach ($value as $key => $val) {
                if (isset($fieldSchema['properties'][$key])) {
                    $propErrors = self::validateField("{$field}.{$key}", $val, $fieldSchema['properties'][$key]);
                    $errors = array_merge($errors, $propErrors);
                }
            }

            // Check required properties within object
            if (isset($fieldSchema['required']) && is_array($fieldSchema['required'])) {
                foreach ($fieldSchema['required'] as $requiredProp) {
                    if (!array_key_exists($requiredProp, $value)) {
                        $errors[] = "Field '{$field}' is missing required property: {$requiredProp}";
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Get JSON Schema type from PHP value
     * 
     * @param mixed $value PHP value
     * @return string JSON Schema type name
     */
    private static function getJsonType(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'number';
        }
        if (is_string($value)) {
            return 'string';
        }
        if (is_array($value)) {
            // Empty arrays are considered sequential arrays, not objects
            if (count($value) === 0) {
                return 'array';
            }
            // Check if it's an associative array (object) or sequential array
            if (array_keys($value) !== range(0, count($value) - 1)) {
                return 'object';
            }
            return 'array';
        }
        if (is_object($value)) {
            return 'object';
        }
        return 'unknown';
    }

    /**
     * Get default values for an entity type based on schema
     * 
     * @param string $type Entity type name
     * @return array<string, mixed> Default values for all fields with defaults
     */
    public static function getDefaults(string $type): array
    {
        $schema = self::getSchema($type);
        if ($schema === null || !isset($schema['properties'])) {
            return [];
        }

        $defaults = [];
        foreach ($schema['properties'] as $field => $fieldSchema) {
            if (isset($fieldSchema['default'])) {
                $defaults[$field] = $fieldSchema['default'];
            }
        }

        return $defaults;
    }

    /**
     * Get field information for an entity type
     * 
     * Returns field names with their types and descriptions for UI rendering.
     * 
     * @param string $type Entity type name
     * @return array<string, array{type: string, description: string|null, required: bool}> Field information
     */
    public static function getFieldInfo(string $type): array
    {
        $schema = self::getSchema($type);
        if ($schema === null || !isset($schema['properties'])) {
            return [];
        }

        $required = $schema['required'] ?? [];
        $fields = [];

        foreach ($schema['properties'] as $field => $fieldSchema) {
            $type = $fieldSchema['type'] ?? 'string';
            if (is_array($type)) {
                $type = array_filter($type, fn($t) => $t !== 'null');
                $type = reset($type) ?: 'string';
            }

            $fields[$field] = [
                'type' => $type,
                'description' => $fieldSchema['description'] ?? null,
                'required' => in_array($field, $required, true),
                'default' => $fieldSchema['default'] ?? null,
                'enum' => $fieldSchema['enum'] ?? null
            ];
        }

        return $fields;
    }

    /**
     * Reset loaded schemas (mainly for testing)
     * 
     * @return void
     */
    public static function reset(): void
    {
        self::$schemas = [];
        self::$loaded = false;
        self::$schemasPath = '';
    }

    /**
     * Get human-readable label for an entity type
     * 
     * @param string $type Entity type name
     * @return string Human-readable label
     */
    public static function getLabel(string $type): string
    {
        $type = strtolower($type);
        
        // Special cases
        $labels = [
            'organisation' => 'Organisation',
            'npc' => 'NPC'
        ];

        if (isset($labels[$type])) {
            return $labels[$type];
        }

        // Default: capitalize first letter
        return ucfirst($type);
    }

    /**
     * Get plural label for an entity type
     * 
     * @param string $type Entity type name
     * @return string Plural label
     */
    public static function getPluralLabel(string $type): string
    {
        $type = strtolower($type);
        
        // Special cases
        $plurals = [
            'family' => 'Families',
            'ability' => 'Abilities',
            'calendar' => 'Calendars'
        ];

        if (isset($plurals[$type])) {
            return $plurals[$type];
        }

        // Default: add 's'
        return ucfirst($type) . 's';
    }
}
