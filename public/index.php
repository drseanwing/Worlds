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
