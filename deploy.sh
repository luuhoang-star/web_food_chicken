#!/bin/bash
# ==============================================================================
# Script Tự Động Triển Khai (Deploy) Cho Dự Án GAO Chicken
# ==============================================================================

set -e

echo "🚀 Bắt đầu quá trình tự động cập nhật hệ thống..."

# 1. Kéo mã nguồn mới nhất từ GitHub
echo "📦 1. Kéo mã nguồn mới từ branch main..."
git fetch origin main
git reset --hard origin/main

# 2. Cài đặt các gói PHP Dependencies
echo "🐘 2. Cài đặt PHP Composer dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3. Cài đặt và build frontend assets (Vite / Tailwind)
echo "⚡ 3. Build giao diện Frontend (Vite)..."
if command -v npm &> /dev/null; then
    npm install --silent
    npm run build
fi

# 4. Chạy migrate database
echo "🗄️ 4. Chạy Migration cơ sở dữ liệu..."
php artisan migrate --force

# 5. Tối ưu hóa và làm mới bộ nhớ đệm (Cache)
echo "🧹 5. Tối ưu hóa Cache hệ thống..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Khởi động lại Queue Worker nếu có
echo "🔄 6. Khởi động lại Queue..."
php artisan queue:restart || true

echo "✅ Deploy hoàn tất thành công! Website đã được cập nhật phiên bản mới nhất."
