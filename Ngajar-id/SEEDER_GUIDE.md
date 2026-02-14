# 🎯 DATA DUMMY SEEDER - NGAJAR.ID

## 📊 **DATA YANG AKAN DIBUAT**

### **Users**

- ✅ **1 Admin** - admin@ngajar.id
- ✅ **5 Pengajar** - budi@ngajar.id, siti@ngajar.id, andi@ngajar.id, fitri@ngajar.id, rizal@ngajar.id
- ✅ **20 Murid** - ahmad@student.id, dewi@student.id, dan 18 lainnya

### **Kelas (8 Kelas dengan Kategori)**

1. ✅ **Pemrograman Web Dasar** - Kategori: Programming
2. ✅ **Laravel untuk Pemula** - Kategori: Programming
3. ✅ **Database Management** - Kategori: Data Science
4. ✅ **React.js** - Kategori: Programming
5. ✅ **Node.js & Express** - Kategori: Programming
6. ✅ **Mobile App dengan Flutter** - Kategori: Teknologi
7. ✅ **Python Data Science** - Kategori: Data Science
8. ✅ **DevOps Fundamentals** - Kategori: Teknologi

### **Materi**

- ✅ **25+ Materi** tersebar di semua kelas
- ✅ Tipe: Video & PDF
- ✅ Deskripsi lengkap untuk setiap materi

### **Modul (10 Modul)**

- ✅ **3 Modul Gratis** (HTML/CSS Cheat Sheet, JavaScript ES6, Git & GitHub)
- ✅ **7 Modul Premium** (Web Dev Guide, Laravel Tips, SQL Mastery, React Best Practices, dll)
- ✅ Harga Token: 300 - 600 token

### **Enrollment**

- ✅ Setiap murid otomatis terdaftar di 2-4 kelas secara random
- ✅ Semua murid download modul gratis
- ✅ 20% murid membeli modul premium (random)

### **Token**

- ✅ Setiap murid mendapat 500-2000 token

### **Donasi**

- ✅ 9 donasi dari berbagai sumber
- ✅ Total ~Rp 26 juta

---

## 🚀 **CARA MENJALANKAN SEEDER**

### **Step 1: Hapus Data Lama & Seed Baru**

```bash
# WARNING: Ini akan menghapus SEMUA data dan membuat data baru
php artisan migrate:fresh --seed
```

**Perintah ini akan:**

1. ✅ Drop semua tabel
2. ✅ Re-create semua tabel dari migrations
3. ✅ Run DatabaseSeeder untuk populate data dummy

### **Step 2: Verifikasi Data**

Setelah seeding selesai, cek apakah data berhasil dibuat:

```bash
# Login sebagai murid
Email: ahmad@student.id
Password: password

# Login sebagai pengajar
Email: budi@ngajar.id
Password: password

# Login sebagai admin
Email: admin@ngajar.id
Password: password
```

---

## 📝 **CREDENTIAL LENGKAP**

### **Admin (1)**

```
Email: admin@ngajar.id
Password: password
Role: admin
```

### **Pengajar (5)**

```
1. budi@ngajar.id    - password (Dr. Budi Santoso, M.Kom)
2. siti@ngajar.id    - password (Siti Aminah, S.Pd., M.T)
3. andi@ngajar.id    - password (Ir. Andi Wijaya, M.Sc)
4. fitri@ngajar.id   - password (Fitri Rahmawati, S.Kom., M.M)
5. rizal@ngajar.id   - password (Muhammad Rizal, S.T., M.Kom)
```

### **Murid (20)**

```
1.  ahmad@student.id      - password (Ahmad Rizki Pratama)
2.  dewi@student.id       - password (Dewi Lestari Putri)
3.  fahmi@student.id      - password (Fahmi Abdullah)
4.  sari@student.id       - password (Sari Wulandari)
5.  rudi@student.id       - password (Rudi Hermawan)
6.  indah@student.id      - password (Indah Permata Sari)
7.  teguh@student.id      - password (Teguh Prasetyo)
8.  lina@student.id       - password (Lina Maryana)
9.  yoga@student.id       - password (Yoga Aditya)
10. ratna@student.id      - password (Ratna Sari Dewi)
11. budi.s@student.id     - password (Budi Santoso)
12. ayu@student.id        - password (Ayu Ting Ting)
13. dimas@student.id      - password (Dimas Anggara)
14. nina@student.id       - password (Nina Zatulini)
15. reza@student.id       - password (Reza Rahadian)
16. gita@student.id       - password (Gita Savitri)
17. arief@student.id      - password (Arief Muhammad)
18. cinta@student.id      - password (Cinta Laura)
19. boy@student.id        - password (Boy William)
20. chelsea@student.id    - password (Chelsea Islan)
```

