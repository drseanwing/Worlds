# Kanka Lightweight Alternative - Task List

## Project Overview

**Goal:** Reproduce Kanka's worldbuilding functionality with minimal stack complexity  
**Stack:** PHP 8 + SQLite + Alpine.js + Tailwind CSS + HTMX  
**Architecture:** Polymorphic entity model with JSON data fields

---

## Phase 1: Project Foundation

### 1.1 Project Setup

- [ ] 1.1.1 Create project directory structure
- [ ] 1.1.2 Initialise Git repository
- [ ] 1.1.3 Create `.gitignore` file for PHP/SQLite project
- [ ] 1.1.4 Create `composer.json` with minimal dependencies
- [ ] 1.1.5 Install Composer dependencies
- [ ] 1.1.6 Create `package.json` for frontend tooling
- [ ] 1.1.7 Install npm dependencies (Tailwind, Alpine)
- [ ] 1.1.8 Create environment configuration file (`.env.example`)
- [ ] 1.1.9 Create configuration loader class
- [ ] 1.1.10 Set up directory permissions for uploads and database

### 1.2 Docker Environment

- [ ] 1.2.1 Create `Dockerfile` for PHP 8 + Apache
- [ ] 1.2.2 Create `docker-compose.yml` for local development
- [ ] 1.2.3 Configure Apache virtual host for clean URLs
- [ ] 1.2.4 Add SQLite extension to PHP container
- [ ] 1.2.5 Mount volumes for persistent data (database, uploads)
- [ ] 1.2.6 Create `.dockerignore` file
- [ ] 1.2.7 Test container builds and runs correctly

### 1.3 Frontend Build Pipeline

- [ ] 1.3.1 Create `tailwind.config.js` configuration
- [ ] 1.3.2 Create base CSS file with Tailwind directives
- [ ] 1.3.3 Configure PostCSS for Tailwind processing
- [ ] 1.3.4 Create npm build script for CSS compilation
- [ ] 1.3.5 Create npm watch script for development
- [ ] 1.3.6 Generate initial compiled CSS file
- [ ] 1.3.7 Create JavaScript entry point with Alpine.js initialisation

---

## Phase 2: Database Layer

### 2.1 Schema Design

- [ ] 2.1.1 Create SQL file for `campaigns` table
- [ ] 2.1.2 Create SQL file for `entities` table (polymorphic)
- [ ] 2.1.3 Create SQL file for `relations` table
- [ ] 2.1.4 Create SQL file for `tags` table
- [ ] 2.1.5 Create SQL file for `entity_tags` junction table
- [ ] 2.1.6 Create SQL file for `attributes` table
- [ ] 2.1.7 Create SQL file for `posts` table
- [ ] 2.1.8 Create SQL file for `files` table (attachments)
- [ ] 2.1.9 Create SQL file for `users` table (simple auth)
- [ ] 2.1.10 Create SQL file for FTS5 virtual table (`entities_fts`)
- [ ] 2.1.11 Create SQL file for database indexes
- [ ] 2.1.12 Create master migration script combining all SQL files

### 2.2 Database Connection

- [ ] 2.2.1 Create Database singleton class
- [ ] 2.2.2 Implement PDO connection with SQLite
- [ ] 2.2.3 Configure PDO error mode to exceptions
- [ ] 2.2.4 Enable foreign key enforcement
- [ ] 2.2.5 Create database file if not exists
- [ ] 2.2.6 Implement migration runner method
- [ ] 2.2.7 Add query logging for development mode
- [ ] 2.2.8 Create database backup utility function

### 2.3 Entity Type Definitions

- [ ] 2.3.1 Create JSON schema file for Character fields
- [ ] 2.3.2 Create JSON schema file for Location fields
- [ ] 2.3.3 Create JSON schema file for Family fields
- [ ] 2.3.4 Create JSON schema file for Organisation fields
- [ ] 2.3.5 Create JSON schema file for Item fields
- [ ] 2.3.6 Create JSON schema file for Note fields
- [ ] 2.3.7 Create JSON schema file for Event fields
- [ ] 2.3.8 Create JSON schema file for Calendar fields
- [ ] 2.3.9 Create JSON schema file for Race fields
- [ ] 2.3.10 Create JSON schema file for Quest fields
- [ ] 2.3.11 Create JSON schema file for Journal fields
- [ ] 2.3.12 Create JSON schema file for Map fields
- [ ] 2.3.13 Create JSON schema file for Timeline fields
- [ ] 2.3.14 Create JSON schema file for Ability fields
- [ ] 2.3.15 Create JSON schema file for Conversation fields
- [ ] 2.3.16 Create JSON schema file for Creature fields
- [ ] 2.3.17 Create JSON schema file for Dice Roll fields
- [ ] 2.3.18 Create JSON schema file for Attribute Template fields
- [ ] 2.3.19 Create EntityType registry class to load all schemas
- [ ] 2.3.20 Implement schema validation method

