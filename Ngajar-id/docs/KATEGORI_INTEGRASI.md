# Integrasi Kategori Program Belajar - Dokumentasi

## Masalah yang Ditemukan

Sebelumnya, aplikasi memiliki **inkonsistensi kategori** di berbagai halaman:

1. **Halaman `/programs`** (programs.blade.php): Menggunakan kategori Sains, Sosial, Bahasa, Teknologi, Seni
2. **Form Pembuatan Kelas Pengajar**: Menggunakan kategori Programming, Design, Business, Marketing, Data Science
3. **Halaman Program Belajar Murid** (murid/kelas/index.blade.php): Menggunakan kategori Programming, Design, Business, Marketing, Data Science

Kategori tidak terintegrasi dengan baik karena menggunakan hardcoded values yang berbeda-beda.

## Solusi yang Diimplementasikan

### 1. Config File Kategori Terpusat (`config/categories.php`)

Dibuat file konfigurasi terpusat untuk mengelola semua kategori kelas:

```php
return [
    'kelas' => [
        'Programming' => 'Programming',
        'Design' => 'Design',
        'Business' => 'Business',
        'Marketing' => 'Marketing',
        'Data Science' => 'Data Science',
        'Sains' => 'Sains',
        'Sosial' => 'Sosial',
        'Bahasa' => 'Bahasa',
        'Teknologi' => 'Teknologi',
        'Seni' => 'Seni',
    ],
    'icons' => [
        'Programming' => ['icon' => 'code', 'color' => 'indigo'],
        'Design' => ['icon' => 'palette', 'color' => 'pink'],
        // ... dst
    ]
];
```

### 2. Update File-File yang Terintegrasi

#### a. `resources/views/murid/kelas/index.blade.php`

- Filter kategori di tab "Jelajah Katalog" sekarang menggunakan `config('categories.kelas')`
- Menampilkan semua 10 kategori yang tersedia

#### b. `resources/views/pengajar/kelas/create.blade.php`

- Dropdown kategori di form pembuatan kelas menggunakan `config('categories.kelas')`
- Kategori otomatis ter-update jika config file diubah

#### c. `resources/views/programs.blade.php`

- Visual kategori dengan icon dan warna sekarang menggunakan `config('categories.kelas')` dan `config('categories.icons')`
- Desain tetap mempertahankan tampilan visual yang menarik

## Keuntungan Integrasi

✅ **Konsistensi**: Semua halaman menggunakan kategori yang sama
✅ **Mudah Dikelola**: Cukup edit 1 file (`config/categories.php`) untuk update semua halaman
✅ **Scalable**: Mudah menambah kategori baru
✅ **Maintainable**: Tidak ada hardcoded values yang tersebar

## Cara Menambah Kategori Baru

1. Buka `config/categories.php`
2. Tambahkan kategori di array `kelas`:
    ```php
    'Kesehatan' => 'Kesehatan',
    ```
3. Tambahkan icon dan warna di array `icons`:
    ```php
    'Kesehatan' => ['icon' => 'health_and_safety', 'color' => 'green'],
    ```
4. Kategori otomatis muncul di semua halaman!

## Notes

- Pastikan icon menggunakan Material Symbols yang valid
- Warna menggunakan Tailwind CSS color palette (indigo, purple, pink, amber, cyan, rose, dll)
- Database sudah mendukung kolom `kategori` di tabel `kelas`
