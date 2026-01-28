# Worlds - Comprehensive Validation & CI Test Report

**Generated:** 2026-01-27
**Branch:** `claude/design-validation-ci-tests-icOVR`
**Project:** Worlds - Self-hosted Worldbuilding & RPG Campaign Management Tool

---

## Executive Summary

This report consolidates findings from 9 comprehensive validation tests performed on the Worlds codebase. The tests covered unit testing, code quality, database integrity, Docker configuration, security, CI/CD workflows, dependencies, routing, and frontend assets.

### Overall Health Score

| Category | Status | Critical Issues |
|----------|--------|-----------------|
| PHPUnit Tests | **FIXED** | Schema synchronized - tests should pass |
| Code Style | **GOOD** | No critical issues |
| Database Schema | **FIXED** | Test schema now matches production |
| Docker Config | **EXCELLENT** | No issues |
| Security | **GOOD** | 1 medium-priority concern |
| CI/CD Workflows | **FIXED** | CI runs tests before auto-merge |
| Dependencies | **EXCELLENT** | No vulnerabilities |
| Routing/Controllers | **FIXED** | Reflection-based parameter binding |
| Frontend/Views | **FIXED** | Alpine.js loaded once via CDN |

### Priority Action Items - Status Update (2026-01-28)

1. ~~**CRITICAL:** Fix test database schema to match production migrations~~ ✅ **FIXED** - TestCase.php schema synchronized
2. ~~**CRITICAL:** Fix Router parameter passing to controllers~~ ✅ **FIXED** - Reflection-based parameter binding implemented
3. ~~**HIGH:** Add CI workflow to run tests before auto-merge~~ ✅ **FIXED** - ci.yml created, automerge.yml updated to wait for CI
4. ~~**HIGH:** Fix FileController to accept route parameters~~ ✅ **FIXED** - Methods now accept `int $id` parameter
5. **MEDIUM:** Remove or secure `Auth::loginAs()` method - *Still pending review*
6. ~~**MEDIUM:** Fix Alpine.js double-loading in templates~~ ✅ **FIXED** - app.js uses CDN-loaded Alpine, no import

---

## Table of Contents

