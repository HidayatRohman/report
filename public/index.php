<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-detect Laravel root directory based on hosting structure
$possiblePaths = [
    __DIR__.'/../laravel',     // Production: public_html/ and laravel/ are siblings  
    __DIR__.'/..',             // Development: normal Laravel structure
    __DIR__.'/../app',         // Alternative structure where app is in parent
];

$laravelRoot = null;
foreach ($possiblePaths as $path) {
    if (is_dir($path) && file_exists($path . '/bootstrap/app.php')) {
        $laravelRoot = $path;
        break;
    }
}

// Fallback to default production structure if not found
if (!$laravelRoot) {
    $laravelRoot = __DIR__.'/../laravel';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelRoot.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $laravelRoot.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
