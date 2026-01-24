# Project Tasks

A granular task list for the Worlds project - a lightweight worldbuilding tool inspired by Kanka.

## Status Legend
- [ ] Incomplete
- [x] Complete
- [!] Blocked/Failed

---

## Phase 1: Project Foundation

### 1.1 Create project .gitignore file
- [x] **Status:** Complete
- **Description:** Define files to exclude from version control
- **Acceptance criteria:**
  - Excludes vendor directory
  - Excludes node_modules directory
  - Excludes SQLite database files
  - Excludes environment files
- **Files:** `.gitignore`

### 1.2 Create composer.json configuration
- [x] **Status:** Complete
- **Description:** Initialize PHP dependency management
- **Acceptance criteria:**
  - Defines project name
  - Sets PHP version requirement (8.0+)
  - Configures PSR-4 autoloading
- **Files:** `composer.json`

### 1.3 Install Composer dependencies
- [x] **Status:** Complete
- **Description:** Run composer install to set up autoloader
- **Acceptance criteria:**
  - Vendor directory created
  - Autoloader generated
- **Files:** `vendor/`

### 1.4 Create package.json configuration
- [x] **Status:** Complete
- **Description:** Initialize frontend dependency management
- **Acceptance criteria:**
  - Defines project name
  - Lists Tailwind CSS dependency
  - Lists Alpine.js dependency
- **Files:** `package.json`

### 1.5 Install npm dependencies
- [x] **Status:** Complete
- **Description:** Run npm install to set up frontend tools
- **Acceptance criteria:**
  - node_modules directory created
  - Tailwind CLI available
- **Files:** `node_modules/`

### 1.6 Create environment configuration example
- [x] **Status:** Complete
- **Description:** Define environment variables template
- **Acceptance criteria:**
  - Lists database path variable
  - Lists debug mode variable
  - Lists upload directory variable
- **Files:** `.env.example`

### 1.7 Create configuration loader class
- [x] **Status:** Complete
- **Description:** Implement PHP class to load environment config
- **Acceptance criteria:**
  - Reads from .env file
  - Provides getter methods for config values
  - Returns defaults when variables not set
- **Files:** `src/Config/Config.php`

### 1.8 Set up directory permissions
- [x] **Status:** Complete
- **Description:** Document required permissions for uploads
- **Acceptance criteria:**
  - Data directory is writable
  - Uploads directory is writable
- **Files:** `data/README.md`

---

## Phase 2: Docker Environment

### 2.1 Create PHP 8 Dockerfile
- [x] **Status:** Complete
- **Description:** Define container image for PHP 8 with Apache
- **Acceptance criteria:**
  - Uses PHP 8.2 base image
  - Installs SQLite extension
  - Enables Apache mod_rewrite
- **Files:** `Dockerfile`

### 2.2 Create docker-compose.yml file
- [x] **Status:** Complete
- **Description:** Define local development environment
- **Acceptance criteria:**
  - Maps port 8080 to container
  - Mounts source directory
  - Mounts data directory as persistent volume
- **Files:** `docker-compose.yml`

### 2.3 Configure Apache virtual host
- [x] **Status:** Complete
- **Description:** Set up clean URL routing
- **Acceptance criteria:**
  - Points document root to public directory
  - Enables .htaccess overrides
- **Files:** `docker/apache.conf`

### 2.4 Create .dockerignore file
- [x] **Status:** Complete
- **Description:** Define files to exclude from Docker build
- **Acceptance criteria:**
  - Excludes .git directory
  - Excludes node_modules directory
  - Excludes vendor directory
- **Files:** `.dockerignore`

### 2.5 Test container build
- [x] **Status:** Complete
- **Description:** Verify Docker container builds correctly
- **Acceptance criteria:**
  - Container builds without errors
  - PHP info page displays correctly
- **Files:** N/A

---

## Phase 3: Frontend Build Pipeline

### 3.1 Create Tailwind configuration
- [x] **Status:** Complete
- **Description:** Configure Tailwind CSS framework
- **Acceptance criteria:**
  - Scans PHP template files for classes
  - Defines custom color palette
- **Files:** `tailwind.config.js`

### 3.2 Create base CSS file
- [x] **Status:** Complete
- **Description:** Define CSS entry point with Tailwind directives
- **Acceptance criteria:**
  - Includes @tailwind base
  - Includes @tailwind components
  - Includes @tailwind utilities
- **Files:** `src/css/app.css`

### 3.3 Configure PostCSS
- [x] **Status:** Complete
- **Description:** Set up CSS processing pipeline
- **Acceptance criteria:**
  - Includes Tailwind plugin
  - Includes autoprefixer plugin
- **Files:** `postcss.config.js`

### 3.4 Create CSS build script
- [x] **Status:** Complete
- **Description:** Add npm script to compile CSS
- **Acceptance criteria:**
  - Outputs minified CSS to public directory
  - Runs Tailwind CLI
- **Files:** `package.json`

### 3.5 Create CSS watch script
- [x] **Status:** Complete
- **Description:** Add npm script for development mode
- **Acceptance criteria:**
  - Watches for CSS changes
  - Rebuilds automatically on save
- **Files:** `package.json`

### 3.6 Generate initial compiled CSS
- [x] **Status:** Complete
- **Description:** Run build to create production CSS
- **Acceptance criteria:**
  - CSS file exists in public/assets/css
  - File is minified
- **Files:** `public/assets/css/app.css`

### 3.7 Create JavaScript entry point
- [x] **Status:** Complete
- **Description:** Initialize Alpine.js framework
- **Acceptance criteria:**
  - Imports Alpine.js
  - Starts Alpine on DOM ready
- **Files:** `public/assets/js/app.js`

---

## Phase 4: Database Layer

### 4.1 Create campaigns table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for campaign storage
- **Acceptance criteria:**
  - Has id primary key
  - Has name column
  - Has settings JSON column
  - Has timestamp columns
- **Files:** `database/001_campaigns.sql`

### 4.2 Create entities table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for polymorphic entity storage
- **Acceptance criteria:**
  - Has id primary key
  - Has campaign_id foreign key
  - Has entity_type column
  - Has data JSON column
  - Has parent_id self-reference
- **Files:** `database/002_entities.sql`

### 4.3 Create relations table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for entity relationships
- **Acceptance criteria:**
  - Has source_id foreign key
  - Has target_id foreign key
  - Has relation type column
  - Has mirror_relation column
- **Files:** `database/003_relations.sql`

### 4.4 Create tags table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for tag storage
- **Acceptance criteria:**
  - Has id primary key
  - Has campaign_id foreign key
  - Has name column
  - Has colour column
- **Files:** `database/004_tags.sql`

### 4.5 Create entity_tags junction table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for entity-tag associations
- **Acceptance criteria:**
  - Has entity_id foreign key
  - Has tag_id foreign key
  - Has composite primary key
