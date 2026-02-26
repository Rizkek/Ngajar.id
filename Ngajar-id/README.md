<h1 align="center">
  <br>
  <img src="https://via.placeholder.com/150x50/4F46E5/FFFFFF?text=Ngajar.id" alt="Ngajar.id Logo" width="200">
  <br>
  Ngajar.id — Platform Belajar Digital Berbasis Relawan
  <br>
</h1>

<p align="center">
  <a href="#tentang-proyek">Tentang</a> •
  <a href="#fitur-utama">Fitur</a> •
  <a href="#arsitektur">Arsitektur</a> •
  <a href="#alur-sistem">Alur Sistem</a> •
  <a href="#instalasi">Instalasi</a> •
  <a href="#akun-demo">Akun Demo</a> •
  <a href="#struktur-proyek">Struktur Proyek</a>
</p>

---

## 📘 Tentang Proyek

**Ngajar.id** adalah platform Learning Management System (LMS) berbasis web yang dibangun di atas ekosistem **Laravel 12** dan **Tailwind CSS v4**. Platform ini menghubungkan **relawan pengajar** dengan **murid** yang ingin belajar secara digital — mirip kelas sosial online yang didukung oleh komunitas.

> **Tagline:** _"Belajar Gratis, Bermartabat, Bersama."_

Platform ini memungkinkan:

- 🧑‍🏫 **Pengajar (Relawan)** membuat dan mengelola kelas, mengupload materi, serta mengadakan sesi live class.
- 🧑‍🎓 **Murid** bergabung ke kelas, belajar materi, mengikuti learning path, dan mendapatkan sertifikat.
- 🛡️ **Admin** memantau seluruh aktivitas, mengelola user, moderasi konten, dan memantau laporan donasi.

---

## ✨ Fitur Utama

### 👤 Sistem Multi-Role

| Role         | Akses                                                                                           |
| ------------ | ----------------------------------------------------------------------------------------------- |
| **Admin**    | Dashboard statistik, manajemen user, moderasi kelas & materi, laporan donasi, pengaturan sistem |
| **Pengajar** | Buat & kelola kelas, upload materi (PDF/Video), jadwal live class, lihat statistik kelas        |
| **Murid**    | Katalog kelas, daftar kelas, belajar materi, ikuti learning path, dapatkan sertifikat           |

### 🏫 Learning Management System (LMS)

- **Kelas & Modul**: Pengajar membuat kelas dengan modul dan materi terstruktur
- **Learning Path**: Kurikulum terurut yang menggabungkan beberapa kelas dalam satu jalur belajar
- **Progress Tracking**: Sistem penanda materi selesai per pengguna (`modul_user`)
- **Live Class**: Integrasi ruang virtual (Jitsi) untuk sesi tatap muka online
- **Diskusi & Catatan**: Fitur tanya jawab dan catatan pribadi per kelas
- **Ulasan**: Murid dapat memberikan rating dan ulasan untuk kelas

### 💰 Sistem Token & Donasi

- **Token Belajar**: Mata uang internal yang dapat digunakan untuk enroll ke kelas berbayar
- **Top-Up Token**: Murid bisa membeli token melalui payment gateway **Midtrans**
- **Beasiswa**: Admin dapat memberikan status beasiswa kepada murid (bypass pembayaran)
- **Donasi Publik**: Halaman donasi umum untuk mendukung operasional platform

### 🤖 AI Chat Bantuan

- Widget chatbot berbasis AI yang dapat menjawab pertanyaan seputar platform
- Throttle 20 request/menit per IP untuk mencegah penyalahgunaan

### 🔐 Autentikasi

- Login/Register manual dengan email & password
- **OAuth Google** (Laravel Socialite)
- Reset password via email
- Session berbasis database (PostgreSQL/Supabase)

### 📊 Admin Dashboard

