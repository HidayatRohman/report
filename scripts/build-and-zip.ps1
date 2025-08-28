# Build Production dan Buat ZIP
Write-Host "🚀 Memulai build production dan ZIP..." -ForegroundColor Green

$ProjectRoot = $PSScriptRoot | Split-Path -Parent
$DistDir = Join-Path $ProjectRoot "dist"
$LaravelDir = Join-Path $DistDir "laravel"
$PublicHtmlDir = Join-Path $DistDir "public_html"
$Timestamp = Get-Date -Format "yyyyMMdd-HHmmss"

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
    & npm install
    
    Write-Host "🔨 Building frontend assets..." -ForegroundColor Yellow
    & npm run build

    # 3. Install composer dependencies untuk production
    Write-Host "🎵 Menginstall Composer dependencies untuk production..." -ForegroundColor Yellow
    & composer install --optimize-autoloader --no-dev

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
        "composer.lock"
    )

    foreach ($file in $BackendFiles) {
        $srcPath = Join-Path $ProjectRoot $file
        $destPath = Join-Path $LaravelDir $file
        
        if (Test-Path $srcPath) {
            Copy-Item $srcPath $destPath -Recurse -Force
            Write-Host "✅ Copied: $file" -ForegroundColor Green
        }
    }

    # 5. Copy .env.example
    $envExampleSrc = Join-Path $ProjectRoot ".env.example"
    $envExampleDest = Join-Path $LaravelDir ".env.example"
    if (Test-Path $envExampleSrc) {
        Copy-Item $envExampleSrc $envExampleDest -Force
        Write-Host "✅ Copied: .env.example" -ForegroundColor Green
    }

    # 6. Copy frontend files (public)
    Write-Host "🌐 Menyalin file frontend ke public_html..." -ForegroundColor Yellow
    $publicPath = Join-Path $ProjectRoot "public"
    if (Test-Path $publicPath) {
        Copy-Item "$publicPath\*" $PublicHtmlDir -Recurse -Force
        Write-Host "✅ Copied: public files" -ForegroundColor Green
    }

    # 7. Buat folder storage yang diperlukan
    Write-Host "📁 Membuat struktur folder storage..." -ForegroundColor Yellow
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
    Write-Host "⚙️ Membuat file konfigurasi production..." -ForegroundColor Yellow
    $envProduction = "APP_NAME=ReportApp`r`nAPP_ENV=production`r`nAPP_KEY=`r`nAPP_DEBUG=false`r`nAPP_TIMEZONE=Asia/Jakarta`r`nAPP_URL=https://yourdomain.com`r`n`r`nLOG_CHANNEL=stack`r`nLOG_DEPRECATIONS_CHANNEL=null`r`nLOG_LEVEL=error`r`n`r`nDB_CONNECTION=mysql`r`nDB_HOST=127.0.0.1`r`nDB_PORT=3306`r`nDB_DATABASE=your_database`r`nDB_USERNAME=your_username`r`nDB_PASSWORD=your_password`r`n`r`nBROADCAST_CONNECTION=log`r`nCACHE_STORE=file`r`nFILESYSTEM_DISK=local`r`nQUEUE_CONNECTION=database`r`nSESSION_DRIVER=database`r`nSESSION_LIFETIME=120`r`n`r`nMAIL_MAILER=log`r`nMAIL_HOST=127.0.0.1`r`nMAIL_PORT=2525`r`nMAIL_USERNAME=null`r`nMAIL_PASSWORD=null`r`nMAIL_ENCRYPTION=null`r`nMAIL_FROM_ADDRESS=hello@example.com`r`nMAIL_FROM_NAME=ReportApp`r`n`r`nVITE_APP_NAME=ReportApp"

    $envPath = Join-Path $LaravelDir ".env.production"
    Set-Content -Path $envPath -Value $envProduction -Encoding UTF8

    # 9. Buat README untuk deployment
    $readmeContent = "# Deployment Guide - Report App`r`n`r`n## Struktur File:`r`n- laravel/ - Backend Laravel application`r`n- public_html/ - Frontend files untuk web server`r`n`r`n## Deployment Steps:`r`n`r`n### 1. Upload Backend (laravel folder):`r`n- Upload semua file di folder laravel/ ke root directory server Anda`r`n- Pastikan folder storage/ dan bootstrap/cache/ writable (775)`r`n`r`n### 2. Upload Frontend (public_html folder):`r`n- Upload semua file di folder public_html/ ke folder public_html server Anda`r`n`r`n### 3. Konfigurasi Environment:`r`n- Rename .env.production menjadi .env`r`n- Edit file .env sesuai dengan konfigurasi server Anda`r`n- Generate app key: php artisan key:generate`r`n`r`n### 4. Setup Database:`r`n- Buat database baru`r`n- Jalankan migrasi: php artisan migrate`r`n- Jalankan seeder (opsional): php artisan db:seed`r`n`r`n### 5. Optimize for Production:`r`nphp artisan config:cache`r`nphp artisan route:cache`r`nphp artisan view:cache`r`nphp artisan optimize`r`n`r`n### 6. Setup Storage Link:`r`nphp artisan storage:link`r`n`r`n## File Permissions:`r`n- Folder storage/: 775`r`n- Folder bootstrap/cache/: 775`r`n- File .env: 644`r`n`r`n## Important Notes:`r`n- Pastikan PHP version >= 8.1`r`n- Enable extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json`r`n- Set document root ke folder public_html`r`n`r`n## Build Info:`r`n- Build Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`r`n- Build Version: $Timestamp"

    $readmePath = Join-Path $DistDir "README.md"
    Set-Content -Path $readmePath -Value $readmeContent -Encoding UTF8

    # 10. Buat file ZIP
    Write-Host "📦 Membuat file ZIP..." -ForegroundColor Yellow
    
    # ZIP untuk backend saja
    $backendZip = Join-Path $DistDir "laravel-backend-$Timestamp.zip"
    Compress-Archive -Path $LaravelDir -DestinationPath $backendZip -Force
    Write-Host "✅ Created: laravel-backend-$Timestamp.zip" -ForegroundColor Green
    
    # ZIP untuk frontend saja  
    $frontendZip = Join-Path $DistDir "public_html-frontend-$Timestamp.zip"
    Compress-Archive -Path $PublicHtmlDir -DestinationPath $frontendZip -Force
    Write-Host "✅ Created: public_html-frontend-$Timestamp.zip" -ForegroundColor Green
    
    # ZIP lengkap (semua file)
    $completeZip = Join-Path $DistDir "report-app-production-$Timestamp.zip"
    $itemsToZip = @($LaravelDir, $PublicHtmlDir, $readmePath)
    Compress-Archive -Path $itemsToZip -DestinationPath $completeZip -Force
    Write-Host "✅ Created: report-app-production-$Timestamp.zip" -ForegroundColor Green

    # 11. Restore composer untuk development
    Write-Host "🔄 Restoring composer untuk development..." -ForegroundColor Yellow
    & composer install

    Write-Host ""
    Write-Host "🎉 Build production selesai!" -ForegroundColor Green
    Write-Host "📦 Output tersedia di: $DistDir" -ForegroundColor Cyan
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
    Write-Host "4. Follow panduan di README.md" -ForegroundColor White

    # Tampilkan ukuran file ZIP
    Write-Host ""
    Write-Host "📊 Ukuran file ZIP:" -ForegroundColor Yellow
    Get-ChildItem -Path $DistDir -Filter "*.zip" | ForEach-Object {
        $size = [math]::Round($_.Length / 1MB, 2)
        Write-Host "📦 $($_.Name): $size MB" -ForegroundColor Cyan
    }

} catch {
    Write-Host ""
    Write-Host "❌ Error during build: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