---

## Phase 3: Core Backend

### 3.1 Routing System

- [ ] 3.1.1 Create Router class
- [ ] 3.1.2 Implement route registration method (GET)
- [ ] 3.1.3 Implement route registration method (POST)
- [ ] 3.1.4 Implement route registration method (PUT/PATCH)
- [ ] 3.1.5 Implement route registration method (DELETE)
- [ ] 3.1.6 Implement URL parameter extraction (e.g., `/entity/{id}`)
- [ ] 3.1.7 Implement query string parsing
- [ ] 3.1.8 Create route dispatcher method
- [ ] 3.1.9 Implement 404 handler
- [ ] 3.1.10 Implement 405 method not allowed handler
- [ ] 3.1.11 Create `.htaccess` for Apache URL rewriting
- [ ] 3.1.12 Create front controller (`index.php`)

### 3.2 Request/Response Handling

- [ ] 3.2.1 Create Request class
- [ ] 3.2.2 Implement method to get request method
- [ ] 3.2.3 Implement method to get request path
- [ ] 3.2.4 Implement method to get query parameters
- [ ] 3.2.5 Implement method to get POST body
- [ ] 3.2.6 Implement method to get JSON body
- [ ] 3.2.7 Implement method to get uploaded files
- [ ] 3.2.8 Create Response class
- [ ] 3.2.9 Implement HTML response method
- [ ] 3.2.10 Implement JSON response method
- [ ] 3.2.11 Implement redirect response method
- [ ] 3.2.12 Implement file download response method
- [ ] 3.2.13 Implement status code setting

### 3.3 Template Engine

- [ ] 3.3.1 Create View class
- [ ] 3.3.2 Implement template file loading
- [ ] 3.3.3 Implement variable passing to templates
- [ ] 3.3.4 Implement template inheritance (layouts)
- [ ] 3.3.5 Implement section/yield functionality
- [ ] 3.3.6 Implement partial/include functionality
- [ ] 3.3.7 Create HTML escaping helper function
- [ ] 3.3.8 Create URL generation helper function
- [ ] 3.3.9 Create asset URL helper function
- [ ] 3.3.10 Create CSRF token helper function

### 3.4 Authentication

- [ ] 3.4.1 Create Auth class
- [ ] 3.4.2 Implement session start/configuration
- [ ] 3.4.3 Implement password hashing (registration)
- [ ] 3.4.4 Implement password verification (login)
- [ ] 3.4.5 Implement login method (set session)
- [ ] 3.4.6 Implement logout method (destroy session)
- [ ] 3.4.7 Implement `isLoggedIn()` check
- [ ] 3.4.8 Implement `currentUser()` getter
- [ ] 3.4.9 Create authentication middleware
- [ ] 3.4.10 Create login form template
- [ ] 3.4.11 Create registration form template
- [ ] 3.4.12 Create AuthController with login/logout/register actions

---

## Phase 4: Entity CRUD

### 4.1 Entity Repository

- [ ] 4.1.1 Create EntityRepository class
- [ ] 4.1.2 Implement `findById()` method
- [ ] 4.1.3 Implement `findByType()` method (list by entity type)
- [ ] 4.1.4 Implement `findByCampaign()` method
- [ ] 4.1.5 Implement `findByParent()` method (children)
- [ ] 4.1.6 Implement `create()` method
- [ ] 4.1.7 Implement `update()` method
- [ ] 4.1.8 Implement `delete()` method
- [ ] 4.1.9 Implement `search()` method using FTS5
- [ ] 4.1.10 Implement pagination helper
- [ ] 4.1.11 Implement sorting options
- [ ] 4.1.12 Implement filtering by tags
- [ ] 4.1.13 Create FTS5 trigger for insert
- [ ] 4.1.14 Create FTS5 trigger for update
- [ ] 4.1.15 Create FTS5 trigger for delete

### 4.2 Entity Controller

