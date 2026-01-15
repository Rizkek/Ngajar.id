# 📚 API Reference Guide - Ngajar.id

Quick reference for all API endpoints in Ngajar.id platform.

**Base URL**: `http://localhost:8000/api/v1`  
**Authentication**: Bearer Token (for protected routes)  
**Content-Type**: `application/json`

---

## 🔓 Public Endpoints

### Authentication

#### Register User

```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "murid"
}

Response 201:
{
  "success": true,
  "message": "Registrasi berhasil!",
  "user": { ... },
  "token": "1|abc123..."
}
```

#### Login

```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response 200:
{
  "success": true,
  "message": "Login berhasil!",
  "user": { ... },
  "token": "2|xyz789..."
}
```

---

### Programs (Classes)

#### List Programs

```http
GET /api/v1/programs?search=matematika&per_page=12

Response 200:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "judul": "Matematika Dasar",
      "deskripsi": "...",
      "pengajar": "Sarah Putri",
      "total_siswa": 45,
      "status": "aktif",
      "created_at": "2026-01-01"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 12,
    "total": 35
  }
}
```

#### Get Program Details

```http
GET /api/v1/programs/{id}

Response 200:
{
  "success": true,
  "data": {
    "id": 1,
    "judul": "Matematika Dasar",
    "deskripsi": "...",
    "status": "aktif",
    "pengajar": {
      "id": 5,
      "name": "Sarah Putri",
      "email": "sarah@ngajar.id"
    },
    "total_siswa": 45,
    "materi": [
      {
        "id": 1,
        "judul": "Pengenalan Aljabar",
        "tipe": "video",
        "deskripsi": "..."
      }
    ],
    "created_at": "2026-01-01 10:00:00"
  }
}
```

---

### Mentors (Teachers)

#### List Mentors

```http
GET /api/v1/mentors?search=sarah&per_page=12

Response 200:
{
  "success": true,
  "data": [
    {
      "id": 5,
      "name": "Sarah Putri",
      "email": "sarah@ngajar.id",
      "total_kelas": 3,
      "total_siswa": 120,
      "status": "aktif",
      "joined_at": "2025-12-01"
    }
  ],
  "pagination": { ... }
}
```

#### Get Mentor Profile

```http
GET /api/v1/mentors/{id}

Response 200:
{
  "success": true,
  "data": {
    "id": 5,
    "name": "Sarah Putri",
    "email": "sarah@ngajar.id",
    "status": "aktif",
    "stats": {
      "total_kelas": 3,
      "total_siswa": 120,
      "total_materi": 25,
      "total_modul": 5
    },
    "kelas": [
      {
        "id": 1,
        "judul": "Matematika Dasar",
        "total_siswa": 45,
        "total_materi": 10
      }
    ],
    "joined_at": "2025-12-01 08:00:00"
  }
}
```

---

### Donations

#### Get Donation Summary

```http
GET /api/v1/donasi?per_page=10

Response 200:
{
  "success": true,
  "data": {
    "total_donasi": 15000000,
    "riwayat_donasi": [
      {
        "nama": "Hamba Allah",
        "jumlah": 500000,
        "tanggal": "2026-01-10 10:00:00"
      }
    ]
  },
  "pagination": { ... }
}
```

#### Create Donation

```http
POST /api/v1/donasi
Content-Type: application/json

{
  "nama": "John Doe",
  "jumlah": 100000
}

Response 201:
{
  "success": true,
  "message": "Terima kasih atas donasi Anda!",
  "data": {
    "donasi_id": 123,
    "nama": "John Doe",
    "jumlah": 100000,
    "tanggal": "2026-01-15 12:00:00"
  }
}
```

---

## 🔐 Protected Endpoints

**Authorization Header Required**: `Authorization: Bearer {token}`

### User

#### Get Current User

```http
GET /api/v1/user
Authorization: Bearer 1|abc123...

Response 200:
{
  "success": true,
  "user": {
    "user_id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "murid",
    "status": "aktif",
    "token": {
      "jumlah": 500
    },
    "kelas_ikuti": [ ... ],
    "modul_dimiliki": [ ... ]
  }
}
```

#### Logout

```http
POST /api/v1/logout
Authorization: Bearer 1|abc123...

Response 200:
{
  "success": true,
  "message": "Logout berhasil."
}
```

---

### Dashboard

#### Murid Dashboard

```http
GET /api/v1/dashboard/murid
Authorization: Bearer 1|abc123...

Response 200:
{
  "success": true,
  "data": {
    "kelas_count": 3,
    "materiList": [
      {
        "judul": "Pengenalan Aljabar",
        "kelas": "Matematika Dasar",
        "tipe": "video",
        "file_url": "https://..."
      }
    ],
    "modulList": [
      {
        "modul_id": 1,
        "judul": "E-Book HTML",
        "deskripsi": "...",
        "tipe": "premium",
        "harga": 50,
        "sudah_dibeli": false
      }
    ],
    "token_balance": 500
  }
}
```

#### Pengajar Dashboard

```http
GET /api/v1/dashboard/pengajar
Authorization: Bearer 2|xyz789...

Response 200:
{
  "success": true,
  "data": {
    "stats": {
      "total_kelas": 5,
      "total_materi": 45,
      "total_siswa": 200
    },
    "kelasList": [
      {
        "kelas_id": 1,
        "judul": "Matematika Dasar",
        "status": "aktif",
        "total_siswa": 45,
        "total_materi": 10
      }
    ]
  }
}
```

---

## 🔴 Error Responses

### Validation Error (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["Email sudah terdaftar."],
        "password": ["Password minimal 8 karakter."]
    }
}
```

### Unauthorized (401)

```json
{
    "success": false,
    "message": "Email atau password salah."
}
```

### Forbidden (403)

```json
{
    "success": false,
    "message": "Akun Anda tidak aktif. Hubungi administrator."
}
```

### Not Found (404)

```json
{
    "success": false,
    "message": "Program tidak ditemukan."
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Registrasi gagal: [error details]"
}
```

---

## 💡 Usage Examples

### cURL (Windows CMD)

**Register**:

```cmd
curl -X POST http://localhost:8000/api/v1/register ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Test User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\",\"role\":\"murid\"}"
```

**Login & Save Token**:

```cmd
curl -X POST http://localhost:8000/api/v1/login ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"email\":\"test@example.com\",\"password\":\"password123\"}"
```

**Use Protected Endpoint**:

```cmd
curl http://localhost:8000/api/v1/user ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -H "Accept: application/json"
```

### JavaScript (Fetch)

```javascript
// Login
const response = await fetch("http://localhost:8000/api/v1/login", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
    body: JSON.stringify({
        email: "test@example.com",
        password: "password123",
    }),
});

const data = await response.json();
const token = data.token;

// Use protected endpoint
const userResponse = await fetch("http://localhost:8000/api/v1/user", {
    headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
    },
});
```

---

## 📋 Query Parameters

| Endpoint    | Parameter  | Type   | Description                  |
| ----------- | ---------- | ------ | ---------------------------- |
| `/programs` | `search`   | string | Search in title/description  |
| `/programs` | `per_page` | int    | Items per page (default: 12) |
| `/mentors`  | `search`   | string | Search in name/email         |
| `/mentors`  | `per_page` | int    | Items per page (default: 12) |
| `/donasi`   | `per_page` | int    | Items per page (default: 10) |

---

**Last Updated**: 2026-01-15  
**API Version**: v1  
**Support**: halo@ngajar.id
