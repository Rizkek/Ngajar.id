# 🗄️ Database Schema - Ngajar.id

Visual representation dari database schema untuk project Ngajar.id

---

## 📊 Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────────┐
│                         NGAJAR.ID DATABASE                           │
│                   PostgreSQL (Supabase Hosted)                       │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│     USERS        │
├──────────────────┤
│ PK user_id       │
│    name          │◄──┐
│    email (UQ)    │   │
│    password      │   │
│    role ENUM     │   │  belongsTo (pengajar)
│    status ENUM   │   │
│    created_at    │   │
└──────────────────┘   │
        │              │
        │ hasMany      │
        │              │
        ▼              │
┌──────────────────┐   │
│     KELAS        │   │
├──────────────────┤   │
│ PK kelas_id      │   │
│ FK pengajar_id   │───┘
│    judul         │
│    deskripsi     │
│    status ENUM   │◄────────┐
│    created_at    │         │ belongsTo
└──────────────────┘         │
        │                    │
        │ hasMany            │
        │                    │
        ▼                    │
┌──────────────────┐         │
│     MATERI       │         │
├──────────────────┤         │
│ PK materi_id     │         │
│ FK kelas_id      │─────────┘
│    judul         │
│    tipe ENUM     │
│    file_url      │
│    deskripsi     │
│    created_at    │
└──────────────────┘


┌──────────────────┐         ┌──────────────────┐
│  KELAS_PESERTA   │         │   MODUL_USER     │
│  (Pivot Table)   │         │  (Pivot Table)   │
├──────────────────┤         ├──────────────────┤
│ PK id            │         │ PK id            │
│ FK siswa_id      │─┐       │ FK user_id       │─┐
│ FK kelas_id      │ │       │ FK modul_id      │ │
│    tanggal_daftar│ │       │    tanggal_beli  │ │
│    created_at    │ │       │    created_at    │ │
└──────────────────┘ │       └──────────────────┘ │
                     │                            │
    Many-to-Many     │       Many-to-Many         │
    USERS ←→ KELAS   │       USERS ←→ MODUL       │
                     │                            │
                     │                            │
                     │       ┌──────────────────┐ │
                     │       │     MODUL        │ │
                     │       ├──────────────────┤ │
                     │       │ PK modul_id      │◄┘
                     │       │    judul         │
                     │       │    deskripsi     │
                     └───────┤ FK dibuat_oleh   │
                             │    file_url      │
                             │    tipe ENUM     │
                             │    token_harga   │
                             │    created_at    │
                             └──────────────────┘


┌──────────────────┐
│      TOKEN       │
├──────────────────┤
│ PK user_id       │◄─────┐ One-to-One
│    jumlah        │      │
│    last_update   │      │
│    created_at    │      │
└──────────────────┘      │
                          │
                          │
┌──────────────────┐      │
│      TOPUP       │      │
├──────────────────┤      │
│ PK topup_id      │      │
│ FK user_id       │──────┘
│    jumlah_token  │
│    harga         │
│    tanggal       │
│    created_at    │
└──────────────────┘
        │
        │ triggers
        │ (via Model Event)
        │
        ▼
┌──────────────────┐
│    TOKEN_LOG     │
├──────────────────┤
│ PK log_id        │
│ FK user_id       │───┐
│ FK modul_id      │   │
│    jumlah        │   │
│    aksi (str)    │   │
│    tanggal       │   │
│    created_at    │   │
└──────────────────┘   │
                       │
                       └──► Tracks token changes