- [ ] 4.2.1 Create EntityController class
- [ ] 4.2.2 Implement `index()` action (list entities by type)
- [ ] 4.2.3 Implement `show()` action (view single entity)
- [ ] 4.2.4 Implement `create()` action (show form)
- [ ] 4.2.5 Implement `store()` action (save new entity)
- [ ] 4.2.6 Implement `edit()` action (show edit form)
- [ ] 4.2.7 Implement `update()` action (save changes)
- [ ] 4.2.8 Implement `destroy()` action (delete entity)
- [ ] 4.2.9 Implement validation for required fields
- [ ] 4.2.10 Implement validation for JSON data fields
- [ ] 4.2.11 Implement flash message handling
- [ ] 4.2.12 Register entity routes in router

### 4.3 Campaign Management

- [ ] 4.3.1 Create CampaignRepository class
- [ ] 4.3.2 Implement campaign CRUD methods
- [ ] 4.3.3 Create CampaignController class
- [ ] 4.3.4 Implement campaign listing action
- [ ] 4.3.5 Implement campaign creation action
- [ ] 4.3.6 Implement campaign settings action
- [ ] 4.3.7 Implement campaign switching logic
- [ ] 4.3.8 Store active campaign in session
- [ ] 4.3.9 Create campaign selection middleware
- [ ] 4.3.10 Register campaign routes

---

## Phase 5: UI Templates

### 5.1 Layout System

- [ ] 5.1.1 Create base layout template
- [ ] 5.1.2 Add HTML5 doctype and meta tags
- [ ] 5.1.3 Add Tailwind CSS stylesheet link
- [ ] 5.1.4 Add Alpine.js script tag
- [ ] 5.1.5 Add HTMX script tag
- [ ] 5.1.6 Create header component with navigation
- [ ] 5.1.7 Create sidebar component with entity type menu
- [ ] 5.1.8 Create footer component
- [ ] 5.1.9 Create flash message component
- [ ] 5.1.10 Create breadcrumb component
- [ ] 5.1.11 Implement dark mode toggle structure

### 5.2 Entity List Views

- [ ] 5.2.1 Create entity list template
- [ ] 5.2.2 Add table/grid view toggle
- [ ] 5.2.3 Create entity card component (grid view)
- [ ] 5.2.4 Create entity row component (table view)
- [ ] 5.2.5 Add pagination controls
- [ ] 5.2.6 Add search input field
- [ ] 5.2.7 Add filter by tag dropdown
- [ ] 5.2.8 Add sort by dropdown
- [ ] 5.2.9 Add "Create New" button
- [ ] 5.2.10 Create empty state component

### 5.3 Entity Detail View

- [ ] 5.3.1 Create entity detail template
- [ ] 5.3.2 Add entity header with image and name
- [ ] 5.3.3 Add entity type badge
- [ ] 5.3.4 Add tags display
- [ ] 5.3.5 Add main entry/description section
- [ ] 5.3.6 Add type-specific fields section
- [ ] 5.3.7 Add relations section
- [ ] 5.3.8 Add attributes section
- [ ] 5.3.9 Add posts section
- [ ] 5.3.10 Add files/attachments section
- [ ] 5.3.11 Add edit/delete action buttons
- [ ] 5.3.12 Add child entities section (if applicable)
- [ ] 5.3.13 Add "mentioned in" section

### 5.4 Entity Forms

- [ ] 5.4.1 Create entity form template (create/edit)
- [ ] 5.4.2 Add name input field
- [ ] 5.4.3 Add type/subtype input field
- [ ] 5.4.4 Add parent entity selector
- [ ] 5.4.5 Add image upload field
- [ ] 5.4.6 Add tags multi-select field
- [ ] 5.4.7 Add entry/description textarea
- [ ] 5.4.8 Create dynamic fields renderer for JSON schema
- [ ] 5.4.9 Add text input field component
- [ ] 5.4.10 Add textarea field component
- [ ] 5.4.11 Add select field component
- [ ] 5.4.12 Add checkbox field component
- [ ] 5.4.13 Add entity reference field component (foreign key)
- [ ] 5.4.14 Add multi-entity reference field component
- [ ] 5.4.15 Add is_private toggle
- [ ] 5.4.16 Add form validation error display
- [ ] 5.4.17 Add submit and cancel buttons

### 5.5 Dashboard

- [ ] 5.5.1 Create dashboard template
- [ ] 5.5.2 Add campaign summary stats
- [ ] 5.5.3 Add recent entities list
- [ ] 5.5.4 Add quick create buttons by entity type
- [ ] 5.5.5 Add campaign switcher dropdown

---

## Phase 6: Core Features

### 6.1 Markdown & Mentions

