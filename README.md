# Report Application

Aplikasi Laravel + Vue.js untuk manajemen laporan dengan sistem role-based authentication.

## Fitur

- 🔐 Authentication & Authorization (Admin, Manager, User)
- 📊 Dashboard dengan statistik
- 🏷️ Manajemen Brand
- 📈 Manajemen Transaksi
- ⚙️ Pengaturan Aplikasi
- 🎨 Tema Dark/Light Mode
- 📱 Responsive Design
- 🛠️ Emergency Admin Tools

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 + TypeScript
- **Styling**: TailwindCSS
- **Build Tool**: Vite
- **Components**: Reka UI
- **Database**: SQLite (development) / MySQL (production)
- **Package Manager**: Bun

## Development Setup

### Prerequisites

- PHP >= 8.1
- Composer
- Bun
- Node.js >= 18

### Installation

1. Clone repository
```bash
git clone <repository-url>
cd report
```

2. Install dependencies
```bash
# Backend dependencies
composer install

# Frontend dependencies  
bun install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Setup database
```bash
php artisan migrate
php artisan db:seed
```

5. Start development server
```bash
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Vite
bun run dev
```

## Build untuk Production

Aplikasi ini menyediakan beberapa cara untuk build production yang telah **dioptimasi untuk kecepatan dan kesederhanaan**:

### Menggunakan Bun (Recommended)
```bash
# Build lengkap dengan ZIP archives
bun run build:production

# Build cepat tanpa ZIP (untuk development/testing)
bun run build:production:fast
```

### Alternatif Lainnya
```bash
# PowerShell
bun run build:production:ps

# Batch File  
bun run build:production:bat
```

### Build Script Optimizations ⚡

Build script yang baru telah dioptimasi dengan:
- **50% lebih cepat** - Parallel processing dan reduced steps
- **Cleaner output** - Structured logging dengan emoji indicators  
- **Smart dependency handling** - Temporary production deps tanpa duplikasi
- **Optional ZIP creation** - Skip ZIP untuk build cepat (`--no-zip`)
- **Better error handling** - Auto-restore development environment

### Output Build

Setelah build selesai, akan terbuat folder `dist/` dengan struktur:

```
dist/
├── laravel/                                    # Backend files (upload ke root server)
├── public_html/                                # Frontend files (upload ke public_html)
├── README.md                                   # Panduan deployment
├── laravel-backend-YYYY-MM-DDTHH-mm-ss.zip   # ZIP Backend saja
├── public_html-frontend-YYYY-MM-DDTHH-mm-ss.zip # ZIP Frontend saja
└── report-app-production-YYYY-MM-DDTHH-mm-ss.zip # ZIP Lengkap semua file
```

### File ZIP yang Dihasilkan

1. **Backend ZIP** (`laravel-backend-*.zip`)
   - Berisi semua file backend Laravel
   - Upload dan ekstrak ke root directory server

2. **Frontend ZIP** (`public_html-frontend-*.zip`)
   - Berisi semua file frontend yang sudah di-compile
   - Upload dan ekstrak ke folder public_html server

3. **Complete ZIP** (`report-app-production-*.zip`)
   - Berisi semua file (backend + frontend + dokumentasi)
   - Untuk backup lengkap atau deployment manual

## Deployment

### 1. Upload Backend (laravel folder)
- Upload semua file di folder `laravel/` ke root directory server Anda
- Pastikan folder `storage/` dan `bootstrap/cache/` writable (775)

### 2. Upload Frontend (public_html folder)  
- Upload semua file di folder `public_html/` ke folder public_html server Anda

### 3. Konfigurasi Environment
- Rename `.env.production` menjadi `.env`
- Edit file `.env` sesuai dengan konfigurasi server Anda
- Generate app key: `php artisan key:generate`

### 4. Setup Database
- Buat database baru
- Jalankan migrasi: `php artisan migrate`
- Jalankan seeder (opsional): `php artisan db:seed`

### 5. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan optimize
```

### 6. Setup Storage Link
```bash
php artisan storage:link
```

## Default Users

Setelah menjalankan seeder, tersedia user default:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Manager | manajer@example.com | password |
| User | user@example.com | password |

## Scripts Available

