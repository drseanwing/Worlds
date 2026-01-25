<?php
/**
 * Helper Functions Demo
 *
 * Demonstrates usage of all helper functions available in the Worlds framework.
 *
 * Note: This file is for reference only. These examples show how to use
 * helper functions in your templates and route handlers.
 */

// =============================================================================
// HTML ESCAPING
// =============================================================================

// Basic HTML escaping - prevents XSS attacks
$userInput = '<script>alert("XSS")</script>';
?>

<div>
    <!-- Safe output - will display as text, not execute -->
    <p>User said: <?= e($userInput) ?></p>

    <!-- Also works with escape() alias -->
    <p>User said: <?= escape($userInput) ?></p>
</div>

<?php

// =============================================================================
// URL GENERATION
// =============================================================================
?>

<!-- Simple path -->
<a href="<?= url('/entities') ?>">All Entities</a>

<!-- Path with parameters -->
<a href="<?= url('/entities/123') ?>">View Entity</a>

<!-- Path with query string -->
<a href="<?= url('/search', ['q' => 'dragons', 'type' => 'creature']) ?>">
    Search for Dragons
</a>
<!-- Generates: /search?q=dragons&type=creature -->

<?php

// =============================================================================
// ASSET URLS (with cache-busting)
// =============================================================================
?>

<!-- CSS with automatic cache-busting based on file modification time -->
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<!-- Generates: /css/app.css?v=1706234567 -->

<!-- JavaScript -->
<script src="<?= asset('js/app.js') ?>"></script>

<!-- Images -->
<img src="<?= asset('images/logo.png') ?>" alt="Logo">

<?php

// =============================================================================
// CSRF PROTECTION
// =============================================================================
?>

<!-- Form with CSRF protection -->
<form method="POST" action="/entities/create">
    <?= csrf_field() ?>
    <!-- Generates: <input type="hidden" name="_csrf_token" value="..."> -->

    <input type="text" name="name" required>
    <button type="submit">Create</button>
</form>

<?php

// In your route handler - verify CSRF token
function handleFormSubmission() {
    if (!verify_csrf_token()) {
        return json_response(['error' => 'Invalid CSRF token'], 403);
    }

    // Process form...
}

// =============================================================================
// OLD INPUT (for form repopulation after validation errors)
// =============================================================================
?>

<!-- Form that repopulates after validation errors -->
<form method="POST" action="/entities/create">
    <?= csrf_field() ?>

    <input type="text"
           name="name"
           value="<?= e(old('name', '')) ?>"
           required>

    <textarea name="description"><?= e(old('description', '')) ?></textarea>

    <button type="submit">Create</button>
</form>

<?php

// In your route handler - flash input on error
function handleFormWithValidation($request) {
    $name = $request->input('name');

    if (empty($name)) {
        // Save input for repopulation
        flash_old_input();

        // Redirect back with error
        flash('error', 'Name is required');
        return redirect('/entities/create');
    }

    // Process successful submission...

    // Clear old input on success
    clear_old_input();

    flash('success', 'Entity created!');
    return redirect('/entities');
}

// =============================================================================
// CONFIGURATION
// =============================================================================

// Get config value
$dbPath = config('DATABASE_PATH', 'data/campaign.db');
$uploadDir = config('UPLOAD_DIR', 'data/uploads');

// Shorthand functions
$appName = app_name();  // Returns Config::getAppName()
$appUrl = app_url();    // Returns Config::getAppUrl()
$debug = is_debug();    // Returns Config::isDebugMode()

// =============================================================================
// DEBUGGING
// =============================================================================

// Dump and die - stops execution
// dd($entity, $related, $metadata);

// Dump without stopping
// dump($entity);
// echo "Continuing execution...";

// =============================================================================
// RESPONSES
// =============================================================================

// JSON response
function apiEndpoint() {
    return json_response([
        'status' => 'success',
        'data' => ['id' => 123, 'name' => 'Dragon']
    ]);
}

// Redirect
function afterSave() {
    return redirect('/entities/123');
}

// Redirect with status code
function permanentRedirect() {
    return redirect('/new-location', 301);
}

// =============================================================================
// VIEWS (template rendering)
// =============================================================================

// Render a view with data
function showEntity($id) {
    $entity = ['id' => $id, 'name' => 'Dragon', 'type' => 'Creature'];

    return view('entities/show', [
        'entity' => $entity,
        'title' => 'View Entity'
    ]);
}

// In views/entities/show.php:
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= e($title ?? app_name()) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <h1><?= e($entity['name']) ?></h1>
    <p>Type: <?= e($entity['type']) ?></p>
</body>
</html>

<?php

// =============================================================================
// SESSION MANAGEMENT
// =============================================================================

// Get session value
$userId = session('user_id');

// Set session value
session('user_id', 123);

// Get all session data
$allSession = session();

// =============================================================================
// FLASH MESSAGES
// =============================================================================

// Set flash message (in route handler)
function afterCreate() {
    flash('success', 'Entity created successfully!');
    return redirect('/entities');
}

// Display flash message (in template)
?>
<?php if ($message = get_flash('success')): ?>
    <div class="alert alert-success">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<?php if ($error = get_flash('error')): ?>
    <div class="alert alert-danger">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php

// =============================================================================
// COMPLETE EXAMPLE: Entity Edit Form
// =============================================================================
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Entity - <?= app_name() ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <div class="container">
        <h1>Edit <?= e($entity['name'] ?? 'Entity') ?></h1>

        <!-- Flash messages -->
        <?php if ($success = get_flash('success')): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = get_flash('error')): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Edit form with CSRF protection and old input repopulation -->
        <form method="POST" action="<?= url('/entities/' . $entity['id']) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="<?= e(old('name', $entity['name'] ?? '')) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="character" <?= old('type', $entity['type'] ?? '') === 'character' ? 'selected' : '' ?>>
                        Character
                    </option>
                    <option value="location" <?= old('type', $entity['type'] ?? '') === 'location' ? 'selected' : '' ?>>
                        Location
                    </option>
                    <option value="item" <?= old('type', $entity['type'] ?? '') === 'item' ? 'selected' : '' ?>>
                        Item
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description"
                          name="description"
                          rows="5"><?= e(old('description', $entity['description'] ?? '')) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= url('/entities') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