- **Files:** `database/005_entity_tags.sql`

### 4.6 Create attributes table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for custom attributes
- **Acceptance criteria:**
  - Has id primary key
  - Has entity_id foreign key
  - Has name column
  - Has value column
  - Has is_private column
- **Files:** `database/006_attributes.sql`

### 4.7 Create posts table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for entity sub-entries
- **Acceptance criteria:**
  - Has id primary key
  - Has entity_id foreign key
  - Has entry column
  - Has position column
- **Files:** `database/007_posts.sql`

### 4.8 Create files table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for file attachments
- **Acceptance criteria:**
  - Has id primary key
  - Has entity_id foreign key
  - Has filename column
  - Has mime_type column
- **Files:** `database/008_files.sql`

### 4.9 Create users table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for simple authentication
- **Acceptance criteria:**
  - Has id primary key
  - Has username column
  - Has password_hash column
  - Has timestamp columns
- **Files:** `database/009_users.sql`

### 4.10 Create FTS5 virtual table SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for full-text search
- **Acceptance criteria:**
  - Creates entities_fts virtual table
  - Indexes name column
  - Indexes entry column
- **Files:** `database/010_fts.sql`

### 4.11 Create database indexes SQL
- [ ] **Status:** Incomplete
- **Description:** Define SQL for performance indexes
- **Acceptance criteria:**
  - Indexes entity_type column
  - Indexes campaign_id column
  - Indexes parent_id column
- **Files:** `database/011_indexes.sql`

### 4.12 Create master migration script
- [ ] **Status:** Incomplete
- **Description:** Combine all SQL files for initialization
- **Acceptance criteria:**
  - Runs all SQL files in order
  - Handles errors gracefully
- **Files:** `database/migrate.sql`

---

## Phase 5: Database Connection

### 5.1 Create Database singleton class
- [ ] **Status:** Incomplete
- **Description:** Implement database connection manager
- **Acceptance criteria:**
  - Returns single PDO instance
  - Creates database file if missing
- **Files:** `src/Config/Database.php`

### 5.2 Configure PDO connection
- [ ] **Status:** Incomplete
- **Description:** Set up SQLite connection options
- **Acceptance criteria:**
  - Uses PDO with SQLite driver
  - Enables exception error mode
- **Files:** `src/Config/Database.php`

### 5.3 Enable foreign key enforcement
- [ ] **Status:** Incomplete
- **Description:** Configure SQLite foreign key support
- **Acceptance criteria:**
  - Runs PRAGMA foreign_keys = ON
  - Applies on every connection
- **Files:** `src/Config/Database.php`

### 5.4 Implement migration runner
- [ ] **Status:** Incomplete
- **Description:** Create method to execute SQL migrations
- **Acceptance criteria:**
  - Reads SQL files from database directory
  - Executes in numeric order
  - Tracks completed migrations
- **Files:** `src/Config/Database.php`

### 5.5 Add query logging
- [ ] **Status:** Incomplete
- **Description:** Log queries in development mode
- **Acceptance criteria:**
  - Logs query string
  - Logs execution time
  - Only active when DEBUG=true
- **Files:** `src/Config/Database.php`

### 5.6 Create database backup utility
- [ ] **Status:** Incomplete
- **Description:** Implement database file backup
- **Acceptance criteria:**
  - Copies database file to timestamped backup
  - Stores in data/backups directory
- **Files:** `src/Config/Database.php`

---

## Phase 6: Entity Type Schemas

### 6.1 Create Character JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Character entities
- **Acceptance criteria:**
  - Defines age field
  - Defines pronouns field
  - Defines is_dead boolean
  - Defines personality traits array
- **Files:** `src/Config/schemas/character.json`

### 6.2 Create Location JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Location entities
- **Acceptance criteria:**
  - Defines location_type field
  - Defines population field
  - Defines map_id reference
- **Files:** `src/Config/schemas/location.json`

### 6.3 Create Family JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Family entities
- **Acceptance criteria:**
  - Defines seat_location_id reference
  - Defines motto field
- **Files:** `src/Config/schemas/family.json`

### 6.4 Create Organisation JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Organisation entities
- **Acceptance criteria:**
  - Defines organisation_type field
  - Defines headquarters_id reference
  - Defines goals field
- **Files:** `src/Config/schemas/organisation.json`

### 6.5 Create Item JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Item entities
- **Acceptance criteria:**
  - Defines item_type field
  - Defines rarity field
  - Defines is_magical boolean
- **Files:** `src/Config/schemas/item.json`

### 6.6 Create Note JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Note entities
- **Acceptance criteria:**
  - Defines note_type field
  - Minimal structure for flexibility
- **Files:** `src/Config/schemas/note.json`

### 6.7 Create Event JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Event entities
- **Acceptance criteria:**
  - Defines date field
  - Defines era field
  - Defines calendar_id reference
- **Files:** `src/Config/schemas/event.json`

### 6.8 Create Calendar JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Calendar entities
- **Acceptance criteria:**
  - Defines months array
  - Defines weekdays array
  - Defines current_date object
- **Files:** `src/Config/schemas/calendar.json`

### 6.9 Create Race JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Race entities
- **Acceptance criteria:**
  - Defines lifespan field
  - Defines size field
  - Defines traits array
- **Files:** `src/Config/schemas/race.json`

### 6.10 Create Quest JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Quest entities
- **Acceptance criteria:**
  - Defines quest_type field
  - Defines status field (planned/ongoing/completed)
  - Defines giver_id reference
- **Files:** `src/Config/schemas/quest.json`

### 6.11 Create Journal JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Journal entities
- **Acceptance criteria:**
  - Defines date field
  - Defines session_number field
  - Defines author_id reference
- **Files:** `src/Config/schemas/journal.json`

### 6.12 Create Map JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Map entities
- **Acceptance criteria:**
  - Defines image_path field
  - Defines markers array
  - Defines bounds object
- **Files:** `src/Config/schemas/map.json`

### 6.13 Create Timeline JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Timeline entities
- **Acceptance criteria:**
  - Defines eras array
  - Defines entries array
- **Files:** `src/Config/schemas/timeline.json`

### 6.14 Create Ability JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Ability entities
- **Acceptance criteria:**
  - Defines ability_type field
  - Defines charges field
  - Defines effects array
- **Files:** `src/Config/schemas/ability.json`

### 6.15 Create Creature JSON schema
- [ ] **Status:** Incomplete
- **Description:** Define data fields for Creature entities
- **Acceptance criteria:**
  - Defines creature_type field
  - Defines habitat field
  - Defines is_sentient boolean
- **Files:** `src/Config/schemas/creature.json`

