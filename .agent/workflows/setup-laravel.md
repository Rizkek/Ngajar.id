---
description: Setup Laravel, Filament, dan Supabase untuk Ngajar.id
---

# 🚀 Setup Laravel + Filament + Supabase untuk Ngajar.id

## Prerequisites yang Diperlukan

### 1. Install PHP 8.2+ dan Composer

**Opsi A: Menggunakan Laragon (Recommended untuk Windows)**
1. Download Laragon dari: https://laragon.org/download/
2. Install Laragon Full (sudah include PHP, Composer, MySQL)
3. Buka Laragon → Menu → PHP → Version → Pilih PHP 8.2 atau 8.3
4. Laragon otomatis menambahkan PHP ke PATH

**Opsi B: Install Manual**
1. Download PHP 8.2+: https://windows.php.net/download/
2. Extract ke folder (misal: `C:\php`)
3. Download Composer: https://getcomposer.org/Composer-Setup.exe
4. Install Composer, pastikan pilih path PHP yang benar

### 2. Install Node.js & NPM
1. Download dari: https://nodejs.org/ (LTS version)
2. Install dengan default settings
3. Verify: `node -v` dan `npm -v`

---

## 📦 Langkah Instalasi Laravel

### Step 1: Backup Data Lama
```bash
# Buat folder backup di parent directory
cd e:\coding
mkdir Ngajar.id-backup
xcopy /E /I "Ngajar.id\*.sql" "Ngajar.id-backup\"
xcopy /E /I "Ngajar.id\uploads" "Ngajar.id-backup\uploads"
```

### Step 2: Hapus File Lama (Hati-hati!)
```bash
cd e:\coding\Ngajar.id
# Hapus semua kecuali .git dan backup
for /d %i in (*) do @if not "%i"==".git" rd /s /q "%i"
del /q *.php *.sql
```

### Step 3: Install Laravel 11
// turbo
```bash
cd e:\coding
composer create-project laravel/laravel Ngajar.id-temp
xcopy /E /I "Ngajar.id-temp\*" "Ngajar.id\"
rd /s /q Ngajar.id-temp
```

### Step 4: Install Filament 3
// turbo
```bash
cd e:\coding\Ngajar.id
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

### Step 5: Konfigurasi Supabase di .env
```bash
# Edit file .env
DB_CONNECTION=pgsql
DB_HOST=db.pnnjmyeerflqwjnwcurf.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[YOUR_SUPABASE_DB_PASSWORD]

# Supabase Storage Configuration
SUPABASE_URL=https://pnnjmyeerflqwjnwcurf.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBubmpteWVlcmZscXdqbndjdXJmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgwNjg0MjMsImV4cCI6MjA4MzY0NDQyM30.GOtH0gURGPdGzGNNlTvUbPxb5xjAuIYUWGseu_J69po
```

### Step 6: Install Supabase Storage Driver (Optional)
// turbo
```bash
composer require supabase-community/storage-php
composer require supabase-community/supabase-php
```

### Step 7: Generate App Key
// turbo
```bash
php artisan key:generate
```

### Step 8: Run Migrations
// turbo
```bash
php artisan migrate:fresh
```

### Step 9: Create Filament Admin User
// turbo
```bash
php artisan make:filament-user
```

### Step 10: Install & Build Assets
// turbo
```bash
npm install
npm run build
```

### Step 11: Run Development Server
// turbo
```bash
php artisan serve
```

---

## 🎯 Akses Aplikasi

- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **Login**: Gunakan email & password yang dibuat di Step 9

---

## ⚠️ Troubleshooting

### Error: "could not find driver"
Install ekstensi PostgreSQL untuk PHP:
1. Edit `php.ini`
2. Uncomment: `extension=pdo_pgsql` dan `extension=pgsql`
3. Restart web server

### Error: Database connection failed
Pastikan:
1. DB_PASSWORD di .env sesuai dengan Supabase database password
2. Supabase project tidak dalam mode pause
3. IP Anda tidak diblokir oleh Supabase

### Error: Node packages failed
```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

---

## 📚 Next Steps

Setelah instalasi berhasil:
1. Cek migrations sudah jalan: `php artisan migrate:status`
2. Seed data dummy: `php artisan db:seed`
3. Buka admin panel dan mulai input data
4. Test upload file ke Supabase Storage
