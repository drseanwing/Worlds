# View Class Documentation

The `View` class is a simple yet powerful PHP template rendering engine for the Worlds project.

## Features

- **Template Loading** - Loads PHP template files from `src/Views/`
- **Variable Extraction** - Makes array keys available as variables in templates
- **Layout Inheritance** - Child templates can extend parent layouts
- **Sections/Yields** - Define and output named content blocks
- **Partial Includes** - Include sub-templates with variable passing
- **Method Chaining** - Fluent interface for setting data

## Basic Usage

### Simple Rendering

```php
use Worlds\Config\View;

$view = new View();
$html = $view->render('entities/show', [
    'entity' => $entity,
    'title' => 'Character Details'
]);

echo $html;
```

### Static Factory Method

```php
$html = View::make('entities/show', ['entity' => $entity]);
```

## Template Inheritance

### Child Template (`src/Views/entities/show.php`)

```php
<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Character: <?= htmlspecialchars($entity['name']) ?>
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<h1><?= htmlspecialchars($entity['name']) ?></h1>
<p><?= nl2br(htmlspecialchars($entity['entry'])) ?></p>
<?php $this->endSection() ?>
```

### Parent Layout (`src/Views/layouts/base.php`)

```php
<!DOCTYPE html>
<html>
<head>
    <title><?php $this->yield('title', 'Default Title') ?></title>
</head>
<body>
    <main>
        <?php $this->yield('content') ?>
    </main>
</body>
</html>
```

## Sections and Yields

### Defining a Section

```php
<?php $this->section('sidebar') ?>
<div class="sidebar">
    <!-- Sidebar content -->
</div>
<?php $this->endSection() ?>
```

### Outputting a Section

```php
<!-- Output section with default content -->
<?php $this->yield('sidebar', '<p>No sidebar content</p>') ?>

<!-- Check if section exists -->
<?php if ($this->hasSection('sidebar')): ?>
    <?php $this->yield('sidebar') ?>
<?php endif; ?>
```

### Getting Section Content

```php
$sidebarContent = $this->getSection('sidebar', 'Default content');
```

## Partial Includes

### Including a Partial

```php
<!-- Include with parent variables -->
<?php $this->include('partials/header') ?>

<!-- Include with additional data -->
<?php $this->include('partials/entity-card', ['entity' => $entity]) ?>
```

### Partial Template (`src/Views/partials/header.php`)

```php
<header>
    <h1><?= $siteName ?? 'Worlds' ?></h1>
    <!-- Access to all parent template variables -->
</header>
```

## Method Chaining

```php
$view = new View();
$html = $view
    ->with('title', 'My Page')
    ->with('user', $currentUser)
    ->withData(['count' => 10, 'items' => $items])
    ->render('dashboard');
```

## Template File Paths

Templates are resolved relative to `src/Views/`:

- `'entities/show'` → `src/Views/entities/show.php`
- `'layouts/base'` → `src/Views/layouts/base.php`
- `'partials/header'` → `src/Views/partials/header.php`

## Variable Extraction

All data passed to templates is extracted to individual variables:

```php
$view->render('entities/show', [
    'entity' => $entity,
    'title' => 'Details'
]);
```

Inside the template:

```php
<!-- $entity and $title are available -->
<h1><?= htmlspecialchars($title) ?></h1>
<p><?= htmlspecialchars($entity['name']) ?></p>
```

## Special Variable: `$this`

Inside templates, `$this` refers to the View instance:

```php
<!-- Available methods -->
<?php $this->extends('layouts/base') ?>
<?php $this->section('content') ?>
<?php $this->endSection() ?>
<?php $this->yield('section') ?>
<?php $this->include('partials/header') ?>
<?php $this->hasSection('name') ?>
<?php $this->getSection('name') ?>
```

## Error Handling

```php
try {
    $html = $view->render('nonexistent/template');
} catch (RuntimeException $e) {
    // Template not found
    echo "Error: " . $e->getMessage();
}
```

## Advanced Usage

### Custom Views Path

```php
$view = new View('/custom/path/to/views');
```

### Clearing Sections

```php
$view->clearSections();
```

### Checking Views Path

```php
$path = $view->getViewsPath();
```

## Complete Example

**Controller:**

```php
use Worlds\Config\View;
use Worlds\Config\Response;

public function show(Request $request, array $params): Response
{
    $entity = $this->entityRepository->findById($params['id']);

    if (!$entity) {
        return Response::error(404, 'Entity not found');
    }

    $view = new View();
    $html = $view->render('entities/show', [
        'entity' => $entity,
        'relatedEntities' => $this->entityRepository->findRelated($entity['id'])
    ]);

    return Response::html($html);
}
```

**Template (`src/Views/entities/show.php`):**

```php
<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
<?= htmlspecialchars($entity['name']) ?> - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<article>
    <header>
        <h1><?= htmlspecialchars($entity['name']) ?></h1>
        <span class="badge"><?= htmlspecialchars($entity['entity_type']) ?></span>
    </header>

    <div class="content">
        <?= nl2br(htmlspecialchars($entity['entry'])) ?>
    </div>

    <?php if (!empty($relatedEntities)): ?>
    <section class="related">
        <h2>Related Entities</h2>
        <?php foreach ($relatedEntities as $related): ?>
            <?php $this->include('partials/entity-card', ['entity' => $related]) ?>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>
</article>
<?php $this->endSection() ?>
```

## Security Notes

- **Always escape output**: Use `htmlspecialchars()` for user data
- **Never trust user input**: Sanitize before rendering
- **Be careful with `extract()`**: The View class uses `EXTR_SKIP` to prevent overwriting existing variables

## Best Practices

1. **Keep templates simple** - Complex logic belongs in controllers
2. **Use partials** - Break large templates into reusable components
3. **Always escape** - Use `htmlspecialchars()` for all dynamic content
4. **Consistent naming** - Use kebab-case for template files
5. **Organize by feature** - Group related templates in subdirectories