### 6.16 Create EntityType registry class
- [ ] **Status:** Incomplete
- **Description:** Load all entity type schemas
- **Acceptance criteria:**
  - Loads JSON schema files
  - Provides list of available types
  - Returns schema by type name
- **Files:** `src/Config/EntityTypes.php`

### 6.17 Implement schema validation
- [ ] **Status:** Incomplete
- **Description:** Validate entity data against schema
- **Acceptance criteria:**
  - Checks required fields
  - Validates field types
  - Returns validation errors
- **Files:** `src/Config/EntityTypes.php`

---

## Phase 7: Routing System

### 7.1 Create Router class
- [ ] **Status:** Incomplete
- **Description:** Implement HTTP request routing
- **Acceptance criteria:**
  - Stores route definitions
  - Matches request to route
- **Files:** `src/Config/Router.php`

### 7.2 Implement GET route registration
- [ ] **Status:** Incomplete
- **Description:** Add method to register GET routes
- **Acceptance criteria:**
  - Accepts URL pattern
  - Accepts controller callback
- **Files:** `src/Config/Router.php`

### 7.3 Implement POST route registration
- [ ] **Status:** Incomplete
- **Description:** Add method to register POST routes
- **Acceptance criteria:**
  - Accepts URL pattern
  - Accepts controller callback
- **Files:** `src/Config/Router.php`

### 7.4 Implement PUT route registration
- [ ] **Status:** Incomplete
- **Description:** Add method to register PUT routes
- **Acceptance criteria:**
  - Accepts URL pattern
  - Accepts controller callback
- **Files:** `src/Config/Router.php`

### 7.5 Implement DELETE route registration
- [ ] **Status:** Incomplete
- **Description:** Add method to register DELETE routes
- **Acceptance criteria:**
  - Accepts URL pattern
  - Accepts controller callback
- **Files:** `src/Config/Router.php`

### 7.6 Implement URL parameter extraction
- [ ] **Status:** Incomplete
- **Description:** Parse route parameters from URL
- **Acceptance criteria:**
  - Extracts {id} from /entity/{id}
  - Supports multiple parameters
- **Files:** `src/Config/Router.php`

### 7.7 Implement query string parsing
- [ ] **Status:** Incomplete
- **Description:** Parse URL query parameters
- **Acceptance criteria:**
  - Extracts key-value pairs
  - Returns as array
- **Files:** `src/Config/Router.php`

### 7.8 Create route dispatcher
- [ ] **Status:** Incomplete
- **Description:** Execute matched route handler
- **Acceptance criteria:**
  - Calls controller method
  - Passes route parameters
  - Passes request object
- **Files:** `src/Config/Router.php`

### 7.9 Implement 404 handler
- [ ] **Status:** Incomplete
- **Description:** Handle unmatched routes
- **Acceptance criteria:**
  - Returns 404 status code
  - Displays error page
- **Files:** `src/Config/Router.php`

### 7.10 Create .htaccess file
- [ ] **Status:** Incomplete
- **Description:** Configure Apache URL rewriting
- **Acceptance criteria:**
  - Enables mod_rewrite
  - Routes all requests to index.php
  - Excludes existing files
- **Files:** `public/.htaccess`

### 7.11 Create front controller
- [ ] **Status:** Incomplete
- **Description:** Initialize application entry point
- **Acceptance criteria:**
  - Loads autoloader
  - Initializes router
  - Dispatches request
- **Files:** `public/index.php`

---

## Phase 8: Request/Response Handling

### 8.1 Create Request class
- [ ] **Status:** Incomplete
- **Description:** Wrap HTTP request data
- **Acceptance criteria:**
  - Encapsulates $_GET data
  - Encapsulates $_POST data
  - Encapsulates $_FILES data
- **Files:** `src/Config/Request.php`

### 8.2 Implement request method getter
- [ ] **Status:** Incomplete
- **Description:** Return HTTP method
- **Acceptance criteria:**
  - Returns GET/POST/PUT/DELETE
  - Handles method spoofing via _method field
- **Files:** `src/Config/Request.php`

### 8.3 Implement request path getter
- [ ] **Status:** Incomplete
- **Description:** Return URL path
- **Acceptance criteria:**
  - Strips query string
  - Normalizes trailing slashes
- **Files:** `src/Config/Request.php`

### 8.4 Implement JSON body parser
- [ ] **Status:** Incomplete
- **Description:** Parse JSON request body
- **Acceptance criteria:**
  - Reads php://input stream
  - Decodes JSON to array
  - Returns null on invalid JSON
- **Files:** `src/Config/Request.php`

### 8.5 Implement file upload getter
- [ ] **Status:** Incomplete
- **Description:** Access uploaded files
- **Acceptance criteria:**
  - Returns file data by field name
  - Includes tmp_name path
  - Includes file type
- **Files:** `src/Config/Request.php`

### 8.6 Create Response class
- [ ] **Status:** Incomplete
- **Description:** Build HTTP responses
- **Acceptance criteria:**
  - Sets status codes
  - Sets headers
  - Outputs body content
- **Files:** `src/Config/Response.php`

### 8.7 Implement HTML response method
- [ ] **Status:** Incomplete
- **Description:** Return HTML content
- **Acceptance criteria:**
  - Sets Content-Type to text/html
  - Outputs HTML string
- **Files:** `src/Config/Response.php`

### 8.8 Implement JSON response method
- [ ] **Status:** Incomplete
- **Description:** Return JSON content
- **Acceptance criteria:**
  - Sets Content-Type to application/json
  - Encodes array to JSON
- **Files:** `src/Config/Response.php`

### 8.9 Implement redirect response method
- [ ] **Status:** Incomplete
- **Description:** Redirect to URL
- **Acceptance criteria:**
  - Sets Location header
  - Sets 302 status code
- **Files:** `src/Config/Response.php`

### 8.10 Implement file download response
- [ ] **Status:** Incomplete
- **Description:** Send file for download
- **Acceptance criteria:**
  - Sets Content-Disposition header
  - Streams file content
- **Files:** `src/Config/Response.php`

---

## Phase 9: Template Engine

### 9.1 Create View class
- [ ] **Status:** Incomplete
- **Description:** Implement template rendering
- **Acceptance criteria:**
  - Loads PHP template files
  - Extracts variables to scope
- **Files:** `src/Config/View.php`

### 9.2 Implement template file loading
- [ ] **Status:** Incomplete
- **Description:** Find template by name
- **Acceptance criteria:**
  - Searches Views directory
  - Throws error if not found
- **Files:** `src/Config/View.php`

### 9.3 Implement variable passing
- [ ] **Status:** Incomplete
- **Description:** Pass data to templates
- **Acceptance criteria:**
  - Accepts associative array
  - Makes keys available as variables
- **Files:** `src/Config/View.php`

