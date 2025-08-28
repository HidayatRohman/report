# Build Production Full - Include Vendor dan Custom Domain
Write-Host "Memulai build production full dengan vendor..." -ForegroundColor Green

$ProjectRoot = $PSScriptRoot | Split-Path -Parent
$DistDir = Join-Path $ProjectRoot "dist-full"
$LaravelDir = Join-Path $DistDir "laravel"
$OmzetDir = Join-Path $DistDir "omzet.anekabisnismurah.com"
$Timestamp = Get-Date -Format "yyyyMMdd-HHmmss"

try {
    # 1. Bersihkan folder dist-full jika ada
    Write-Host "Membersihkan folder dist-full..." -ForegroundColor Yellow
    if (Test-Path $DistDir) {
        Remove-Item $DistDir -Recurse -Force
    }
    New-Item -ItemType Directory -Path $DistDir -Force | Out-Null
    New-Item -ItemType Directory -Path $LaravelDir -Force | Out-Null
    New-Item -ItemType Directory -Path $OmzetDir -Force | Out-Null

    # 2. Install dependencies dan build frontend
    Write-Host "Menginstall dependencies..." -ForegroundColor Yellow
    Set-Location $ProjectRoot
    & npm install
    
    Write-Host "Building frontend assets..." -ForegroundColor Yellow
    & npm run build

    # 3. Pastikan composer dependencies lengkap (termasuk vendor)
    Write-Host "Menginstall Composer dependencies lengkap untuk production..." -ForegroundColor Yellow
    & composer install --optimize-autoloader --no-dev

    # 4. Copy semua file backend (Laravel) termasuk vendor
    Write-Host "Menyalin semua file backend Laravel (termasuk vendor)..." -ForegroundColor Yellow
    
    $BackendFiles = @(
        "app",
        "bootstrap", 
        "config",
        "database",
        "routes",
        "storage",
        "vendor",
        "artisan",
        "composer.json",
        "composer.lock",
        ".htaccess"
    )

    foreach ($file in $BackendFiles) {
        $srcPath = Join-Path $ProjectRoot $file
        $destPath = Join-Path $LaravelDir $file
        
        if (Test-Path $srcPath) {
            Write-Host "Copying: $file..." -ForegroundColor Cyan
            Copy-Item $srcPath $destPath -Recurse -Force
            Write-Host "Copied: $file" -ForegroundColor Green
        } else {
            Write-Host "Skipped: $file (not found)" -ForegroundColor Yellow
        }
    }

    # 5. Copy .env.example
    $envExampleSrc = Join-Path $ProjectRoot ".env.example"
    $envExampleDest = Join-Path $LaravelDir ".env.example"
    if (Test-Path $envExampleSrc) {
        Copy-Item $envExampleSrc $envExampleDest -Force
        Write-Host "Copied: .env.example" -ForegroundColor Green
    }

    # 6. Copy frontend files ke domain omzet.anekabisnismurah.com
    Write-Host "Menyalin file frontend ke omzet.anekabisnismurah.com..." -ForegroundColor Yellow
    $publicPath = Join-Path $ProjectRoot "public"
    if (Test-Path $publicPath) {
        Copy-Item "$publicPath\*" $OmzetDir -Recurse -Force
        Write-Host "Copied: public files to omzet.anekabisnismurah.com" -ForegroundColor Green
    }

    # 7. Buat folder storage yang diperlukan dengan permissions
    Write-Host "Membuat struktur folder storage..." -ForegroundColor Yellow
    $StorageDirs = @(
        "storage\framework\cache\data",
        "storage\framework\sessions", 
        "storage\framework\views",
        "storage\logs",
        "storage\app\public",
        "bootstrap\cache"
    )

    foreach ($dir in $StorageDirs) {
        $fullPath = Join-Path $LaravelDir $dir
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
        Write-Host "Created: $dir" -ForegroundColor Green
    }

    # 8. Buat file .env.production dengan domain yang benar
    Write-Host "Membuat file konfigurasi production untuk domain omzet.anekabisnismurah.com..." -ForegroundColor Yellow
    $envLines = @(
        "APP_NAME=ReportApp",
        "APP_ENV=production",
        "APP_KEY=",
        "APP_DEBUG=false",
        "APP_TIMEZONE=Asia/Jakarta", 
        "APP_URL=https://omzet.anekabisnismurah.com",
        "",
        "LOG_CHANNEL=stack",
        "LOG_DEPRECATIONS_CHANNEL=null", 
        "LOG_LEVEL=error",
        "",
        "DB_CONNECTION=mysql",
        "DB_HOST=localhost",
        "DB_PORT=3306",
        "DB_DATABASE=omzet_db",
        "DB_USERNAME=omzet_user",
        "DB_PASSWORD=your_secure_password",
        "",
        "BROADCAST_CONNECTION=log",
        "CACHE_STORE=file",
        "FILESYSTEM_DISK=local",
        "QUEUE_CONNECTION=database",
        "SESSION_DRIVER=database", 
        "SESSION_LIFETIME=120",
        "SESSION_DOMAIN=.anekabisnismurah.com",
        "",
        "MAIL_MAILER=smtp",
        "MAIL_HOST=mail.anekabisnismurah.com",
        "MAIL_PORT=587",
        "MAIL_USERNAME=noreply@anekabisnismurah.com",
        "MAIL_PASSWORD=your_mail_password",
        "MAIL_ENCRYPTION=tls",
        "MAIL_FROM_ADDRESS=noreply@anekabisnismurah.com",
        "MAIL_FROM_NAME=ReportApp",
        "",
        "VITE_APP_NAME=ReportApp",
        "VITE_APP_URL=https://omzet.anekabisnismurah.com"
    )

    $envPath = Join-Path $LaravelDir ".env.production"
    $envLines | Out-File -FilePath $envPath -Encoding UTF8

    # 9. Buat .htaccess untuk Laravel directory (security)
    Write-Host "Membuat .htaccess untuk keamanan Laravel directory..." -ForegroundColor Yellow
    $htaccessLaravel = @(
        "# Deny access to Laravel files",
        "Order deny,allow",
        "Deny from all",
        "",
        "# Allow access only to specific files if needed",
        "<Files artisan>",
        "    Allow from 127.0.0.1",
        "</Files>"
    )
    
    $htaccessLaravelPath = Join-Path $LaravelDir ".htaccess"
    $htaccessLaravel | Out-File -FilePath $htaccessLaravelPath -Encoding UTF8

    # 10. Update .htaccess di public untuk domain yang benar
    Write-Host "Mengupdate .htaccess untuk domain omzet.anekabisnismurah.com..." -ForegroundColor Yellow
    $htaccessPublic = @(
        "<IfModule mod_rewrite.c>",
        "    <IfModule mod_negotiation.c>",
        "        Options -MultiViews -Indexes",
        "    </IfModule>",
        "",
        "    RewriteEngine On",
        "",
        "    # Handle Authorization Header",
        "    RewriteCond %{HTTP:Authorization} .",
        "    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]",
        "",
        "    # Redirect Trailing Slashes If Not A Folder...",
        "    RewriteCond %{REQUEST_FILENAME} !-d",
        "    RewriteCond %{REQUEST_URI} (.+)/$",
        "    RewriteRule ^ %1 [L,R=301]",
        "",
        "    # Send Requests To Front Controller...",
        "    RewriteCond %{REQUEST_FILENAME} !-d",
        "    RewriteCond %{REQUEST_FILENAME} !-f",
        "    RewriteRule ^ index.php [L]",
        "</IfModule>",
        "",
        "# Security Headers",
        "Header always set X-Content-Type-Options nosniff",
        "Header always set X-Frame-Options DENY",
        "Header always set X-XSS-Protection '1; mode=block'",
        "",
        "# Cache Control",
        "<IfModule mod_expires.c>",
        "    ExpiresActive on",
        "    ExpiresByType text/css 'access plus 1 year'",
        "    ExpiresByType application/javascript 'access plus 1 year'",
        "    ExpiresByType image/png 'access plus 1 year'",
        "    ExpiresByType image/jpg 'access plus 1 year'",
        "    ExpiresByType image/jpeg 'access plus 1 year'",
        "    ExpiresByType image/gif 'access plus 1 year'",
        "    ExpiresByType image/svg+xml 'access plus 1 year'",
        "</IfModule>"
    )
    
    $htaccessPublicPath = Join-Path $OmzetDir ".htaccess"
    $htaccessPublic | Out-File -FilePath $htaccessPublicPath -Encoding UTF8

    # 11. Buat README untuk deployment khusus domain
    $readmeLines = @(
        "# Deployment Guide - Report App",
        "# Domain: omzet.anekabisnismurah.com",
        "",
        "## Struktur File:",
        "- laravel/ - Backend Laravel application (upload ke folder di luar public_html)",
        "- omzet.anekabisnismurah.com/ - Frontend files (upload ke subdomain folder)",
        "",
        "## Deployment Steps:",
        "",
        "### 1. Setup Subdomain:",
        "- Buat subdomain omzet.anekabisnismurah.com di cPanel",
        "- Document root: /public_html/omzet.anekabisnismurah.com",
        "",
        "### 2. Upload Backend (laravel folder):",
        "- Upload semua file di folder laravel/ ke folder di luar public_html",
        "- Contoh: /home/username/laravel_app/",
        "- Pastikan folder storage/ dan bootstrap/cache/ writable (755/775)",
        "",
        "### 3. Upload Frontend (omzet.anekabisnismurah.com folder):",
        "- Upload semua file ke document root subdomain",
        "- Path: /public_html/omzet.anekabisnismurah.com/",
        "",
        "### 4. Update index.php:",
        "- Edit file index.php di subdomain folder",
        "- Update path ke Laravel application:",
        "- require __DIR__.'/../../../laravel_app/vendor/autoload.php';",
        "- `$app = require_once __DIR__.'/../../../laravel_app/bootstrap/app.php';",
        "",
        "### 5. Konfigurasi Environment:",
        "- Rename .env.production menjadi .env",
        "- Edit file .env sesuai dengan konfigurasi server:",
        "  - DB_DATABASE=omzet_db", 
        "  - DB_USERNAME=omzet_user",
        "  - DB_PASSWORD=your_secure_password",
        "  - APP_URL=https://omzet.anekabisnismurah.com",
        "- Generate app key: php artisan key:generate",
        "",
        "### 6. Setup Database:",
        "- Buat database: omzet_db",
        "- Buat user database: omzet_user",
        "- Jalankan migrasi: php artisan migrate",
        "- Jalankan seeder: php artisan db:seed",
        "",
        "### 7. Optimize for Production:",
        "cd /home/username/laravel_app/",
        "php artisan config:cache",
        "php artisan route:cache", 
        "php artisan view:cache",
        "php artisan optimize",
        "",
        "### 8. Setup Storage Link:",
        "php artisan storage:link",
        "",
        "### 9. File Permissions:",
        "chmod -R 755 storage/",
        "chmod -R 755 bootstrap/cache/",
        "chmod 644 .env",
        "",
        "## Struktur Server yang Disarankan:",
        "/home/username/",
        "    laravel_app/          # Backend Laravel",
        "        app/",
        "        vendor/           # Dependencies included",
        "        .env",
        "        ...",
        "    public_html/",
        "        omzet.anekabisnismurah.com/  # Frontend",
        "            index.php",
        "            build/",
        "            ...",
        "",
        "## Important Notes:",
        "- PHP version >= 8.1",
        "- Extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json, curl",
        "- SSL Certificate untuk https://omzet.anekabisnismurah.com",
        "- Backup database secara rutin",
        "",
        "## Build Info:",
        "- Build Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
        "- Build Version: $Timestamp",
        "- Domain: omzet.anekabisnismurah.com",
        "- Include: Full vendor dependencies"
    )

    $readmePath = Join-Path $DistDir "README-DEPLOYMENT.md"
    $readmeLines | Out-File -FilePath $readmePath -Encoding UTF8

    # 12. Buat file konfigurasi tambahan
    Write-Host "Membuat file konfigurasi tambahan..." -ForegroundColor Yellow
    
    # File untuk update index.php
    $indexUpdateLines = @(
        "<?php",
        "// File ini adalah contoh update untuk index.php",
        "// Copy konten ini ke file index.php di subdomain",
        "",
        "use Illuminate\Contracts\Http\Kernel;",
        "use Illuminate\Http\Request;",
        "",
        "define('LARAVEL_START', microtime(true));",
        "",
        "// Update path ini sesuai dengan lokasi Laravel app di server",
        "require __DIR__.'/../../../laravel_app/vendor/autoload.php';",
        "",
        "`$app = require_once __DIR__.'/../../../laravel_app/bootstrap/app.php';",
        "",
        "`$kernel = `$app->make(Kernel::class);",
        "",
        "`$response = `$kernel->handle(",
        "    `$request = Request::capture()",
        ")->send();",
        "",
        "`$kernel->terminate(`$request, `$response);"
    )
    
    $indexUpdatePath = Join-Path $DistDir "index.php.example"
    $indexUpdateLines | Out-File -FilePath $indexUpdatePath -Encoding UTF8

    # 13. Buat file ZIP
    Write-Host "Membuat file ZIP..." -ForegroundColor Yellow
    
    # ZIP untuk backend full
    $backendZip = Join-Path $DistDir "laravel-backend-full-$Timestamp.zip"
    Compress-Archive -Path $LaravelDir -DestinationPath $backendZip -Force
    Write-Host "Created: laravel-backend-full-$Timestamp.zip" -ForegroundColor Green
    
    # ZIP untuk frontend domain
    $frontendZip = Join-Path $DistDir "omzet.anekabisnismurah.com-$Timestamp.zip"
    Compress-Archive -Path $OmzetDir -DestinationPath $frontendZip -Force
    Write-Host "Created: omzet.anekabisnismurah.com-$Timestamp.zip" -ForegroundColor Green
    
    # ZIP lengkap
    $completeZip = Join-Path $DistDir "report-app-production-full-$Timestamp.zip"
    $itemsToZip = @($LaravelDir, $OmzetDir, $readmePath, $indexUpdatePath)
    Compress-Archive -Path $itemsToZip -DestinationPath $completeZip -Force
    Write-Host "Created: report-app-production-full-$Timestamp.zip" -ForegroundColor Green

    # 14. Restore composer untuk development
    Write-Host "Restoring composer untuk development..." -ForegroundColor Yellow
    & composer install

    Write-Host ""
    Write-Host "Build production FULL selesai!" -ForegroundColor Green
    Write-Host "Output tersedia di: $DistDir" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "File ZIP yang tersedia:" -ForegroundColor Yellow
    Write-Host "- laravel-backend-full-$Timestamp.zip - Backend lengkap dengan vendor" -ForegroundColor White
    Write-Host "- omzet.anekabisnismurah.com-$Timestamp.zip - Frontend untuk subdomain" -ForegroundColor White  
    Write-Host "- report-app-production-full-$Timestamp.zip - Lengkap semua file" -ForegroundColor White
    Write-Host ""
    Write-Host "Domain Configuration:" -ForegroundColor Yellow
    Write-Host "- URL: https://omzet.anekabisnismurah.com" -ForegroundColor Cyan
    Write-Host "- Document Root: /public_html/omzet.anekabisnismurah.com/" -ForegroundColor Cyan
    Write-Host "- Laravel App: /home/username/laravel_app/" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Langkah selanjutnya:" -ForegroundColor Yellow
    Write-Host "1. Setup subdomain omzet.anekabisnismurah.com di cPanel" -ForegroundColor White
    Write-Host "2. Upload backend ke folder di luar public_html" -ForegroundColor White
    Write-Host "3. Upload frontend ke subdomain folder" -ForegroundColor White
    Write-Host "4. Update path di index.php" -ForegroundColor White
    Write-Host "5. Follow panduan lengkap di README-DEPLOYMENT.md" -ForegroundColor White

    # Tampilkan ukuran file ZIP
    Write-Host ""
    Write-Host "Ukuran file ZIP:" -ForegroundColor Yellow
    Get-ChildItem -Path $DistDir -Filter "*.zip" | ForEach-Object {
        $size = [math]::Round($_.Length / 1MB, 2)
        Write-Host "- $($_.Name): $size MB" -ForegroundColor Cyan
    }

    # Tampilkan summary vendor
    Write-Host ""
    Write-Host "Vendor Summary:" -ForegroundColor Yellow
    $vendorPath = Join-Path $LaravelDir "vendor"
    if (Test-Path $vendorPath) {
        $vendorSize = (Get-ChildItem $vendorPath -Recurse | Measure-Object -Property Length -Sum).Sum
        $vendorSizeMB = [math]::Round($vendorSize / 1MB, 2)
        Write-Host "- Vendor folder size: $vendorSizeMB MB" -ForegroundColor Cyan
        
        $composerLock = Join-Path $LaravelDir "composer.lock"
        if (Test-Path $composerLock) {
            $lockContent = Get-Content $composerLock | ConvertFrom-Json
            $packageCount = $lockContent.packages.Count
            Write-Host "- Total packages: $packageCount" -ForegroundColor Cyan
        }
    }

} catch {
    Write-Host ""
    Write-Host "Error during build: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Stack trace: $($_.ScriptStackTrace)" -ForegroundColor Red
    exit 1
}