┌──────────────────┐
│     DONASI       │
│  (Standalone)    │
├──────────────────┤
│ PK donasi_id     │
│    nama          │
│    jumlah        │
│    tanggal       │
│    created_at    │
└──────────────────┘
```

---

## 📝 Table Details

### 👤 USERS

**Purpose:** Multi-role user management

| Column     | Type         | Constraints                    | Description       |
| ---------- | ------------ | ------------------------------ | ----------------- |
| user_id    | BIGINT       | PRIMARY KEY, AUTO_INCREMENT    | Unique user ID    |
| name       | VARCHAR(100) | NOT NULL                       | Full name         |
| email      | VARCHAR(100) | UNIQUE, NOT NULL               | Email address     |
| password   | VARCHAR(255) | NOT NULL                       | Hashed password   |
| role       | ENUM         | ('murid', 'pengajar', 'admin') | User role         |
| status     | ENUM         | ('aktif', 'nonaktif')          | User status       |
| created_at | TIMESTAMP    | -                              | Registration date |

**Relationships:**

- `hasMany` → Kelas (as pengajar)
- `belongsToMany` → Kelas (as murid via kelas_peserta)
- `belongsToMany` → Modul (via modul_user)
- `hasOne` → Token
- `hasMany` → Topup
- `hasMany` → TokenLog

---

### 📚 KELAS

**Purpose:** Learning classes created by pengajar

| Column      | Type         | Constraints                     | Description       |
| ----------- | ------------ | ------------------------------- | ----------------- |
| kelas_id    | BIGINT       | PRIMARY KEY, AUTO_INCREMENT     | Class ID          |
| pengajar_id | BIGINT       | FOREIGN KEY → users.user_id     | Teacher ID        |
| judul       | VARCHAR(150) | NOT NULL                        | Class title       |
| deskripsi   | TEXT         | -                               | Class description |
| status      | ENUM         | ('aktif', 'selesai', 'ditolak') | Class status      |
| created_at  | TIMESTAMP    | -                               | Creation date     |

**Relationships:**

- `belongsTo` → User (pengajar)
- `hasMany` → Materi
- `belongsToMany` → User (peserta via kelas_peserta)

---

### 📄 MATERI

**Purpose:** Course materials (video, PDF, quiz)

| Column     | Type         | Constraints                  | Description       |
| ---------- | ------------ | ---------------------------- | ----------------- |
| materi_id  | BIGINT       | PRIMARY KEY, AUTO_INCREMENT  | Material ID       |
| kelas_id   | BIGINT       | FOREIGN KEY → kelas.kelas_id | Class ID          |
| judul      | VARCHAR(150) | NOT NULL                     | Material title    |
| tipe       | ENUM         | ('video', 'pdf', 'soal')     | Material type     |
| file_url   | VARCHAR(255) | -                            | Supabase file URL |
| deskripsi  | TEXT         | -                            | Description       |
| created_at | TIMESTAMP    | -                            | Upload date       |

**Relationships:**

- `belongsTo` → Kelas

**File Storage:** Supabase Storage (`ngajar-files/materi/`)

---

### 📖 MODUL

**Purpose:** Premium & free modules for sale

| Column      | Type         | Constraints                 | Description       |
| ----------- | ------------ | --------------------------- | ----------------- |
| modul_id    | BIGINT       | PRIMARY KEY, AUTO_INCREMENT | Module ID         |
| judul       | VARCHAR(150) | NOT NULL                    | Module title      |
| deskripsi   | TEXT         | -                           | Description       |
| file_url    | VARCHAR(255) | -                           | Supabase file URL |
| tipe        | ENUM         | ('gratis', 'premium')       | Module type       |
| token_harga | INTEGER      | DEFAULT 0                   | Price in tokens   |
| dibuat_oleh | BIGINT       | FOREIGN KEY → users.user_id | Creator ID        |
| created_at  | TIMESTAMP    | -                           | Creation date     |

**Relationships:**

- `belongsTo` → User (pembuat)
- `belongsToMany` → User (pembeli via modul_user)
- `hasMany` → TokenLog

**File Storage:** Supabase Storage (`ngajar-files/modul/`)

---

### 👥 KELAS_PESERTA (Pivot)

**Purpose:** Student enrollment in classes (many-to-many)

| Column         | Type      | Constraints                  | Description     |
| -------------- | --------- | ---------------------------- | --------------- |
| id             | BIGINT    | PRIMARY KEY, AUTO_INCREMENT  | Record ID       |
| siswa_id       | BIGINT    | FOREIGN KEY → users.user_id  | Student ID      |
| kelas_id       | BIGINT    | FOREIGN KEY → kelas.kelas_id | Class ID        |
| tanggal_daftar | TIMESTAMP | -                            | Enrollment date |
| created_at     | TIMESTAMP | -                            | Record creation |

**Relationships:**

- Pivot between: User (as murid) ←→ Kelas

---

### 🛒 MODUL_USER (Pivot)

**Purpose:** Purchased modules (many-to-many)

| Column       | Type      | Constraints                  | Description     |
| ------------ | --------- | ---------------------------- | --------------- |
| id           | BIGINT    | PRIMARY KEY, AUTO_INCREMENT  | Record ID       |
| user_id      | BIGINT    | FOREIGN KEY → users.user_id  | Buyer ID        |
| modul_id     | BIGINT    | FOREIGN KEY → modul.modul_id | Module ID       |
| tanggal_beli | TIMESTAMP | -                            | Purchase date   |
| created_at   | TIMESTAMP | -                            | Record creation |

**Relationships:**

- Pivot between: User ←→ Modul

---

### 💰 TOKEN

**Purpose:** User token balance (virtual currency)

| Column      | Type      | Constraints                                                 | Description      |
| ----------- | --------- | ----------------------------------------------------------- | ---------------- |
| user_id     | BIGINT    | PRIMARY KEY (non-incrementing), FOREIGN KEY → users.user_id | User ID          |
| jumlah      | INTEGER   | DEFAULT 0                                                   | Token amount     |
| last_update | TIMESTAMP | -                                                           | Last update time |
| created_at  | TIMESTAMP | -                                                           | Record creation  |

**Relationships:**

- `belongsTo` → User (one-to-one)

**Note:** Auto-updated via Topup model event

---

### 💵 TOPUP

**Purpose:** Token purchase transactions

| Column       | Type      | Constraints                 | Description      |
| ------------ | --------- | --------------------------- | ---------------- |
| topup_id     | BIGINT    | PRIMARY KEY, AUTO_INCREMENT | Transaction ID   |
| user_id      | BIGINT    | FOREIGN KEY → users.user_id | Buyer ID         |
| jumlah_token | INTEGER   | DEFAULT 0                   | Tokens purchased |
| harga        | INTEGER   | DEFAULT 0                   | Price in IDR     |
| tanggal      | TIMESTAMP | -                           | Transaction date |
| created_at   | TIMESTAMP | -                           | Record creation  |

**Relationships:**

- `belongsTo` → User

**Event:** On create → Update Token table + Create TokenLog

---

### 📊 TOKEN_LOG

**Purpose:** Token usage history tracking

| Column     | Type        | Constraints                  | Description                  |
| ---------- | ----------- | ---------------------------- | ---------------------------- |
| log_id     | BIGINT      | PRIMARY KEY, AUTO_INCREMENT  | Log ID                       |
| user_id    | BIGINT      | FOREIGN KEY → users.user_id  | User ID                      |
| modul_id   | BIGINT      | FOREIGN KEY → modul.modul_id | Related module (if any)      |
| jumlah     | INTEGER     | DEFAULT 0                    | Token amount                 |
| aksi       | VARCHAR(20) | -                            | Action: 'tambah' or 'kurang' |
| tanggal    | TIMESTAMP   | -                            | Action date                  |
| created_at | TIMESTAMP   | -                            | Record creation              |

**Relationships:**

- `belongsTo` → User
- `belongsTo` → Modul (nullable)

---

### 💝 DONASI

**Purpose:** Donation transactions (standalone)

| Column     | Type         | Constraints                 | Description     |
| ---------- | ------------ | --------------------------- | --------------- |
| donasi_id  | BIGINT       | PRIMARY KEY, AUTO_INCREMENT | Donation ID     |
| nama       | VARCHAR(100) | -                           | Donor name      |
| jumlah     | INTEGER      | DEFAULT 0                   | Amount in IDR   |
| tanggal    | TIMESTAMP    | -                           | Donation date   |
| created_at | TIMESTAMP    | -                           | Record creation |

**Relationships:** None (standalone)

---

## 🔗 Relationship Summary

### One-to-Many (1:N)

```
User (pengajar) ──► 1:N ──► Kelas
Kelas ──► 1:N ──► Materi
User ──► 1:N ──► Topup
User ──► 1:N ──► TokenLog
User (creator) ──► 1:N ──► Modul
Modul ──► 1:N ──► TokenLog
```

### Many-to-Many (N:M)

```
User (murid) ◄──► N:M ◄──► Kelas (via kelas_peserta)
User ◄──► N:M ◄──► Modul (via modul_user)
```

### One-to-One (1:1)

```
User ◄──► 1:1 ◄──► Token
```

---

## 📈 Data Flow Examples

### Scenario 1: Murid Mendaftar ke Kelas

```
1. Murid login (users table)
2. Pilih kelas yang tersedia (kelas table)
3. Klik "Daftar"
4. Insert ke kelas_peserta (siswa_id, kelas_id)
5. Murid dapat akses semua materi di kelas tsb
```

### Scenario 2: Murid Beli Modul Premium

```
1. Murid cek saldo token (token table)
2. Pilih modul premium (modul table)
3. Cek harga: token_harga = 500
4. Sistem kurangi token:
   - Token: jumlah = jumlah - 500
   - Insert TokenLog: aksi='kurang', jumlah=500, modul_id=X