---

## ✅ **FITUR YANG TERINTEGRASI**

### **1. Kategori System** ⭐

Semua kelas sudah memiliki kategori yang terintegras dengan `config/categories.php`:

- Programming (4 kelas)
- Data Science (2 kelas)
- Teknologi (2 kelas)

### **2. Enrollment System** ⭐

- Murid otomatis enrolled ke 2-4 kelas
- Random distribution untuk simulasi real data
- Tanggal daftar: 1-30 hari yang lalu

### **3. Token Economy** ⭐

- Setiap murid punya balance 500-2000 token
- Modul premium bisa dibeli dengan token
- Modul gratis langsung accessible

### **4. Content Structure** ⭐

- Setiap kelas punya 2-5 materi
- Materi tipe: Video (tutorial) & PDF (dokumentasi)
- Deskripsi lengkap dan realistic

---

## 🔧 **TROUBLESHOOTING**

### **Error: "SQLSTATE[42P01]: Undefined table"**

**Solusi:**

```bash
# Clear cache dulu
php artisan cache:clear
php artisan config:clear

# Jalankan ulang
php artisan migrate:fresh --seed
```

### **Error: "Class 'Database\Seeders\User' not found"**

**Solusi:**

```bash
# Generate autoload
composer dump-autoload

# Jalankan ulang
php artisan migrate:fresh --seed
```

### **Seeding Terlalu Lama**

**Catatan:**

- Seeding 20 murid + 8 kelas + 25 materi + enrollment = ~30-60 detik
- Ini normal untuk data yang banyak
- Tunggu sampai selesai, jangan interrupt

---

## 📈 **EXPECTED OUTPUT**

Setelah seeding berhasil, Anda akan melihat:

```
✅ ========================================
✅ DATABASE SEEDED SUCCESSFULLY!
✅ ========================================

📊 SUMMARY:
   👤 Admin: 1
   👨‍🏫 Pengajar: 5
   👨‍🎓 Murid: 20
   📚 Kelas: 8 (ALL WITH KATEGORI)
   📖 Materi: 25+
   📦 Modul: 10
   💰 Donasi: 9

🔐 LOGIN CREDENTIALS (semua password: password):
   ADMIN:
   📧 admin@ngajar.id

   PENGAJAR:
   📧 budi@ngajar.id
   ...
```

---

## 🎯 **TESTING CHECKLIST**

Setelah seeding, test fitur-fitur ini:

### **Sebagai Murid (ahmad@student.id)**

- [ ] Lihat "Kelas Saya" → Harus ada 2-4 kelas
- [ ] Klik salah satu kelas → Lihat materi
- [ ] Coba akses materi → Harus bisa view
- [ ] Cek Token Balance → 500-2000 token
- [ ] Lihat Modul Marketplace → Ada modul gratis & premium
- [ ] Download modul gratis → Langsung bisa

### **Sebagai Pengajar (budi@ngajar.id)**

- [ ] Lihat "Kelas Saya" → Ada 2 kelas (Web Dasar & Laravel)
- [ ] Klik kelas → Lihat daftar materi
- [ ] Lihat statistik → Total siswa terdaftar
- [ ] Upload materi baru → Test CRUD

### **Sebagai Admin (admin@ngajar.id)**

- [ ] Dashboard → Statistik lengkap (users, kelas, dll)
- [ ] Lihat Laporan Donasi → 9 donasi tercatat
- [ ] Monitoring User → 26 total users
- [ ] Charts → Data visualization

---

## 📝 **CATATAN PENTING**

1. ✅ **Kategori sudah terintegrasi** - Filter di halaman Program Belajar akan berfungsi
2. ✅ **Data realistis** - Nama pengajar, judul kelas, deskripsi semuanya realistic
3. ✅ **Random distribution** - Setiap kali seed, enrollment akan berbeda (random)
4. ✅ **Token balance** - Cukup untuk testing pembelian modul premium
5. ✅ **Donasi** - Total ~Rp 26 juta untuk testing laporan

---

**Last Updated:** 10 Februari 2026, 01:50 WIB  
**Created By:** Development Team
