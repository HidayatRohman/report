# Build Production Script untuk Windows PowerShell
param(
    [switch]$Help
)

if ($Help) {
    Write-Host "Build Production Script untuk Laravel + Vue" -ForegroundColor Green
    Write-Host "Usage: .\build-production.ps1" -Fo    # 10. Restore composer untuk development
    Write-Host "🔄 Restoring composer untuk development..." -ForegroundColor Yellow
    & composer install

    Write-Host ""
    Write-Host "🎉 Build production selesai!" -ForegroundColor Green
    Write-Host "📦 Output tersedia di: $DistDir" -ForegroundColor Cyan
    Write-Host "📂 laravel/ - Backend files (upload ke root server)" -ForegroundColor Cyan
    Write-Host "🌐 public_html/ - Frontend files (upload ke public_html)" -ForegroundColor Cyan
    Write-Host "📖 README.md - Panduan deployment" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "📦 File ZIP yang tersedia:" -ForegroundColor Yellow
    Write-Host "🔧 laravel-backend-$Timestamp.zip - Backend saja" -ForegroundColor White
    Write-Host "🌐 public_html-frontend-$Timestamp.zip - Frontend saja" -ForegroundColor White
    Write-Host "📦 report-app-production-$Timestamp.zip - Lengkap (semua file)" -ForegroundColor White
    Write-Host ""
    Write-Host "📋 Langkah selanjutnya:" -ForegroundColor Yellow
    Write-Host "1. Download dan ekstrak ZIP sesuai kebutuhan" -ForegroundColor White
    Write-Host "2. Upload backend ke root directory server" -ForegroundColor White
    Write-Host "3. Upload frontend ke public_html server" -ForegroundColor White
    Write-Host "4. Follow panduan di README.md" -ForegroundColor Whiteellow
    Write-Host ""
    Write-Host "Script ini akan:"
    Write-Host "1. Build frontend assets"
    Write-Host "2. Copy backend files ke folder laravel/"
    Write-Host "3. Copy frontend files ke folder public_html/"
    Write-Host "4. Membuat struktur untuk deployment"
    exit
}

Write-Host "🚀 Memulai build production..." -ForegroundColor Green
Write-Host ""

$ProjectRoot = $PSScriptRoot | Split-Path -Parent
$DistDir = Join-Path $ProjectRoot "dist"
$LaravelDir = Join-Path $DistDir "laravel"
$PublicHtmlDir = Join-Path $DistDir "public_html"

try {
    # 1. Bersihkan folder dist jika ada
    Write-Host "📁 Membersihkan folder dist..." -ForegroundColor Yellow
    if (Test-Path $DistDir) {
        Remove-Item $DistDir -Recurse -Force
    }
    New-Item -ItemType Directory -Path $DistDir -Force | Out-Null
    New-Item -ItemType Directory -Path $LaravelDir -Force | Out-Null
    New-Item -ItemType Directory -Path $PublicHtmlDir -Force | Out-Null

    # 2. Install dependencies dan build frontend
    Write-Host "📦 Menginstall dependencies..." -ForegroundColor Yellow
    Set-Location $ProjectRoot
    & bun install
    if ($LASTEXITCODE -ne 0) { throw "Bun install failed" }
    
    Write-Host "🔨 Building frontend assets..." -ForegroundColor Yellow
    & bun run build
    if ($LASTEXITCODE -ne 0) { throw "Frontend build failed" }

    # 3. Install composer dependencies untuk production
    Write-Host "🎵 Menginstall Composer dependencies untuk production..." -ForegroundColor Yellow
    & composer install --optimize-autoloader --no-dev
    if ($LASTEXITCODE -ne 0) { throw "Composer install failed" }

    # 4. Copy backend files (Laravel)
    Write-Host "📂 Menyalin file backend Laravel..." -ForegroundColor Yellow
    
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
        ".env.example"
    )

    foreach ($File in $BackendFiles) {
        $SrcPath = Join-Path $ProjectRoot $File
        $DestPath = Join-Path $LaravelDir $File
        
        if (Test-Path $SrcPath) {
            if (Test-Path $SrcPath -PathType Container) {
                # Folder - copy dengan exclude cache
                robocopy $SrcPath $DestPath /E /XD "cache" "sessions" "views" "logs" /NFL /NDL /NJH /NJS
            } else {
                # File - copy langsung
                Copy-Item $SrcPath $DestPath -Force
            }
            Write-Host "✅ Copied: $File" -ForegroundColor Green
        }
    }

    # 5. Copy frontend files (public)
    Write-Host "🌐 Menyalin file frontend ke public_html..." -ForegroundColor Yellow
    
    $PublicPath = Join-Path $ProjectRoot "public"
    if (Test-Path $PublicPath) {
        robocopy $PublicPath $PublicHtmlDir /E /NFL /NDL /NJH /NJS
        Write-Host "✅ Copied: public files" -ForegroundColor Green
    }

    # 6. Buat folder storage yang diperlukan di Laravel
    Write-Host "📁 Membuat struktur folder storage..." -ForegroundColor Yellow
    $StorageDirs = @(
        "storage\framework\cache\data",
        "storage\framework\sessions",
        "storage\framework\views", 
        "storage\logs",
        "storage\app\public"
    )

    foreach ($Dir in $StorageDirs) {
        $FullPath = Join-Path $LaravelDir $Dir
        New-Item -ItemType Directory -Path $FullPath -Force | Out-Null
    }

    # 7. Buat file konfigurasi khusus production
    Write-Host "⚙️  Membuat file konfigurasi production..." -ForegroundColor Yellow
    
    $EnvProduction = @"
