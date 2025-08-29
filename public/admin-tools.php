<?php
/**
 * Emergency Admin Tools
 * 
 * This file provides basic admin functions when the main application is not accessible.
 * Access: http://yoursite.com/admin-tools.php
 * 
 * Security: This file should be removed or secured in production.
 * You can add HTTP basic auth or IP restrictions for additional security.
 */

// Security check - only allow admin access
session_start();
$admin_password = 'admin123'; // Change this password!

if (!isset($_SESSION['admin_authenticated'])) {
    if (isset($_POST['password']) && $_POST['password'] === $admin_password) {
        $_SESSION['admin_authenticated'] = true;
    } else {
        showLoginForm();
        exit;
    }
}

// Get the Laravel root directory - adjust for server structure
$currentDir = __DIR__;

// Try multiple approaches to find Laravel directory
$possiblePaths = [
    // Production: public_html/ and laravel/ are siblings
    dirname($currentDir) . '/laravel',
    // Alternative production structure
    dirname(dirname($currentDir)) . '/laravel', 
    // Development: normal Laravel structure
    dirname(__DIR__),
    // If admin-tools.php is in domain root
    $currentDir . '/laravel',
];

$laravelRoot = null;
foreach ($possiblePaths as $path) {
    if (is_dir($path) && file_exists($path . '/artisan')) {
        $laravelRoot = $path;
        break;
    }
}

// If still not found, use default assumption
if (!$laravelRoot) {
    $laravelRoot = dirname($currentDir) . '/laravel';
}

// Verify Laravel directory exists
if (!is_dir($laravelRoot) || !file_exists($laravelRoot . '/artisan')) {
    $debugInfo = "Laravel directory not found. Debug info:\n";
    $debugInfo .= "Current directory: " . $currentDir . "\n";
    $debugInfo .= "Tried paths:\n";
    foreach ($possiblePaths as $i => $path) {
        $debugInfo .= "  " . ($i + 1) . ". " . $path . " - " . (is_dir($path) ? "Directory exists" : "Directory missing") . " - " . (file_exists($path . '/artisan') ? "Artisan found" : "Artisan missing") . "\n";
    }
    $debugInfo .= "Final Laravel root: " . $laravelRoot . "\n";
    $debugInfo .= "Directory listing of parent: " . print_r(scandir(dirname($currentDir)), true);
    die('<pre>' . $debugInfo . '</pre>');
}

chdir($laravelRoot);

