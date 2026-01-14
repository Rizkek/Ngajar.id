# 🚀 Quick Start Guide - Ngajar.id Laravel

## ⚡ Instalasi Cepat

### 1. Install Prerequisites

**Download & Install:**

- [Laragon](https://laragon.org/download/) - Includes PHP 8.2, Composer, Node.js
- Atau install manual: PHP 8.2+, Composer, Node.js 18+

### 2. Setup Laravel Project

```bash
cd e:\coding\Ngajar.id

# Jika belum ada Laravel, install dulu:
composer create-project laravel/laravel temp-laravel
xcopy /E /I temp-laravel\* .
rd /s /q temp-laravel

# Install dependencies
composer install
npm install
```

### 3. Setup Environment

```bash
# Copy environment
copy LARAVEL_CONFIG\.env.example .env

# Generate app key
php artisan key:generate
```

Edit `.env` - **PENTING! Ganti DB_PASSWORD:**

```env
DB_CONNECTION=pgsql
DB_HOST=db.pnnjmyeerflqwjnwcurf.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=GANTI_DENGAN_PASSWORD_SUPABASE_ANDA

SUPABASE_URL=https://pnnjmyeerflqwjnwcurf.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBubmpteWVlcmZscXdqbndjdXJmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgwNjg0MjMsImV4cCI6MjA4MzY0NDQyM30.GOtH0gURGPdGzGNNlTvUbPxb5xjAuIYUWGseu_J69po
SUPABASE_BUCKET=ngajar-files
```

### 4. Install Filament

```bash
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

### 5. Enable PostgreSQL Extension

Edit `php.ini` (di Laragon: `C:\laragon\bin\php\php-8.x\php.ini`):

```ini
extension=pdo_pgsql
extension=pgsql
```

Restart Laragon/web server.

### 6. Copy Files

```bash
# Copy migrations
xcopy /Y LARAVEL_MIGRATIONS\*.php database\migrations\

# Copy models
xcopy /Y LARAVEL_MODELS\*.php app\Models\

# Copy Filament resources
mkdir app\Filament\Resources
xcopy /Y LARAVEL_FILAMENT\*.php app\Filament\Resources\

# Copy services
mkdir app\Services
xcopy /Y LARAVEL_SERVICES\*.php app\Services\

# Copy config
copy /Y LARAVEL_CONFIG\filesystems.php config\filesystems.php

# Copy seeder
copy /Y LARAVEL_SEEDERS\DatabaseSeeder.php database\seeders\DatabaseSeeder.php
```

### 7. Setup Filament Resource Pages

Setelah copy resources, generate pages untuk setiap resource:

```bash
# Generate pages untuk UserResource
php artisan make:filament-pages ManageUsers --resource=UserResource --type=ManageRecords

# Atau biarkan Filament auto-generate saat pertama kali akses admin panel
```

**ATAU** buat manual pages untuk setiap resource (UserResource, KelasResource, dll) jika ada error.

### 8. Setup Supabase Storage

1. Login ke [Supabase Dashboard](https://supabase.com/dashboard)
2. Pilih project `pnnjmyeerflqwjnwcurf`
3. Storage → Create Bucket:
   - Name: `ngajar-files`
   - Public: ✅ Yes
4. Buat folder: `materi`, `modul`, `profiles` di dalam bucket

### 9. Run Migrations & Seed

```bash
# Run migrations
php artisan migrate:fresh

# Seed data dummy
php artisan db:seed
```

### 10. Create Admin User

```bash
php artisan make:filament-user
```

Input:

- Name: `Admin`
- Email: `admin@ngajar.id`
- Password: `password` (minimal 8 karakter)

### 11. Build Assets & Run

```bash
# Build frontend assets
npm run build

# Run development server
php artisan serve
```

**Akses:**

- Frontend: http://localhost:8000
- Admin Panel: http://localhost:8000/admin

**Login Admin:**

- Email: `admin@ngajar.id`
- Password: `password`

---

## 🔥 One-Liner Install (After Laravel is Ready)

```bash
xcopy /Y LARAVEL_MIGRATIONS\*.php database\migrations\ && xcopy /Y LARAVEL_MODELS\*.php app\Models\ && mkdir app\Filament\Resources && xcopy /Y LARAVEL_FILAMENT\*.php app\Filament\Resources\ && mkdir app\Services && xcopy /Y LARAVEL_SERVICES\*.php app\Services\ && copy /Y LARAVEL_CONFIG\filesystems.php config\filesystems.php && copy /Y LARAVEL_SEEDERS\DatabaseSeeder.php database\seeders\DatabaseSeeder.php && php artisan migrate:fresh && php artisan db:seed && npm run build && php artisan serve
```

---

## 📋 Checklist

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] Node.js 18+ installed
- [ ] PostgreSQL extensions enabled
- [ ] `.env` configured with Supabase credentials
- [ ] Supabase bucket `ngajar-files` created & public
- [ ] Migrations copied & run
- [ ] Models & Resources copied
- [ ] Filament installed
- [ ] Admin user created
- [ ] Assets built
- [ ] Server running

---

## ⚠️ Common Issues

**Error: could not find driver**

```bash
# Enable PostgreSQL extension in php.ini
extension=pdo_pgsql
extension=pgsql
# Restart web server
```

**Error: Filament Resource not found**

```bash
# Regenerate Filament resources
php artisan make:filament-resource User --generate
```

**Error: 500 when accessing admin**

```bash
# Clear cache
php artisan optimize:clear
php artisan config:clear
```

---

## 🎯 Default Login Credentials

After running `php artisan db:seed`:

| Role     | Email            | Password |
| -------- | ---------------- | -------- |
| Admin    | admin@ngajar.id  | password |
| Pengajar | budi@ngajar.id   | password |
| Murid    | ahmad@student.id | password |

---

## 📚 Next Steps

1. Login ke admin panel: http://localhost:8000/admin
2. Explore semua menu (Users, Kelas, Materi, Modul, Topup, Donasi)
3. Test create/edit/delete data
4. Test file upload ke Supabase
5. Customize sesuai kebutuhan Anda

Happy coding! 🚀