### 9.4 Implement template inheritance
- [ ] **Status:** Incomplete
- **Description:** Support layout templates
- **Acceptance criteria:**
  - Child template extends parent
  - Parent defines content areas
- **Files:** `src/Config/View.php`

### 9.5 Implement section/yield functionality
- [ ] **Status:** Incomplete
- **Description:** Define replaceable content blocks
- **Acceptance criteria:**
  - @section defines content
  - @yield outputs content
- **Files:** `src/Config/View.php`

### 9.6 Implement partial includes
- [ ] **Status:** Incomplete
- **Description:** Include sub-templates
- **Acceptance criteria:**
  - @include inserts partial
  - Passes parent variables
- **Files:** `src/Config/View.php`

### 9.7 Create HTML escaping helper
- [ ] **Status:** Incomplete
- **Description:** Escape output for XSS prevention
- **Acceptance criteria:**
  - Escapes HTML special characters
  - Returns safe string
- **Files:** `src/Config/helpers.php`

### 9.8 Create URL generation helper
- [ ] **Status:** Incomplete
- **Description:** Generate application URLs
- **Acceptance criteria:**
  - Accepts route name
  - Accepts route parameters
  - Returns full URL path
- **Files:** `src/Config/helpers.php`

### 9.9 Create asset URL helper
- [ ] **Status:** Incomplete
- **Description:** Generate asset file URLs
- **Acceptance criteria:**
  - Accepts asset path
  - Adds cache-busting query string
- **Files:** `src/Config/helpers.php`

### 9.10 Create CSRF token helper
- [ ] **Status:** Incomplete
- **Description:** Generate form CSRF tokens
- **Acceptance criteria:**
  - Creates random token
  - Stores in session
  - Returns hidden input HTML
- **Files:** `src/Config/helpers.php`

---

## Phase 10: Authentication

### 10.1 Create Auth class
- [ ] **Status:** Incomplete
- **Description:** Implement authentication manager
- **Acceptance criteria:**
  - Manages user session
  - Provides auth check methods
- **Files:** `src/Config/Auth.php`

### 10.2 Configure session handling
- [ ] **Status:** Incomplete
- **Description:** Set up PHP sessions
- **Acceptance criteria:**
  - Configures secure settings
  - Starts session on request
- **Files:** `src/Config/Auth.php`

### 10.3 Implement password hashing
- [ ] **Status:** Incomplete
- **Description:** Hash passwords for storage
- **Acceptance criteria:**
  - Uses password_hash function
  - Uses BCRYPT algorithm
- **Files:** `src/Config/Auth.php`

### 10.4 Implement password verification
- [ ] **Status:** Incomplete
- **Description:** Verify password against hash
- **Acceptance criteria:**
  - Uses password_verify function
  - Returns boolean result
- **Files:** `src/Config/Auth.php`

### 10.5 Implement login method
- [ ] **Status:** Incomplete
- **Description:** Authenticate user credentials
- **Acceptance criteria:**
  - Validates username exists
  - Validates password matches
  - Stores user ID in session
- **Files:** `src/Config/Auth.php`

### 10.6 Implement logout method
- [ ] **Status:** Incomplete
- **Description:** End user session
- **Acceptance criteria:**
  - Destroys session data
  - Regenerates session ID
- **Files:** `src/Config/Auth.php`

### 10.7 Implement isLoggedIn check
- [ ] **Status:** Incomplete
- **Description:** Check authentication status
- **Acceptance criteria:**
  - Returns true if user in session
  - Returns false otherwise
- **Files:** `src/Config/Auth.php`

### 10.8 Implement currentUser getter
- [ ] **Status:** Incomplete
- **Description:** Get authenticated user
- **Acceptance criteria:**
  - Returns user object
  - Returns null if not logged in
- **Files:** `src/Config/Auth.php`

### 10.9 Create authentication middleware
- [ ] **Status:** Incomplete
- **Description:** Protect routes requiring auth
- **Acceptance criteria:**
  - Checks login status
  - Redirects to login if not authenticated
- **Files:** `src/Config/Auth.php`

### 10.10 Create login form template
- [ ] **Status:** Incomplete
- **Description:** Build login page UI
- **Acceptance criteria:**
  - Has username field
  - Has password field
  - Has submit button
- **Files:** `src/Views/auth/login.php`

### 10.11 Create registration form template
- [ ] **Status:** Incomplete
- **Description:** Build registration page UI
- **Acceptance criteria:**
  - Has username field
  - Has password field
  - Has password confirmation field
- **Files:** `src/Views/auth/register.php`

### 10.12 Create AuthController class
- [ ] **Status:** Incomplete
- **Description:** Handle authentication requests
- **Acceptance criteria:**
  - Implements login action
  - Implements logout action
  - Implements register action
- **Files:** `src/Controllers/AuthController.php`

---

## Phase 11: Entity Repository

### 11.1 Create EntityRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement entity database access
- **Acceptance criteria:**
  - Uses PDO for queries
  - Handles entity CRUD operations
- **Files:** `src/Repositories/EntityRepository.php`

### 11.2 Implement findById method
- [ ] **Status:** Incomplete
- **Description:** Retrieve single entity
- **Acceptance criteria:**
  - Accepts entity ID
  - Returns entity array
  - Returns null if not found
- **Files:** `src/Repositories/EntityRepository.php`

### 11.3 Implement findByType method
- [ ] **Status:** Incomplete
- **Description:** List entities by type
- **Acceptance criteria:**
  - Accepts entity_type string
  - Returns array of entities
  - Orders by name
- **Files:** `src/Repositories/EntityRepository.php`

### 11.4 Implement findByCampaign method
- [ ] **Status:** Incomplete
- **Description:** List entities in campaign
- **Acceptance criteria:**
  - Accepts campaign_id
  - Returns array of entities
- **Files:** `src/Repositories/EntityRepository.php`

### 11.5 Implement findByParent method
- [ ] **Status:** Incomplete
- **Description:** List child entities
- **Acceptance criteria:**
  - Accepts parent_id
  - Returns array of child entities
- **Files:** `src/Repositories/EntityRepository.php`

### 11.6 Implement create method
- [ ] **Status:** Incomplete
- **Description:** Insert new entity
- **Acceptance criteria:**
  - Accepts entity data array
  - Returns new entity ID
  - Sets created_at timestamp
- **Files:** `src/Repositories/EntityRepository.php`

### 11.7 Implement update method
- [ ] **Status:** Incomplete
- **Description:** Modify existing entity
- **Acceptance criteria:**
  - Accepts entity ID
  - Accepts updated data array
  - Sets updated_at timestamp
- **Files:** `src/Repositories/EntityRepository.php`

### 11.8 Implement delete method
- [ ] **Status:** Incomplete
- **Description:** Remove entity
- **Acceptance criteria:**
  - Accepts entity ID
  - Removes related records
  - Returns success boolean