- Statistik real-time: total user, kelas aktif, pendapatan donasi
- Manajemen pengajar & murid (aktivasi/nonaktivasi akun)
- Moderasi & approval kelas dan materi
- Laporan donasi & revenue (ekspor CSV/PDF)
- Broadcast notifikasi ke seluruh murid
- Pengaturan platform (informasi umum, sosial media, payment, peraturan)

---

## 🏗️ Arsitektur

```
Ngajar.id (Laravel 12 Monolith)
│
├── Frontend     : Blade Templates + Tailwind CSS v4 + Alpine.js (inline)
├── Backend      : Laravel 12 (PHP 8.2+), MVC Pattern
├── Database     : PostgreSQL via Supabase (remote)
├── Storage      : Supabase Storage (file upload materi/foto profil)
├── Payment      : Midtrans (Top-up token & Donasi)
├── Auth         : Laravel Sanctum + Laravel Socialite (Google)
├── Queue        : Database Queue (notifikasi async)
├── Live Class   : Jitsi Meet (embedded iframe)
└── Build Tool   : Vite + Tailwind CSS v4
```

### Database Utama (PostgreSQL via Supabase)

| Tabel                 | Deskripsi                                    |
| --------------------- | -------------------------------------------- |
| `users`               | Data semua pengguna (admin, pengajar, murid) |
| `kelas`               | Data kelas yang dibuat pengajar              |
| `modul`               | Modul/chapter dalam setiap kelas             |
| `materi`              | Konten materi (video/PDF/teks) dalam modul   |
| `kelas_peserta`       | Relasi murid ↔ kelas (enrollment)            |
| `modul_user`          | Progress belajar murid per modul             |
| `learning_paths`      | Jalur belajar (kurikulum terurut)            |
| `learning_path_kelas` | Relasi Learning Path ↔ Kelas                 |
| `user_path_progress`  | Progress murid per learning path             |
| `token`               | Saldo token setiap murid                     |
| `token_log`           | Riwayat transaksi token                      |
| `topup`               | Riwayat top-up token via Midtrans            |
| `donasi`              | Riwayat donasi publik                        |
| `ulasans`             | Ulasan & rating kelas dari murid             |
| `sessions`            | Sesi login user (database-backed)            |
| `cache`               | Cache berbasis database                      |
| `jobs`                | Queue jobs (notifikasi async)                |

---

## 🔄 Alur Sistem

### Alur Murid (Belajar)

```
Murid Daftar/Login
    ↓
Halaman Landing (statistik, kelas populer, learning path)
    ↓
Katalog Kelas → Pilih Kelas → Enroll (Gratis / Bayar Token)
    ↓
Dashboard Murid → "Kelas Saya"
    ↓
Halaman Belajar (LMS) → Akses Modul & Materi
    ↓
Tandai Materi Selesai → Progress Bar Update
    ↓
Selesaikan Semua Materi → Download Sertifikat
```

### Alur Pengajar (Mengajar)

```
Pengajar Login (harus diaktivasi Admin terlebih dahulu)
    ↓
Dashboard Pengajar → Statistik (total murid, kelas, rating)
    ↓
Buat Kelas Baru → Tambah Modul → Upload Materi
    ↓
Jadwalkan Live Class (Jitsi Meet)
    ↓
Monitor Progress & Ulasan Murid
```

### Alur Admin (Manajemen)

```
Admin Login
    ↓
Dashboard Admin → Statistik Platform
    ↓
Kelola User: Aktivasi/nonaktivasi Pengajar & Murid
           : Atur token & beasiswa murid
    ↓
Moderasi: Review & Approval Kelas / Materi
    ↓
Learning Path: Buat kurikulum terurut dari kelas yang ada
    ↓
Donasi: Monitor & validasi donasi masuk, proses refund
    ↓
Notifikasi: Broadcast pesan ke seluruh murid / notif live class
    ↓
Laporan: Export data donasi & revenue
```

### Alur Token & Pembayaran