5. Insert ke modul_user (user_id, modul_id)
6. Murid dapat download modul
```

### Scenario 3: Murid Topup Token

```
1. Murid pilih paket topup (misal: 1000 token = Rp 50,000)
2. Insert ke topup:
   - user_id, jumlah_token=1000, harga=50000
3. Model Event triggered:
   - Update token: jumlah = jumlah + 1000
   - Insert token_log: aksi='tambah', jumlah=1000
4. Murid dapat pakai token untuk beli modul
```

---

## 🔐 Constraints & Validations

### Foreign Key Constraints

```sql
kelas.pengajar_id → users.user_id ON DELETE CASCADE
materi.kelas_id → kelas.kelas_id ON DELETE CASCADE
modul.dibuat_oleh → users.user_id ON DELETE SET NULL
kelas_peserta.siswa_id → users.user_id ON DELETE CASCADE
kelas_peserta.kelas_id → kelas.kelas_id ON DELETE CASCADE
modul_user.user_id → users.user_id ON DELETE CASCADE
modul_user.modul_id → modul.modul_id ON DELETE CASCADE
token.user_id → users.user_id ON DELETE CASCADE
topup.user_id → users.user_id ON DELETE CASCADE
token_log.user_id → users.user_id ON DELETE CASCADE
token_log.modul_id → modul.modul_id ON DELETE SET NULL
```

### Unique Constraints

```sql
users.email → UNIQUE
```

### ENUM Values

```sql
users.role → ('murid', 'pengajar', 'admin')
users.status → ('aktif', 'nonaktif')
kelas.status → ('aktif', 'selesai', 'ditolak')
materi.tipe → ('video', 'pdf', 'soal')
modul.tipe → ('gratis', 'premium')
token_log.aksi → VARCHAR but validation: ('tambah', 'kurang')
```

---

## 📦 Storage Integration

### Supabase Storage Structure

```
ngajar-files/  (bucket - PUBLIC)
├── materi/
│   ├── video/
│   │   └── [timestamp]_[hash].mp4
│   ├── pdf/
│   │   └── [timestamp]_[hash].pdf
│   └── soal/
│       └── [timestamp]_[hash].pdf
├── modul/
│   └── [timestamp]_[hash].pdf
└── profiles/
    └── [user_id]_[hash].jpg
```

### File URL Format

```
https://pnnjmyeerflqwjnwcurf.supabase.co/storage/v1/object/public/ngajar-files/materi/12345_abc.pdf
```

---

## 🎯 Indexes (Recommended)

For optimal query performance:

```sql
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_kelas_pengajar ON kelas(pengajar_id);
CREATE INDEX idx_kelas_status ON kelas(status);
CREATE INDEX idx_materi_kelas ON materi(kelas_id);
CREATE INDEX idx_modul_tipe ON modul(tipe);
CREATE INDEX idx_token_log_user ON token_log(user_id);
CREATE INDEX idx_token_log_tanggal ON token_log(tanggal);
```

Laravel migrations akan auto-create foreign key indexes.

---

**Database Type:** PostgreSQL 15+ (Supabase)
**Total Tables:** 10
**Total Relationships:** 15+
**Storage:** Supabase Storage (S3-compatible)

---

Created: 2026-01-11 | For: Ngajar.id Platform
