import fs from 'fs-extra';
import path from 'path';
import { execSync } from 'child_process';
import { fileURLToPath } from 'url';
import archiver from 'archiver';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.join(__dirname, '..');

// Konfigurasi folder output
const DIST_DIR = path.join(ROOT_DIR, 'dist');
const LARAVEL_DIR = path.join(DIST_DIR, 'laravel');
const PUBLIC_HTML_DIR = path.join(DIST_DIR, 'public_html');

// Konfigurasi files yang akan di-copy
const BACKEND_FILES = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'vendor', 'artisan', 'composer.json', 'composer.lock', '.env.example'];
const FRONTEND_SOURCE = path.join(ROOT_DIR, 'public');

console.log('🚀 Memulai build production (optimized)...\n');

// Helper functions
function executeStep(step, fn) {
    console.log(`${step}`);
    return fn();
}

function filterFiles(src) {
    // Skip cache, logs, dan node_modules
    return !src.includes('storage/logs') && 
           !src.includes('storage/framework/cache') &&
           !src.includes('storage/framework/sessions') &&
           !src.includes('storage/framework/views') &&
           !src.includes('node_modules');
}

async function createZipFile(sourceDir, outputFile) {
    return new Promise((resolve, reject) => {
        const output = fs.createWriteStream(outputFile);
        const archive = archiver('zip', { zlib: { level: 9 } });
        
        output.on('close', () => resolve(outputFile));
        archive.on('error', (err) => reject(err));
        
        archive.pipe(output);
        archive.directory(sourceDir, false);
        archive.finalize();
    });
}

async function buildProduction() {
    try {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
        
        // 1. Cleanup dan setup directories
        await executeStep('📁 Setup build directories...', async () => {
            if (fs.existsSync(DIST_DIR)) await fs.remove(DIST_DIR);
            await fs.ensureDir(LARAVEL_DIR);
            await fs.ensureDir(PUBLIC_HTML_DIR);
        });

        // 2. Build frontend (hanya jika belum di-build)
        await executeStep('🔨 Building frontend assets...', () => {
            execSync('npm run build', { stdio: 'inherit', cwd: ROOT_DIR });
        });

        // 3. Install production dependencies (temporary)
        await executeStep('📦 Installing production dependencies...', () => {
            execSync('composer install --optimize-autoloader --no-dev --quiet', { stdio: 'inherit', cwd: ROOT_DIR });
        });

        // 4. Copy backend files (bulk copy dengan filter)
        await executeStep('📂 Copying backend files...', async () => {
            await Promise.all(BACKEND_FILES.map(async (file) => {
                const srcPath = path.join(ROOT_DIR, file);
                const destPath = path.join(LARAVEL_DIR, file);
                
                if (fs.existsSync(srcPath)) {
                    await fs.copy(srcPath, destPath, {
                        dereference: true,
                        filter: filterFiles
                    });
                }
            }));
            console.log(`✅ Copied ${BACKEND_FILES.length} backend components`);
        });

        // 5. Copy frontend files
        await executeStep('🌐 Copying frontend files...', async () => {
            if (fs.existsSync(FRONTEND_SOURCE)) {
                await fs.copy(FRONTEND_SOURCE, PUBLIC_HTML_DIR, { dereference: true });
                console.log('✅ Frontend files copied');
            }
        });

        // 6. Create production .env (simple template)
        await executeStep('⚙️ Creating production config...', async () => {
            const envTemplate = `APP_NAME=ReportApp
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cache & Session
CACHE_STORE=file
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail (optional)
MAIL_MAILER=log

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error`;
            
            await fs.writeFile(path.join(LARAVEL_DIR, '.env.production'), envTemplate);
        });

        // 7. Set permissions (Unix only)
        await executeStep('🔐 Setting permissions...', () => {
            try {
                execSync(`chmod -R 775 "${path.join(LARAVEL_DIR, 'storage')}"`, { stdio: 'pipe' });
                execSync(`chmod -R 775 "${path.join(LARAVEL_DIR, 'bootstrap/cache')}"`, { stdio: 'pipe' });
                console.log('✅ Permissions set');
            } catch (e) {
                console.log('⚠️ Skipping chmod (Windows system)');
            }
        });

        // 8. Create ZIP archives (optional - skip for faster build)
        const skipZip = process.argv.includes('--no-zip');
        if (!skipZip) {
            await executeStep('📦 Creating ZIP archives...', async () => {
                const backendZip = path.join(DIST_DIR, `laravel-backend-${timestamp}.zip`);
                const frontendZip = path.join(DIST_DIR, `public_html-frontend-${timestamp}.zip`);

                // Create archives in parallel (skip complete archive to save time)
                await Promise.all([
                    createZipFile(LARAVEL_DIR, backendZip),
                    createZipFile(PUBLIC_HTML_DIR, frontendZip)
                ]);
                
                console.log('✅ ZIP archives created (use --no-zip to skip)');
            });
        } else {
            console.log('⏩ Skipping ZIP creation (--no-zip flag)');
        }

        // 9. Restore development dependencies
        await executeStep('🔄 Restoring development environment...', () => {
            execSync('composer install --quiet', { stdio: 'inherit', cwd: ROOT_DIR });
        });

        // 10. Summary
        console.log('\n🎉 Build production completed!\n');
        console.log('📁 Output structure:');
        console.log('├── laravel/                           # Backend files');
        console.log('├── public_html/                       # Frontend files');
        if (!skipZip) {
            console.log('├── laravel-backend-*.zip             # Backend ZIP');
            console.log('└── public_html-frontend-*.zip        # Frontend ZIP\n');
        } else {
            console.log('└── (ZIP archives skipped)\n');
        }
        console.log(`📦 Location: ${DIST_DIR}`);
        console.log('📖 See deploy.md for deployment instructions');
        console.log('\n💡 Tips:');
        console.log('   • Use --no-zip for faster builds during development');
        console.log('   • Test deployment with admin-tools.php for troubleshooting');

    } catch (error) {
        console.error('❌ Build failed:', error.message);
        
        // Restore development environment on error
        try {
            execSync('composer install --quiet', { stdio: 'inherit', cwd: ROOT_DIR });
        } catch (restoreError) {
            console.error('⚠️ Failed to restore development environment');
        }
        
        process.exit(1);
    }
}

buildProduction();
