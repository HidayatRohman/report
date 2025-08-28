@echo off
setlocal enabledelayedexpansion

echo 🚀 Memulai build production...
echo.

set "PROJECT_ROOT=%~dp0.."
set "DIST_DIR=%PROJECT_ROOT%\:: 9. Restore composer untuk development
echo 🔄 Restoring composer untuk development...
call composer install

echo.
echo 🎉 Build production selesai!
echo 📦 Output tersedia di: %DIST_DIR%
echo 📂 laravel/ - Backend files ^(upload ke root server^)
echo 🌐 public_html/ - Frontend files ^(upload ke public_html^)
echo.
echo 📦 File ZIP yang tersedia:
echo 🔧 laravel-backend-%timestamp%.zip - Backend saja
echo 🌐 public_html-frontend-%timestamp%.zip - Frontend saja  
echo 📦 report-app-production-%timestamp%.zip - Lengkap ^(semua file^)
echo.
echo 📋 Langkah selanjutnya:
echo 1. Download dan ekstrak ZIP sesuai kebutuhan
echo 2. Upload backend ke root directory server
echo 3. Upload frontend ke public_html server
echo 4. Follow panduan di README.mdRAVEL_DIR=%DIST_DIR%\laravel"
set "PUBLIC_HTML_DIR=%DIST_DIR%\public_html"

:: 1. Bersihkan folder dist jika ada
echo 📁 Membersihkan folder dist...
if exist "%DIST_DIR%" (
    rmdir /s /q "%DIST_DIR%"
)
mkdir "%DIST_DIR%"
mkdir "%LARAVEL_DIR%"
mkdir "%PUBLIC_HTML_DIR%"

:: 2. Install dependencies dan build frontend
echo 📦 Menginstall dependencies...
cd /d "%PROJECT_ROOT%"
call bun install
if errorlevel 1 (
    echo ❌ Bun install gagal
    exit /b 1
)

echo 🔨 Building frontend assets...
call bun run build
if errorlevel 1 (
    echo ❌ Frontend build gagal
    exit /b 1
)

:: 3. Install composer dependencies untuk production
echo 🎵 Menginstall Composer dependencies untuk production...
call composer install --optimize-autoloader --no-dev
if errorlevel 1 (
    echo ❌ Composer install gagal
    exit /b 1
)

:: 4. Copy backend files (Laravel)
echo 📂 Menyalin file backend Laravel...

for %%f in (app bootstrap config database routes storage vendor artisan composer.json composer.lock .env.example) do (
    if exist "%PROJECT_ROOT%\%%f" (
        echo ✅ Copying: %%f
        if exist "%PROJECT_ROOT%\%%f\nul" (
            robocopy "%PROJECT_ROOT%\%%f" "%LARAVEL_DIR%\%%f" /E /XD cache sessions views logs /NFL /NDL /NJH /NJS > nul
        ) else (
            copy "%PROJECT_ROOT%\%%f" "%LARAVEL_DIR%\%%f" > nul
        )
    )
)

:: 5. Copy frontend files (public)
echo 🌐 Menyalin file frontend ke public_html...

for %%f in (index.php robots.txt favicon.ico favicon.svg apple-touch-icon.png) do (
    if exist "%PROJECT_ROOT%\public\%%f" (
        echo ✅ Copying: public/%%f
        copy "%PROJECT_ROOT%\public\%%f" "%PUBLIC_HTML_DIR%\%%f" > nul
    )
)

if exist "%PROJECT_ROOT%\public\build" (
    echo ✅ Copying: public/build
    robocopy "%PROJECT_ROOT%\public\build" "%PUBLIC_HTML_DIR%\build" /E /NFL /NDL /NJH /NJS > nul
)

if exist "%PROJECT_ROOT%\public\images" (
    echo ✅ Copying: public/images  
    robocopy "%PROJECT_ROOT%\public\images" "%PUBLIC_HTML_DIR%\images" /E /NFL /NDL /NJH /NJS > nul
)

