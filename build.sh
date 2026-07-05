#!/usr/bin/env bash
# Matikan eksekusi jika ada eror
set -o errexit

# Install dependency tanpa bawa package testing/dev
composer install --no-dev --no-interaction --prefer-dist

# Optimasi internal cache Laravel 12
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan migrasi database otomatis di hostingan (--force wajib buat production)
php artisan migrate --force