- [ ] 6.1.1 Install/include Parsedown library
- [ ] 6.1.2 Create Markdown parser wrapper class
- [ ] 6.1.3 Implement `@[entity:id]` mention syntax detection
- [ ] 6.1.4 Implement mention replacement with entity links
- [ ] 6.1.5 Implement mention replacement with entity tooltips
- [ ] 6.1.6 Create tooltip HTML structure
- [ ] 6.1.7 Add Alpine.js tooltip display logic
- [ ] 6.1.8 Implement `#[tag:id]` tag mention syntax
- [ ] 6.1.9 Create "mentioned in" query method
- [ ] 6.1.10 Integrate Markdown editor (SimpleMDE or EasyMDE)
- [ ] 6.1.11 Add entity autocomplete to editor for mentions

### 6.2 Relations System

- [ ] 6.2.1 Create RelationRepository class
- [ ] 6.2.2 Implement `findByEntity()` method
- [ ] 6.2.3 Implement `create()` method
- [ ] 6.2.4 Implement `update()` method
- [ ] 6.2.5 Implement `delete()` method
- [ ] 6.2.6 Implement bidirectional relation creation (with mirror)
- [ ] 6.2.7 Create RelationController class
- [ ] 6.2.8 Implement relation CRUD actions
- [ ] 6.2.9 Create relation list partial template
- [ ] 6.2.10 Create relation form modal template
- [ ] 6.2.11 Add HTMX for inline relation management
- [ ] 6.2.12 Create relation type suggestions list

### 6.3 Tags System

- [ ] 6.3.1 Create TagRepository class
- [ ] 6.3.2 Implement tag CRUD methods
- [ ] 6.3.3 Implement `attachToEntity()` method
- [ ] 6.3.4 Implement `detachFromEntity()` method
- [ ] 6.3.5 Implement `findByEntity()` method
- [ ] 6.3.6 Implement `findEntitiesByTag()` method
- [ ] 6.3.7 Create TagController class
- [ ] 6.3.8 Implement tag management page
- [ ] 6.3.9 Create tag picker component (multi-select)
- [ ] 6.3.10 Add tag colour picker
- [ ] 6.3.11 Create tag badge component with colour

### 6.4 Custom Attributes

- [ ] 6.4.1 Create AttributeRepository class
- [ ] 6.4.2 Implement attribute CRUD methods
- [ ] 6.4.3 Implement `findByEntity()` method
- [ ] 6.4.4 Create AttributeController class
- [ ] 6.4.5 Implement inline attribute editing
- [ ] 6.4.6 Create attribute list partial template
- [ ] 6.4.7 Create attribute form partial template
- [ ] 6.4.8 Add HTMX for inline attribute management
- [ ] 6.4.9 Implement attribute templates (reusable sets)
- [ ] 6.4.10 Implement "apply template" functionality

### 6.5 Posts (Sub-entries)

- [ ] 6.5.1 Create PostRepository class
- [ ] 6.5.2 Implement post CRUD methods
- [ ] 6.5.3 Implement `findByEntity()` method with ordering
- [ ] 6.5.4 Implement position reordering method
- [ ] 6.5.5 Create PostController class
- [ ] 6.5.6 Implement post CRUD actions
- [ ] 6.5.7 Create posts list partial template
- [ ] 6.5.8 Create post form modal template
- [ ] 6.5.9 Add drag-and-drop reordering (Alpine.js)
- [ ] 6.5.10 Add HTMX for inline post management

### 6.6 File Uploads

- [ ] 6.6.1 Create FileRepository class
- [ ] 6.6.2 Implement file upload handling
- [ ] 6.6.3 Implement file type validation
- [ ] 6.6.4 Implement file size validation
- [ ] 6.6.5 Implement unique filename generation
- [ ] 6.6.6 Implement file storage to uploads directory
- [ ] 6.6.7 Implement image thumbnail generation
- [ ] 6.6.8 Implement `findByEntity()` method
- [ ] 6.6.9 Implement file deletion (with filesystem cleanup)
- [ ] 6.6.10 Create FileController class
- [ ] 6.6.11 Create file upload component
- [ ] 6.6.12 Create file list partial template
- [ ] 6.6.13 Add drag-and-drop upload support

### 6.7 Search

- [ ] 6.7.1 Create SearchController class
- [ ] 6.7.2 Implement global search action
- [ ] 6.7.3 Implement search results template
- [ ] 6.7.4 Add search result highlighting
- [ ] 6.7.5 Add filter by entity type
- [ ] 6.7.6 Add filter by campaign
- [ ] 6.7.7 Create search bar component in header
- [ ] 6.7.8 Add HTMX for live search results
- [ ] 6.7.9 Add keyboard navigation for search results

