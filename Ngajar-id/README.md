<h1 align="center">
  <br>
  <img src="https://via.placeholder.com/150x50/4F46E5/FFFFFF?text=Ngajar.id" alt="Ngajar.id Logo" width="200">
  <br>
  Ngajar.id — Platform Belajar Digital Berbasis Relawan
  <br>
</h1>

## 📘 Project Brief & Features

**Ngajar.id** adalah platform Learning Management System (LMS) berbasis web yang menghubungkan **relawan pengajar** dengan **murid** yang ingin belajar secara digital — mirip kelas sosial online yang didukung oleh komunitas.

**Fitur Utama:**
- 🧑‍🏫 **Sistem Multi-Role:** Admin, Pengajar, dan Murid dengan hak akses berbeda.
- 🏫 **LMS & Learning Path:** Modul belajar terstruktur, tracking progress, live class, dan sertifikat.
- 💰 **Token & Donasi:** Sistem token belajar (top-up via Midtrans) dan donasi publik.
- 🤖 **AI Chat Bantuan:** Chatbot pintar untuk menjawab pertanyaan terkait platform.

## 💻 Tech Stack & Prerequisites

Pastikan environment Anda memenuhi prasyarat berikut:

| Teknologi | Versi |
|-----------|-------|
| PHP       | 8.2+  |
| Node.js   | 18+   |
| Composer  | 2.x   |
| Laravel   | 12.0  |
| Tailwind  | 4.0   |

*(Catatan: Proyek ini menggunakan **PostgreSQL via Supabase** cloud database).*

## 🚀 Getting Started (Instalasi Lokal)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di komputer lokal:

```bash
# 1. Clone Repository
git clone <url-repository> ngajar-id
cd ngajar-id

# 2. Install Dependencies
composer install
npm install

# 3. Setup Environment
cp .env.example .env
php artisan key:generate

# 4. Migrate & Seed Database
php artisan migrate:fresh --seed

# 5. Jalankan Development Server
composer run dev
```

Aplikasi akan berjalan di: **http://localhost:8000**

## 🔐 Environment Variables (.env)

Berikut adalah variabel penting yang dibutuhkan agar aplikasi dapat berjalan. *(Nilai asli tidak dicantumkan demi keamanan).*

```env
DB_CONNECTION=pgsql
DB_HOST=dummy_host
DB_PORT=5432
DB_DATABASE=dummy_db
DB_USERNAME=dummy_user
DB_PASSWORD=dummy_password

SUPABASE_URL=dummy_url
SUPABASE_KEY=dummy_key
SUPABASE_BUCKET=dummy_bucket

GOOGLE_CLIENT_ID=dummy_client_id
GOOGLE_CLIENT_SECRET=dummy_client_secret

MIDTRANS_SERVER_KEY=dummy_server_key
MIDTRANS_CLIENT_KEY=dummy_client_key
MIDTRANS_IS_PRODUCTION=false
```

## 🧪 Akun Demo (Setelah Seeder)

| Role         | Email              | Password   |
| ------------ | ------------------ | ---------- |
| **Admin**    | `admin@ngajar.id`  | `password` |
| **Pengajar** | `budi@ngajar.id`   | `password` |
| **Murid**    | `ahmad@student.id` | `password` |

## 📚 Panduan & Dokumentasi Lengkap

Untuk menjaga *root folder* tetap bersih, seluruh dokumentasi teknis dan panduan operasional telah dikelompokkan di dalam folder `/docs`. Silakan merujuk pada daftar dokumen berikut sesuai kebutuhan peran Anda (Developer, QA, atau PM):

### ⚙️ Panduan Teknis & Arsitektur
- 📄 **[Architecture & API Integration](docs/ARCHITECTURE_AND_API.md)** — Panduan mengenai struktur folder Laravel, standar komponen UI, dan integrasi API.
- 📄 **[API Reference](docs/API_REFERENCE.md)** — Rangkuman seluruh endpoint API utama (Auth, Sistem Rating/Review, Notifikasi) beserta standar format JSON-nya.
- 📄 **[Database Schema](docs/DATABASE_SCHEMA.md)** — Dokumentasi 10 tabel utama database beserta relasi antar entitas (User, Kelas, Materi, Transaksi).
- 📄 **[Supabase Setup](docs/SUPABASE_SETUP.md)** — Konfigurasi wajib untuk menghubungkan aplikasi dengan Cloud Database & Storage Supabase.
- 📄 **[Tailwind Setup](docs/TAILWIND_SETUP.md)** — Panduan konfigurasi *styling* dan *theming* kustom menggunakan Tailwind CSS.

### 📖 Panduan Operasional & Bisnis
- 📄 **[User Manual & Flow Guide](docs/USER_MANUAL.md)** — Buku panduan super lengkap yang menjelaskan *customer journey*, alur belajar murid (Sistem XP & Gamifikasi), alur pengajar, hingga fungsi kontrol Admin CMS.
- 📄 **[Seeder & Dummy Data Guide](docs/SEEDER_GUIDE.md)** — Panduan cara mengisi otomatis database dengan ratusan data *dummy* realistis untuk kebutuhan *testing*.

### 🚀 Maintenance & Deployment
- 📄 **[Deployment & Changelog](docs/DEPLOYMENT_AND_CHANGELOG.md)** — Instruksi teknis untuk mem-*build* dan merilis aplikasi ke server *production*, lengkap dengan catatan riwayat rilis (*changelog*).
- 📄 **[Maintenance & Audit Checklist](docs/MAINTENANCE_AUDIT.md)** — Catatan *technical debt* dan daftar *cleanup* untuk file-file usang.
- 📄 **[Error Solved (Knowledge Base)](docs/ERROR_SOLVED.md)** — Kumpulan solusi atas masalah instalasi atau *bug* rumit yang sudah berhasil diatasi tim *developer*.
