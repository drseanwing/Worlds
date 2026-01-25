# Helper Functions Reference

This document provides a complete reference for all helper functions available in the Worlds PHP framework.

## Table of Contents

- [HTML Escaping](#html-escaping)
- [URL Generation](#url-generation)
- [CSRF Protection](#csrf-protection)
- [Form Input](#form-input)
- [Configuration](#configuration)
- [Debugging](#debugging)
- [Responses](#responses)
- [Views](#views)
- [Session Management](#session-management)
- [Flash Messages](#flash-messages)

---

## HTML Escaping

### `e(?string $value): string`

Escapes HTML special characters to prevent XSS attacks.

**Parameters:**
- `$value` - String to escape (null safe)

**Returns:** Escaped string safe for HTML output

**Example:**
```php
<p>User input: <?= e($userInput) ?></p>

// Handles null safely
<p>Optional: <?= e($maybeNull) ?></p>
```

### `escape(?string $value): string`

Alias for `e()`. Same functionality.

---

## URL Generation

### `url(string $path, array $params = []): string`

Generates a URL path with optional query parameters.

**Parameters:**
- `$path` - URL path (e.g., '/entities/123')
- `$params` - Optional query parameters as associative array

**Returns:** Full URL path with query string

**Example:**
```php
<a href="<?= url('/entities') ?>">All Entities</a>
<a href="<?= url('/entities/123') ?>">View Entity</a>
<a href="<?= url('/search', ['q' => 'dragon', 'type' => 'creature']) ?>">Search</a>
```

### `asset(string $path): string`

Generates a URL for static assets with cache-busting based on file modification time.

**Parameters:**
- `$path` - Asset path relative to public directory (e.g., 'css/app.css')

**Returns:** Asset URL with cache-busting query parameter

**Example:**
```php
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<script src="<?= asset('js/app.js') ?>"></script>
<img src="<?= asset('images/logo.png') ?>" alt="Logo">
```

**Output:** `/css/app.css?v=1706234567`

---

## CSRF Protection

### `csrf_token(): string`

Gets or generates a CSRF token. Stores in session automatically.

**Returns:** CSRF token string

**Example:**
```php
$token = csrf_token();
```

### `csrf_field(): string`

Generates a hidden input field containing the CSRF token.

**Returns:** HTML hidden input field

**Example:**
```php
<form method="POST" action="/entities/create">
    <?= csrf_field() ?>
    <input type="text" name="name">
    <button type="submit">Create</button>
</form>
```

**Output:**
```html
<input type="hidden" name="_csrf_token" value="abc123...">
```

### `verify_csrf_token(?string $token = null): bool`

Verifies a CSRF token against the session token.

**Parameters:**
- `$token` - Token to verify (defaults to `$_POST['_csrf_token']`)

**Returns:** True if token is valid

**Example:**
```php
if (!verify_csrf_token()) {
    flash('error', 'Invalid CSRF token');
    return redirect('/');
}
```

---

## Form Input

### `old(string $key, mixed $default = null): mixed`

Retrieves old form input from session (for repopulating forms after validation errors).

**Parameters:**
- `$key` - Input field name
- `$default` - Default value if no old input exists

**Returns:** Old input value or default

**Example:**
```php
<input type="text"
       name="name"
       value="<?= e(old('name', '')) ?>">

<textarea name="description"><?= e(old('description', '')) ?></textarea>
```

### `flash_old_input(?array $input = null): void`

Stores form input in session for one-time retrieval (typically before redirect).

**Parameters:**
- `$input` - Input data (defaults to `$_POST`)

**Example:**
```php
if ($validationFails) {
    flash_old_input();
    flash('error', 'Please fix the errors');
    return redirect('/entities/create');
}
```

### `clear_old_input(): void`

Clears old input from session.

**Example:**
```php
// After successful form processing
clear_old_input();
```

---

## Configuration

### `config(string $key, ?string $default = null): string`

Gets a configuration value from the Config class.

**Parameters:**
- `$key` - Configuration key
- `$default` - Default value if not found

**Returns:** Configuration value or default

**Example:**
```php
$dbPath = config('DATABASE_PATH', 'data/campaign.db');
$uploadDir = config('UPLOAD_DIR', 'data/uploads');
```

### `app_name(): string`

Gets the application name from configuration.

**Returns:** Application name

**Example:**
```php
<title><?= e(app_name()) ?></title>
```

### `app_url(): string`

Gets the application URL from configuration.

**Returns:** Application URL

**Example:**
```php
$fullUrl = app_url() . url('/entities/123');
```

### `is_debug(): bool`

Checks if debug mode is enabled.

**Returns:** True if debug mode is enabled

**Example:**
```php
<?php if (is_debug()): ?>
    <div class="debug-info">...</div>
<?php endif; ?>
```

---

## Debugging

### `dd(mixed ...$vars): never`

Dumps variables in a readable format and terminates execution. Shows stack trace in debug mode.

**Parameters:**
- `...$vars` - Variables to dump

**Example:**
```php
dd($entity);
dd($entity, $related, $metadata);
```

### `dump(mixed ...$vars): void`

Dumps variables without terminating execution.

**Parameters:**
- `...$vars` - Variables to dump

**Example:**
```php
dump($entity);
echo "Execution continues...";
```

---

## Responses

### `redirect(string $url, int $statusCode = 302): Response`

Creates a redirect response.

**Parameters:**
- `$url` - URL to redirect to
- `$statusCode` - HTTP status code (default 302)

**Returns:** Response object

**Example:**
```php
return redirect('/entities');
return redirect('/entities/123');
return redirect('/new-location', 301); // Permanent redirect
```

### `json_response(mixed $data, int $statusCode = 200): Response`

Creates a JSON response.

**Parameters:**
- `$data` - Data to encode as JSON
- `$statusCode` - HTTP status code (default 200)

**Returns:** Response object

**Example:**
```php
return json_response([
    'status' => 'success',
    'data' => ['id' => 123, 'name' => 'Dragon']
]);

return json_response(['error' => 'Not found'], 404);
```

---

## Views

### `view(string $viewPath, array $data = []): string`

Renders a PHP template file with extracted variables.

**Parameters:**
- `$viewPath` - Path to view file relative to views directory
- `$data` - Variables to extract into view scope

**Returns:** Rendered view content

**Example:**
```php
return view('entities/show', [
    'entity' => $entity,
    'title' => 'View Entity'
]);
```

**View file (`views/entities/show.php`):**
```php
<!DOCTYPE html>
<html>
<head>
    <title><?= e($title) ?></title>
</head>
<body>
    <h1><?= e($entity['name']) ?></h1>
</body>
</html>
```

---

## Session Management

### `session(?string $key = null, mixed $value = null): mixed`

Gets or sets session values.

**Parameters:**
- `$key` - Session key (null to get all session data)
- `$value` - Value to set (null to get value)

**Returns:** Session value(s)

**Example:**
```php
// Get value
$userId = session('user_id');

// Set value
session('user_id', 123);

// Get all session data
$allSession = session();
```

---

## Flash Messages

### `flash(string $key, string $message): void`

Stores a message in session for one-time display (typically after redirect).

**Parameters:**
- `$key` - Flash message key (e.g., 'success', 'error', 'warning')
- `$message` - Message content

**Example:**
```php
flash('success', 'Entity created successfully!');
flash('error', 'Name is required');
flash('warning', 'This action cannot be undone');
```

### `get_flash(string $key): ?string`

Retrieves and removes a flash message from session.

**Parameters:**
- `$key` - Flash message key

**Returns:** Flash message or null if not found

**Example:**
```php
<?php if ($success = get_flash('success')): ?>
    <div class="alert alert-success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<?php if ($error = get_flash('error')): ?>
    <div class="alert alert-danger">
        <?= e($error) ?>
    </div>
<?php endif; ?>
```

---

## Complete Usage Example

Here's a complete example showing many helpers working together:

```php
// Route handler
$router->post('/entities', function (Request $request) {
    // Verify CSRF
    if (!verify_csrf_token()) {
        flash('error', 'Invalid CSRF token');
        return redirect('/entities/create');
    }

    // Validate input
    $name = $request->input('name');
    if (empty($name)) {
        flash_old_input();
        flash('error', 'Name is required');
        return redirect('/entities/create');
    }

    // Create entity
    $entity = createEntity($name);

    // Clear old input on success
    clear_old_input();

    // Flash success message
    flash('success', 'Entity created successfully!');

    // Redirect to show page
    return redirect(url('/entities/' . $entity['id']));
});

// View template
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Entity - <?= app_name() ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <h1>Create Entity</h1>

    <!-- Flash messages -->
    <?php if ($error = get_flash('error')): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="<?= url('/entities') ?>">
        <?= csrf_field() ?>

        <input type="text"
               name="name"
               value="<?= e(old('name', '')) ?>"
               placeholder="Entity name"
               required>

        <button type="submit">Create</button>
    </form>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
```

---

## Security Notes

1. **Always use `e()` or `escape()`** when outputting user-provided data in HTML
2. **Always use `csrf_field()`** in forms that modify data (POST/PUT/DELETE)
3. **Always verify CSRF tokens** in route handlers that process form submissions
4. **Use `old()` with `e()`** when repopulating form fields
5. **Never trust user input** - always validate and sanitize

## Performance Notes

1. **`asset()`** checks file modification time on every call - consider caching in production
2. **Session functions** start sessions automatically if not already started
3. **Helper functions are loaded globally** - avoid naming conflicts with your own functions