```
Murid ingin Top-Up Token
    ↓
Pilih jumlah token → Midtrans Payment Gateway (Snap)
    ↓
Bayar via Transfer/E-Wallet/QRIS
    ↓
Midtrans Webhook → Laravel TopupController::callback()
    ↓
Token bertambah di tabel `token` + dicatat di `token_log`
    ↓
Murid gunakan token untuk enroll kelas berbayar / learning path
```

---

## 🚀 Instalasi untuk Tim Pengembang

### Prasyarat (Requirements)

Pastikan semua tools berikut sudah terinstall di mesin Anda:

| Tools        | Versi Minimum | Link                                       |
| ------------ | ------------- | ------------------------------------------ |
| **PHP**      | 8.2+          | [php.net](https://www.php.net/downloads)   |
| **Composer** | 2.x           | [getcomposer.org](https://getcomposer.org) |
| **Node.js**  | 18+ (LTS)     | [nodejs.org](https://nodejs.org)           |
| **npm**      | 9+            | Terinstall bersama Node.js                 |
| **Git**      | Latest        | [git-scm.com](https://git-scm.com)         |

> ⚠️ **Database**: Proyek ini menggunakan **PostgreSQL via Supabase** (cloud). Anda tidak perlu install PostgreSQL lokal — cukup pastikan file `.env` sudah dikonfigurasi dengan benar. Hubungi Project Manager untuk mendapatkan kredensial database.

### Ekstensi PHP yang Diperlukan

Pastikan ekstensi PHP berikut aktif (cek dengan `php -m`):

```
pgsql          (koneksi PostgreSQL)
pdo_pgsql      (PDO driver untuk PostgreSQL)
mbstring       (manipulasi string multibyte)
openssl        (enkripsi & HTTPS)
tokenizer      (parsing PHP)
xml            (pemrosesan XML)
ctype          (validasi karakter)
json           (pemrosesan JSON)
bcmath         (operasi presisi tinggi)
fileinfo       (deteksi tipe file upload)
gd / imagick   (manipulasi gambar, opsional)
```

**Cara mengaktifkan ekstensi di Windows (XAMPP/Laragon):**
Buka `php.ini` dan hilangkan tanda `;` di depan baris ekstensi yang dibutuhkan.

---

### Langkah-Langkah Instalasi

#### 1. Clone Repository

```bash
git clone <url-repository> ngajar-id
cd ngajar-id
```

#### 2. Install Dependency PHP (Composer)

```bash
composer install
```

#### 3. Install Dependency JavaScript (NPM)

```bash
npm install
```

#### 4. Konfigurasi Environment

```bash
# Salin file .env dari contoh
cp .env.example .env

# Generate application key
php artisan key:generate
```

Kemudian edit file `.env` sesuai konfigurasi lokal Anda. Poin penting yang **wajib diisi**:

```env
# Database (hubungi PM untuk kredensial Supabase)
DB_CONNECTION=pgsql
DB_HOST=db.xxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Supabase Storage
SUPABASE_URL=https://xxxx.supabase.co
SUPABASE_KEY=your_supabase_anon_key
SUPABASE_BUCKET=ngajar-files

# Google OAuth (hubungi PM untuk client credentials)
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Midtrans Payment (gunakan Sandbox untuk dev)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
MIDTRANS_IS_PRODUCTION=false

# Mail (gunakan log untuk dev lokal)
MAIL_MAILER=log
```

#### 5. Jalankan Migrasi Database

```bash
php artisan migrate
```

#### 6. Jalankan Seeder (Data Awal)

```bash
php artisan db:seed
```

> 📌 Lihat file `SEEDER_GUIDE.md` untuk panduan lengkap mengenai data seeder.

#### 7. Jalankan Development Server

**Cara 1 — Jalankan semua sekaligus (Recommended):**

```bash
composer run dev
```

Perintah ini akan menjalankan secara bersamaan:

- `php artisan serve` → Backend Laravel di `http://localhost:8000`
- `npm run dev` → Vite asset bundler (hot reload)
- `php artisan queue:listen` → Queue worker untuk notifikasi
- `php artisan pail` → Log viewer real-time

**Cara 2 — Jalankan terpisah (di terminal berbeda):**

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite (frontend asset hot reload)
npm run dev

# Terminal 3 - Queue Worker (wajib untuk fitur notifikasi)
php artisan queue:listen --tries=1
```

Aplikasi berjalan di: **http://localhost:8000**

---

## 📦 Daftar Dependency Lengkap

### PHP / Composer Dependencies

#### Production

| Package                 | Versi | Fungsi                                |
| ----------------------- | ----- | ------------------------------------- |
| `laravel/framework`     | ^12.0 | Core framework Laravel                |
| `laravel/sanctum`       | ^4.2  | API authentication & token management |
| `laravel/socialite`     | ^5.24 | OAuth login (Google, dll.)            |
| `laravel/tinker`        | ^2.10 | REPL interaktif untuk debugging       |
| `midtrans/midtrans-php` | ^2.6  | Integrasi payment gateway Midtrans    |

#### Development

| Package                | Versi | Fungsi                                   |
| ---------------------- | ----- | ---------------------------------------- |
| `fakerphp/faker`       | ^1.23 | Generate data palsu untuk seeder/testing |
| `laravel/pail`         | ^1.2  | Log viewer real-time di terminal         |
| `laravel/pint`         | ^1.24 | PHP code style fixer (PSR-12)            |
| `laravel/sail`         | ^1.41 | Docker environment untuk Laravel         |
| `mockery/mockery`      | ^1.6  | Mocking objects untuk unit test          |
| `nunomaduro/collision` | ^8.6  | Error reporter yang lebih informatif     |
| `phpunit/phpunit`      | ^11.5 | Framework unit testing                   |

### JavaScript / NPM Dependencies

| Package                   | Versi | Fungsi                                 |
| ------------------------- | ----- | -------------------------------------- |
| `vite`                    | ^7.0  | Build tool & dev server modern         |
| `laravel-vite-plugin`     | ^2.0  | Integrasi Vite dengan Laravel          |
| `tailwindcss`             | ^4.0  | CSS framework utility-first            |
| `@tailwindcss/vite`       | ^4.0  | Plugin Tailwind CSS untuk Vite         |
| `@tailwindcss/forms`      | ^0.5  | Reset & styling form default Tailwind  |
| `@tailwindcss/typography` | ^0.5  | Styling konten prose/artikel           |
| `axios`                   | ^1.11 | HTTP client untuk request AJAX         |
| `concurrently`            | ^9.0  | Jalankan beberapa script npm bersamaan |

---

## 🔧 Perintah Artisan yang Sering Digunakan

```bash
# Bersihkan cache (wajib setelah ubah .env atau config)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Jalankan migrasi ulang + seeder (HATI-HATI: menghapus semua data!)
php artisan migrate:fresh --seed

# Buat migration baru
php artisan make:migration nama_migration

# Buat model + migration sekaligus
php artisan make:model NamaModel -m

# Buat controller baru
php artisan make:controller NamaController

# Cek daftar semua route
php artisan route:list

# Jalankan tests
composer run test
```

---

## 🗂️ Struktur Proyek

```
ngajar-id/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin*Controller.php     # Controller khusus admin
│   │   │   ├── AuthController.php       # Login, Register, Google OAuth
│   │   │   ├── BelajarController.php    # Halaman LMS belajar
│   │   │   ├── DashboardController.php  # Dashboard semua role
│   │   │   ├── DonasiController.php     # Donasi + Midtrans webhook
│   │   │   ├── LandingController.php    # Halaman beranda publik
│   │   │   ├── LearningPathController.php # Learning path murid
│   │   │   ├── LiveClassController.php  # Jitsi live class
│   │   │   └── TopupController.php      # Top-up token via Midtrans
│   │   └── Requests/                    # Form Request validation
│   ├── Models/
│   │   ├── User.php                     # Model User (multi-role)
│   │   ├── Kelas.php                    # Model Kelas
│   │   ├── Modul.php                    # Model Modul/Chapter
│   │   ├── Materi.php                   # Model Materi (PDF/Video)
│   │   ├── LearningPath.php             # Model Learning Path
│   │   ├── Token.php                    # Model Saldo Token
│   │   ├── Donasi.php                   # Model Donasi
│   │   └── ...
│   ├── Notifications/                   # Notifikasi (Database + Email)
│   └── Services/                        # Service layer (Midtrans, AI, dll.)
│
├── database/
│   ├── migrations/                      # 30 file migrasi database
│   └── seeders/                         # Data awal (user, kelas, dll.)
│
├── resources/
│   ├── views/
│   │   ├── admin/                       # View halaman admin
│   │   ├── murid/                       # View dashboard murid
│   │   ├── pengajar/                    # View dashboard pengajar
│   │   ├── auth/                        # Login, register, reset password
│   │   ├── belajar/                     # Halaman LMS belajar
│   │   └── landing/                     # Halaman publik / beranda
│   └── css/ & js/                       # Asset CSS & JS (diproses Vite)
│
├── routes/
│   ├── web.php                          # Semua route web utama
│   ├── api.php                          # Route API (Sanctum)
│   └── learning_paths_routes.php        # Route khusus learning path
│
├── docs/                                # Dokumentasi tambahan
├── .env.example                         # Template konfigurasi environment
├── composer.json                        # Dependency PHP
├── package.json                         # Dependency JavaScript
├── tailwind.config.js                   # Konfigurasi Tailwind CSS
├── vite.config.js                       # Konfigurasi Vite
└── README.md                            # File ini
```

---

## 🧪 Akun Demo (Setelah Seeder)

| Role         | Email              | Password   | Akses Utama                                 |
| ------------ | ------------------ | ---------- | ------------------------------------------- |
| **Admin**    | `admin@ngajar.id`  | `password` | Dashboard Admin, Statistik, Manajemen User  |
| **Pengajar** | `budi@ngajar.id`   | `password` | Kelola Kelas, Upload Materi, Live Class     |
| **Murid**    | `ahmad@student.id` | `password` | Katalog, Belajar, Learning Path, Sertifikat |

---

## 📄 Dokumen Tambahan

Tersedia beberapa dokumentasi tambahan di root proyek:

| File                    | Isi                                          |
| ----------------------- | -------------------------------------------- |
| `API_REFERENCE.md`      | Referensi endpoint API (Sanctum)             |
| `SEEDER_GUIDE.md`       | Panduan menjalankan dan memahami seeder      |
| `MIDTRANS_SETUP.md`     | Cara setup dan konfigurasi Midtrans Sandbox  |
| `ISSUES_ANALYSIS.md`    | Analisis masalah & bug yang pernah ditemukan |
| `JITSI_FIX.md`          | Troubleshooting integrasi Live Class Jitsi   |
| `KATEGORI_INTEGRASI.md` | Panduan integrasi fitur kategori kelas       |

---

## 👥 Tim Pengembang

| Nama                          | NIM     | Role               |
| ----------------------------- | ------- | ------------------ |
| Muhammad Abdul Azis           | 2308937 | Project Manager    |
| Muhammad Naufal Fadhlurrahman | 2310837 | Backend Developer  |
| Ihsan Abdurrahman Bi Amrillah | 2301308 | Frontend Developer |
| Syahdan Alfiansyah            | 2305929 | UI/UX Designer     |
| Pujma Rizqy Fadetra           | 2301130 | QA Engineer        |

---

## 📝 Lisensi

Proyek ini dikembangkan untuk keperluan akademik. Hak cipta © 2025 Tim Ngajar.id.