APP_NAME=ReportApp
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="`${APP_NAME}"

VITE_APP_NAME="`${APP_NAME}"
"@

    $EnvPath = Join-Path $LaravelDir ".env.production"
    $EnvProduction | Out-File -FilePath $EnvPath -Encoding UTF8

    # 8. Buat README untuk deployment
    $ReadmeContent = @"
# Deployment Guide

## Struktur File:
- ``laravel/`` - Backend Laravel application
- ``public_html/`` - Frontend files untuk web server

## Deployment Steps:

### 1. Upload Backend (laravel folder):
- Upload semua file di folder ``laravel/`` ke root directory server Anda
- Pastikan folder ``storage/`` dan ``bootstrap/cache/`` writable (775)

### 2. Upload Frontend (public_html folder):
- Upload semua file di folder ``public_html/`` ke folder public_html server Anda

### 3. Konfigurasi Environment:
- Rename ``.env.production`` menjadi ``.env``
- Edit file ``.env`` sesuai dengan konfigurasi server Anda
- Generate app key: ``php artisan key:generate``

### 4. Setup Database:
- Buat database baru
- Jalankan migrasi: ``php artisan migrate``
- Jalankan seeder (opsional): ``php artisan db:seed``

### 5. Optimize for Production:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 6. Setup Storage Link:
```bash
php artisan storage:link
```

## File Permissions:
- Folder ``storage/``: 775
- Folder ``bootstrap/cache/``: 775
- File ``.env``: 644

## Important Notes:
- Pastikan PHP version >= 8.1
- Enable extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json
- Set document root ke folder public_html

## Ekstrak dan Upload:
1. Ekstrak folder ``laravel/`` ke root directory server (biasanya public_html atau www)
2. Ekstrak folder ``public_html/`` ke folder public_html server
3. Follow deployment steps di atas
"@

    $ReadmePath = Join-Path $DistDir "README.md"
    $ReadmeContent | Out-File -FilePath $ReadmePath -Encoding UTF8

    # 9. Membuat file ZIP
    Write-Host "📦 Membuat file ZIP..." -ForegroundColor Yellow
    
    $Timestamp = Get-Date -Format "yyyy-MM-ddTHH-mm-ss"
    $LaravelZipPath = Join-Path $DistDir "laravel-backend-$Timestamp.zip"
    $PublicZipPath = Join-Path $DistDir "public_html-frontend-$Timestamp.zip" 
    $FullZipPath = Join-Path $DistDir "report-app-production-$Timestamp.zip"

    # Buat ZIP untuk backend Laravel
    Compress-Archive -Path $LaravelDir -DestinationPath $LaravelZipPath -CompressionLevel Optimal
    $LaravelSize = [math]::Round((Get-Item $LaravelZipPath).Length / 1MB, 2)
    Write-Host "✅ Backend ZIP: $LaravelSize MB" -ForegroundColor Green
    
    # Buat ZIP untuk frontend public_html
    Compress-Archive -Path $PublicHtmlDir -DestinationPath $PublicZipPath -CompressionLevel Optimal
    $PublicSize = [math]::Round((Get-Item $PublicZipPath).Length / 1MB, 2)
    Write-Host "✅ Frontend ZIP: $PublicSize MB" -ForegroundColor Green
    
    # Buat ZIP lengkap
    Compress-Archive -Path "$LaravelDir\*", "$PublicHtmlDir\*", $ReadmePath -DestinationPath $FullZipPath -CompressionLevel Optimal
    $FullSize = [math]::Round((Get-Item $FullZipPath).Length / 1MB, 2)
    Write-Host "✅ Complete ZIP: $FullSize MB" -ForegroundColor Green

    # 10. Restore composer untuk development
    Write-Host "🔄 Restoring composer untuk development..." -ForegroundColor Yellow
    & composer install

    Write-Host ""
    Write-Host "🎉 Build production selesai!" -ForegroundColor Green
    Write-Host "📦 Output tersedia di: $DistDir" -ForegroundColor Cyan
    Write-Host "📂 laravel/ - Backend files (upload ke root server)" -ForegroundColor Cyan
    Write-Host "🌐 public_html/ - Frontend files (upload ke public_html)" -ForegroundColor Cyan
    Write-Host "📖 README.md - Panduan deployment" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "📋 Langkah selanjutnya:" -ForegroundColor Yellow
    Write-Host "1. Ekstrak folder laravel/ ke root directory server" -ForegroundColor White
    Write-Host "2. Ekstrak folder public_html/ ke public_html server" -ForegroundColor White
    Write-Host "3. Follow panduan di README.md" -ForegroundColor White

} catch {
    Write-Host "❌ Error during build: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
} finally {
    Set-Location $ProjectRoot
}
