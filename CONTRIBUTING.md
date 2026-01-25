# Contributing to Worlds

Thank you for your interest in contributing! This guide explains how to contribute code, report bugs, and participate in the Worlds project.

## Code of Conduct

Be respectful, inclusive, and professional in all interactions. We're building a welcoming community for everyone.

## Getting Started

### 1. Set Up Development Environment

Follow the installation guide:

- **For native setup**: See [INSTALL.md](INSTALL.md)
- **For Docker setup**: See [DOCKER.md](DOCKER.md)

### 2. Create a Feature Branch

Create a new branch for your work:

```bash
git checkout -b feature/your-feature-name
```

Branch naming conventions:
- `feature/description` - New features
- `fix/description` - Bug fixes
- `docs/description` - Documentation updates
- `refactor/description` - Code refactoring
- `test/description` - Test additions

## Code Style Guidelines

### PHP (PSR-12)

All PHP code must follow **PSR-12: Extended Coding Style Guide**.

Key rules:

- 4 spaces for indentation (not tabs)
- Maximum 120 characters per line
- One statement per line
- Spaces around operators: `$x = 1 + 2;`
- Opening braces on same line for classes/functions

Example:

```php
<?php
namespace Worlds\Controllers;

use Worlds\Models\Character;

class CharacterController
{
    public function index()
    {
        $characters = Character::all();

        return view('characters.index', [
            'characters' => $characters
        ]);
    }

    public function store($request)
    {
        $validated = $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|nullable'
        ]);

        $character = Character::create($validated);

        return redirect()->to("/character/{$character->id}");
    }
}
```

### PHP Naming Conventions

