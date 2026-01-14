# 🎓 Ngajar.id - Platform Pembelajaran Online

Platform pembelajaran online berbasis Laravel 11 + Filament 3 + Supabase PostgreSQL.

## 📋 Fitur Utama

### 🔐 Authentication & Authorization

- Multi-role system (Admin, Pengajar, Murid)
- Role-based access control
- Status user (Aktif/Non-aktif)

### 📚 Manajemen Pembelajaran

- **Kelas**: Pengajar dapat membuat dan mengelola kelas
- **Materi**: Upload materi berupa Video, PDF, atau Soal
- **Modul**: Sistem modul premium dan gratis
- **Peserta**: Sistem pendaftaran peserta ke kelas

### 💰 Sistem Token & Pembayaran

- Token sebagai virtual currency
- Topup token untuk akses modul premium
- Log histori penggunaan token
- Sistem donasi

### 🎨 Admin Panel (Filament 3)

- Dashboard statistik
- CRUD untuk semua entitas
- File upload ke Supabase Storage
- Filter dan search advanced
- Export data

## 🛠️ Tech Stack

- **Framework**: Laravel 11
- **Admin Panel**: Filament 3
- **Database**: Supabase PostgreSQL
- **Storage**: Supabase Storage (S3-compatible)
- **Frontend**: Blade + Livewire (via Filament)
- **PHP**: 8.2+
- **Node.js**: 18+ (untuk build assets)

---

## 🚀 Instalasi

### Prerequisites

Pastikan sudah terinstall:

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM (v18+)
- PostgreSQL client (opsional, untuk testing lokal)

### Langkah 1: Install PHP & Composer

**Opsi A: Menggunakan Laragon (Recommended untuk Windows)**

