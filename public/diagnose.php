<?php
/**
 * Quick Diagnostic Tool for View Issues
 * Access: http://yoursite.com/diagnose.php
 */

echo "<h1>Laravel Path Diagnostic</h1>";

// Current directory
echo "<h2>Path Information</h2>";
echo "<p><strong>Current Dir:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Parent Dir:</strong> " . dirname(__DIR__) . "</p>";

// Test possible Laravel paths
$possiblePaths = [
    __DIR__.'/../laravel',     // Production: public_html/ and laravel/ are siblings  
    __DIR__.'/..',             // Development: normal Laravel structure
    __DIR__.'/../app',         // Alternative structure where app is in parent
];

echo "<h2>Laravel Root Detection</h2>";
$laravelRoot = null;
foreach ($possiblePaths as $i => $path) {
    $realPath = realpath($path);
    $bootstrapExists = file_exists($path . '/bootstrap/app.php');
    $viewsExists = is_dir($path . '/resources/views');
    $appViewExists = file_exists($path . '/resources/views/app.blade.php');
    
    echo "<p><strong>Path " . ($i + 1) . ":</strong> $path</p>";
    echo "<ul>";
    echo "<li>Real Path: " . ($realPath ?: 'Not found') . "</li>";
    echo "<li>Bootstrap exists: " . ($bootstrapExists ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>Views dir exists: " . ($viewsExists ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>app.blade.php exists: " . ($appViewExists ? '✅ Yes' : '❌ No') . "</li>";
    echo "</ul>";
    
    if ($bootstrapExists && !$laravelRoot) {
        $laravelRoot = $path;
        echo "<p><strong>✅ Selected as Laravel Root</strong></p>";
    }
    echo "<hr>";
}

// Show selected Laravel root info
if ($laravelRoot) {
    echo "<h2>Selected Laravel Root: $laravelRoot</h2>";
    
    // List contents of views directory
    $viewsDir = $laravelRoot . '/resources/views';
    if (is_dir($viewsDir)) {
        echo "<h3>Views Directory Contents:</h3>";
        $files = scandir($viewsDir);
        echo "<ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    }
    
    // Test if we can load Laravel bootstrap
    echo "<h3>Laravel Bootstrap Test:</h3>";
    try {
        define('LARAVEL_START', microtime(true));
        require_once $laravelRoot . '/vendor/autoload.php';
        echo "<p>✅ Autoloader loaded successfully</p>";
        
        $app = require_once $laravelRoot . '/bootstrap/app.php';
        echo "<p>✅ Laravel app bootstrapped successfully</p>";
        
        // Test view path
        $viewPaths = $app->make('view')->getFinder()->getPaths();
        echo "<p><strong>View Paths:</strong></p>";
        echo "<ul>";
        foreach ($viewPaths as $path) {
            echo "<li>" . $path . " - " . (is_dir($path) ? '✅ Exists' : '❌ Missing') . "</li>";
        }
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p>❌ Laravel bootstrap failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h2>❌ No valid Laravel root found!</h2>";
}

echo "<h2>PHP Info</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
?>