- **Classes**: PascalCase (e.g., `CharacterController`)
- **Methods/Functions**: camelCase (e.g., `getCharacter()`)
- **Properties**: camelCase (e.g., `$characterName`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_NAME_LENGTH`)

### JavaScript/HTML

- 2 spaces for indentation
- Use semantic HTML5 elements
- Use Alpine.js for interactivity
- Follow Tailwind CSS conventions

Example:

```html
<div class="card p-6 bg-white rounded-lg shadow">
  <h2 class="text-2xl font-bold mb-4">{{ character.name }}</h2>

  <button
    @click="deleteCharacter()"
    class="btn btn-danger"
  >
    Delete
  </button>
</div>
```

### CSS

Use Tailwind CSS utility classes. Avoid custom CSS unless necessary.

```html
<!-- Good: Use Tailwind utilities -->
<div class="flex items-center gap-4 p-4 bg-gray-50 rounded">
  <img src="avatar.jpg" class="w-12 h-12 rounded-full">
  <div>
    <h3 class="font-bold">John Doe</h3>
    <p class="text-gray-500">Character</p>
  </div>
</div>

<!-- Avoid: Don't create custom CSS classes for Tailwind-able styles -->
<!-- Bad: <div class="character-card"> ... </div> -->
```

## Testing

### Writing Tests

All new features should include tests. Use PHPUnit:

```php
<?php
namespace Worlds\Tests;

use PHPUnit\Framework\TestCase;
use Worlds\Models\Character;

class CharacterTest extends TestCase
{
    public function testCharacterCanBeCreated()
    {
        $character = Character::create([
            'name' => 'Aragorn',
            'description' => 'Ranger and King'
        ]);

        $this->assertNotNull($character->id);
        $this->assertEquals('Aragorn', $character->name);
    }

    public function testCharacterRequiresName()
    {
        $this->expectException(ValidationException::class);

        Character::create([
            'description' => 'Missing name'
        ]);
    }
}
```

### Running Tests

```bash
# Run all tests
composer run test

# Run specific test class
composer run test tests/CharacterTest.php

# Run with coverage
composer run test --coverage-html coverage/
```

## Pull Request Process

### 1. Before Starting Work

Check if someone else is already working on this:

```bash
# See existing issues and PRs on GitHub
# Avoid duplicate work
```

### 2. Write Your Code

Follow the style guidelines above. Commit regularly with clear messages.

### 3. Run Tests Locally

Ensure all tests pass:

```bash
composer run test
```

### 4. Build CSS (if frontend changes)

```bash
npm run build:css
```

### 5. Push Your Branch

```bash
git push origin feature/your-feature-name
```

### 6. Create a Pull Request

On GitHub, create a PR with:

- **Title**: Clear, descriptive title
  - Good: "Add ability to filter characters by race"
  - Bad: "Fix stuff"

- **Description**: Explain what changed and why
  ```
  ## Summary
  Adds filtering functionality to the characters list page.

  ## Changes
  - Added `race_id` filter parameter to CharacterRepository
  - Updated character index view with filter dropdown
  - Added tests for race filtering

  ## Testing
  - All tests pass
  - Manually tested filter with multiple races

  ## Screenshots (if UI changes)
  [Include before/after screenshots]
  ```

- **Link to issue**: If fixing an issue, reference it
  ```
  Fixes #42
  ```

### 7. Code Review

A maintainer will review your code. Be prepared to:
- Answer questions about your implementation
- Make requested changes
- Update tests if needed

### 8. Merge

Once approved, your PR will be merged into `main`.

## Reporting Bugs

### 1. Check Existing Issues

Search GitHub issues to avoid duplicates.

### 2. Create a New Issue

Include:

- **Title**: Brief, descriptive title
  - Good: "Characters cannot be deleted if they have family links"
  - Bad: "Bug"

- **Description**: Detailed explanation
  ```
  ## Description
  When I try to delete a character, I get a database error.

  ## Steps to Reproduce
  1. Create a character
  2. Add the character to a family
  3. Try to delete the character
  4. Get error: "Foreign key constraint failed"

  ## Expected Behavior
  Character should be deleted and removed from family references

  ## Actual Behavior
  Database error prevents deletion

  ## Environment
  - OS: macOS 13.0
  - PHP: 8.1.0
  - Browser: Chrome 120
  ```

- **Screenshots**: If UI-related
- **Error logs**: Stack trace or error messages

## Documentation Updates

### Updating Existing Docs

Edit the relevant `.md` file and follow the same PR process.

### Creating New Docs

Place in appropriate directory:
- `documentation/` - Project-level documentation
- `docs/` - Development/technical documentation

Use Markdown format with:
- Clear headings and structure
- Code examples where applicable
- Links to related documentation

## Entity Schema Changes

If you modify entity schemas in `src/Config/schemas/`:

1. Update the schema JSON file
2. Update `documentation/entity-schemas.md`
3. Add database migration if needed
4. Include tests

Example schema modification:

```json
{
    "$schema": "http://json-schema.org/draft-07/schema#",
    "title": "Character",
    "type": "object",
    "properties": {
        "name": {
            "type": "string",
            "description": "Character's full name"
        },
        "new_field": {
            "type": "string",
            "description": "New field description"
        }
    }
}
```

## Commit Message Guidelines

Write clear, descriptive commit messages:

```
# Good
feat: add character race filtering to list view

docs: update installation instructions for PHP 8.1

fix: resolve database foreign key constraint on character deletion

refactor: extract validation logic into separate class

test: add tests for quest status transitions

# Bad
update stuff
fix bug
changes
wip
```

Format: `type: description`

Types:
- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation
- `test:` - Tests
- `refactor:` - Code refactoring
- `chore:` - Build, dependencies, tooling

## Performance Considerations

When contributing code:

- **Database queries**: Use eager loading to avoid N+1 problems
  ```php
  // Bad: causes 1 + N queries
  $characters = Character::all();
  foreach ($characters as $char) {
      echo $char->race->name;
  }

  // Good: single query with join
  $characters = Character::with('race')->get();
  ```

- **Frontend**: Minimize re-renders, lazy-load data
- **Caching**: Consider caching expensive operations

## Development Commands Reference

```bash
# Install/update dependencies
composer install
npm install

# Run tests
composer run test

# Build CSS
npm run build:css
npm run watch:css

# Start development server
php -S localhost:8080 -t public/

# Using Docker
docker-compose up
docker-compose exec web bash

# Git workflows
git checkout -b feature/name
git add .
git commit -m "type: description"
git push origin feature/name
```

## Questions?

- Check existing documentation in `documentation/`
- Review the task list: `documentation/kanka-task-list.md`
- Open an issue for clarification

## License

By contributing to Worlds, you agree that your contributions are licensed under the same license as the project.

Thank you for contributing!