:: 6. Buat folder storage yang diperlukan di Laravel
echo 📁 Membuat struktur folder storage...
mkdir "%LARAVEL_DIR%\storage\framework\cache\data" 2>nul
mkdir "%LARAVEL_DIR%\storage\framework\sessions" 2>nul
mkdir "%LARAVEL_DIR%\storage\framework\views" 2>nul
mkdir "%LARAVEL_DIR%\storage\logs" 2>nul
mkdir "%LARAVEL_DIR%\storage\app\public" 2>nul

:: 7. Buat file .env.production
echo ⚙️  Membuat file konfigurasi production...
(
echo APP_NAME=ReportApp
echo APP_ENV=production
echo APP_KEY=
echo APP_DEBUG=false
echo APP_TIMEZONE=Asia/Jakarta
echo APP_URL=https://yourdomain.com
echo.
echo LOG_CHANNEL=stack
echo LOG_DEPRECATIONS_CHANNEL=null
echo LOG_LEVEL=error
echo.
echo DB_CONNECTION=mysql
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=your_database
echo DB_USERNAME=your_username
echo DB_PASSWORD=your_password
echo.
echo BROADCAST_CONNECTION=log
echo CACHE_STORE=file
echo FILESYSTEM_DISK=local
echo QUEUE_CONNECTION=database
echo SESSION_DRIVER=database
echo SESSION_LIFETIME=120
echo.
echo MAIL_MAILER=log
echo MAIL_HOST=127.0.0.1
echo MAIL_PORT=2525
echo MAIL_USERNAME=null
echo MAIL_PASSWORD=null
echo MAIL_ENCRYPTION=null
echo MAIL_FROM_ADDRESS="hello@example.com"
echo MAIL_FROM_NAME="${APP_NAME}"
echo.
echo VITE_APP_NAME="${APP_NAME}"
) > "%LARAVEL_DIR%\.env.production"

:: 8. Membuat file ZIP
echo 📦 Membuat file ZIP...

:: Buat timestamp untuk nama file
for /f "tokens=1-6 delims=/:. " %%a in ("%date% %time%") do (
    set "timestamp=%%c-%%a-%%b_%%d-%%e-%%f"
)
set "timestamp=%timestamp: =0%"

set "LARAVEL_ZIP=%DIST_DIR%\laravel-backend-%timestamp%.zip"
set "PUBLIC_ZIP=%DIST_DIR%\public_html-frontend-%timestamp%.zip"
set "FULL_ZIP=%DIST_DIR%\report-app-production-%timestamp%.zip"

:: Buat ZIP menggunakan PowerShell (tersedia di Windows 10+)
powershell -command "Compress-Archive -Path '%LARAVEL_DIR%' -DestinationPath '%LARAVEL_ZIP%' -CompressionLevel Optimal"
echo ✅ Backend ZIP created

powershell -command "Compress-Archive -Path '%PUBLIC_HTML_DIR%' -DestinationPath '%PUBLIC_ZIP%' -CompressionLevel Optimal"
echo ✅ Frontend ZIP created

powershell -command "Compress-Archive -Path '%LARAVEL_DIR%\*','%PUBLIC_HTML_DIR%\*','%DIST_DIR%\README.md' -DestinationPath '%FULL_ZIP%' -CompressionLevel Optimal"
echo ✅ Complete ZIP created

:: 9. Restore composer untuk development
echo 🔄 Restoring composer untuk development...
call composer install

echo.
echo 🎉 Build production selesai!
echo 📦 Output tersedia di: %DIST_DIR%
echo 📂 laravel/ - Backend files ^(upload ke root server^)
echo 🌐 public_html/ - Frontend files ^(upload ke public_html^)
echo.
echo 📋 Langkah selanjutnya:
echo 1. Ekstrak folder laravel/ ke root directory server
echo 2. Ekstrak folder public_html/ ke public_html server
echo 3. Follow panduan di README.md

pause