- **Files:** `src/Repositories/EntityRepository.php`

### 11.9 Implement search method
- [ ] **Status:** Incomplete
- **Description:** Full-text search entities
- **Acceptance criteria:**
  - Uses FTS5 MATCH syntax
  - Returns ranked results
- **Files:** `src/Repositories/EntityRepository.php`

### 11.10 Implement pagination helper
- [ ] **Status:** Incomplete
- **Description:** Paginate query results
- **Acceptance criteria:**
  - Accepts page number
  - Accepts per_page count
  - Returns total pages count
- **Files:** `src/Repositories/EntityRepository.php`

### 11.11 Create FTS5 insert trigger
- [ ] **Status:** Incomplete
- **Description:** Sync FTS on entity insert
- **Acceptance criteria:**
  - Triggers after INSERT
  - Adds to FTS index
- **Files:** `database/012_fts_triggers.sql`

### 11.12 Create FTS5 update trigger
- [ ] **Status:** Incomplete
- **Description:** Sync FTS on entity update
- **Acceptance criteria:**
  - Triggers after UPDATE
  - Updates FTS index
- **Files:** `database/012_fts_triggers.sql`

### 11.13 Create FTS5 delete trigger
- [ ] **Status:** Incomplete
- **Description:** Sync FTS on entity delete
- **Acceptance criteria:**
  - Triggers after DELETE
  - Removes from FTS index
- **Files:** `database/012_fts_triggers.sql`

---

## Phase 12: Entity Controller

### 12.1 Create EntityController class
- [ ] **Status:** Incomplete
- **Description:** Handle entity HTTP requests
- **Acceptance criteria:**
  - Uses EntityRepository
  - Renders appropriate views
- **Files:** `src/Controllers/EntityController.php`

### 12.2 Implement index action
- [ ] **Status:** Incomplete
- **Description:** Display entity list
- **Acceptance criteria:**
  - Fetches entities by type
  - Supports pagination
  - Renders list view
- **Files:** `src/Controllers/EntityController.php`

### 12.3 Implement show action
- [ ] **Status:** Incomplete
- **Description:** Display single entity
- **Acceptance criteria:**
  - Fetches entity by ID
  - Returns 404 if not found
  - Renders detail view
- **Files:** `src/Controllers/EntityController.php`

### 12.4 Implement create action
- [ ] **Status:** Incomplete
- **Description:** Display creation form
- **Acceptance criteria:**
  - Loads entity type schema
  - Renders empty form
- **Files:** `src/Controllers/EntityController.php`

### 12.5 Implement store action
- [ ] **Status:** Incomplete
- **Description:** Save new entity
- **Acceptance criteria:**
  - Validates input data
  - Creates entity record
  - Redirects to show page
- **Files:** `src/Controllers/EntityController.php`

### 12.6 Implement edit action
- [ ] **Status:** Incomplete
- **Description:** Display edit form
- **Acceptance criteria:**
  - Fetches existing entity
  - Renders form with data
- **Files:** `src/Controllers/EntityController.php`

### 12.7 Implement update action
- [ ] **Status:** Incomplete
- **Description:** Save entity changes
- **Acceptance criteria:**
  - Validates input data
  - Updates entity record
  - Redirects to show page
- **Files:** `src/Controllers/EntityController.php`

### 12.8 Implement destroy action
- [ ] **Status:** Incomplete
- **Description:** Delete entity
- **Acceptance criteria:**
  - Removes entity record
  - Redirects to list page
- **Files:** `src/Controllers/EntityController.php`

### 12.9 Implement validation
- [ ] **Status:** Incomplete
- **Description:** Validate entity input
- **Acceptance criteria:**
  - Checks required fields
  - Validates against schema
  - Returns error messages
- **Files:** `src/Controllers/EntityController.php`

### 12.10 Register entity routes
- [ ] **Status:** Incomplete
- **Description:** Define entity URL routes
- **Acceptance criteria:**
  - Maps /entities to index
  - Maps /entities/{id} to show
  - Maps CRUD routes
- **Files:** `public/index.php`

---

## Phase 13: Campaign Management

### 13.1 Create CampaignRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement campaign database access
- **Acceptance criteria:**
  - Provides CRUD methods
  - Filters by user access
- **Files:** `src/Repositories/CampaignRepository.php`

### 13.2 Implement campaign CRUD methods
- [ ] **Status:** Incomplete
- **Description:** Create campaign data operations
- **Acceptance criteria:**
  - Implements create method
  - Implements update method
  - Implements delete method
- **Files:** `src/Repositories/CampaignRepository.php`

### 13.3 Create CampaignController class
- [ ] **Status:** Incomplete
- **Description:** Handle campaign requests
- **Acceptance criteria:**
  - Lists user campaigns
  - Handles campaign switching
- **Files:** `src/Controllers/CampaignController.php`

### 13.4 Implement campaign listing
- [ ] **Status:** Incomplete
- **Description:** Display campaign list
- **Acceptance criteria:**
  - Shows all user campaigns
  - Indicates active campaign
- **Files:** `src/Controllers/CampaignController.php`

### 13.5 Implement campaign creation
- [ ] **Status:** Incomplete
- **Description:** Create new campaign
- **Acceptance criteria:**
  - Accepts campaign name
  - Accepts campaign description
  - Sets as active campaign
- **Files:** `src/Controllers/CampaignController.php`

### 13.6 Implement campaign switching
- [ ] **Status:** Incomplete
- **Description:** Change active campaign
- **Acceptance criteria:**
  - Updates session
  - Redirects to dashboard
- **Files:** `src/Controllers/CampaignController.php`

### 13.7 Store active campaign in session
- [ ] **Status:** Incomplete
- **Description:** Track current campaign
- **Acceptance criteria:**
  - Stores campaign_id in session
  - Loads on each request
- **Files:** `src/Config/Auth.php`

### 13.8 Register campaign routes
- [ ] **Status:** Incomplete
- **Description:** Define campaign URL routes
- **Acceptance criteria:**
  - Maps /campaigns to list
  - Maps /campaigns/switch/{id} to switch
- **Files:** `public/index.php`

---

## Phase 14: UI Templates

### 14.1 Create base layout template
- [ ] **Status:** Incomplete
- **Description:** Build main page structure
- **Acceptance criteria:**
  - Defines HTML doctype
  - Includes CSS link
  - Includes JS scripts
  - Defines content area
- **Files:** `src/Views/layouts/base.php`

### 14.2 Create header component
- [ ] **Status:** Incomplete
- **Description:** Build page header
- **Acceptance criteria:**
  - Shows application name
  - Shows search bar
  - Shows user menu
- **Files:** `src/Views/partials/header.php`

