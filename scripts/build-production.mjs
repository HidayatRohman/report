import { $ } from 'bun';
import { existsSync, mkdirSync, rmSync, cpSync, writeFileSync } from 'fs';
import { join } from 'path';
import archiver from 'archiver';
import { createWriteStream } from 'fs';

// Konfigurasi folder output
const DIST_DIR =        console.log('\n🎉 Build production selesai!');
        console.log(`📦 Output tersedia di: ${DIST_DIR}`);
        console.log('📂 laravel/ - Backend files (upload ke root server)');
        console.log('🌐 public_html/ - Frontend files (upload ke public_html)');
        console.log('📖 README.md - Panduan deployment');
        console.log('\n📦 File ZIP yang tersedia:');
        console.log(`🔧 laravel-backend-${timestamp}.zip - Backend saja`);
        console.log(`🌐 public_html-frontend-${timestamp}.zip - Frontend saja`);
        console.log('\n📋 Langkah selanjutnya:');
        console.log('1. Download dan ekstrak ZIP sesuai kebutuhan');
        console.log('2. Upload backend ke root directory server');
        console.log('3. Upload frontend ke public_html server');
        console.log('4. Follow panduan di README.md');cwd(), 'dist');
const LARAVEL_DIR = join(DIST_DIR, 'laravel');
const PUBLIC_HTML_DIR = join(DIST_DIR, 'public_html');

console.log('🚀 Memulai build production dengan Bun...\n');

// Fungsi untuk membuat ZIP file
async function createZipFile(sourceDir, outputPath, name) {
    return new Promise((resolve, reject) => {
        const output = createWriteStream(outputPath);
        const archive = archiver('zip', {
            zlib: { level: 9 } // Maximum compression
        });

        output.on('close', () => {
            console.log(`✅ ${name}: ${(archive.pointer() / 1024 / 1024).toFixed(2)} MB`);
            resolve();
        });

        archive.on('error', (err) => {
            reject(err);
        });

        archive.pipe(output);
        archive.directory(sourceDir, false);
        archive.finalize();
    });
}

async function buildProduction() {
    try {
        // 1. Bersihkan folder dist jika ada
        console.log('📁 Membersihkan folder dist...');
        if (existsSync(DIST_DIR)) {
            rmSync(DIST_DIR, { recursive: true, force: true });
        }
        mkdirSync(DIST_DIR, { recursive: true });
        mkdirSync(LARAVEL_DIR, { recursive: true });
        mkdirSync(PUBLIC_HTML_DIR, { recursive: true });

        // 2. Install dependencies dan build frontend
        console.log('📦 Menginstall dependencies...');
        await $`bun install`;
        
        console.log('🔨 Building frontend assets...');
        await $`bun run build`;

        // 3. Install composer dependencies untuk production
        console.log('🎵 Menginstall Composer dependencies untuk production...');
        await $`composer install --optimize-autoloader --no-dev`;

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
            const srcPath = join(process.cwd(), file);
            const destPath = join(LARAVEL_DIR, file);
            
            if (existsSync(srcPath)) {
                cpSync(srcPath, destPath, { 
                    recursive: true,
                    filter: (src) => {
                        // Skip cache dan log files
                        return !src.includes('storage\\logs') && 
                               !src.includes('storage\\framework\\cache') &&
                               !src.includes('storage\\framework\\sessions') &&
                               !src.includes('storage\\framework\\views');
                    }
                });
                console.log(`✅ Copied: ${file}`);
            }
        }

        // 5. Copy frontend files (public)
        console.log('🌐 Menyalin file frontend ke public_html...');
        
        const publicPath = join(process.cwd(), 'public');
        if (existsSync(publicPath)) {
            // Copy selectively to avoid symbolic link issues
            const publicFiles = [
                'index.php',
                'robots.txt', 
                'favicon.ico',
                'favicon.svg',
                'apple-touch-icon.png',
                'build',
                'images'
            ];
            
            for (const file of publicFiles) {
                const srcFile = join(publicPath, file);
                const destFile = join(PUBLIC_HTML_DIR, file);
                
                if (existsSync(srcFile)) {
                    cpSync(srcFile, destFile, { recursive: true });
                    console.log(`✅ Copied: public/${file}`);
                }
            }
            
            console.log('✅ Copied: public files (excluding storage symlink)');
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
            mkdirSync(join(LARAVEL_DIR, dir), { recursive: true });
        }

        // 7. Buat file konfigurasi khusus production
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

        writeFileSync(join(LARAVEL_DIR, '.env.production'), envProduction);

        // 8. Buat README untuk deployment
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

## Ekstrak dan Upload:
1. Ekstrak folder \`laravel/\` ke root directory server (biasanya public_html atau www)
2. Ekstrak folder \`public_html/\` ke folder public_html server
3. Follow deployment steps di atas
`;

        writeFileSync(join(DIST_DIR, 'README.md'), readmeContent);

        // 9. Membuat file ZIP
        console.log('📦 Membuat file ZIP...');
        
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
        const laravelZipPath = join(DIST_DIR, `laravel-backend-${timestamp}.zip`);
        const publicZipPath = join(DIST_DIR, `public_html-frontend-${timestamp}.zip`);

        // Buat ZIP untuk backend Laravel
        await createZipFile(LARAVEL_DIR, laravelZipPath, 'Backend ZIP');
        
        // Buat ZIP untuk frontend public_html
        await createZipFile(PUBLIC_HTML_DIR, publicZipPath, 'Frontend ZIP');
        
        console.log('✅ ZIP files berhasil dibuat');

        // 10. Restore composer untuk development
        console.log('🔄 Restoring composer untuk development...');
        await $`composer install`;

        console.log('\n🎉 Build production selesai!');
        console.log(`📦 Output tersedia di: ${DIST_DIR}`);
        console.log('📂 laravel/ - Backend files (upload ke root server)');
        console.log('🌐 public_html/ - Frontend files (upload ke public_html)');
        console.log('📖 README.md - Panduan deployment');
        console.log('\n� File ZIP yang tersedia:');
        console.log(`🔧 laravel-backend-${timestamp}.zip - Backend saja`);
        console.log(`🌐 public_html-frontend-${timestamp}.zip - Frontend saja`);
        console.log(`📦 report-app-production-${timestamp}.zip - Lengkap (semua file)`);
        console.log('\n�📋 Langkah selanjutnya:');
        console.log('1. Download dan ekstrak ZIP sesuai kebutuhan');
        console.log('2. Upload backend ke root directory server');
        console.log('3. Upload frontend ke public_html server');
        console.log('4. Follow panduan di README.md');

    } catch (error) {
        console.error('❌ Error during build:', error.message);
        process.exit(1);
    }
}

buildProduction();
