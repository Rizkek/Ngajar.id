# ✅ CHECKLIST - Pre & Post Installation

Gunakan checklist ini untuk memastikan semua langkah instalasi sudah dilakukan dengan benar.

---

## 📋 PRE-INSTALLATION CHECKLIST

### 1. Prerequisites Software

- [ ] **PHP 8.2+** installed dan bisa dijalankan via terminal

  ```bash
  php --version
  # Should show: PHP 8.2.x or higher
  ```

- [ ] **Composer** installed

  ```bash
  composer --version
  # Should show: Composer version 2.x.x
  ```

- [ ] **Node.js 18+** dan NPM installed

  ```bash
  node -v
  # Should show: v18.x.x or higher

  npm -v
  # Should show: 9.x.x or higher
  ```

- [ ] **PostgreSQL Extensions** enabled di PHP
  - Edit `php.ini`
  - Uncomment: `extension=pdo_pgsql`
  - Uncomment: `extension=pgsql`
  - Restart web server

### 2. Supabase Account Ready

- [ ] Login ke [Supabase Dashboard](https://supabase.com/dashboard)
- [ ] Project `pnnjmyeerflqwjnwcurf` accessible
- [ ] **Database Password** sudah didapat
  - Settings → Database → Connection String
  - Atau reset password jika lupa
- [ ] Copy & save password di tempat aman

### 3. Tools & Editor

- [ ] Text editor installed (VS Code recommended)
- [ ] Terminal/CMD available
- [ ] Git installed (opsional, untuk version control)
- [ ] Browser modern (Chrome/Firefox/Edge)

### 4. Documentation Read

- [ ] Sudah baca minimal 1 documentation file:
  - `INDEX.md` (navigation guide)
  - `QUICK_START.md` (quick installation)
  - `README_LARAVEL.md` (full docs)

### 5. Planning

- [ ] Sudah tentukan akan install di mana:
  - Local development saja? ✓
  - Akan deploy production? (Baca `DEPLOYMENT.md`)
- [ ] Sudah backup data lama (jika ada):
  - `ngajar_id.sql` → Backed up
  - Folder `uploads/` → Backed up
- [ ] Waktu luang tersedia: 30-60 menit

---

## 🚀 INSTALLATION CHECKLIST

### Step 1: Laravel Installation

- [ ] Create Laravel project:

  ```bash
  composer create-project laravel/laravel temp-laravel
  xcopy /E /I temp-laravel\* e:\coding\Ngajar.id\
  rd /s /q temp-laravel
  ```

- [ ] Install dependencies:
  ```bash
  cd e:\coding\Ngajar.id
  composer install
  npm install
  ```

### Step 2: Copy Files

- [ ] Copy migrations:

  ```bash
  xcopy /Y LARAVEL_MIGRATIONS\*.php database\migrations\
  ```

- [ ] Copy models:

  ```bash
  xcopy /Y LARAVEL_MODELS\*.php app\Models\
  ```

- [ ] Copy Filament resources:

  ```bash
  mkdir app\Filament\Resources
  xcopy /Y LARAVEL_FILAMENT\*.php app\Filament\Resources\
  ```

- [ ] Copy services:

  ```bash
  mkdir app\Services
  xcopy /Y LARAVEL_SERVICES\*.php app\Services\
  ```

- [ ] Copy config:

  ```bash
  copy /Y LARAVEL_CONFIG\filesystems.php config\filesystems.php
  ```

- [ ] Copy environment:

  ```bash
  copy /Y LARAVEL_CONFIG\.env.example .env
  ```

- [ ] Copy seeder:
  ```bash
  copy /Y LARAVEL_SEEDERS\DatabaseSeeder.php database\seeders\DatabaseSeeder.php
  ```

### Step 3: Environment Setup

- [ ] Generate app key:

  ```bash
  php artisan key:generate
  ```

- [ ] Edit `.env` file:
  - [ ] `APP_NAME="Ngajar.id"`
  - [ ] `APP_ENV=local`
  - [ ] `APP_DEBUG=true`
  - [ ] `DB_CONNECTION=pgsql`
  - [ ] `DB_HOST=db.pnnjmyeerflqwjnwcurf.supabase.co`
  - [ ] `DB_PORT=5432`
  - [ ] `DB_DATABASE=postgres`
  - [ ] `DB_USERNAME=postgres`
  - [ ] **`DB_PASSWORD=` (isi dengan password Supabase!)**
  - [ ] `SUPABASE_URL=https://pnnjmyeerflqwjnwcurf.supabase.co`
  - [ ] `SUPABASE_KEY=` (sudah ada di template)
  - [ ] `SUPABASE_BUCKET=ngajar-files`

### Step 4: Filament Installation

- [ ] Install Filament:

  ```bash
  composer require filament/filament:"^3.2" -W
  php artisan filament:install --panels
  ```

- [ ] Verify Filament installed:
  ```bash
  php artisan list filament
  # Should show Filament commands
  ```

### Step 5: Supabase Setup

- [ ] Create Storage Bucket:
  - Dashboard → Storage → Create Bucket
  - Name: `ngajar-files`
  - **Public:** ✅ Yes
- [ ] Create folders in bucket:

  - [ ] `materi/`
  - [ ] `modul/`
  - [ ] `profiles/`

- [ ] Set bucket policies (public read access)

### Step 6: Database Setup

- [ ] Test database connection:

  ```bash
  php artisan migrate:status
  # Should connect successfully
  ```

- [ ] Run migrations:

  ```bash
  php artisan migrate:fresh
  # Should create 10+ tables
  ```

- [ ] Verify tables created:
  - Check Supabase Dashboard → Table Editor
  - Should see: users, kelas, materi, modul, dll

### Step 7: Seed Data

- [ ] Run seeder:

  ```bash
  php artisan db:seed
  ```

- [ ] Verify data inserted:
  - Check Supabase → Table Editor → users
  - Should have admin, pengajar, murid

### Step 8: Create Admin User

- [ ] Create Filament admin:

  ```bash
  php artisan make:filament-user
  ```

  Input:

  - Name: `Admin`
  - Email: `admin@ngajar.id`
  - Password: `password` (min 8 chars)

### Step 9: Build Assets

- [ ] Build frontend assets:

  ```bash
  npm run build
  ```

- [ ] Verify build success:
  - Should create `public/build/` folder

### Step 10: Run Development Server

- [ ] Start Laravel server:

  ```bash
  php artisan serve
  ```

- [ ] Server running on:
  - [ ] http://localhost:8000 ✓

---

## 🧪 POST-INSTALLATION TESTING

### 1. Admin Panel Access

- [ ] Open browser: http://localhost:8000/admin
- [ ] Login berhasil dengan:
  - Email: `admin@ngajar.id`
  - Password: `password`
- [ ] Dashboard muncul dengan menu sidebar

### 2. Navigation Test

- [ ] Klik menu **Users**
  - [ ] Tabel users muncul
  - [ ] Ada data admin, pengajar, murid
- [ ] Klik menu **Kelas**
  - [ ] Tabel kelas muncul
  - [ ] Ada data kelas (jika seed berhasil)
- [ ] Test semua menu:
  - [ ] Materi
  - [ ] Modul
  - [ ] Topup Token
  - [ ] Donasi

### 3. CRUD Operations Test

- [ ] **Create:**
  - [ ] Klik "New User" → Form muncul
  - [ ] Isi form → Save
  - [ ] User baru muncul di list
- [ ] **Edit:**
  - [ ] Klik icon edit (pensil)
  - [ ] Ubah data → Save
  - [ ] Data terupdate
- [ ] **Delete:**
  - [ ] Klik icon delete (trash)
  - [ ] Konfirmasi → Data terhapus

### 4. File Upload Test

- [ ] Buka **Materi** → Create New
- [ ] Select kelas
- [ ] Upload file test (PDF/video)
- [ ] Save
- [ ] Verify file ada di Supabase Storage:
  - Dashboard → Storage → ngajar-files → materi
  - File muncul

### 5. Search & Filter Test

- [ ] Di tabel Users, test search:
  - [ ] Ketik nama → Filter works
- [ ] Test filter dropdown:
  - [ ] Filter by role → Works
  - [ ] Filter by status → Works

### 6. Relationship Test

- [ ] Buka detail Kelas
- [ ] Verify pengajar name muncul
- [ ] Verify jumlah peserta benar
- [ ] Verify jumlah materi benar

### 7. Token System Test (if seeded)

- [ ] Cek tabel Users
- [ ] User murid ada kolom "Saldo Token"
- [ ] Angka token benar (sesuai seed: 1000, 500, 750)

---

## 🎯 OPTIONAL: ADVANCED TESTING

### API Testing (if needed)

- [ ] Test create via Tinker:

  ```bash
  php artisan tinker

  User::create([
    'name' => 'Test User',
    'email' => 'test@test.com',
    'password' => Hash::make('password'),
    'role' => 'murid',
    'status' => 'aktif'
  ]);
  ```

### Cache Testing

- [ ] Clear all cache:
  ```bash
  php artisan optimize:clear
  ```
- [ ] Re-access admin → Should still work

### Database Backup

- [ ] Export dari Supabase:
  - Dashboard → Database → Backups
- [ ] Verify backups working

---

## 🚀 PRODUCTION CHECKLIST (Optional)

Jika akan deploy, baca `DEPLOYMENT.md` dan cek:

### Pre-Deploy

- [ ] `.env` set to production:

  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_URL=https://yourdomain.com`

- [ ] Optimize:

  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan optimize
  npm run build
  ```

- [ ] Test locally dengan production mode dulu

### Deploy

- [ ] Server setup (Forge/VPS)
- [ ] Database migrated
- [ ] Assets built
- [ ] SSL installed
- [ ] Domain pointed

### Post-Deploy

- [ ] Site accessible via domain
- [ ] Admin login works
- [ ] File upload works
- [ ] Monitor logs for 24h

---

## 📊 COMPLETION STATUS

Count your checks:

- **Pre-Installation:** \_\_\_/15 ✓
- **Installation:** \_\_\_/40 ✓
- **Testing:** \_\_\_/25 ✓
- **Production (Optional):** \_\_\_/10 ✓

**Total:** \_\_\_/90 ✓

---

## 🎉 ALL DONE?

Jika semua ✓:

**Congratulations!** 🎊

Ngajar.id sudah berhasil di-install dengan Laravel + Filament + Supabase!

**Next Steps:**

1. Explore semua fitur admin panel
2. Customize sesuai kebutuhan
3. Tambah fitur baru (jika perlu)
4. Deploy ke production (lihat `DEPLOYMENT.md`)

---

## 🆘 Stuck di Suatu Step?

**Troubleshooting:**

- Cek `QUICK_START.md` → Common Issues
- Cek `README_LARAVEL.md` → Troubleshooting
- Cek `SUPABASE_SETUP.md` → Supabase specific
- Cek Laravel logs: `storage/logs/laravel.log`

**Need Help:**

- Google the error message
- Laravel Docs: https://laravel.com/docs/11.x
- Filament Docs: https://filamentphp.com/docs/3.x

---

**Checklist Version:** 1.0.0
**Last Updated:** 2026-01-11
**For:** Ngajar.id Laravel Installation

Good luck! 🚀
