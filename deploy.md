# Tutorial Deployment Report Application

Panduan lengkap untuk deployment aplikasi Laravel + Vue.js Report Application ke server production.

## 📋 Daftar Isi

- [Persiapan](#persiapan)
- [Build Production](#build-production)
- [Upload ke Server](#upload-ke-server)
- [Konfigurasi Server](#konfigurasi-server)
- [Setup Database](#setup-database)
- [Optimasi Production](#optimasi-production)
- [Testing Deployment](#testing-deployment)
- [Troubleshooting](#troubleshooting)
- [Maintenance](#maintenance)

## 🔧 Persiapan

### Requirements Server

#### Minimum Requirements
- **PHP**: >= 8.1 (Recommended: 8.3)
- **MySQL/MariaDB**: >= 5.7 / >= 10.3
- **Web Server**: Apache/Nginx
- **Disk Space**: Minimum 500MB
- **Memory**: Minimum 512MB RAM

#### PHP Extensions Required
```bash
# Extensions yang diperlukan
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- Curl
```

### Tools yang Dibutuhkan
- FTP/SFTP client (FileZilla, WinSCP, atau terminal)
- Text editor untuk edit file
- Access ke cPanel/Plesk atau terminal server

## 🚀 Build Production

### Step 1: Build Local

Jalankan build production di komputer lokal:

```bash
# Pastikan di direktori project
cd report

# Build menggunakan Bun (recommended)
bun run build:production

# Atau menggunakan PowerShell
bun run build:production:ps

# Atau menggunakan Batch
bun run build:production:bat
```

### Step 2: Verifikasi Build

Setelah build selesai, pastikan folder `dist/` terbuat dengan struktur:

```
dist/
├── laravel/                           # Backend files
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── artisan
│   ├── composer.json
│   └── .env.production
├── public_html/                       # Frontend files
│   ├── build/
│   ├── admin-tools.php
│   ├── index.php
│   └── ...
├── README.md                          # Deployment guide
├── laravel-backend-*.zip             # Backend ZIP
├── public_html-frontend-*.zip        # Frontend ZIP
└── report-app-production-*.zip       # Complete ZIP
```

## 📤 Upload ke Server

### Metode 1: Upload Manual (Shared Hosting)

#### A. Upload Backend (Laravel)

1. **Extract backend ZIP** atau gunakan folder `dist/laravel/`

2. **Upload ke root directory server** (biasanya `public_html/../` atau `/var/www/html/../`)
   ```
   Server Structure:
   /home/username/
   ├── public_html/          # Web root (akan diisi frontend)
   ├── laravel/              # Backend files (upload kesini)
   │   ├── app/
   │   ├── bootstrap/
   │   ├── config/
   │   └── ...
   └── ...
   ```

#### B. Upload Frontend (Public)

1. **Extract frontend ZIP** atau gunakan folder `dist/public_html/`

2. **Upload ke web root** (folder `public_html/` atau `/var/www/html/`)
   ```
   public_html/
   ├── build/                # Vite build assets
   ├── admin-tools.php       # Emergency tools
   ├── index.php            # Laravel entry point
   ├── favicon.ico
   └── ...
   ```

### Metode 2: Upload via ZIP (cPanel)

1. **Login ke cPanel**

2. **Upload Backend**:
   - Masuk File Manager
   - Navigasi ke parent directory dari `public_html`
   - Upload `laravel-backend-*.zip`
   - Extract ZIP file
   - Rename folder hasil extract menjadi `laravel`

3. **Upload Frontend**:
   - Navigasi ke folder `public_html`
   - Upload `public_html-frontend-*.zip`
   - Extract ZIP file di `public_html`

### Metode 3: Command Line (VPS/Dedicated)

```bash
# Connect via SSH
ssh user@yourserver.com

# Create directories
mkdir -p /var/www/yoursite
cd /var/www/yoursite

# Upload using scp (from local)
scp laravel-backend-*.zip user@yourserver.com:/var/www/yoursite/
scp public_html-frontend-*.zip user@yourserver.com:/var/www/yoursite/

# Extract files
unzip laravel-backend-*.zip
unzip public_html-frontend-*.zip -d /var/www/yoursite/public
```

## ⚙️ Konfigurasi Server

### Step 1: Environment Configuration

1. **Rename .env file**:
   ```bash
   # Di direktori laravel/
   mv .env.production .env
   ```

2. **Edit .env file** sesuai server:
   ```env
   APP_NAME="Report Application"
   APP_ENV=production
   APP_KEY=base64:YOUR_GENERATED_KEY_HERE
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   # Database Configuration
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   
   # Mail Configuration (optional)
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.yourdomain.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@yourdomain.com
   MAIL_PASSWORD=your_mail_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

### Step 2: Web Server Configuration

#### Apache (.htaccess)

File `.htaccess` sudah included. Pastikan `mod_rewrite` aktif.

#### Nginx Configuration

Jika menggunakan Nginx, tambahkan konfigurasi:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/yoursite/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

### Step 3: File Permissions

```bash
# Set proper permissions
chmod -R 755 laravel/
chmod -R 775 laravel/storage/
chmod -R 775 laravel/bootstrap/cache/
chmod 644 laravel/.env

# Untuk Apache user (bisa berbeda per server)
chown -R www-data:www-data laravel/storage/
chown -R www-data:www-data laravel/bootstrap/cache/
```

## 🗄️ Setup Database

### Step 1: Create Database

#### Via cPanel
1. Login cPanel → MySQL Databases
2. Create Database: `username_reportapp`
3. Create User dengan password kuat
4. Add User to Database dengan ALL PRIVILEGES

#### Via Command Line
```bash
mysql -u root -p
CREATE DATABASE reportapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'reportuser'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON reportapp.* TO 'reportuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 2: Generate App Key

```bash
# Via SSH/Terminal
cd /path/to/laravel
php artisan key:generate

# Atau via Emergency Admin Tools
# Akses: http://yourdomain.com/admin-tools.php
# Klik: "Generate App Key"
```

### Step 3: Run Migrations

```bash
# Via command line
php artisan migrate --force

# Via Admin Tools (recommended untuk shared hosting)
# Akses admin-tools.php → Database Tools → "Run Migrations"
```

### Step 4: Seed Database (Optional)

```bash
# Untuk sample data
php artisan db:seed --force

# Ini akan membuat user default:
# Admin: admin@example.com / password
# Manager: manajer@example.com / password  
# User: user@example.com / password
```

## ⚡ Optimasi Production

### Step 1: Cache Configuration

```bash
# Via command line
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Via Admin Tools
# Akses admin-tools.php → Application Optimization
```

### Step 2: Storage Link

```bash
# Via command line
php artisan storage:link

# Via Admin Tools (recommended)
# Akses admin-tools.php → Storage → "Debug Storage Link"
```

### Step 3: Verify Deployment

Test beberapa URL:

```
✅ http://yourdomain.com - Should load login page
✅ http://yourdomain.com/login - Login form
✅ http://yourdomain.com/admin-tools.php - Admin tools (password: admin123)
```

## 🧪 Testing Deployment

### Functional Testing

1. **Login Test**:
   - Coba login dengan user default
   - Verifikasi redirect ke dashboard

2. **File Upload Test**:
   - Test upload gambar di Brand management
   - Verifikasi storage link bekerja

3. **Database Test**:
   - Create, edit, delete brand/transaksi
   - Verifikasi data tersimpan

4. **Performance Test**:
   - Check loading time halaman
   - Verifikasi asset loading (CSS, JS)

### Admin Tools Test

1. **Cache Test**:
   - Clear cache via admin tools
   - Verifikasi tidak ada error

2. **Health Check**:
   - Jalankan "System Health Check"
   - Pastikan semua extension loaded

3. **Storage Test**:
   - Test "Debug Storage Link"
   - Pastikan symlink bekerja

## 🔧 Troubleshooting

### Common Issues

#### 1. Internal Server Error (500)

**Solusi**:
```bash
# Check error logs
tail -f laravel/storage/logs/laravel.log

# Clear cache
# Via admin-tools.php → "Clear All Cache"

# Check permissions
chmod -R 775 laravel/storage/
chmod -R 775 laravel/bootstrap/cache/
```

#### 2. Storage/Images Not Loading

**Solusi**:
```bash
# Via Admin Tools
# admin-tools.php → Storage → "Debug Storage Link"
# atau "Cleanup Storage"

# Manual fix
cd public_html
ln -s ../laravel/storage/app/public storage
```

#### 3. Database Connection Error

**Solusi**:
1. Verify `.env` database credentials
2. Check database server status
3. Test connection via admin tools

#### 4. Permission Issues

**Solusi**:
```bash
# Fix storage permissions
# Via admin-tools.php → "Fix Storage Permissions"

# Or manual
find laravel/storage -type f -exec chmod 664 {} \;
find laravel/storage -type d -exec chmod 775 {} \;
```

#### 5. App Key Missing

**Solusi**:
```bash
# Via admin-tools.php → "Generate App Key"
# Or manual
php artisan key:generate --force
```

#### 6. PHP Executable Not Found (Admin Tools)

**Error**: `Could not find PHP executable. Tried PHP_BINARY, /usr/bin/php, /usr/local/bin/php`

**Solusi**:

1. **Contact Hosting Provider** untuk mendapatkan path PHP yang benar
2. **Common PHP 8.3 hosting paths** (prioritized):
   ```bash
   # cPanel hosting PHP 8.3
   /opt/cpanel/ea-php83/root/usr/bin/php
   
   # Other common PHP 8.3 paths
   /usr/local/php83/bin/php
   /usr/local/lsws/lsphp83/bin/php
   /usr/local/bin/php83
   /usr/bin/php83
   
   # Alternative versions (fallback)
   /opt/cpanel/ea-php82/root/usr/bin/php
   /opt/cpanel/ea-php81/root/usr/bin/php
   ```

3. **Test PHP path** melalui admin tools "System Health Check"

4. **Alternative solutions**:
   - Use hosting control panel (cPanel, Plesk) for Laravel commands
   - Create custom PHP wrapper script if needed
   - Some hosting may disable shell_exec entirely

### Debug Tools

#### 1. Laravel Logs
```bash
# Check application logs
tail -f laravel/storage/logs/laravel.log

# Or via admin tools
# admin-tools.php → "Clear Log Files" (untuk cleanup)
```

#### 2. Web Server Logs
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx  
tail -f /var/log/nginx/error.log
```

#### 3. PHP Errors
```bash
# Check PHP error log
tail -f /var/log/php_errors.log

# Enable temporary debugging (ONLY for troubleshooting)
# Edit .env: APP_DEBUG=true (jangan lupa set false lagi!)
```

## 🔄 Maintenance

### Regular Tasks

#### Daily
- Monitor error logs
- Check disk space usage (via admin tools)

#### Weekly  
- Backup database
- Backup .env file (via admin tools)
- Clear old logs if necessary

#### Monthly
- Update composer dependencies (if needed)
- Review and rotate backups
- Security audit

### Backup Strategy

#### Database Backup
```bash
# Manual backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Via cPanel → phpMyAdmin → Export
```

#### Files Backup
```bash
# Backup laravel directory
tar -czf laravel_backup_$(date +%Y%m%d).tar.gz laravel/

# Backup public_html
tar -czf public_backup_$(date +%Y%m%d).tar.gz public_html/
```

### Update Deployment

#### 1. Build New Version
```bash
# Local machine
bun run build:production
```

#### 2. Backup Current
```bash
# Backup current deployment
cp -r laravel laravel_backup_$(date +%Y%m%d)
cp -r public_html public_html_backup_$(date +%Y%m%d)
```

#### 3. Deploy Update
```bash
# Upload and replace files
# Run migrations if needed
php artisan migrate --force

# Clear and rebuild cache
php artisan optimize
```

### Security Checklist

- [ ] Remove or secure `admin-tools.php` in production
- [ ] Change default admin tools password
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Use HTTPS in production
- [ ] Regular security updates
- [ ] Monitor access logs
- [ ] Use strong database passwords
- [ ] Backup regularly

## 🎯 Best Practices

### Security
1. **Remove admin-tools.php** atau tambahkan IP restriction
2. **Use HTTPS** untuk production
3. **Strong passwords** untuk database dan admin
4. **Regular updates** untuk dependencies
5. **Monitor logs** untuk suspicious activity

### Performance
1. **Enable caching** (config, routes, views)
2. **Use CDN** untuk static assets jika diperlukan
3. **Optimize images** sebelum upload
4. **Regular cleanup** logs dan cache
5. **Database optimization** periodic

### Monitoring
1. **Setup alerts** untuk disk space
2. **Monitor error logs** daily
3. **Performance monitoring** untuk slow queries
4. **Uptime monitoring** untuk availability
5. **Backup verification** regular

## 📞 Support

Jika mengalami kesulitan:

1. **Check troubleshooting section** di dokumen ini
2. **Use admin tools** untuk diagnostic
3. **Check server logs** untuk error details
4. **Contact hosting support** untuk server issues

---

**Remember**: Deployment adalah process yang memerlukan attention to detail. Selalu test semua functionality setelah deployment dan keep backup yang regular!