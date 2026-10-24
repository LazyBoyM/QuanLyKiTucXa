<?php
/**
 * DormFlow - Modern Dormitory Management System
 * Entry Point
 */

// Start session
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/dormflow');

// Load environment variables
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env');
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        putenv($line);
    }
}

// Error reporting
if (getenv('APP_DEBUG') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load helpers
require_once BASE_PATH . '/app/Helpers/functions.php';

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load routes
$routes = require_once BASE_PATH . '/routes/web.php';

// Initialize router
$router = new App\Core\Router();

// Register routes
foreach ($routes as $route) {
    $method = strtolower($route['method']);
    $router->$method($route['path'], $route['handler'], $route['middlewares'] ?? []);
}

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_GET['url'] ?? '/';

try {
    $router->dispatch($method, $uri);
} catch (Exception $e) {
    if (getenv('APP_DEBUG') === 'true') {
        echo '<h1>Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>500 - Internal Server Error</h1>';
    }
}