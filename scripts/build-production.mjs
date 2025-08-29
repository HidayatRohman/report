import fs from 'fs-extra';
import path from 'path';
import { execSync } from 'child_process';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Konfigurasi folder output
const DIST_DIR = path.join(__dirname, '..', 'dist');
const LARAVEL_DIR = path.join(DIST_DIR, 'laravel');
const PUBLIC_HTML_DIR = path.join(DIST_DIR, 'public_html');

console.log('🚀 Memulai build production...\n');

async function buildProduction() {
    try {
        // 1. Bersihkan folder dist jika ada
        console.log('📁 Membersihkan folder dist...');
        if (fs.existsSync(DIST_DIR)) {
            await fs.remove(DIST_DIR);
        }
        await fs.ensureDir(DIST_DIR);
        await fs.ensureDir(LARAVEL_DIR);
        await fs.ensureDir(PUBLIC_HTML_DIR);

        // 2. Install dependencies dan build frontend
        console.log('📦 Menginstall dependencies...');
        execSync('npm install', { stdio: 'inherit' });
        
        console.log('🔨 Building frontend assets...');
        execSync('npm run build', { stdio: 'inherit' });

        // 3. Install composer dependencies untuk production
        console.log('🎵 Menginstall Composer dependencies untuk production...');
        execSync('composer install --optimize-autoloader --no-dev', { stdio: 'inherit' });

        // 4. Copy backend files (Laravel)
        console.log('📂 Menyalin file backend Laravel...');
        
        const backendFiles = [
            'app',
            'bootstrap',
            'config',
            'database',
            'routes',
            'storage',
            'vendor',
            'artisan',
            'composer.json',
            'composer.lock',
            '.env.example'
        ];

        for (const file of backendFiles) {
            const srcPath = path.join(__dirname, '..', file);
            const destPath = path.join(LARAVEL_DIR, file);
            
            if (fs.existsSync(srcPath)) {
                await fs.copy(srcPath, destPath, {
                    dereference: true,
                    filter: (src) => {
                        // Skip cache dan log files
                        return !src.includes('storage/logs') && 
                               !src.includes('storage/framework/cache') &&
                               !src.includes('storage/framework/sessions') &&
                               !src.includes('storage/framework/views');
                    }
                });
                console.log(`✅ Copied: ${file}`);
            }
        }

        // 5. Copy frontend files (public)
        console.log('🌐 Menyalin file frontend ke public_html...');
        
        const publicPath = path.join(__dirname, '..', 'public');
        if (fs.existsSync(publicPath)) {
            await fs.copy(publicPath, PUBLIC_HTML_DIR, { dereference: true });
            console.log('✅ Copied: public files');
        }

        // 6. Buat folder storage yang diperlukan di Laravel
        console.log('📁 Membuat struktur folder storage...');
        const storageDirs = [
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'storage/app/public'
        ];

        for (const dir of storageDirs) {
            await fs.ensureDir(path.join(LARAVEL_DIR, dir));
        }

        // 7. Set permissions untuk storage (untuk Unix systems)
        console.log('🔐 Setting permissions...');
        try {
            execSync(`chmod -R 775 "${path.join(LARAVEL_DIR, 'storage')}"`, { stdio: 'inherit' });
            execSync(`chmod -R 775 "${path.join(LARAVEL_DIR, 'bootstrap/cache')}"`, { stdio: 'inherit' });
        } catch (e) {
            console.log('⚠️  Skipping chmod (Windows system)');
        }

        // 8. Buat file konfigurasi khusus production
        console.log('⚙️  Membuat file konfigurasi production...');
        
        // Buat file .env.production
        const envProduction = `APP_NAME=ReportApp
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
MAIL_FROM_NAME="\${APP_NAME}"

VITE_APP_NAME="\${APP_NAME}"`;

        await fs.writeFile(path.join(LARAVEL_DIR, '.env.production'), envProduction);

        // 9. Buat README untuk deployment
        const readmeContent = `# Deployment Guide

## Struktur File:
- \`laravel/\` - Backend Laravel application
- \`public_html/\` - Frontend files untuk web server

## Deployment Steps:

### 1. Upload Backend (laravel folder):
- Upload semua file di folder \`laravel/\` ke root directory server Anda
- Pastikan folder \`storage/\` dan \`bootstrap/cache/\` writable (775)

### 2. Upload Frontend (public_html folder):
- Upload semua file di folder \`public_html/\` ke folder public_html server Anda

### 3. Konfigurasi Environment:
- Rename \`.env.production\` menjadi \`.env\`
- Edit file \`.env\` sesuai dengan konfigurasi server Anda
- Generate app key: \`php artisan key:generate\`

### 4. Setup Database:
- Buat database baru
- Jalankan migrasi: \`php artisan migrate\`
- Jalankan seeder (opsional): \`php artisan db:seed\`

### 5. Optimize for Production:
\`\`\`bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
\`\`\`

### 6. Setup Storage Link:
\`\`\`bash
php artisan storage:link
\`\`\`

## File Permissions:
- Folder \`storage/\`: 775
- Folder \`bootstrap/cache/\`: 775
- File \`.env\`: 644

## Important Notes:
- Pastikan PHP version >= 8.1
- Enable extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json
- Set document root ke folder public_html
`;

        await fs.writeFile(path.join(DIST_DIR, 'README.md'), readmeContent);

        // 10. Restore composer untuk development
        console.log('🔄 Restoring composer untuk development...');
        execSync('composer install', { stdio: 'inherit' });

        console.log('\n🎉 Build production selesai!');
        console.log(`📦 Output tersedia di: ${DIST_DIR}`);
        console.log('📂 laravel/ - Backend files');
        console.log('🌐 public_html/ - Frontend files');
        console.log('📖 README.md - Panduan deployment');

    } catch (error) {
        console.error('❌ Error during build:', error.message);
        process.exit(1);
    }
}

buildProduction();