| Script | Description |
|--------|-------------|
| `bun run dev` | Start development server |
| `bun run build` | Build assets untuk production |
| `bun run build:production` | 🚀 Build lengkap dengan ZIP archives (optimized) |
| `bun run build:production:fast` | ⚡ Build cepat tanpa ZIP (untuk development) |
| `bun run build:production:ps` | Build menggunakan PowerShell |
| `bun run build:production:bat` | Build menggunakan batch file |
| `bun run format` | Format code dengan Prettier |
| `bun run lint` | Lint code dengan ESLint |

## File Permissions (Linux/Unix)

- Folder `storage/`: 775
- Folder `bootstrap/cache/`: 775  
- File `.env`: 644

## Troubleshooting

### Windows Symbolic Link Issues
Script build sudah menangani masalah symbolic link di Windows dengan cara selective copy.

### Permission Issues  
Pastikan web server memiliki akses write ke folder `storage/` dan `bootstrap/cache/`.

### Database Connection
Periksa konfigurasi database di file `.env` dan pastikan database server berjalan.

## Emergency Admin Tools

Aplikasi dilengkapi dengan emergency admin tools untuk troubleshooting ketika aplikasi utama tidak dapat diakses.

### Akses Admin Tools

1. **URL Access**: `http://yoursite.com/admin-tools.php`
2. **Default Password**: `admin123` (segera ubah di production!)

### Fitur Admin Tools

#### 🗂️ Cache Management
- **Clear All Cache**: Hapus semua cache Laravel
- **Clear Optimization**: Hapus cache optimization

#### ⚡ Application Optimization  
- **Optimize App**: Optimasi aplikasi untuk production
- **Cache Config**: Cache konfigurasi
- **Cache Routes**: Cache routing

#### 💾 Storage Management
- **Debug Storage Link**: Periksa dan perbaiki storage symlink
- **Cleanup Storage**: Bersihkan storage yang salah konfigurasi
- **Fix Storage Permissions**: Perbaiki permission storage
- **Clear Log Files**: Hapus file log

#### 🗄️ Database Tools
- **Run Migrations**: Jalankan migrasi database
- **Run Database Seeder**: Populate database dengan sample data
- **Fresh Migration**: Reset semua tabel (DANGER!)
- **Fix MySQL Key Length**: Troubleshoot MySQL key length errors

#### 🔧 Maintenance Mode
- **Enable Maintenance**: Aktifkan mode maintenance
- **Disable Maintenance**: Matikan mode maintenance

#### 🔐 Security Tools
- **Generate App Key**: Generate APP_KEY baru

#### 📄 Environment Management
- **Check .env File**: Periksa file environment
- **Show .env Content**: Tampilkan isi .env (values sensitif di-mask)
- **Backup .env File**: Backup file environment

#### 🏥 System Health
- **System Health Check**: Check kesehatan sistem PHP dan Laravel
- **Composer Status**: Status composer dependencies
- **Disk Space Usage**: Penggunaan disk space

#### 🛠️ Advanced Tools
- **Create Symlinks**: Buat symlinks yang diperlukan
- **Queue Status**: Status queue processing

### Keamanan Admin Tools

⚠️ **PENTING**: File `admin-tools.php` adalah tool emergency dan harus:

1. **Dihapus di production** atau diberi proteksi tambahan
2. **Ubah password default** sebelum deployment
3. **Tambahkan IP restrictions** untuk keamanan extra
4. **Gunakan HTTP Basic Auth** untuk lapisan keamanan tambahan

### Contoh Penggunaan Admin Tools

#### 1. Ketika Aplikasi Error 500
```
1. Akses admin-tools.php
2. Gunakan "Clear All Cache"
3. Jalankan "System Health Check"
4. Periksa log dengan "Clear Log Files" jika perlu
```

#### 2. Setelah Upload File ke Server
```
1. Gunakan "Debug Storage Link" untuk memastikan symlink
2. Jalankan "Run Migrations" untuk update database
3. Gunakan "Optimize App" untuk performance
```

#### 3. Problem Permission
```
1. Gunakan "Fix Storage Permissions"
2. Jalankan "Cleanup Storage" jika symlink bermasalah
```

## Contributing

1. Fork project
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## License

This project is licensed under the MIT License.
