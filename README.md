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

Aplikasi ini menyediakan beberapa cara untuk build production:

### Menggunakan Bun (Recommended)
```bash
bun run build:production
```

### Menggunakan PowerShell
```bash
bun run build:production:ps
```

### Menggunakan Batch File
```bash
bun run build:production:bat
```

Atau langsung jalankan:
```bash
# PowerShell
.\scripts\build-production.ps1

# Batch
.\scripts\build-production.bat
```

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
| `bun run build` | Build untuk production |
| `bun run build:production` | Build lengkap dengan separation backend/frontend |
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

## Contributing

1. Fork project
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## License

This project is licensed under the MIT License.
