# 🗄️ Setup Supabase untuk Ngajar.id

Panduan lengkap setup Supabase Database dan Storage untuk project Ngajar.id.

---

## 📍 Informasi Project Supabase Anda

**Project URL:** https://pnnjmyeerflqwjnwcurf.supabase.co
**Project Reference:** pnnjmyeerflqwjnwcurf
**Anon Key:** eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InBubmpteWVlcmZscXdqbndjdXJmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgwNjg0MjMsImV4cCI6MjA4MzY0NDQyM30.GOtH0gURGPdGzGNNlTvUbPxb5xjAuIYUWGseu_J69po

---

## 🔑 Step 1: Dapatkan Database Password

1. Login ke [Supabase Dashboard](https://supabase.com/dashboard)
2. Pilih project: **pnnjmyeerflqwjnwcurf**
3. Klik **Settings** (gear icon di sidebar)
4. Klik **Database** di sidebar kiri
5. Scroll ke section **Connection String**
6. Pilih tab **URI**
7. Klik **Reset database password** jika lupa
8. Copy password dan simpan di tempat aman

**Connection Info:**

```
Host: db.pnnjmyeerflqwjnwcurf.supabase.co
Port: 5432
Database: postgres
User: postgres
Password: [PASSWORD_ANDA]
```

Masukkan ke `.env`:

```env
DB_PASSWORD=password_yang_anda_copy
```

---

## 📦 Step 2: Setup Storage Bucket

### 2.1 Create Bucket

1. Di Supabase Dashboard
2. Klik **Storage** di sidebar
3. Klik **Create a new bucket**
4. Isi:
   - **Name:** `ngajar-files`
   - **Public bucket:** ✅ **Yes** (centang ini!)
   - **File size limit:** 50 MB (opsional)
   - **Allowed MIME types:** Leave blank (semua type)
5. Klik **Create Bucket**

### 2.2 Create Folders

Dalam bucket `ngajar-files`, buat folder:

1. **materi** - untuk file materi (video, PDF)
2. **modul** - untuk file modul (PDF)
3. **profiles** - untuk foto profil user

**Cara buat folder:**

- Klik bucket `ngajar-files`
- Klik **Upload** → **Create folder**
- Masukkan nama folder
- Klik **Create**

### 2.3 Set Bucket Policies (PENTING!)

Agar file bisa diakses public, set policies:

1. Klik bucket `ngajar-files`
2. Klik **Policies** tab
3. Klik **New Policy**

**Policy untuk Public Read:**

```sql
-- Allow public read access
CREATE POLICY "Public Access"
ON storage.objects FOR SELECT
USING ( bucket_id = 'ngajar-files' );
```

**Policy untuk Authenticated Upload:**

```sql
-- Allow authenticated users to upload
CREATE POLICY "Authenticated Upload"
ON storage.objects FOR INSERT
WITH CHECK ( bucket_id = 'ngajar-files' AND auth.role() = 'authenticated' );
```

**ATAU** gunakan UI:

- Click **New Policy** → **For full customization**
- Policy name: `Public Read Access`
- Allowed operation: `SELECT`
- Target roles: `public`
- USING expression: `bucket_id = 'ngajar-files'`
- Click **Review** → **Save policy**

---

## 🧪 Step 3: Test Connection dari Laravel

### 3.1 Test Database Connection

Buat file test `test-supabase.php` di root project:

```php
<?php
// test-supabase.php

$host = 'db.pnnjmyeerflqwjnwcurf.supabase.co';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres';
$password = 'YOUR_PASSWORD_HERE'; // Ganti dengan password Anda

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connection successful!\n";

    // Test query
    $stmt = $pdo->query('SELECT version()');
    $version = $stmt->fetchColumn();
    echo "PostgreSQL version: $version\n";

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
```

Run:

```bash
php test-supabase.php
```

### 3.2 Test Storage Upload (After Laravel Setup)

```bash
php artisan tinker
```

```php
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;

$storage = new SupabaseStorageService();

// Create test file
$file = UploadedFile::fake()->create('test.pdf', 100);

// Upload
$url = $storage->uploadFile($file, 'test');

echo $url;
// Should return: https://pnnjmyeerflqwjnwcurf.supabase.co/storage/v1/object/public/ngajar-files/test/...
```

---

## 🔐 Step 4: Security Best Practices

### 4.1 Environment Variables

**JANGAN commit `.env` ke Git!**

Pastikan `.gitignore` berisi:

```
.env
.env.backup
.env.production
```

### 4.2 API Keys

- **Anon Key** yang sudah diberikan: OK untuk frontend
- **Service Role Key**: Jangan expose ke public! Hanya untuk backend.

Dapatkan Service Role Key:

1. Settings → API
2. Copy **service_role** key
3. Simpan di `.env` (jika perlu akses admin level)

```env
SUPABASE_SERVICE_KEY=your_service_role_key_here
```

### 4.3 Row Level Security (RLS)

Untuk produksi, aktifkan RLS di tables:

```sql
-- Enable RLS on users table
ALTER TABLE users ENABLE ROW LEVEL SECURITY;

-- Policy: Users can only see their own data
CREATE POLICY "Users can view own data"
ON users FOR SELECT
USING (auth.uid() = user_id);
```

**Note:** Untuk development/testing, bisa disable RLS dulu.

---

## 📊 Step 5: Monitoring & Maintenance

### 5.1 Database Dashboard

Monitor usage:

- Dashboard → Database
- Lihat:
  - Connection pooling
  - Table sizes
  - Query performance

### 5.2 Storage Dashboard

Monitor storage:

- Dashboard → Storage
- Lihat:
  - Total storage used
  - Bandwidth used
  - File counts

### 5.3 Logs

Cek errors:

- Dashboard → Logs
- Filter by:
  - Database logs
  - Storage logs
  - API logs

---

## 🆓 Free Tier Limits (Supabase)

Pastikan tidak exceed limits:

| Resource      | Free Tier Limit    |
| ------------- | ------------------ |
| Database Size | 500 MB             |
| Storage       | 1 GB               |
| Bandwidth     | 2 GB/month         |
| File Uploads  | 50 MB max per file |

**Tips menghemat:**

- Compress images sebelum upload
- Delete unused files
- Use CDN untuk static assets (jika deploy)

---

## 🔄 Database Migration dari MySQL

Jika ingin migrate data dari MySQL lama:

### Option 1: Manual Export-Import

```bash
# Export dari MySQL (old database)
mysqldump -u root ngajar_id > ngajar_id_backup.sql

# Convert & Import ke PostgreSQL
# (perlu adjust syntax MySQL → PostgreSQL)
```

### Option 2: Gunakan Tool

- [pgLoader](https://pgloader.io/) - Auto convert MySQL to PostgreSQL
- [Supabase Studio](https://supabase.com/docs/guides/migration) - Built-in migration tool

### Option 3: Fresh Start (Recommended)

1. Run `php artisan migrate:fresh`
2. Run `php artisan db:seed`
3. Input data manually via Filament admin panel

---

## ✅ Checklist Setup Supabase

- [ ] Login ke Supabase Dashboard
- [ ] Dapatkan Database Password
- [ ] Masukkan credentials ke `.env`
- [ ] Create bucket `ngajar-files` (set PUBLIC)
- [ ] Create folders: `materi`, `modul`, `profiles`
- [ ] Set bucket policies (public read)
- [ ] Test database connection
- [ ] Run migrations (`php artisan migrate`)
- [ ] Test file upload
- [ ] Verify files accessible via public URL

---

## 🆘 Troubleshooting

### Error: "Could not connect to server"

**Solusi:**

1. Cek internet connection
2. Cek DB_PASSWORD di `.env`
3. Pastikan project tidak di-pause (free tier auto-pause jika 7 hari idle)
4. Restart project di Supabase Dashboard

### Error: "File upload failed"

**Solusi:**

1. Cek bucket `ngajar-files` sudah dibuat
2. Pastikan bucket PUBLIC
3. Cek policies sudah di-set
4. Cek SUPABASE_KEY di `.env` benar
5. Cek file size < 50MB

### Error: "Access denied"

**Solusi:**

1. Pastikan RLS policies sudah di-set dengan benar
2. Untuk development, disable RLS:
   ```sql
   ALTER TABLE nama_table DISABLE ROW LEVEL SECURITY;
   ```

### Project auto-paused

Free tier auto-pause setelah 7 hari idle.

**Cara re-activate:**

1. Dashboard → Settings → General
2. Klik **Restore project**
3. Wait 2-5 minutes

---

## 📞 Contact & Support

- Supabase Docs: https://supabase.com/docs
- Supabase Discord: https://discord.supabase.com
- GitHub Issues: https://github.com/supabase/supabase/issues

---

**Setup Date:** 2026-01-11
**Project:** Ngajar.id Laravel Migration
**Status:** ✅ Ready

Good luck! 🚀
