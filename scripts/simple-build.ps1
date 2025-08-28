# Build Production dan Buat ZIP
Write-Host "Memulai build production dan ZIP..." -ForegroundColor Green

$ProjectRoot = $PSScriptRoot | Split-Path -Parent
$DistDir = Join-Path $ProjectRoot "dist"
$LaravelDir = Join-Path $DistDir "laravel"
$PublicHtmlDir = Join-Path $DistDir "public_html"
$Timestamp = Get-Date -Format "yyyyMMdd-HHmmss"

try {
    # 1. Bersihkan folder dist jika ada
    Write-Host "Membersihkan folder dist..." -ForegroundColor Yellow
    if (Test-Path $DistDir) {
        Remove-Item $DistDir -Recurse -Force
    }
    New-Item -ItemType Directory -Path $DistDir -Force | Out-Null
    New-Item -ItemType Directory -Path $LaravelDir -Force | Out-Null
    New-Item -ItemType Directory -Path $PublicHtmlDir -Force | Out-Null

    # 2. Install dependencies dan build frontend
    Write-Host "Menginstall dependencies..." -ForegroundColor Yellow
    Set-Location $ProjectRoot
    & npm install
    
    Write-Host "Building frontend assets..." -ForegroundColor Yellow
    & npm run build

    # 3. Install composer dependencies untuk production
    Write-Host "Menginstall Composer dependencies untuk production..." -ForegroundColor Yellow
    & composer install --optimize-autoloader --no-dev

    # 4. Copy backend files (Laravel)
    Write-Host "Menyalin file backend Laravel..." -ForegroundColor Yellow
    
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
        "composer.lock"
    )

    foreach ($file in $BackendFiles) {
        $srcPath = Join-Path $ProjectRoot $file
        $destPath = Join-Path $LaravelDir $file
        
        if (Test-Path $srcPath) {
            Copy-Item $srcPath $destPath -Recurse -Force
            Write-Host "Copied: $file" -ForegroundColor Green
        }
    }

    # 5. Copy .env.example
    $envExampleSrc = Join-Path $ProjectRoot ".env.example"
    $envExampleDest = Join-Path $LaravelDir ".env.example"
    if (Test-Path $envExampleSrc) {
        Copy-Item $envExampleSrc $envExampleDest -Force
        Write-Host "Copied: .env.example" -ForegroundColor Green
    }

    # 6. Copy frontend files (public)
    Write-Host "Menyalin file frontend ke public_html..." -ForegroundColor Yellow
    $publicPath = Join-Path $ProjectRoot "public"
    if (Test-Path $publicPath) {
        Copy-Item "$publicPath\*" $PublicHtmlDir -Recurse -Force
        Write-Host "Copied: public files" -ForegroundColor Green
    }

    # 7. Buat folder storage yang diperlukan
    Write-Host "Membuat struktur folder storage..." -ForegroundColor Yellow
    $StorageDirs = @(
        "storage\framework\cache\data",
        "storage\framework\sessions", 
        "storage\framework\views",
        "storage\logs",
        "storage\app\public"
    )

    foreach ($dir in $StorageDirs) {
        $fullPath = Join-Path $LaravelDir $dir
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
    }

    # 8. Buat file .env.production
    Write-Host "Membuat file konfigurasi production..." -ForegroundColor Yellow
    $envLines = @(
        "APP_NAME=ReportApp",
        "APP_ENV=production",
        "APP_KEY=",
        "APP_DEBUG=false",
        "APP_TIMEZONE=Asia/Jakarta", 
        "APP_URL=https://yourdomain.com",
        "",
        "LOG_CHANNEL=stack",
        "LOG_DEPRECATIONS_CHANNEL=null", 
        "LOG_LEVEL=error",
        "",
        "DB_CONNECTION=mysql",
        "DB_HOST=127.0.0.1",
        "DB_PORT=3306",
        "DB_DATABASE=your_database",
        "DB_USERNAME=your_username",
        "DB_PASSWORD=your_password",
        "",
        "BROADCAST_CONNECTION=log",
        "CACHE_STORE=file",
        "FILESYSTEM_DISK=local",
        "QUEUE_CONNECTION=database",
        "SESSION_DRIVER=database", 
        "SESSION_LIFETIME=120",
        "",
        "MAIL_MAILER=log",
        "MAIL_HOST=127.0.0.1",
        "MAIL_PORT=2525",
        "MAIL_USERNAME=null",
        "MAIL_PASSWORD=null",
        "MAIL_ENCRYPTION=null",
        "MAIL_FROM_ADDRESS=hello@example.com",
        "MAIL_FROM_NAME=ReportApp",
        "",
        "VITE_APP_NAME=ReportApp"
    )

    $envPath = Join-Path $LaravelDir ".env.production"
    $envLines | Out-File -FilePath $envPath -Encoding UTF8

    # 9. Buat README untuk deployment
    $readmeLines = @(
        "# Deployment Guide - Report App",
        "",
        "## Struktur File:",
        "- laravel/ - Backend Laravel application",
        "- public_html/ - Frontend files untuk web server",
        "",
        "## Deployment Steps:",
        "",
        "### 1. Upload Backend (laravel folder):",
        "- Upload semua file di folder laravel/ ke root directory server Anda",
        "- Pastikan folder storage/ dan bootstrap/cache/ writable (775)",
        "",
        "### 2. Upload Frontend (public_html folder):",
        "- Upload semua file di folder public_html/ ke folder public_html server Anda",
        "",
        "### 3. Konfigurasi Environment:",
        "- Rename .env.production menjadi .env",
        "- Edit file .env sesuai dengan konfigurasi server Anda",
        "- Generate app key: php artisan key:generate",
        "",
        "### 4. Setup Database:",
        "- Buat database baru",
        "- Jalankan migrasi: php artisan migrate",
        "- Jalankan seeder (opsional): php artisan db:seed",
        "",
        "### 5. Optimize for Production:",
        "php artisan config:cache",
        "php artisan route:cache", 
        "php artisan view:cache",
        "php artisan optimize",
        "",
        "### 6. Setup Storage Link:",
        "php artisan storage:link",
        "",
        "## File Permissions:",
        "- Folder storage/: 775",
        "- Folder bootstrap/cache/: 775",
        "- File .env: 644",
        "",
        "## Important Notes:",
        "- Pastikan PHP version >= 8.1",
        "- Enable extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json",
        "- Set document root ke folder public_html",
        "",
        "## Build Info:",
        "- Build Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
        "- Build Version: $Timestamp"
    )

    $readmePath = Join-Path $DistDir "README.md"
    $readmeLines | Out-File -FilePath $readmePath -Encoding UTF8

    # 10. Buat file ZIP
    Write-Host "Membuat file ZIP..." -ForegroundColor Yellow
    
    # ZIP untuk backend saja
    $backendZip = Join-Path $DistDir "laravel-backend-$Timestamp.zip"
    Compress-Archive -Path $LaravelDir -DestinationPath $backendZip -Force
    Write-Host "Created: laravel-backend-$Timestamp.zip" -ForegroundColor Green
    
    # ZIP untuk frontend saja  
    $frontendZip = Join-Path $DistDir "public_html-frontend-$Timestamp.zip"
    Compress-Archive -Path $PublicHtmlDir -DestinationPath $frontendZip -Force
    Write-Host "Created: public_html-frontend-$Timestamp.zip" -ForegroundColor Green
    
    # ZIP lengkap (semua file)
    $completeZip = Join-Path $DistDir "report-app-production-$Timestamp.zip"
    $itemsToZip = @($LaravelDir, $PublicHtmlDir, $readmePath)
    Compress-Archive -Path $itemsToZip -DestinationPath $completeZip -Force
    Write-Host "Created: report-app-production-$Timestamp.zip" -ForegroundColor Green

    # 11. Restore composer untuk development
    Write-Host "Restoring composer untuk development..." -ForegroundColor Yellow
    & composer install

    Write-Host ""
    Write-Host "Build production selesai!" -ForegroundColor Green
    Write-Host "Output tersedia di: $DistDir" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "File ZIP yang tersedia:" -ForegroundColor Yellow
    Write-Host "- laravel-backend-$Timestamp.zip - Backend saja" -ForegroundColor White
    Write-Host "- public_html-frontend-$Timestamp.zip - Frontend saja" -ForegroundColor White  
    Write-Host "- report-app-production-$Timestamp.zip - Lengkap (semua file)" -ForegroundColor White
    Write-Host ""
    Write-Host "Langkah selanjutnya:" -ForegroundColor Yellow
    Write-Host "1. Download dan ekstrak ZIP sesuai kebutuhan" -ForegroundColor White
    Write-Host "2. Upload backend ke root directory server" -ForegroundColor White
    Write-Host "3. Upload frontend ke public_html server" -ForegroundColor White
    Write-Host "4. Follow panduan di README.md" -ForegroundColor White

    # Tampilkan ukuran file ZIP
    Write-Host ""
    Write-Host "Ukuran file ZIP:" -ForegroundColor Yellow
    Get-ChildItem -Path $DistDir -Filter "*.zip" | ForEach-Object {
        $size = [math]::Round($_.Length / 1MB, 2)
        Write-Host "- $($_.Name): $size MB" -ForegroundColor Cyan
    }

} catch {
    Write-Host ""
    Write-Host "Error during build: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