### 14.3 Create sidebar component
- [ ] **Status:** Incomplete
- **Description:** Build navigation sidebar
- **Acceptance criteria:**
  - Lists entity types
  - Shows active item
  - Collapses on mobile
- **Files:** `src/Views/partials/sidebar.php`

### 14.4 Create footer component
- [ ] **Status:** Incomplete
- **Description:** Build page footer
- **Acceptance criteria:**
  - Shows version info
  - Shows copyright
- **Files:** `src/Views/partials/footer.php`

### 14.5 Create flash message component
- [ ] **Status:** Incomplete
- **Description:** Display notification messages
- **Acceptance criteria:**
  - Shows success messages
  - Shows error messages
  - Auto-dismisses after timeout
- **Files:** `src/Views/partials/flash.php`

### 14.6 Create breadcrumb component
- [ ] **Status:** Incomplete
- **Description:** Show navigation path
- **Acceptance criteria:**
  - Shows page hierarchy
  - Links to parent pages
- **Files:** `src/Views/partials/breadcrumb.php`

### 14.7 Create entity list template
- [ ] **Status:** Incomplete
- **Description:** Build entity listing page
- **Acceptance criteria:**
  - Shows grid of entity cards
  - Shows pagination controls
  - Shows create button
- **Files:** `src/Views/entities/index.php`

### 14.8 Create entity card component
- [ ] **Status:** Incomplete
- **Description:** Build entity preview card
- **Acceptance criteria:**
  - Shows entity image
  - Shows entity name
  - Shows entity type badge
- **Files:** `src/Views/partials/entity-card.php`

### 14.9 Create entity detail template
- [ ] **Status:** Incomplete
- **Description:** Build entity view page
- **Acceptance criteria:**
  - Shows entity header
  - Shows description
  - Shows relations
  - Shows attributes
- **Files:** `src/Views/entities/show.php`

### 14.10 Create entity form template
- [ ] **Status:** Incomplete
- **Description:** Build entity edit form
- **Acceptance criteria:**
  - Shows name field
  - Shows type-specific fields
  - Shows save button
- **Files:** `src/Views/entities/form.php`

### 14.11 Create dashboard template
- [ ] **Status:** Incomplete
- **Description:** Build main dashboard page
- **Acceptance criteria:**
  - Shows campaign summary
  - Shows recent entities
  - Shows quick create buttons
- **Files:** `src/Views/dashboard.php`

---

## Phase 15: Markdown Processing

### 15.1 Install Parsedown library
- [ ] **Status:** Incomplete
- **Description:** Add Markdown parser dependency
- **Acceptance criteria:**
  - Added to composer.json
  - Installed via composer
- **Files:** `composer.json`

### 15.2 Create Markdown wrapper class
- [ ] **Status:** Incomplete
- **Description:** Implement custom Markdown parser
- **Acceptance criteria:**
  - Extends Parsedown
  - Adds custom syntax handling
- **Files:** `src/Config/Markdown.php`

### 15.3 Implement mention detection
- [ ] **Status:** Incomplete
- **Description:** Parse @[entity:id] syntax
- **Acceptance criteria:**
  - Detects mention pattern
  - Extracts entity ID
- **Files:** `src/Config/Markdown.php`

### 15.4 Implement mention replacement
- [ ] **Status:** Incomplete
- **Description:** Convert mentions to links
- **Acceptance criteria:**
  - Replaces with entity link
  - Adds tooltip data attribute
- **Files:** `src/Config/Markdown.php`

### 15.5 Create tooltip component
- [ ] **Status:** Incomplete
- **Description:** Build entity preview tooltip
- **Acceptance criteria:**
  - Shows entity name
  - Shows entity image
  - Shows brief description
- **Files:** `src/Views/partials/tooltip.php`

### 15.6 Add Alpine.js tooltip logic
- [ ] **Status:** Incomplete
- **Description:** Implement tooltip display
- **Acceptance criteria:**
  - Shows on hover
  - Positions near cursor
  - Hides on mouse leave
- **Files:** `public/assets/js/app.js`

### 15.7 Integrate Markdown editor
- [ ] **Status:** Incomplete
- **Description:** Add rich text editor
- **Acceptance criteria:**
  - Shows formatting toolbar
  - Provides preview mode
- **Files:** `public/assets/js/app.js`

---

## Phase 16: Relations System

### 16.1 Create RelationRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement relation database access
- **Acceptance criteria:**
  - Provides CRUD methods
  - Handles bidirectional relations
- **Files:** `src/Repositories/RelationRepository.php`

### 16.2 Implement findByEntity method
- [ ] **Status:** Incomplete
- **Description:** Get entity relations
- **Acceptance criteria:**
  - Finds outgoing relations
  - Finds incoming relations
- **Files:** `src/Repositories/RelationRepository.php`

### 16.3 Implement relation creation
- [ ] **Status:** Incomplete
- **Description:** Create new relation
- **Acceptance criteria:**
  - Creates primary relation
  - Creates mirror relation
- **Files:** `src/Repositories/RelationRepository.php`

### 16.4 Implement relation deletion
- [ ] **Status:** Incomplete
- **Description:** Remove relation
- **Acceptance criteria:**
  - Removes primary relation
  - Removes mirror relation
- **Files:** `src/Repositories/RelationRepository.php`

### 16.5 Create RelationController class
- [ ] **Status:** Incomplete
- **Description:** Handle relation requests
- **Acceptance criteria:**
  - Implements CRUD actions
  - Returns JSON for AJAX
- **Files:** `src/Controllers/RelationController.php`

### 16.6 Create relation list partial
- [ ] **Status:** Incomplete
- **Description:** Display entity relations
- **Acceptance criteria:**
  - Groups by relation type
  - Shows linked entity name
- **Files:** `src/Views/partials/relations.php`

### 16.7 Create relation form modal
- [ ] **Status:** Incomplete
- **Description:** Build relation editor
- **Acceptance criteria:**
  - Has target entity selector
  - Has relation type field
- **Files:** `src/Views/partials/relation-form.php`

---

## Phase 17: Tags System

### 17.1 Create TagRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement tag database access
- **Acceptance criteria:**
  - Provides CRUD methods
  - Handles entity associations
- **Files:** `src/Repositories/TagRepository.php`

### 17.2 Implement tag CRUD methods
- [ ] **Status:** Incomplete
- **Description:** Create tag operations
- **Acceptance criteria:**
  - Implements create method
  - Implements update method
  - Implements delete method
- **Files:** `src/Repositories/TagRepository.php`

### 17.3 Implement attachToEntity method
- [ ] **Status:** Incomplete
- **Description:** Link tag to entity
- **Acceptance criteria:**
  - Creates junction record
  - Prevents duplicates
- **Files:** `src/Repositories/TagRepository.php`

### 17.4 Implement detachFromEntity method
- [ ] **Status:** Incomplete
- **Description:** Unlink tag from entity
- **Acceptance criteria:**
  - Removes junction record