---

## Phase 7: Entity-Specific Features

### 7.1 Character-Specific

- [ ] 7.1.1 Create character profile header layout
- [ ] 7.1.2 Add organisation memberships display
- [ ] 7.1.3 Add family memberships display
- [ ] 7.1.4 Add race/ancestry display
- [ ] 7.1.5 Add personality traits section
- [ ] 7.1.6 Add appearance traits section
- [ ] 7.1.7 Add "is dead" indicator styling

### 7.2 Location-Specific

- [ ] 7.2.1 Create location hierarchy breadcrumb
- [ ] 7.2.2 Add child locations list
- [ ] 7.2.3 Add characters at location list
- [ ] 7.2.4 Add organisations at location list
- [ ] 7.2.5 Add map preview (if linked)

### 7.3 Organisation-Specific

- [ ] 7.3.1 Create organisation members list
- [ ] 7.3.2 Add member role/rank display
- [ ] 7.3.3 Add headquarters location link
- [ ] 7.3.4 Add child organisations list

### 7.4 Family-Specific

- [ ] 7.4.1 Create family members list
- [ ] 7.4.2 Add family seat/location link
- [ ] 7.4.3 Add child families list
- [ ] 7.4.4 (Optional) Basic family tree visualisation

### 7.5 Quest-Specific

- [ ] 7.5.1 Create quest status indicator (planned/ongoing/completed)
- [ ] 7.5.2 Add quest characters list
- [ ] 7.5.3 Add quest locations list
- [ ] 7.5.4 Add quest organisations list
- [ ] 7.5.5 Add quest items (rewards) list
- [ ] 7.5.6 Add parent/child quest hierarchy

### 7.6 Calendar-Specific

- [ ] 7.6.1 Create calendar configuration form
- [ ] 7.6.2 Add months definition interface
- [ ] 7.6.3 Add weekdays definition interface
- [ ] 7.6.4 Add moons definition interface
- [ ] 7.6.5 Create calendar view display
- [ ] 7.6.6 Add events/reminders on calendar dates
- [ ] 7.6.7 Add current date tracker

### 7.7 Map-Specific

- [ ] 7.7.1 Integrate Leaflet.js library
- [ ] 7.7.2 Create map image upload
- [ ] 7.7.3 Implement map marker placement
- [ ] 7.7.4 Link markers to entities
- [ ] 7.7.5 Add marker popup with entity preview
- [ ] 7.7.6 Implement map layers (optional)
- [ ] 7.7.7 Save marker positions to database

### 7.8 Timeline-Specific

- [ ] 7.8.1 Create timeline entry form
- [ ] 7.8.2 Create timeline vertical display
- [ ] 7.8.3 Add era grouping support
- [ ] 7.8.4 Link timeline entries to entities
- [ ] 7.8.5 Add timeline entry reordering

---

## Phase 8: Advanced Features

### 8.1 Inventory System

- [ ] 8.1.1 Create `inventory_items` table
- [ ] 8.1.2 Create InventoryRepository class
- [ ] 8.1.3 Implement inventory item CRUD methods
- [ ] 8.1.4 Create inventory display partial
- [ ] 8.1.5 Create inventory item form
- [ ] 8.1.6 Add quantity tracking
- [ ] 8.1.7 Link inventory items to Item entities (optional)

### 8.2 Abilities System

- [ ] 8.2.1 Create `entity_abilities` junction table
- [ ] 8.2.2 Implement ability attachment to entities
- [ ] 8.2.3 Create abilities display partial
- [ ] 8.2.4 Create ability assignment interface

### 8.3 Relation Explorer (Graph View)

- [ ] 8.3.1 Include vis.js or D3.js library
- [ ] 8.3.2 Create relation graph endpoint (JSON)
- [ ] 8.3.3 Create graph visualisation component
- [ ] 8.3.4 Add node click navigation
- [ ] 8.3.5 Add zoom and pan controls

### 8.4 Import/Export

- [ ] 8.4.1 Create ExportController class
- [ ] 8.4.2 Implement campaign JSON export
- [ ] 8.4.3 Implement entity JSON export
- [ ] 8.4.4 Create ImportController class
- [ ] 8.4.5 Implement Kanka JSON import parser
- [ ] 8.4.6 Implement entity import with relation mapping
- [ ] 8.4.7 Create import preview/confirmation step
- [ ] 8.4.8 Add backup download functionality

