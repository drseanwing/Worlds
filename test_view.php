<?php

require_once __DIR__ . '/vendor/autoload.php';

use Worlds\Config\View;

// Test 1: Simple rendering
echo "=== Test 1: Simple Rendering ===\n";
$view = new View();
try {
    $output = $view->render('entities/show', [
        'entity' => [
            'id' => 1,
            'name' => 'Gandalf the Grey',
            'entity_type' => 'Character',
            'entry' => 'A powerful wizard known for his wisdom and magical abilities.',
            'created_at' => '2024-01-15 10:30:00',
            'updated_at' => '2024-01-20 14:45:00'
        ]
    ]);
    echo "✓ Template rendered successfully\n";
    echo "✓ Output length: " . strlen($output) . " bytes\n";

    // Check if output contains expected content
    if (strpos($output, 'Gandalf the Grey') !== false) {
        echo "✓ Entity name found in output\n";
    } else {
        echo "✗ Entity name NOT found in output\n";
    }

    if (strpos($output, 'Character') !== false) {
        echo "✓ Entity type found in output\n";
    } else {
        echo "✗ Entity type NOT found in output\n";
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Static make method
echo "\n=== Test 2: Static Make Method ===\n";
try {
    $output = View::make('entities/show', [
        'entity' => [
            'id' => 2,
            'name' => 'Test Entity',
            'entity_type' => 'Location',
            'entry' => 'A test location.',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ]);
    echo "✓ Static make() method works\n";
    echo "✓ Output length: " . strlen($output) . " bytes\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Template not found error
echo "\n=== Test 3: Template Not Found ===\n";
try {
    $view = new View();
    $output = $view->render('nonexistent/template', []);
    echo "✗ Should have thrown an exception\n";
} catch (RuntimeException $e) {
    echo "✓ Correctly threw RuntimeException\n";
    echo "✓ Error message: " . $e->getMessage() . "\n";
}

// Test 4: With method chaining
echo "\n=== Test 4: Method Chaining ===\n";
try {
    $view = new View();
    $view->with('test', 'value')
         ->withData(['another' => 'data']);
    echo "✓ Method chaining works\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 5: Section functionality
echo "\n=== Test 5: Section Methods ===\n";
try {
    $view = new View();
    $view->section('test');
    echo "Content";
    $view->endSection();

    if ($view->hasSection('test')) {
        echo "✓ Section was created\n";
    }

    $content = $view->getSection('test');
    if ($content === 'Content') {
        echo "✓ Section content retrieved correctly\n";
    } else {
        echo "✗ Section content mismatch: got '{$content}'\n";
    }

    $view->clearSections();
    if (!$view->hasSection('test')) {
        echo "✓ Sections cleared successfully\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== All Tests Completed ===\n";