- **Files:** `src/Repositories/TagRepository.php`

### 17.5 Create TagController class
- [ ] **Status:** Incomplete
- **Description:** Handle tag requests
- **Acceptance criteria:**
  - Implements tag management page
  - Implements AJAX endpoints
- **Files:** `src/Controllers/TagController.php`

### 17.6 Create tag picker component
- [ ] **Status:** Incomplete
- **Description:** Build tag selector UI
- **Acceptance criteria:**
  - Shows available tags
  - Allows multiple selection
  - Creates new tags inline
- **Files:** `src/Views/partials/tag-picker.php`

### 17.7 Create tag badge component
- [ ] **Status:** Incomplete
- **Description:** Display single tag
- **Acceptance criteria:**
  - Shows tag name
  - Shows tag colour
  - Links to filtered list
- **Files:** `src/Views/partials/tag-badge.php`

---

## Phase 18: Custom Attributes

### 18.1 Create AttributeRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement attribute database access
- **Acceptance criteria:**
  - Provides CRUD methods
  - Handles entity associations
- **Files:** `src/Repositories/AttributeRepository.php`

### 18.2 Implement attribute CRUD methods
- [ ] **Status:** Incomplete
- **Description:** Create attribute operations
- **Acceptance criteria:**
  - Implements create method
  - Implements update method
  - Implements delete method
- **Files:** `src/Repositories/AttributeRepository.php`

### 18.3 Implement findByEntity method
- [ ] **Status:** Incomplete
- **Description:** Get entity attributes
- **Acceptance criteria:**
  - Returns ordered list
  - Respects privacy settings
- **Files:** `src/Repositories/AttributeRepository.php`

### 18.4 Create AttributeController class
- [ ] **Status:** Incomplete
- **Description:** Handle attribute requests
- **Acceptance criteria:**
  - Implements inline editing
  - Returns JSON for AJAX
- **Files:** `src/Controllers/AttributeController.php`

### 18.5 Create attribute list partial
- [ ] **Status:** Incomplete
- **Description:** Display entity attributes
- **Acceptance criteria:**
  - Shows name-value pairs
  - Supports inline editing
- **Files:** `src/Views/partials/attributes.php`

### 18.6 Create attribute form partial
- [ ] **Status:** Incomplete
- **Description:** Build attribute editor
- **Acceptance criteria:**
  - Has name field
  - Has value field
  - Has privacy toggle
- **Files:** `src/Views/partials/attribute-form.php`

---

## Phase 19: Posts (Sub-entries)

### 19.1 Create PostRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement post database access
- **Acceptance criteria:**
  - Provides CRUD methods
  - Handles ordering
- **Files:** `src/Repositories/PostRepository.php`

### 19.2 Implement post CRUD methods
- [ ] **Status:** Incomplete
- **Description:** Create post operations
- **Acceptance criteria:**
  - Implements create method
  - Implements update method
  - Implements delete method
- **Files:** `src/Repositories/PostRepository.php`

### 19.3 Implement post reordering
- [ ] **Status:** Incomplete
- **Description:** Change post positions
- **Acceptance criteria:**
  - Updates position values
  - Maintains order integrity
- **Files:** `src/Repositories/PostRepository.php`

### 19.4 Create PostController class
- [ ] **Status:** Incomplete
- **Description:** Handle post requests
- **Acceptance criteria:**
  - Implements CRUD actions
  - Handles reordering
- **Files:** `src/Controllers/PostController.php`

### 19.5 Create posts list partial
- [ ] **Status:** Incomplete
- **Description:** Display entity posts
- **Acceptance criteria:**
  - Shows posts in order
  - Supports collapse/expand
- **Files:** `src/Views/partials/posts.php`

### 19.6 Create post form modal
- [ ] **Status:** Incomplete
- **Description:** Build post editor
- **Acceptance criteria:**
  - Has title field
  - Has content field
  - Has privacy toggle
- **Files:** `src/Views/partials/post-form.php`

---

## Phase 20: File Uploads

### 20.1 Create FileRepository class
- [ ] **Status:** Incomplete
- **Description:** Implement file database access
- **Acceptance criteria:**
  - Provides CRUD methods
  - Handles file storage
- **Files:** `src/Repositories/FileRepository.php`

### 20.2 Implement file upload handling
- [ ] **Status:** Incomplete
- **Description:** Process uploaded files
- **Acceptance criteria:**
  - Moves temp file to storage
  - Generates unique filename
- **Files:** `src/Repositories/FileRepository.php`

### 20.3 Implement file type validation
- [ ] **Status:** Incomplete
- **Description:** Check file MIME types
- **Acceptance criteria:**
  - Allows images
  - Allows documents
  - Rejects executables
- **Files:** `src/Repositories/FileRepository.php`

### 20.4 Implement file size validation
- [ ] **Status:** Incomplete
- **Description:** Check file size limits
- **Acceptance criteria:**
  - Enforces max file size
  - Returns error if exceeded
- **Files:** `src/Repositories/FileRepository.php`

### 20.5 Implement image thumbnail generation
- [ ] **Status:** Incomplete
- **Description:** Create image previews
- **Acceptance criteria:**
  - Resizes to thumbnail size
  - Maintains aspect ratio
- **Files:** `src/Repositories/FileRepository.php`

### 20.6 Implement file deletion
- [ ] **Status:** Incomplete
- **Description:** Remove file records
- **Acceptance criteria:**
  - Deletes database record
  - Removes physical file
- **Files:** `src/Repositories/FileRepository.php`

### 20.7 Create FileController class
- [ ] **Status:** Incomplete
- **Description:** Handle file requests
- **Acceptance criteria:**
  - Implements upload action
  - Implements download action
  - Implements delete action
- **Files:** `src/Controllers/FileController.php`

### 20.8 Create file upload component
- [ ] **Status:** Incomplete
- **Description:** Build file uploader UI
- **Acceptance criteria:**
  - Supports drag-drop
  - Shows upload progress
- **Files:** `src/Views/partials/file-upload.php`

### 20.9 Create file list partial
- [ ] **Status:** Incomplete
- **Description:** Display entity files
- **Acceptance criteria:**
  - Shows file names
  - Shows file thumbnails
  - Has download links
- **Files:** `src/Views/partials/files.php`

---

## Phase 21: Search

### 21.1 Create SearchController class
- [ ] **Status:** Incomplete
- **Description:** Handle search requests
- **Acceptance criteria:**
  - Accepts search query
  - Returns paginated results
- **Files:** `src/Controllers/SearchController.php`

### 21.2 Implement global search action
- [ ] **Status:** Incomplete
- **Description:** Search all entities
- **Acceptance criteria:**
  - Searches by name
  - Searches by description
  - Ranks by relevance