### 8.5 API (Optional)

- [ ] 8.5.1 Create API authentication (token-based)
- [ ] 8.5.2 Create API route group
- [ ] 8.5.3 Implement entity list endpoint
- [ ] 8.5.4 Implement entity detail endpoint
- [ ] 8.5.5 Implement entity create endpoint
- [ ] 8.5.6 Implement entity update endpoint
- [ ] 8.5.7 Implement entity delete endpoint
- [ ] 8.5.8 Implement search endpoint
- [ ] 8.5.9 Add API rate limiting
- [ ] 8.5.10 Create API documentation page

---

## Phase 9: Polish & UX

### 9.1 Responsive Design

- [ ] 9.1.1 Test and fix mobile navigation
- [ ] 9.1.2 Test and fix mobile sidebar
- [ ] 9.1.3 Test and fix mobile entity lists
- [ ] 9.1.4 Test and fix mobile entity forms
- [ ] 9.1.5 Test and fix mobile modals
- [ ] 9.1.6 Add touch-friendly controls

### 9.2 Keyboard Navigation

- [ ] 9.2.1 Add keyboard shortcut for global search (/)
- [ ] 9.2.2 Add keyboard shortcut for new entity (n)
- [ ] 9.2.3 Add keyboard shortcut for edit (e)
- [ ] 9.2.4 Add keyboard navigation in dropdowns
- [ ] 9.2.5 Add escape key to close modals
- [ ] 9.2.6 Create keyboard shortcuts help modal

### 9.3 Performance

- [ ] 9.3.1 Add database query result caching
- [ ] 9.3.2 Optimise FTS5 queries
- [ ] 9.3.3 Add lazy loading for images
- [ ] 9.3.4 Minify CSS for production
- [ ] 9.3.5 Minify JavaScript for production
- [ ] 9.3.6 Add HTTP caching headers for assets
- [ ] 9.3.7 Implement pagination limits

### 9.4 Error Handling

- [ ] 9.4.1 Create custom error page templates (404, 500)
- [ ] 9.4.2 Implement global exception handler
- [ ] 9.4.3 Add error logging to file
- [ ] 9.4.4 Add user-friendly error messages
- [ ] 9.4.5 Add form validation error display
- [ ] 9.4.6 Add CSRF protection

---

## Phase 10: Deployment

### 10.1 Production Configuration

- [ ] 10.1.1 Create production environment config
- [ ] 10.1.2 Disable debug mode in production
- [ ] 10.1.3 Configure secure session settings
- [ ] 10.1.4 Set up HTTPS redirect
- [ ] 10.1.5 Configure production database path
- [ ] 10.1.6 Set up backup cron job

### 10.2 Documentation

- [ ] 10.2.1 Create README.md with project overview
- [ ] 10.2.2 Create INSTALL.md with setup instructions
- [ ] 10.2.3 Create DOCKER.md with container instructions
- [ ] 10.2.4 Create CONTRIBUTING.md
- [ ] 10.2.5 Document environment variables
- [ ] 10.2.6 Document entity type JSON schemas
- [ ] 10.2.7 Create user guide for basic operations

### 10.3 Testing

- [ ] 10.3.1 Set up PHPUnit configuration
- [ ] 10.3.2 Create database test helpers
- [ ] 10.3.3 Write tests for EntityRepository
- [ ] 10.3.4 Write tests for authentication
- [ ] 10.3.5 Write tests for routing
- [ ] 10.3.6 Write tests for FTS5 search
- [ ] 10.3.7 Write tests for file uploads

---

## Summary

| Phase | Tasks | Focus Area |
|-------|-------|------------|
| 1 | 24 | Project setup & infrastructure |
| 2 | 35 | Database schema & entity types |
| 3 | 45 | Routing, requests, templates, auth |
| 4 | 25 | Entity CRUD & campaigns |
| 5 | 43 | UI templates & components |
| 6 | 59 | Core features (markdown, relations, tags, etc.) |
| 7 | 35 | Entity-specific features |
| 8 | 27 | Advanced features (inventory, graph, API) |
| 9 | 22 | Polish, UX, performance |
| 10 | 19 | Deployment & documentation |
| **Total** | **334** | |

---

## Notes

- Tasks are designed to be completable in 15-60 minutes each
- Dependencies flow top-to-bottom within phases
- Phases 1-5 form the MVP
- Phases 6-7 add feature parity with Kanka core
- Phases 8-10 are enhancement/polish
