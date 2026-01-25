<?php

/**
 * Worlds - Front Controller
 * 
 * Application entry point that initializes the autoloader,
 * configures the router, and dispatches incoming requests.
 */

// Ensure we're running from the public directory
define('PUBLIC_PATH', __DIR__);
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo '<h1>Error: Autoloader not found</h1>';
    echo '<p>Run <code>composer install</code> to install dependencies.</p>';
    exit(1);
}

require_once $autoloadPath;

// Load helper functions (included via composer autoload, but also load manually as fallback)
$helpersPath = BASE_PATH . '/src/Config/helpers.php';
if (file_exists($helpersPath)) {
    require_once $helpersPath;
}

use Worlds\Config\Config;
use Worlds\Config\Database;
use Worlds\Config\Router;
use Worlds\Config\Request;
use Worlds\Controllers\AuthController;
use Worlds\Controllers\CampaignController;
use Worlds\Controllers\EntityController;
use Worlds\Controllers\TagController;
use Worlds\Controllers\RelationController;
use Worlds\Controllers\AttributeController;
use Worlds\Controllers\PostController;
use Worlds\Controllers\FileController;

// Load environment configuration
Config::load();

// Enable error display in debug mode
if (Config::isDebugMode()) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Run database migrations (only in debug mode or when AUTO_MIGRATE=true)
// In production, migrations should be run during deployment
if (Config::isDebugMode() || Config::getBool('AUTO_MIGRATE', false)) {
    try {
        Database::runMigrations();
    } catch (Exception $e) {
        if (Config::isDebugMode()) {
            echo '<h1>Database Error</h1>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            exit(1);
        }
        // In production, log the error and continue
        error_log('Database migration error: ' . $e->getMessage());
    }
}

// Initialize router
$router = new Router();

// Define application routes
// Home page
$router->get('/', function (Request $request) {
    $appName = Config::getAppName();
    
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$appName}</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800">{$appName}</h1>
            <p class="text-gray-600 mt-2">A lightweight worldbuilding tool</p>
        </header>
        
        <main class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Welcome!</h2>
            <p class="text-gray-600 mb-4">
                Your worldbuilding application is up and running.
            </p>
            <p class="text-sm text-gray-500">
                The routing system is working. Start adding your routes and controllers.
            </p>
        </main>
    </div>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
HTML;
});

// Dashboard route
$router->get('/dashboard', function (Request $request) {
    // Require authentication
    if (!\Worlds\Config\Auth::check()) {
        return \Worlds\Config\Response::redirect('/login');
    }

    $view = new \Worlds\Config\View();

    // Get active campaign info
    $campaignId = \Worlds\Config\Auth::getActiveCampaignId();
    $campaign = null;
    $entities = [];
    $stats = ['characters' => 0, 'locations' => 0, 'quests' => 0, 'total' => 0];

    if ($campaignId) {
        $campaignRepo = new \Worlds\Repositories\CampaignRepository();
        $campaign = $campaignRepo->findById($campaignId);

        $entityRepo = new \Worlds\Repositories\EntityRepository();
        // Get recent entities
        $entities = $entityRepo->findByCampaign($campaignId, 1, 6)['data'] ?? [];

        // Simple stats (counts by type)
        $pdo = \Worlds\Config\Database::getInstance();
        $stmt = $pdo->prepare("SELECT entity_type, COUNT(*) as count FROM entities WHERE campaign_id = ? GROUP BY entity_type");
        $stmt->execute([$campaignId]);
        $typeCounts = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $stats = [
            'characters' => $typeCounts['character'] ?? 0,
            'locations' => $typeCounts['location'] ?? 0,
            'quests' => $typeCounts['quest'] ?? 0,
            'total' => array_sum($typeCounts)
        ];
    }

    return \Worlds\Config\Response::html($view->render('dashboard', [
        'campaign' => $campaign,
        'entities' => $entities,
        'stats' => $stats
    ]));
});

// Auth routes
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// Campaign routes
$router->get('/campaigns', [CampaignController::class, 'index']);
$router->get('/campaigns/create', [CampaignController::class, 'create']);
$router->post('/campaigns', [CampaignController::class, 'store']);
$router->get('/campaigns/{id}', [CampaignController::class, 'show']);
$router->get('/campaigns/{id}/edit', [CampaignController::class, 'edit']);
$router->put('/campaigns/{id}', [CampaignController::class, 'update']);
$router->delete('/campaigns/{id}', [CampaignController::class, 'destroy']);
$router->post('/campaigns/{id}/switch', [CampaignController::class, 'switchCampaign']);

// Entity routes - {type} is the entity type (character, location, etc.)
$router->get('/entities/{type}', [EntityController::class, 'index']);
$router->get('/entities/{type}/create', [EntityController::class, 'create']);
$router->post('/entities/{type}', [EntityController::class, 'store']);
$router->get('/entities/{type}/{id}', [EntityController::class, 'show']);
$router->get('/entities/{type}/{id}/edit', [EntityController::class, 'edit']);
$router->put('/entities/{type}/{id}', [EntityController::class, 'update']);
$router->delete('/entities/{type}/{id}', [EntityController::class, 'destroy']);

// Tag routes
$router->get('/tags', [TagController::class, 'index']);
$router->post('/tags', [TagController::class, 'store']);
$router->put('/tags/{id}', [TagController::class, 'update']);
$router->delete('/tags/{id}', [TagController::class, 'destroy']);

// Entity-Tag association routes (API endpoints)
$router->post('/api/entities/{id}/tags', [TagController::class, 'attach']);
$router->delete('/api/entities/{id}/tags/{tagId}', [TagController::class, 'detach']);

// Relation routes (API endpoints)
$router->get('/api/entities/{id}/relations', [RelationController::class, 'index']);
$router->post('/api/entities/{id}/relations', [RelationController::class, 'store']);
$router->put('/api/relations/{id}', [RelationController::class, 'update']);
$router->delete('/api/relations/{id}', [RelationController::class, 'destroy']);

// Attribute routes (API endpoints)
$router->post('/api/entities/{id}/attributes', [AttributeController::class, 'store']);
$router->put('/api/attributes/{id}', [AttributeController::class, 'update']);
$router->delete('/api/attributes/{id}', [AttributeController::class, 'destroy']);

// Post routes (API endpoints)
$router->post('/api/entities/{id}/posts', [PostController::class, 'store']);
$router->put('/api/posts/{id}', [PostController::class, 'update']);
$router->delete('/api/posts/{id}', [PostController::class, 'destroy']);
$router->post('/api/entities/{id}/posts/reorder', [PostController::class, 'reorder']);

// File routes
$router->post('/api/entities/{id}/files', [FileController::class, 'store']);
$router->delete('/api/files/{id}', [FileController::class, 'destroy']);
$router->get('/files/{id}', [FileController::class, 'download']);
$router->get('/files/{id}/thumb', [FileController::class, 'download']);

// Set custom 404 handler
$router->setNotFoundHandler(function (Request $request) {
    $path = htmlspecialchars($request->getPath());
    $appName = Config::getAppName();
    
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - {$appName}</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-gray-300">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mt-4">Page Not Found</h2>
        <p class="text-gray-500 mt-2">The page <code class="bg-gray-200 px-2 py-1 rounded">{$path}</code> doesn't exist.</p>
        <a href="/" class="inline-block mt-6 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            Return Home
        </a>
    </div>
</body>
</html>
HTML;
});

// Create request from current HTTP request
$request = Request::createFromGlobals();

// Dispatch the request and capture the response
$response = $router->dispatch($request);

// If the handler returned a string, output it
if (is_string($response)) {
    echo $response;
}