- **Files:** `src/Controllers/SearchController.php`

### 21.3 Create search results template
- [ ] **Status:** Incomplete
- **Description:** Display search results
- **Acceptance criteria:**
  - Shows matching entities
  - Highlights search terms
- **Files:** `src/Views/search/results.php`

### 21.4 Add entity type filter
- [ ] **Status:** Incomplete
- **Description:** Filter results by type
- **Acceptance criteria:**
  - Shows type checkboxes
  - Updates results dynamically
- **Files:** `src/Views/search/results.php`

### 21.5 Create search bar component
- [ ] **Status:** Incomplete
- **Description:** Build header search input
- **Acceptance criteria:**
  - Shows in header
  - Submits on enter
- **Files:** `src/Views/partials/search-bar.php`

### 21.6 Implement live search
- [ ] **Status:** Incomplete
- **Description:** Show results as user types
- **Acceptance criteria:**
  - Debounces input
  - Shows dropdown preview
- **Files:** `public/assets/js/app.js`

---

## Phase 22: Error Handling

### 22.1 Create 404 error template
- [ ] **Status:** Incomplete
- **Description:** Build not found page
- **Acceptance criteria:**
  - Shows friendly message
  - Provides navigation links
- **Files:** `src/Views/errors/404.php`

### 22.2 Create 500 error template
- [ ] **Status:** Incomplete
- **Description:** Build server error page
- **Acceptance criteria:**
  - Shows friendly message
  - Hides technical details
- **Files:** `src/Views/errors/500.php`

### 22.3 Implement global exception handler
- [ ] **Status:** Incomplete
- **Description:** Catch unhandled exceptions
- **Acceptance criteria:**
  - Logs error details
  - Displays error page
- **Files:** `src/Config/ErrorHandler.php`

### 22.4 Implement error logging
- [ ] **Status:** Incomplete
- **Description:** Write errors to log file
- **Acceptance criteria:**
  - Logs exception message
  - Logs stack trace
  - Logs timestamp
- **Files:** `src/Config/ErrorHandler.php`

### 22.5 Implement CSRF protection
- [ ] **Status:** Incomplete
- **Description:** Validate form tokens
- **Acceptance criteria:**
  - Generates token per session
  - Validates on POST requests
  - Rejects invalid tokens
- **Files:** `src/Config/Auth.php`

---

## Phase 23: Testing

### 23.1 Set up PHPUnit configuration
- [ ] **Status:** Incomplete
- **Description:** Configure test framework
- **Acceptance criteria:**
  - Creates phpunit.xml
  - Configures test directories
- **Files:** `phpunit.xml`

### 23.2 Create test database helper
- [ ] **Status:** Incomplete
- **Description:** Set up test database
- **Acceptance criteria:**
  - Creates in-memory database
  - Runs migrations
  - Provides cleanup
- **Files:** `tests/TestCase.php`

### 23.3 Write EntityRepository tests
- [ ] **Status:** Incomplete
- **Description:** Test entity CRUD
- **Acceptance criteria:**
  - Tests create
  - Tests read
  - Tests update
  - Tests delete
- **Files:** `tests/EntityRepositoryTest.php`

### 23.4 Write authentication tests
- [ ] **Status:** Incomplete
- **Description:** Test auth functions
- **Acceptance criteria:**
  - Tests login
  - Tests logout
  - Tests password hashing
- **Files:** `tests/AuthTest.php`

### 23.5 Write router tests
- [ ] **Status:** Incomplete
- **Description:** Test URL routing
- **Acceptance criteria:**
  - Tests route matching
  - Tests parameter extraction
- **Files:** `tests/RouterTest.php`

### 23.6 Write search tests
- [ ] **Status:** Incomplete
- **Description:** Test FTS5 search
- **Acceptance criteria:**
  - Tests basic search
  - Tests ranking
- **Files:** `tests/SearchTest.php`

---

## Phase 24: Documentation

### 24.1 Update README.md
- [ ] **Status:** Incomplete
- **Description:** Write project overview
- **Acceptance criteria:**
  - Describes project purpose
  - Lists features
  - Shows tech stack
- **Files:** `README.md`

### 24.2 Create INSTALL.md
- [ ] **Status:** Incomplete
- **Description:** Write setup instructions
- **Acceptance criteria:**
  - Lists prerequisites
  - Documents installation steps
  - Explains configuration
- **Files:** `INSTALL.md`

### 24.3 Create DOCKER.md
- [ ] **Status:** Incomplete
- **Description:** Write Docker instructions
- **Acceptance criteria:**
  - Documents container build
  - Documents container run
  - Explains volume mounts
- **Files:** `DOCKER.md`

### 24.4 Create CONTRIBUTING.md
- [ ] **Status:** Incomplete
- **Description:** Write contribution guide
- **Acceptance criteria:**
  - Documents code style
  - Explains PR process
- **Files:** `CONTRIBUTING.md`

### 24.5 Document environment variables
- [ ] **Status:** Incomplete
- **Description:** List configuration options
- **Acceptance criteria:**
  - Documents each variable
  - Shows default values
- **Files:** `.env.example`

### 24.6 Document entity JSON schemas
- [ ] **Status:** Incomplete
- **Description:** Explain entity data fields
- **Acceptance criteria:**
  - Lists all entity types
  - Documents each field
- **Files:** `documentation/entity-schemas.md`

---

## Summary

| Phase | Task Count | Focus Area |
|-------|------------|------------|
| 1 | 8 | Project Foundation |
| 2 | 5 | Docker Environment |
| 3 | 7 | Frontend Build Pipeline |
| 4 | 12 | Database Schema |
| 5 | 6 | Database Connection |
| 6 | 17 | Entity Type Schemas |
| 7 | 11 | Routing System |
| 8 | 10 | Request/Response Handling |
| 9 | 10 | Template Engine |
| 10 | 12 | Authentication |
| 11 | 13 | Entity Repository |
| 12 | 10 | Entity Controller |
| 13 | 8 | Campaign Management |
| 14 | 11 | UI Templates |
| 15 | 7 | Markdown Processing |
| 16 | 7 | Relations System |
| 17 | 7 | Tags System |
| 18 | 6 | Custom Attributes |
| 19 | 6 | Posts (Sub-entries) |
| 20 | 9 | File Uploads |
| 21 | 6 | Search |
| 22 | 5 | Error Handling |
| 23 | 6 | Testing |
| 24 | 6 | Documentation |
| **Total** | **205** | |

---

## Notes

- Each task follows the "ralph" principle: granular enough to describe without using the word "and"
- Tasks are designed to be completable in 15-60 minutes each
- Dependencies flow top-to-bottom within phases
- Phases 1-14 form the MVP
- Phases 15-21 add core features
- Phases 22-24 are polish items
