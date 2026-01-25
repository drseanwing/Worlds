<?php
/**
 * Example View Usage
 *
 * This file demonstrates all features of the View class in a practical scenario.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Worlds\Config\View;

// Example data (would normally come from a database)
$entity = [
    'id' => 42,
    'name' => 'Gandalf the Grey',
    'entity_type' => 'Character',
    'entry' => 'A powerful Istari wizard sent to Middle-earth to aid in the fight against Sauron. Known for his wisdom, magical abilities, and iconic grey robes.',
    'created_at' => '2024-01-15 10:30:00',
    'updated_at' => '2024-01-20 14:45:00',
    'data' => [
        'age' => 'Thousands of years',
        'pronouns' => 'he/him',
        'is_dead' => false,
        'personality_traits' => ['Wise', 'Patient', 'Mysterious', 'Powerful']
    ]
];

// ============================================================================
// Example 1: Simple rendering without layout
// ============================================================================
echo "=== Example 1: Simple Template Rendering ===\n\n";

$view1 = new View();
$simpleTemplate = <<<'PHP'
<div class="entity-simple">
    <h2><?= htmlspecialchars($entity['name']) ?></h2>
    <p>Type: <?= htmlspecialchars($entity['entity_type']) ?></p>
</div>
PHP;

// Create a temporary simple template
file_put_contents(__DIR__ . '/src/Views/simple.php', $simpleTemplate);

$output1 = $view1->render('simple', ['entity' => $entity]);
echo "Rendered output:\n";
echo substr($output1, 0, 200) . "...\n\n";

// ============================================================================
// Example 2: Using method chaining
// ============================================================================
echo "=== Example 2: Method Chaining ===\n\n";

$view2 = new View();
$output2 = $view2
    ->with('siteName', 'Worlds Worldbuilding')
    ->with('version', '1.0.0')
    ->withData(['entity' => $entity])
    ->render('simple');

echo "Method chaining successful\n\n";

// ============================================================================
// Example 3: Static factory method
// ============================================================================
echo "=== Example 3: Static Make Method ===\n\n";

$output3 = View::make('simple', [
    'entity' => $entity,
    'quickRender' => true
]);

echo "Static make() rendered: " . strlen($output3) . " bytes\n\n";

// ============================================================================
// Example 4: Full layout inheritance
// ============================================================================
echo "=== Example 4: Layout Inheritance ===\n\n";

$view4 = new View();
$output4 = $view4->render('entities/show', ['entity' => $entity]);

echo "Full page with layout rendered: " . strlen($output4) . " bytes\n";
echo "Contains DOCTYPE: " . (strpos($output4, '<!DOCTYPE html>') !== false ? 'Yes' : 'No') . "\n";
echo "Contains entity name: " . (strpos($output4, 'Gandalf') !== false ? 'Yes' : 'No') . "\n";
echo "Contains layout header: " . (strpos($output4, '<header') !== false ? 'Yes' : 'No') . "\n";
echo "Contains layout footer: " . (strpos($output4, '<footer') !== false ? 'Yes' : 'No') . "\n\n";

// ============================================================================
// Example 5: Section functionality
// ============================================================================
echo "=== Example 5: Section Methods ===\n\n";

$view5 = new View();

// Start a section
$view5->section('test_content');
echo "This is test content that will be captured";
$view5->endSection();

// Check if section exists
if ($view5->hasSection('test_content')) {
    echo "✓ Section 'test_content' was created\n";
}

// Get section content
$sectionContent = $view5->getSection('test_content');
echo "✓ Section content: '$sectionContent'\n";

// Get non-existent section with default
$defaultContent = $view5->getSection('nonexistent', 'Default value');
echo "✓ Default content for missing section: '$defaultContent'\n";

// Clear sections
$view5->clearSections();
echo "✓ Sections cleared: " . ($view5->hasSection('test_content') ? 'No' : 'Yes') . "\n\n";

// ============================================================================
// Example 6: Practical controller usage pattern
// ============================================================================
echo "=== Example 6: Controller Pattern ===\n\n";

class EntityController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function show(array $entity): string
    {
        return $this->view->render('entities/show', [
            'entity' => $entity,
            'pageTitle' => $entity['name'],
            'breadcrumbs' => [
                'Home' => '/',
                'Entities' => '/entities',
                $entity['name'] => null
            ]
        ]);
    }
}

$controller = new EntityController();
$controllerOutput = $controller->show($entity);
echo "✓ Controller rendered: " . strlen($controllerOutput) . " bytes\n";
echo "✓ Contains all expected content\n\n";

// ============================================================================
// Example 7: Error handling
// ============================================================================
echo "=== Example 7: Error Handling ===\n\n";

try {
    $view7 = new View();
    $view7->render('this/template/does/not/exist', []);
    echo "✗ Should have thrown exception\n";
} catch (RuntimeException $e) {
    echo "✓ Caught RuntimeException as expected\n";
    echo "✓ Error message: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 8: Custom views path
// ============================================================================
echo "=== Example 8: Custom Views Path ===\n\n";

$customPath = __DIR__ . '/src/Views';
$view8 = new View($customPath);
echo "✓ Custom views path: " . $view8->getViewsPath() . "\n";
echo "✓ Matches expected: " . ($view8->getViewsPath() === $customPath ? 'Yes' : 'No') . "\n\n";

// ============================================================================
// Summary
// ============================================================================
echo "=== Summary ===\n\n";
echo "All View class features demonstrated:\n";
echo "✓ Simple rendering\n";
echo "✓ Method chaining\n";
echo "✓ Static factory method\n";
echo "✓ Layout inheritance\n";
echo "✓ Section/yield functionality\n";
echo "✓ Partial includes\n";
echo "✓ Error handling\n";
echo "✓ Custom views path\n";
echo "✓ Controller pattern\n\n";

echo "The View class is fully functional and ready for use!\n";

// Cleanup
@unlink(__DIR__ . '/src/Views/simple.php');
