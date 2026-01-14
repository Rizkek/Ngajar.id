# 🚀 Setup Laravel - Step by Step

## ⚠️ Error: vendor/autoload.php tidak ditemukan

Error ini terjadi karena **dependencies Laravel belum diinstall**. Berikut cara fixnya:

---

## 📋 Langkah-Langkah Setup

### ✅ **Step 1: Enable PHP Extensions** (PENTING!)

Laravel memerlukan beberapa PHP extensions. Jalankan script ini:

```bash
cd Ngajar-id
enable-php-extensions.bat
```

Script ini akan:

- ✅ Backup php.ini Anda
- ✅ Enable extension `fileinfo` (required!)
- ✅ Enable extension `pdo_pgsql` & `pgsql` (untuk PostgreSQL/Supabase)
- ✅ Enable extension lainnya (curl, gd, mbstring, openssl, zip)

**PENTING:** Setelah run script ini, **tutup terminal** dan buka terminal baru!

---

### ✅ **Step 2: Install Composer Dependencies**

```bash
cd Ngajar-id
composer install
```

Ini akan download semua Laravel dependencies ke folder `vendor/`.

**Jika masih error "ext-fileinfo":**

```bash
composer install --ignore-platform-req=ext-fileinfo
```

---

### ✅ **Step 3: Install NPM Dependencies**

```bash
npm install
```

Ini akan install Tailwind CSS dan dependencies lainnya.

---

### ✅ **Step 4: Setup Environment File**

```bash
# Copy .env.example ke .env
copy .env.example .env
```

Kemudian **EDIT file `.env`** dengan kredensial Supabase Anda:

```env
APP_NAME=Ngajar.id
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=db.pnnjmyeerflqwjnwcurf.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YOUR_SUPABASE_PASSWORD_HERE

# Supabase Storage
SUPABASE_URL=https://pnnjmyeerflqwjnwcurf.supabase.co
SUPABASE_KEY=YOUR_SUPABASE_ANON_KEY_HERE
SUPABASE_BUCKET=ngajar-files
```

---

### ✅ **Step 5: Generate Application Key**

```bash
php artisan key:generate
```

Ini akan generate `APP_KEY` di file `.env`.

---

### ✅ **Step 6: Run Migrations**

```bash
php artisan migrate
```

Ini akan create semua tables di Supabase database.

**Jika error terkait database:**

- Pastikan kredensial Supabase di `.env` sudah benar
- Pastikan extension `pdo_pgsql` sudah enabled
- Test koneksi: `php artisan db:show`

---

### ✅ **Step 7: Seed Database (Optional)**

```bash
php artisan db:seed
```

Ini akan populate database dengan data dummy untuk testing.

---

### ✅ **Step 8: Install Filament**

```bash
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

Kemudian create admin user:

```bash
php artisan make:filament-user
```

Input:

- Name: Admin
- Email: admin@ngajar.id
- Password: (your password)

---

### ✅ **Step 9: Build Frontend Assets**

```bash
# Development (watch mode)
npm run dev
```

Biarkan terminal ini tetap buka (watch mode).

**Atau untuk production build:**

```bash
npm run build
```

---

### ✅ **Step 10: Run Laravel Development Server**

Buka terminal BARU dan jalankan:

```bash
cd Ngajar-id
php artisan serve
```

Server akan jalan di: **http://localhost:8000**

---

## 🎯 Akses Aplikasi

### Frontend:

- URL: http://localhost:8000
- Landing page dengan Tailwind CSS

### Admin Panel (Filament):

- URL: http://localhost:8000/admin
- Email: admin@ngajar.id
- Password: (yang Anda set di step 8)

---

## 🔧 Troubleshooting

### ❌ Error: "ext-fileinfo missing"

**Fix:**

1. Run: `enable-php-extensions.bat`
2. Tutup dan buka terminal baru
3. Atau: `composer install --ignore-platform-req=ext-fileinfo`

### ❌ Error: "vendor/autoload.php not found"

**Fix:**

```bash
composer install
```

### ❌ Error: Database connection failed

**Fix:**

1. Cek kredensial di `.env`
2. Pastikan Supabase database sudah running
3. Test: `php artisan db:show`

### ❌ Error: "No application encryption key"

**Fix:**

```bash
php artisan key:generate
```

### ❌ CSS tidak muncul

**Fix:**

```bash
npm run dev
# Atau
npm run build
```

### ❌ Port 8000 sudah dipakai

**Fix:**

```bash
php artisan serve --port=8001
```

---

## 📝 Quick Commands Reference

```bash
# Development
php artisan serve              # Run server
npm run dev                    # Watch CSS/JS
php artisan migrate:fresh      # Reset database
php artisan db:seed            # Seed data

# Production
npm run build                  # Build assets
php artisan optimize           # Optimize app
php artisan config:cache       # Cache config

# Maintenance
php artisan cache:clear        # Clear cache
php artisan optimize:clear     # Clear all caches
php artisan queue:work         # Run queue worker
```

---

## ✅ Checklist Setup

- [ ] PHP extensions enabled
- [ ] `composer install` berhasil
- [ ] `npm install` berhasil
- [ ] `.env` file sudah di-configure
- [ ] `php artisan key:generate` sudah run
- [ ] Database migrations berhasil
- [ ] Filament installed
- [ ] Admin user created
- [ ] `npm run dev` running
- [ ] `php artisan serve` running
- [ ] Bisa akses http://localhost:8000
- [ ] Bisa akses http://localhost:8000/admin

---

## 🎉 Selesai!

Jika semua langkah sudah dilakukan, aplikasi Anda sudah siap untuk development!

**Next:**

- Mulai develop fitur
- Customize Filament admin panel
- Design frontend pages dengan Tailwind

---

**Created:** 2026-01-11  
**Updated:** 2026-01-11 23:30