1. Download [Laragon Full](https://laragon.org/download/)
2. Install dan buka Laragon
3. Menu → PHP → Version → Pilih PHP 8.2/8.3
4. Composer sudah included

**Opsi B: Install Manual**

- Download PHP: https://windows.php.net/download/
- Download Composer: https://getcomposer.org/Composer-Setup.exe

### Langkah 2: Clone & Setup Project

```bash
cd e:\coding\Ngajar.id

# Install dependencies (setelah Laravel terinstall)
composer install

# Copy environment file
copy LARAVEL_CONFIG\.env.example .env

# Generate application key
php artisan key:generate









































# Setup permissions (jika di Linux/Mac)
# chmod -R 775 storage bootstrap/cache
```

### Langkah 3: Konfigurasi Database Supabase

Edit file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=db.pnnjmyeerflqwjnwcurf.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YOUR_SUPABASE_DB_PASSWORD

SUPABASE_URL=https://pnnjmyeerflqwjnwcurf.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBubmpteWVlcmZscXdqbndjdXJmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgwNjg0MjMsImV4cCI6MjA4MzY0NDQyM30.GOtH0gURGPdGzGNNlTvUbPxb5xjAuIYUWGseu_J69po
SUPABASE_BUCKET=ngajar-files
```

**Cara mendapatkan DB_PASSWORD:**

1. Login ke Supabase Dashboard: https://supabase.com/dashboard
2. Pilih project **pnnjmyeerflqwjnwcurf**
3. Settings → Database → Connection String
4. Copy password dari sana

### Langkah 4: Setup Supabase Storage Bucket

1. Buka Supabase Dashboard → Storage
2. Create bucket baru dengan nama: `ngajar-files`
3. Set ke **Public** agar file bisa diakses
4. Buat folder: `materi`, `modul`, `profiles`

### Langkah 5: Copy Migrations ke Laravel

Setelah Laravel terinstall, copy migrations:

```bash
# Copy migrations
xcopy /Y LARAVEL_MIGRATIONS\*.php database\migrations\

# Copy models
xcopy /Y LARAVEL_MODELS\*.php app\Models\

# Copy Filament resources (setelah Filament terinstall)
xcopy /Y LARAVEL_FILAMENT\*.php app\Filament\Resources\

# Copy services
xcopy /Y LARAVEL_SERVICES\*.php app\Services\

# Copy config
copy /Y LARAVEL_CONFIG\filesystems.php config\filesystems.php
```

### Langkah 6: Install Filament

```bash
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

### Langkah 7: Install PostgreSQL Extension untuk PHP

Edit `php.ini` dan pastikan ini enabled:

```ini
extension=pdo_pgsql
extension=pgsql
```

Restart web server jika menggunakan XAMPP/Laragon.

### Langkah 8: Run Migrations

```bash
php artisan migrate:fresh
```

### Langkah 9: Create Admin User

```bash
php artisan make:filament-user
```

Isi:

- Name: Admin
- Email: admin@ngajar.id
- Password: (bebas, minimal 8 karakter)

### Langkah 10: Install & Build Assets

```bash
npm install
npm run build
```

### Langkah 11: Run Development Server

```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

Admin panel di: **http://localhost:8000/admin**

---

## 📁 Struktur Database

### Tabel Utama

1. **users** - User dengan role (murid, pengajar, admin)
2. **kelas** - Kelas pembelajaran
3. **materi** - Materi kelas (video/pdf/soal)
4. **modul** - Modul premium/gratis
5. **kelas_peserta** - Pivot table siswa-kelas
6. **modul_user** - Pivot table user-modul yang dibeli
7. **token** - Saldo token user
8. **topup** - Transaksi topup token
9. **token_log** - Histori penggunaan token
10. **donasi** - Transaksi donasi

### Relationships

```
User → hasMany → Kelas (sebagai pengajar)
User → belongsToMany → Kelas (sebagai murid via kelas_peserta)
User → belongsToMany → Modul (via modul_user)
User → hasOne → Token
Kelas → hasMany → Materi
Kelas → belongsTo → User (pengajar)
```

---

## 🎯 Penggunaan

### Admin Panel

Login ke `/admin` dengan kredensial yang dibuat di step 9.

**Menu yang tersedia:**

- **User Management**
  - Users - Kelola semua user
- **Pembelajaran**
  - Kelas - Kelola kelas
  - Materi - Kelola materi
  - Modul - Kelola modul
- **Transaksi**
  - Topup Token - Kelola transaksi topup
  - Donasi - Kelola donasi

### Upload File ke Supabase

File yang diupload via Filament otomatis masuk ke Supabase Storage.

Cara manual upload di code:

```php
use App\Services\SupabaseStorageService;

$storageService = new SupabaseStorageService();

// Upload materi
$url = $storageService->uploadMateri($request->file('file'));

// Upload modul
$url = $storageService->uploadModul($request->file('file'));

// Upload profile
$url = $storageService->uploadProfileImage($request->file('avatar'));
```

---

## 🔧 Commands Berguna

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate
php artisan migrate:fresh  # Drop all tables & re-run
php artisan migrate:rollback  # Rollback last migration

# Create Filament user
php artisan make:filament-user

# Create Filament resource
php artisan make:filament-resource NamaModel

# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Development server
php artisan serve
php artisan serve --host=0.0.0.0 --port=8000
```

---

## ⚠️ Troubleshooting

### Error: "could not find driver"

**Solusi**: Install PostgreSQL extension

1. Edit `php.ini`
2. Uncomment: `extension=pdo_pgsql` dan `extension=pgsql`
3. Restart web server

### Error: "SQLSTATE[08006] Connection refused"

**Solusi**:

1. Cek DB_HOST, DB_PORT, DB_PASSWORD di `.env`
2. Pastikan Supabase project tidak di-pause
3. Cek koneksi internet

### Error: "Class 'Filament\Resources\Resource' not found"

**Solusi**: Install Filament

```bash
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

### File upload gagal ke Supabase

**Solusi**:

1. Cek SUPABASE_URL dan SUPABASE_KEY di `.env`
2. Pastikan bucket `ngajar-files` sudah dibuat di Supabase
3. Set bucket ke **Public**
4. Cek logs: `storage/logs/laravel.log`

### Node modules error

**Solusi**:

```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

---

## 📚 Resources

- Laravel Docs: https://laravel.com/docs/11.x
- Filament Docs: https://filamentphp.com/docs/3.x
- Supabase Docs: https://supabase.com/docs
- PostgreSQL Docs: https://www.postgresql.org/docs/

---

## 👨‍💻 Development Team

Migrasi dari PHP Native ke Laravel + Filament + Supabase

**Original**: PHP Native + MySQL
**New Stack**: Laravel 11 + Filament 3 + Supabase (PostgreSQL)

---

## 📝 License

Educational Project - Tubes Pemrograman Web

---

## 🎉 Next Steps Setelah Instalasi

1. ✅ Buat beberapa user dengan role berbeda
2. ✅ Buat kelas dan materi
3. ✅ Test upload file ke Supabase
4. ✅ Test sistem token dan topup
5. ✅ Custom dashboard widgets (opsional)
6. ✅ Buat API endpoints untuk frontend (jika perlu)
7. ✅ Deploy ke production

Happy Coding! 🚀