$output = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    try {
        // Add debug info and clean up any wrong storage links
        if ($action === 'storage_link') {
            $output .= "Debug Info:\n";
            $output .= "Current Dir: " . $currentDir . "\n";
            $output .= "Laravel Root: " . $laravelRoot . "\n";
            $output .= "Working Directory: " . getcwd() . "\n";
            $output .= "Server Structure: Production (admin-tools.php in public_html)\n";
            $output .= "PHP Version: " . PHP_VERSION . "\n";
            $output .= "PHP Binary: " . (defined('PHP_BINARY') ? PHP_BINARY : 'Not defined') . "\n";
            $output .= "shell_exec available: " . (function_exists('shell_exec') ? 'Yes' : 'No') . "\n";
            
            // Clean up wrong storage link in laravel/public if it exists
            $wrongStoragePath = $laravelRoot . '/public/storage';
            if (file_exists($wrongStoragePath)) {
                $output .= "\n⚠️ Removing wrong storage link at: " . $wrongStoragePath . "\n";
                if (is_link($wrongStoragePath)) {
                    unlink($wrongStoragePath);
                    $output .= "✅ Wrong symlink removed\n";
                } else {
                    $output .= "⚠️ Wrong storage is not a symlink, manual cleanup needed\n";
                }
            }
            
            $checkPublicPath = $currentDir; // public_html directory
            $checkStoragePath = $checkPublicPath . '/storage';
            
            $output .= "Public Path: " . $checkPublicPath . "\n";
            $output .= "Storage Target: " . $laravelRoot . '/storage/app/public' . "\n";
            $output .= "Storage Link Path: " . $checkStoragePath . "\n";
            $output .= "Storage app/public exists: " . (is_dir($laravelRoot . '/storage/app/public') ? 'Yes' : 'No') . "\n";
            $output .= "Public/storage exists: " . (file_exists($checkStoragePath) ? 'Yes' : 'No') . "\n\n";
        }
        
        switch ($action) {
            case 'clear_cache':
                $output = executeCommand('php artisan cache:clear');
                $output .= "\n" . executeCommand('php artisan config:clear');
                $output .= "\n" . executeCommand('php artisan route:clear');
                $output .= "\n" . executeCommand('php artisan view:clear');
                break;
                
            case 'optimize_clear':
                $output = executeCommand('php artisan optimize:clear');
                break;
                
            case 'optimize':
                $output = executeCommand('php artisan optimize');
                break;
                
            case 'storage_link':
                // Determine the correct public path structure
                // admin-tools.php is in public_html, so storage link should be created here
                $publicPath = $currentDir; // Same directory as admin-tools.php
                $storagePath = $publicPath . '/storage';
                $storageTarget = $laravelRoot . '/storage/app/public';
                
                $output .= "Debug Storage Link Info:\n";
                $output .= "Public Path: " . $publicPath . "\n";
                $output .= "Storage Link Path: " . $storagePath . "\n";
                $output .= "Storage Target: " . $storageTarget . "\n";
                $output .= "Target exists: " . (is_dir($storageTarget) ? 'Yes' : 'No') . "\n";
                $output .= "Link exists: " . (file_exists($storagePath) ? 'Yes' : 'No') . "\n";
                
                if (file_exists($storagePath)) {
                    if (is_link($storagePath)) {
                        $target = readlink($storagePath);
                        $output .= "Current link target: " . $target . "\n";
                        $output .= "Target accessible: " . (is_readable($target) ? 'Yes' : 'No') . "\n";
                        
                        // Check if link points to wrong location
                        if ($target !== $storageTarget) {
                            $output .= "\n❌ Link points to wrong location. Recreating...\n";
                            unlink($storagePath);
                            if (symlink($storageTarget, $storagePath)) {
                                $output .= "✅ Link recreated successfully!\n";
                            } else {
                                $output .= "❌ Failed to recreate link\n";
                            }
                        } else {
                            $output .= "\n✅ Storage link exists and points to correct location\n";
                            
                            // Check permissions
                            $perms = fileperms($storageTarget);
                            $output .= "Target permissions: " . decoct($perms & 0777) . "\n";
                        }
                    } else {
                        $output .= "\n⚠️ Storage exists but is not a symlink (directory/file)\n";
                        $output .= "Consider removing and recreating as symlink\n";
                    }
                } else {
                    // Create new storage link
                    if (!is_dir($storageTarget)) {
                        $output .= "\n❌ Target directory does not exist: " . $storageTarget;
                    } else {
                        $output .= "\n🔨 Creating storage link...\n";
                        
                        // Try artisan command first
                        $artisanOutput = executeCommand('php artisan storage:link');
                        $output .= "Artisan output: " . $artisanOutput . "\n";
                        
                        // Check if it worked
                        if (file_exists($storagePath)) {
                            $output .= "✅ Storage link created via artisan!\n";
                        } else {
                            // Try manual symlink
                            $output .= "Artisan failed, trying manual symlink...\n";
                            if (function_exists('symlink')) {
                                if (symlink($storageTarget, $storagePath)) {
                                    $output .= "✅ Manual symlink created!\n";
                                } else {
                                    $output .= "❌ Manual symlink failed\n";
                                }
                            } else {
                                $output .= "❌ symlink() function not available\n";
                            }
                        }
                    }
                }
                break;
                
            case 'migrate':
                $output = executeCommand('php artisan migrate --force');
                break;
                
            case 'migrate_fresh':
                $output = executeCommand('php artisan migrate:fresh --force');
                break;
                
            case 'config_cache':
                $output = executeCommand('php artisan config:cache');
                break;
                
            case 'route_cache':
                $output = executeCommand('php artisan route:cache');
                break;
                
            case 'maintenance_down':
                $output = executeCommand('php artisan down --secret=admin-secret');
                break;
                
            case 'maintenance_up':
                $output = executeCommand('php artisan up');
                break;
                
            case 'key_generate':
                $output = executeCommand('php artisan key:generate --force');
                break;
                
            case 'clear_logs':
                $logPath = $laravelRoot . '/storage/logs';
                if (is_dir($logPath)) {
                    $files = glob($logPath . '/*.log');
                    $count = 0;
                    foreach ($files as $file) {
                        if (unlink($file)) {
                            $count++;
                        }
                    }
                    $output = "Deleted {$count} log files";
                } else {
                    $output = "Log directory not found";
                }
                break;
                
            case 'cleanup_storage':
                $wrongStoragePath = $laravelRoot . '/public/storage';
                $correctStoragePath = $currentDir . '/storage';
                $storageTarget = $laravelRoot . '/storage/app/public';
                
                $output = "Storage Cleanup:\n";
                
                // Remove wrong storage link in laravel/public
                if (file_exists($wrongStoragePath)) {
                    $output .= "Removing wrong storage link: " . $wrongStoragePath . "\n";
                    if (is_link($wrongStoragePath)) {
                        unlink($wrongStoragePath);
                        $output .= "✅ Wrong symlink removed\n";
                    } else {
                        $output .= "⚠️ Not a symlink, skipping\n";
                    }
                }
                
                // Handle correct storage path in public_html
                if (file_exists($correctStoragePath)) {
                    if (is_link($correctStoragePath)) {
                        $target = readlink($correctStoragePath);
                        $output .= "✅ Storage is already a symlink pointing to: " . $target . "\n";
                        if ($target !== $storageTarget) {
                            $output .= "⚠️ Symlink points to wrong target, fixing...\n";
                            unlink($correctStoragePath);
                            symlink($storageTarget, $correctStoragePath);
                            $output .= "✅ Symlink recreated with correct target\n";
                        }
                    } else {
                        // Remove directory and create symlink
                        $output .= "⚠️ Storage exists as directory/file, not symlink. Removing...\n";
                        if (is_dir($correctStoragePath)) {
                            // Remove directory recursively
                            removeDirectory($correctStoragePath);
                            $output .= "✅ Directory removed\n";
                        } else {
                            unlink($correctStoragePath);
                            $output .= "✅ File removed\n";
                        }
                        
                        // Create symlink
                        if (symlink($storageTarget, $correctStoragePath)) {
                            $output .= "✅ Created correct storage symlink\n";
                        } else {
                            $output .= "❌ Failed to create storage symlink\n";
                        }
                    }
                } else {
                    // Create new symlink
                    if (symlink($storageTarget, $correctStoragePath)) {
                        $output .= "✅ Created new storage symlink at: " . $correctStoragePath . "\n";
                    } else {
                        $output .= "❌ Failed to create storage symlink\n";
                    }
                }
                
                // Verify the symlink works
                if (is_link($correctStoragePath) && is_readable($correctStoragePath)) {
                    $output .= "✅ Storage symlink is working and readable\n";
                } else {
                    $output .= "❌ Storage symlink may have issues\n";
                }
                break;
                
            case 'fix_storage_permissions':
                $publicPath = $currentDir;
                $storagePath = $publicPath . '/storage';
                $storageTarget = $laravelRoot . '/storage/app/public';
                
                $output = "Fixing Storage Permissions:\n";
                
                // Set proper permissions for storage target
                if (is_dir($storageTarget)) {
                    chmod($storageTarget, 0755);
                    $output .= "✅ Set target directory permissions to 755\n";
                    
                    // Set permissions for files inside
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storageTarget));
                    $count = 0;
                    foreach ($iterator as $file) {
                        if ($file->isFile()) {
                            chmod($file->getPathname(), 0644);
                            $count++;
                        } elseif ($file->isDir() && !in_array($file->getFilename(), ['.', '..'])) {
                            chmod($file->getPathname(), 0755);
                        }
                    }
                    $output .= "✅ Fixed permissions for {$count} files\n";
                } else {
                    $output .= "❌ Storage target directory not found\n";
                }
                break;
                
            case 'backup_env':
                $envPath = $laravelRoot . '/.env';
                $backupPath = $laravelRoot . '/.env.backup.' . date('Y-m-d_H-i-s');
                
                if (file_exists($envPath)) {
                    if (copy($envPath, $backupPath)) {
                        $output = "✅ Environment file backed up to: " . basename($backupPath);
                    } else {
                        $output = "❌ Failed to backup environment file";
                    }
                } else {
                    $output = "❌ Environment file not found";
                }
                break;
                
            case 'check_env':
                $envPath = $laravelRoot . '/.env';
                $envExamplePath = $laravelRoot . '/.env.example';
                
                $output = "Environment File Check:\n";
                $output .= ".env exists: " . (file_exists($envPath) ? 'Yes' : 'No') . "\n";
                $output .= ".env.example exists: " . (file_exists($envExamplePath) ? 'Yes' : 'No') . "\n";
                
                if (file_exists($envPath)) {
                    $envSize = filesize($envPath);
                    $output .= ".env size: " . $envSize . " bytes\n";
                    $output .= ".env modified: " . date('Y-m-d H:i:s', filemtime($envPath)) . "\n";
                    
                    // Check key variables
                    $envContent = file_get_contents($envPath);
                    $requiredVars = ['APP_KEY', 'DB_CONNECTION', 'DB_DATABASE'];
                    foreach ($requiredVars as $var) {
                        $exists = strpos($envContent, $var . '=') !== false;
                        $output .= "{$var}: " . ($exists ? 'Set' : 'Missing') . "\n";
                    }
                }
                break;
                
            case 'health_check':
                $output = "System Health Check:\n\n";
                
                // PHP Info
                $output .= "🔧 PHP Information:\n";
                $output .= "PHP Version: " . PHP_VERSION . "\n";
                $output .= "Memory Limit: " . ini_get('memory_limit') . "\n";
                $output .= "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
                $output .= "Upload Max Size: " . ini_get('upload_max_filesize') . "\n";
                $output .= "Post Max Size: " . ini_get('post_max_size') . "\n";
                
                // Extensions
                $output .= "\n🔌 Extensions:\n";
                $requiredExtensions = ['pdo', 'mbstring', 'tokenizer', 'json', 'openssl', 'curl'];
                foreach ($requiredExtensions as $ext) {
                    $loaded = extension_loaded($ext);
                    $output .= "{$ext}: " . ($loaded ? '✅ Loaded' : '❌ Missing') . "\n";
                }
                
                // Laravel Files
                $output .= "\n📁 Laravel Files:\n";
                $files = ['artisan', 'composer.json', '.env', 'bootstrap/app.php'];
                foreach ($files as $file) {
                    $exists = file_exists($laravelRoot . '/' . $file);
                    $output .= "{$file}: " . ($exists ? '✅ Exists' : '❌ Missing') . "\n";
                }
                
                // Directories
                $output .= "\n📂 Directories:\n";
                $dirs = ['storage', 'storage/app', 'storage/logs', 'storage/framework', 'bootstrap/cache'];
                foreach ($dirs as $dir) {
                    $path = $laravelRoot . '/' . $dir;
                    $exists = is_dir($path);
                    $writable = $exists ? is_writable($path) : false;
                    $output .= "{$dir}: " . ($exists ? '✅ Exists' : '❌ Missing') . 
                              ($writable ? ' (Writable)' : ($exists ? ' (Not Writable)' : '')) . "\n";
                }
                break;
                
            case 'composer_status':
                $output = "Composer Status:\n";
                $output .= executeCommand('composer --version');
                $output .= "\n\nComposer Dependencies:\n";
                $output .= executeCommand('composer show --installed');
                break;
                
            case 'queue_status':
                $output = "Queue Status:\n";
                $output .= executeCommand('php artisan queue:work --stop-when-empty --timeout=10');
                break;
                
            case 'create_symlinks':
                $output = "Creating Symlinks:\n";
                
                // Storage link
                $publicPath = $currentDir;
                $storagePath = $publicPath . '/storage';
                $storageTarget = $laravelRoot . '/storage/app/public';
                
                if (!file_exists($storagePath) && is_dir($storageTarget)) {
                    if (symlink($storageTarget, $storagePath)) {
                        $output .= "✅ Storage symlink created\n";
                    } else {
                        $output .= "❌ Failed to create storage symlink\n";
                    }
                } else {
                    $output .= "⚠️ Storage symlink already exists or target missing\n";
                }
                
                // Check for other common symlinks needed
                $output .= "\nSymlink Status:\n";
                $output .= "Storage: " . (is_link($storagePath) ? '✅ Symlink' : '❌ Not a symlink') . "\n";
                break;
                
            case 'clear_all_cache':
                $output = "Clearing All Cache and Optimization:\n";
                $output .= executeCommand('php artisan cache:clear');
                $output .= "\n" . executeCommand('php artisan config:clear');
                $output .= "\n" . executeCommand('php artisan route:clear');
                $output .= "\n" . executeCommand('php artisan view:clear');
                $output .= "\n" . executeCommand('php artisan optimize:clear');
                break;
                
            case 'show_env':
                $envPath = $laravelRoot . '/.env';
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    // Mask sensitive values
                    $maskedContent = preg_replace('/(APP_KEY|DB_PASSWORD|.*_SECRET|.*_TOKEN|.*_KEY)=(.+)/i', '$1=***MASKED***', $envContent);
                    $output = "Environment File Content (sensitive values masked):\n\n" . $maskedContent;
                } else {
                    $output = "❌ Environment file not found";
                }
                break;
                
            case 'disk_space':
                $output = "Disk Space Information:\n";
                
                // Laravel directory size
                $laravelSize = getDirSize($laravelRoot);
                $output .= "Laravel directory: " . formatBytes($laravelSize) . "\n";
                
                // Storage directory size
                $storageSize = getDirSize($laravelRoot . '/storage');
                $output .= "Storage directory: " . formatBytes($storageSize) . "\n";
                
                // Log files size
                $logsSize = getDirSize($laravelRoot . '/storage/logs');
                $output .= "Log files: " . formatBytes($logsSize) . "\n";
                
                // Available disk space
                $freeSpace = disk_free_space($laravelRoot);
                $totalSpace = disk_total_space($laravelRoot);
                $usedSpace = $totalSpace - $freeSpace;
                
                $output .= "\nDisk Usage:\n";
                $output .= "Used: " . formatBytes($usedSpace) . "\n";
                $output .= "Free: " . formatBytes($freeSpace) . "\n";
                $output .= "Total: " . formatBytes($totalSpace) . "\n";
                $output .= "Usage: " . round(($usedSpace / $totalSpace) * 100, 2) . "%\n";
                break;
                
            case 'logout':
                session_destroy();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
                break;
                
            default:
                $error = "Unknown action: " . htmlspecialchars($action);
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function executeCommand($command) {
    // Check if shell_exec is available
    if (!function_exists('shell_exec')) {
        return 'Error: shell_exec function is disabled on this server';
    }
    
    // Add full path to PHP if needed
    if (strpos($command, 'php ') === 0) {
        // Try multiple methods to find PHP executable
        $phpPath = null;
        
        // Method 1: Use PHP_BINARY constant (most reliable)
        if (defined('PHP_BINARY') && is_executable(PHP_BINARY)) {
            $phpPath = PHP_BINARY;
        }
        // Method 2: Try common paths
        elseif (is_executable('/usr/bin/php')) {
            $phpPath = '/usr/bin/php';
        }
        elseif (is_executable('/usr/local/bin/php')) {
            $phpPath = '/usr/local/bin/php';
        }
        // Method 3: Use which/where command (if available)
        else {
            $which = trim(shell_exec('which php 2>/dev/null') ?: '');
            if ($which && is_executable($which)) {
                $phpPath = $which;
            }
        }
        
        if ($phpPath) {
            $command = str_replace('php ', escapeshellarg($phpPath) . ' ', $command);
        } else {
            return 'Error: Could not find PHP executable. Tried PHP_BINARY, /usr/bin/php, /usr/local/bin/php';
        }
    }
    
    $output = shell_exec($command . ' 2>&1');
    return $output ?: 'Command executed (no output)';
}

