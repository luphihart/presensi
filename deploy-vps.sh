#!/bin/bash
# ==============================================================================
# Script Depoloyment Otomatis Laravel - Presensi Digital Sekolah
# Target Domain : presensi.sinaumedia.my.id
# Target IP     : 202.10.48.207
# ==============================================================================

echo "🚀 Memulai Proses Deployment Presensi Digital..."

# 1. Tarik kode terbaru dari GitHub
echo "📥 1/7 Pulling kode terbaru dari GitHub..."
git pull origin main

# 2. Install dependensi Composer
echo "📦 2/7 Installing PHP Composer Dependencies (Production)..."
composer install --no-dev --optimize-autoloader

# 3. Kunci Aplikasi & Database Migration
echo "🗄️ 3/7 Running Database Migrations..."
php artisan migrate --force

# 4. Storage Link, Livewire Assets & Permission
echo "📁 4/7 Linking Storage, Publishing Livewire Assets & Fixing Permissions..."
php artisan storage:link --quiet
php artisan livewire:publish --assets --quiet
chown -R www-data:www-data storage bootstrap/cache public/vendor
chmod -R 775 storage bootstrap/cache public/vendor

# 5. Build Assets (Vite) jika NodeJS tersedia
if command -v npm &> /dev/null
then
    echo "⚡ 5/7 Building Vite Production Assets..."
    npm install
    npm run build
fi

# 6. Optimize Cache Laravel
echo "🧹 6/7 Caching Configuration & Routes..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 7. Restart Services
echo "🔄 7/7 Restarting PHP-FPM & Nginx..."
sudo systemctl restart php8.2-fpm || sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx

echo "✅ DEPLOYMENT SELESAI! Aplikasi berjalan di https://presensi.sinaumedia.my.id"