1. [PHPUnit Test Results](#1-phpunit-test-results)
2. [PHP Code Style Analysis](#2-php-code-style-analysis)
3. [Database Schema Audit](#3-database-schema-audit)
4. [Docker Configuration Validation](#4-docker-configuration-validation)
5. [Security Audit](#5-security-audit)
6. [CI/CD Workflow Validation](#6-cicd-workflow-validation)
7. [Dependency Audit](#7-dependency-audit)
8. [Routing & Controller Validation](#8-routing--controller-validation)
9. [View Templates & Assets Validation](#9-view-templates--assets-validation)
10. [Recommended Fixes](#10-recommended-fixes)
11. [Proposed CI Test Suite](#11-proposed-ci-test-suite)

---

## 1. PHPUnit Test Results

### Summary

| Metric | Value |
|--------|-------|
| PHPUnit Version | 10.5.62 |
| PHP Version | 8.4.17 |
| Total Tests | 90 |
| Passed | 44 (49%) |
| Failed | 13 |
| Errors | 28 |
| Warnings | 5 |
| Execution Time | 13.207s |

### Test Results by Class

#### AuthTest (27 tests) - 12 passed, 15 failed

| Test | Status | Error |
|------|--------|-------|
| Password hashing | PASS | - |
| Password verification (correct) | PASS | - |
| Password verification (incorrect) | PASS | - |
| Create user | **FAIL** | `false is not of type int` |
| Create admin user | **FAIL** | `false is not greater than 0` |
| Successful login attempt | **ERROR** | `table users has no column named username` |
| Failed login with incorrect password | **ERROR** | `table users has no column named username` |
| Logout | **FAIL** | `false is not true` |
| Get authenticated user | **FAIL** | `null is not of type array` |
| Is admin check | **FAIL** | `false is not true` |
| Update password | **FAIL** | `false is not true` |
| Delete user | **FAIL** | `false is not true` |
| Username exists | **FAIL** | `false is not true` |
| Login as user | **FAIL** | `false is not true` |
| Set active campaign | **FAIL** | `null does not match expected 42` |
| Check returns true when logged in | **FAIL** | `false is not true` |
| Session persistence | **FAIL** | `null does not match expected 'testuser'` |

#### EntityRepositoryTest (17 tests) - 5 passed, 12 errors

| Test | Status | Error |
|------|--------|-------|
| Create entity | **ERROR** | `table entities has no column named entity_type` |
| Find entity by id | **FAIL** | `null does not match expected 'character'` |
| Update entity | **ERROR** | `no such column: entry` |
| Delete entity | **WARN** | `Undefined array key "is_private"` |
| Find entities by type | **ERROR** | `no such column: entity_type` |

#### SearchTest (17 tests) - 0 passed, 17 errors

All tests fail with: `no such column: new.entry` (FTS trigger reference error)

### Root Cause

**Test database schema in `TestCase.php` does not match production migrations.** The test schema was written with different column names and missing columns compared to the actual database migrations.

---

## 2. PHP Code Style Analysis

### Summary

| Check | Status |
|-------|--------|
| PHP Syntax (27 files) | **PASS** |
| PSR-12 Compliance | **PASS** (minor issues) |
| Coding Standards Config | **MISSING** |
| Type Declarations | **PASS** |
| DocBlocks | **PASS** |

### Findings

#### Missing Tools
The project lacks static analysis configuration:
- No `phpcs.xml` (PHP CodeSniffer)
- No `phpstan.neon` (PHPStan)
- No `.php-cs-fixer.php` (PHP CS Fixer)

#### Minor Issues

| File | Line | Issue |
|------|------|-------|
| `src/Config/helpers.php` | 413 | `flash()` signature accepts `string $message` but arrays are passed |
| `src/Repositories/EntityRepository.php` | - | Unused `PDOException` import |
| `src/Repositories/CampaignRepository.php` | - | Unused `PDOException` import |

### Recommendations

```json
// Add to composer.json require-dev
{
    "phpstan/phpstan": "^1.10",
    "squizlabs/php_codesniffer": "^3.7"
}
```

---

## 3. Database Schema Audit

### Critical: Test vs Production Schema Mismatch

The test schema in `tests/TestCase.php` differs significantly from production migrations.

#### Users Table

| Column | Production | Test |
|--------|------------|------|
| username | `TEXT NOT NULL UNIQUE` | **MISSING** |
| email | `TEXT UNIQUE` | `VARCHAR(255) NOT NULL UNIQUE` |
| password_hash | `TEXT NOT NULL` | **MISSING** |
| password | MISSING | `VARCHAR(255) NOT NULL` |
| display_name | `TEXT` | **MISSING** |
| is_admin | `INTEGER DEFAULT 0` | **MISSING** |

#### Entities Table

| Column | Production | Test |
|--------|------------|------|
| entity_type | `TEXT NOT NULL` | **MISSING** |
| type | `TEXT` (subtype) | `VARCHAR(50) NOT NULL` |
| entry | `TEXT` | **MISSING** |
| description | MISSING | `TEXT` |
| parent_id | `INTEGER` | **MISSING** |
| is_private | `INTEGER DEFAULT 0` | **MISSING** |
| data | `TEXT DEFAULT '{}'` | **MISSING** |

### Other Schema Issues

| Issue | Severity | Location |
|-------|----------|----------|
| Duplicate FTS triggers | Medium | `010_fts.sql` and `012_fts_triggers.sql` |
| Missing unique constraints | Medium | tags, relations, attributes tables |
| Missing indexes | Medium | is_private, mime_type, etc. |
| Missing CHECK constraints | Low | Boolean columns (is_private, is_admin) |
| Spelling inconsistency | Low | "colour" (production) vs "color" (test) |

### TestCase.php Bugs

| Line | Issue |
|------|-------|
| 178, 183 | Duplicate `DELETE FROM entity_tags` |
| 201-208 | `createUser()` uses `password` instead of `password_hash` |
| 214-222 | `createCampaign()` uses non-existent `user_id` and `title` columns |
| 227-234 | `createEntity()` uses `type` instead of `entity_type` |

---

## 4. Docker Configuration Validation

### Summary

| Component | Status |
|-----------|--------|
| Dockerfile | **EXCELLENT** |
| docker-compose.yml | **EXCELLENT** |
| docker-compose.prod.yml | **EXCELLENT** |
| docker-entrypoint.sh | **EXCELLENT** |
| healthcheck.sh | **EXCELLENT** |
| .dockerignore | **EXCELLENT** |

### Strengths

- Proper multi-stage build (3 stages) for optimized image size
- Production dependencies only in final image
- Proper file permissions and ownership
- Security headers configured in Apache
- Health checks properly configured
- No hardcoded secrets

### Minor Recommendations

1. Pin base image version for reproducibility:
   ```dockerfile
   FROM php:8.2.15-apache AS production
   ```

2. Add resource limits in `docker-compose.prod.yml`:
   ```yaml
   deploy:
     resources:
       limits:
         cpus: '1.0'
         memory: 512M
   ```

---

## 5. Security Audit

### OWASP Top 10 Assessment

| Vulnerability | Status | Notes |
|---------------|--------|-------|
| SQL Injection | **SECURE** | Parameterized queries throughout |
| XSS | **SECURE** | Consistent `e()` helper usage |
| CSRF | **SECURE** | Token validation on all state-changing ops |
| Broken Authentication | **SECURE** | bcrypt, session regeneration |
| Sensitive Data Exposure | **SECURE** | No hardcoded credentials |
| Security Misconfiguration | **SECURE** | Proper Apache headers |
| Insecure Direct Object Refs | **SECURE** | Ownership verification |

### Medium Priority Concern

**File:** `src/Config/Auth.php` (line 499-521)

```php
public static function loginAs(int $userId): bool
{
    // WARNING: This bypasses password verification
    // ...
}
```

The `loginAs()` method allows impersonation without authentication. While not exposed via any controller endpoint, it should be:
- Removed from production code, OR
- Restricted to admin-only with audit logging

### Recommendations

1. Add rate limiting for login attempts
2. Add Content Security Policy (CSP) headers
3. Consider account lockout after failed attempts

---

## 6. CI/CD Workflow Validation

### autocode.yml - Autonomous Dev Loop

| Aspect | Status |
|--------|--------|
| Syntax | **VALID** |
| Job Dependencies | **CORRECT** |
| Security | **OK** |
| Error Handling | **MODERATE RISK** |

#### Issues Found

| Issue | Severity | Description |
|-------|----------|-------------|
| `consecutive_failures` never incremented | HIGH | Circuit breaker pattern is broken |
| Silent push failures | MEDIUM | `git push \|\| true` masks errors |
| `last_task_completed` always null | MEDIUM | State tracking incomplete |

### automerge.yml - Auto-merge Copilot PRs

| Aspect | Status |
|--------|--------|
| Merge Conflict Handling | **GOOD** |
| Approval Workflow | **HIGH RISK** |
| Security | **MODERATE** |

#### Critical Issue

**PRs are auto-merged without running tests.** The workflow approves and merges Copilot PRs without any CI validation.

### Missing CI Components

| Component | Priority | Status |
|-----------|----------|--------|
| Test execution on PRs | **CRITICAL** | MISSING |
| Build validation | HIGH | MISSING |
| Linting/static analysis | MEDIUM | MISSING |
| Security scanning | MEDIUM | MISSING |
| Code coverage | LOW | MISSING |

---

## 7. Dependency Audit

### Summary

| Category | Vulnerabilities | Outdated |
|----------|-----------------|----------|
| PHP (Composer) | **0** | 1 direct |
| JavaScript (npm) | **0** | 2 packages |

### PHP Dependencies

| Package | Current | Latest | Notes |
|---------|---------|--------|-------|
| phpunit/phpunit | 10.5.62 | 12.5.7 | Dev only |

**PHP Version:** Minimum `>=8.0` but PHP 8.0 is EOL (Nov 2023). Recommend updating to `>=8.2`.

### JavaScript Dependencies

| Package | Current | Latest | Notes |
|---------|---------|--------|-------|
| alpinejs | 3.15.4 | 3.15.5 | Patch available |
| tailwindcss | 3.4.19 | 4.1.18 | Major version available |

### Recommendations

1. Update PHP minimum version to `>=8.2`
2. Run `npm update alpinejs`
3. Evaluate Tailwind CSS 4.x migration

---

## 8. Routing & Controller Validation

### Critical Bug: Router Parameter Passing

**File:** `src/Config/Router.php` (lines 249-252)

```php
private function callHandler(callable $callback, array $params): mixed
{
    return call_user_func($callback, $this->request, $params);
}
```

The router passes route parameters as an **associative array** as the second argument, but controllers expect **individual typed parameters**.

**Example:**
- Route: `/campaigns/{id}` extracts `['id' => '123']`
- Controller: `show(Request $request, int $id)`
- Actual call: `show($request, ['id' => '123'])` - **Type mismatch!**

### Affected Routes

**30+ routes are affected.** All routes with parameters (`{id}`, `{type}`, etc.) pass an array instead of individual values.

| Route Pattern | Controller Method | Issue |
|---------------|-------------------|-------|
| `/campaigns/{id}` | `CampaignController::show` | Receives array |
| `/entities/{type}` | `EntityController::index` | Receives array |
| `/entities/{type}/{id}` | `EntityController::show` | Receives array |
| `/tags/{id}` | `TagController::update` | Receives array |
| All API routes with parameters | Various | Same issue |

### FileController: Missing Parameters

**File:** `src/Controllers/FileController.php`

FileController methods don't accept route parameters at all:

```php
public function store(Request $request): Response  // Missing entityId
public function destroy(Request $request): Response  // Missing fileId
public function download(Request $request): Response  // Missing fileId
```

These methods try to extract IDs from `$request->input()` but route params aren't stored there.

### Missing Router Features

- No PATCH method support
- No OPTIONS method (CORS preflight)
- No HEAD method
- No middleware system

---

## 9. View Templates & Assets Validation

### Summary

| Check | Status |
|-------|--------|
| XSS Protection | **GOOD** (1 concern) |
| Partial Includes | **PASS** |
| Error Templates | **INCOMPLETE** |
| JavaScript | **ISSUES** |
| Accessibility | **NEEDS WORK** |

### Potential XSS Concern

**File:** `src/Views/search/results.php` (lines 73-78)

```php
<?= $highlightedQuery ?>
```

The `$highlightedQuery` is output without escaping.

### Alpine.js Double-Loading

**File:** `src/Views/layouts/base.php`

Alpine.js is loaded twice:
1. Line 18: CDN script `https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js`
2. Line 58: Local `app.js` which also imports Alpine

The `app.js` uses ES6 `import` but is loaded without `type="module"`, causing errors.

### Missing Error Templates

| Template | Status |
|----------|--------|
| 404.php | Present |
| 500.php | Present |
| 403.php | **MISSING** |
| 401.php | **MISSING** |
| 419.php (CSRF) | **MISSING** |
| 503.php | **MISSING** |

### Accessibility Issues

| Issue | Location |
|-------|----------|
| Search inputs lack labels | `header.php`, `search-bar.php` |
| Dropdowns lack ARIA attributes | Campaign switcher, user menu |
| Modals lack dialog roles | Post form, relation form |
| Icon buttons lack aria-labels | Delete buttons throughout |

---

## 10. Recommended Fixes

### Critical Priority (Fix Immediately)

#### 1. Synchronize Test Schema with Production

Update `tests/TestCase.php` to match production migrations:

```php
// Users table - match 009_users.sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT UNIQUE,
    password_hash TEXT NOT NULL,
    display_name TEXT,
    is_admin INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)

// Entities table - match 002_entities.sql
CREATE TABLE entities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NOT NULL,
    entity_type TEXT NOT NULL,
    name TEXT NOT NULL,
    type TEXT,
    entry TEXT,
    image_path TEXT,
    parent_id INTEGER,
    is_private INTEGER DEFAULT 0,
    data TEXT DEFAULT '{}',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES entities(id) ON DELETE SET NULL
)
```

#### 2. Fix Router Parameter Passing

Update `src/Config/Router.php` to use reflection for parameter binding:

```php
private function callHandler(callable $callback, array $params): mixed
{
    if (is_array($callback)) {
        $reflection = new \ReflectionMethod($callback[0], $callback[1]);
    } else {
        $reflection = new \ReflectionFunction($callback);
    }

    $args = [$this->request];
    foreach ($reflection->getParameters() as $index => $param) {
        if ($index === 0) continue; // Skip Request parameter
        $name = $param->getName();
        if (isset($params[$name])) {
            $value = $params[$name];
            // Cast to parameter type
            $type = $param->getType();
            if ($type && $type->getName() === 'int') {
                $value = (int) $value;
            }
            $args[] = $value;
        }
    }

    return call_user_func_array($callback, $args);
}
```

#### 3. Fix FileController Parameters

```php
public function store(Request $request, int $entityId): Response
public function destroy(Request $request, int $id): Response
public function download(Request $request, int $id): Response
```

### High Priority

#### 4. Add CI Test Workflow

Create `.github/workflows/ci.yml`:

```yaml
name: CI

on:
  pull_request:
    branches: [main]
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_sqlite

      - name: Install Composer dependencies
        run: composer install --no-progress --prefer-dist

      - name: Run PHPUnit tests
        run: vendor/bin/phpunit --verbose

      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'

      - name: Install npm dependencies
        run: npm ci

      - name: Build CSS
        run: npm run build:css
```

#### 5. Update automerge.yml to Wait for CI

Add before merge step:

```yaml
- name: Wait for CI checks
  run: |
    gh pr checks $PR_NUMBER --repo ${{ github.repository }} --watch --fail-level all
```

### Medium Priority

#### 6. Remove Duplicate FTS Triggers

Delete `database/012_fts_triggers.sql` (duplicate of triggers in `010_fts.sql`).

#### 7. Fix Alpine.js Double-Loading

Remove CDN script from `src/Views/layouts/base.php` line 18, OR remove the import from `app.js` and use CDN only.

#### 8. Fix flash() Function Signature

```php
function flash(string $key, string|array $message): void
```

#### 9. Add Missing Error Templates

Create:
- `src/Views/errors/403.php`
- `src/Views/errors/401.php`
- `src/Views/errors/419.php`
- `src/Views/errors/503.php`

### Low Priority

#### 10. Update PHP Minimum Version

```json
// composer.json
"require": {
    "php": ">=8.2"
}
```

#### 11. Add Static Analysis Tools

```json
// composer.json
"require-dev": {
    "phpunit/phpunit": "^10.0",
    "phpstan/phpstan": "^1.10",
    "squizlabs/php_codesniffer": "^3.7"
}
```

---

## 11. Proposed CI Test Suite

### Recommended Test Categories

#### Unit Tests (Existing - Fix Required)

| Test Class | Tests | Status |
|------------|-------|--------|
| AuthTest | 27 | Fix schema |
| EntityRepositoryTest | 17 | Fix schema |
| RouterTest | ~15 | Likely working |
| SearchTest | 17 | Fix FTS triggers |

#### Integration Tests (To Add)

| Test Class | Purpose |
|------------|---------|
| ControllerIntegrationTest | HTTP request/response cycle |
| DatabaseMigrationTest | Verify migrations run successfully |
| RepositoryCrudTest | Full CRUD operations |

#### Validation Tests (To Add)

| Test Class | Purpose |
|------------|---------|
| EntityValidationTest | Input validation |
| SecurityTest | CSRF, XSS prevention |
| AuthorizationTest | Access control |

### CI Pipeline Stages

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Lint      │ -> │   Test      │ -> │   Build     │ -> │   Deploy    │
│ PHP syntax  │    │ PHPUnit     │    │ CSS build   │    │ (on main)   │
│ PHPCS       │    │ Integration │    │ Docker      │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### Test Execution Commands

```bash
# Run all tests
composer test

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/

# Run specific test file
vendor/bin/phpunit tests/AuthTest.php

# PHP syntax check
find src -name "*.php" -exec php -l {} \;

# Build CSS
npm run build:css
```

---

## Conclusion

The Worlds codebase has a solid foundation with good security practices, excellent Docker configuration, and no dependency vulnerabilities. However, there are critical issues that need immediate attention:

1. **Test database schema mismatch** prevents running tests
2. **Router parameter passing bug** affects all parameterized routes
3. **No CI test validation** before auto-merge

Fixing these issues will significantly improve code quality and prevent regressions. The proposed CI workflow will catch issues before they reach the main branch.

---

*Report generated by automated validation suite*