function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

function getDirSize($dir) {
    if (!is_dir($dir)) {
        return 0;
    }
    
    $size = 0;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($files as $file) {
        $size += $file->getSize();
    }
    return $size;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

function showLoginForm() {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Tools - Login</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
            button:hover { background: #005a87; }
            .warning { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <h2>Admin Tools Login</h2>
        <div class="warning">
            <strong>Warning:</strong> This is an emergency admin tool. Remove this file in production.
        </div>
        <form method="post">
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>
    <?php
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Emergency Admin Tools</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .container { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin: 20px 0; 
        }
        .card { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 6px; 
            border: 1px solid #e9ecef; 
        }
        .card h3 { margin: 0 0 10px 0; color: #495057; }
        button { 
            background: #007cba; 
            color: white; 
            padding: 8px 16px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin: 5px 5px 5px 0;
        }
        button:hover { background: #005a87; }
        button.danger { background: #dc3545; }
        button.danger:hover { background: #c82333; }
        button.warning { background: #ffc107; color: #212529; }
        button.warning:hover { background: #e0a800; }
        .output { 
            background: #f8f9fa; 
            border: 1px solid #e9ecef; 
            padding: 15px; 
            border-radius: 4px; 
            white-space: pre-wrap; 
            font-family: monospace; 
            margin: 20px 0;
            max-height: 300px;
            overflow-y: auto;
        }
        .error { 
            background: #f8d7da; 
            border: 1px solid #f5c6cb; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 4px; 
            margin: 20px 0;
        }
        .success { 
            background: #d1edff; 
            border: 1px solid #bee5eb; 
            color: #0c5460; 
            padding: 15px; 
            border-radius: 4px; 
            margin: 20px 0;
        }
        .warning-banner { 
            background: #fff3cd; 
            border: 1px solid #ffeeba; 
            color: #856404; 
            padding: 15px; 
            border-radius: 4px; 
            margin-bottom: 20px;
        }
        .system-info { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 10px; 
            margin: 20px 0;
            background: #e9ecef;
            padding: 15px;
            border-radius: 4px;
        }
        .info-item { font-size: 14px; }
        .info-item strong { color: #495057; }
        .logout { float: right; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Emergency Admin Tools</h1>
            <form method="post" style="margin: 0;">
                <button type="submit" name="action" value="logout" class="danger logout">Logout</button>
            </form>
        </div>
        
        <div class="warning-banner">
            <strong>Warning:</strong> This is an emergency administration tool. Remove this file in production or restrict access via IP/authentication.
        </div>

        <div class="system-info">
            <div class="info-item"><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></div>
            <div class="info-item"><strong>Laravel Root:</strong> <?php echo $laravelRoot; ?></div>
            <div class="info-item"><strong>Storage Link:</strong> <?php echo file_exists($laravelRoot . '/public/storage') ? 'Exists' : 'Missing'; ?></div>
            <div class="info-item"><strong>Environment:</strong> <?php echo file_exists($laravelRoot . '/.env') ? 'File exists' : 'Missing'; ?></div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($output): ?>
            <div class="success">Command executed successfully!</div>
            <div class="output"><?php echo htmlspecialchars($output); ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h3>Cache Management</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="clear_cache">Clear All Cache</button>
                    <button type="submit" name="action" value="optimize_clear">Clear Optimization</button>
                </form>
            </div>

            <div class="card">
                <h3>Application Optimization</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="optimize">Optimize App</button>
                    <button type="submit" name="action" value="config_cache">Cache Config</button>
                    <button type="submit" name="action" value="route_cache">Cache Routes</button>
                </form>
            </div>

            <div class="card">
                <h3>Storage</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="storage_link">Debug Storage Link</button>
                    <button type="submit" name="action" value="cleanup_storage" class="warning">Cleanup Storage</button>
                    <button type="submit" name="action" value="fix_storage_permissions" class="warning">Fix Storage Permissions</button>
                    <button type="submit" name="action" value="clear_logs" class="warning">Clear Log Files</button>
                </form>
            </div>

            <div class="card">
                <h3>Database</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="migrate">Run Migrations</button>
                    <button type="submit" name="action" value="migrate_fresh" class="danger" onclick="return confirm('This will delete all data. Are you sure?')">Fresh Migration</button>
                </form>
            </div>

            <div class="card">
                <h3>Maintenance Mode</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="maintenance_down" class="warning">Enable Maintenance</button>
                    <button type="submit" name="action" value="maintenance_up">Disable Maintenance</button>
                </form>
            </div>

            <div class="card">
                <h3>Security</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="key_generate" class="danger" onclick="return confirm('This will generate a new APP_KEY. Continue?')">Generate App Key</button>
                </form>
            </div>

            <div class="card">
                <h3>Environment</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="check_env">Check .env File</button>
                    <button type="submit" name="action" value="show_env">Show .env Content</button>
                    <button type="submit" name="action" value="backup_env" class="warning">Backup .env File</button>
                </form>
            </div>

            <div class="card">
                <h3>System Health</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="health_check">System Health Check</button>
                    <button type="submit" name="action" value="composer_status">Composer Status</button>
                    <button type="submit" name="action" value="disk_space">Disk Space Usage</button>
                </form>
            </div>

            <div class="card">
                <h3>Advanced Tools</h3>
                <form method="post" style="margin: 0;">
                    <button type="submit" name="action" value="clear_all_cache" class="warning">Clear All Cache</button>
                    <button type="submit" name="action" value="create_symlinks">Create Symlinks</button>
                    <button type="submit" name="action" value="queue_status">Check Queue Status</button>
                </form>
            </div>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 12px; color: #6c757d;">
            <p><strong>Emergency Access:</strong> If the main application is broken, you can use this tool to clear cache and fix common issues.</p>
            <p><strong>Security Note:</strong> This file should be removed or secured in production. Consider adding HTTP basic authentication or IP restrictions.</p>
        </div>
    </div>
</body>
</html>