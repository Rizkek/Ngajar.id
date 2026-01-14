<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\Token;
use App\Models\Donasi;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin Ngajar.id',
            'email' => 'admin@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Create Pengajar
        $pengajar1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        $pengajar2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        // Create Murid
        $murid1 = User::create([
            'name' => 'Ahmad Rizki',
            'email' => 'ahmad@student.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'status' => 'aktif',
        ]);

        $murid2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@student.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'status' => 'aktif',
        ]);

        $murid3 = User::create([
            'name' => 'Fahmi Abdullah',
            'email' => 'fahmi@student.id',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'status' => 'aktif',
        ]);

        // Create Tokens for Murid
        Token::create(['user_id' => $murid1->user_id, 'jumlah' => 1000, 'last_update' => now()]);
        Token::create(['user_id' => $murid2->user_id, 'jumlah' => 500, 'last_update' => now()]);
        Token::create(['user_id' => $murid3->user_id, 'jumlah' => 750, 'last_update' => now()]);

        // Create Kelas
        $kelas1 = Kelas::create([
            'pengajar_id' => $pengajar1->user_id,
            'judul' => 'Pemrograman Web Dasar',
            'deskripsi' => 'Kelas untuk mempelajari dasar-dasar pemrograman web menggunakan HTML, CSS, dan JavaScript',
            'status' => 'aktif',
        ]);

        $kelas2 = Kelas::create([
            'pengajar_id' => $pengajar1->user_id,
            'judul' => 'Laravel untuk Pemula',
            'deskripsi' => 'Belajar framework Laravel dari nol hingga mahir membuat aplikasi web',
            'status' => 'aktif',
        ]);

        $kelas3 = Kelas::create([
            'pengajar_id' => $pengajar2->user_id,
            'judul' => 'Database Management dengan MySQL',
            'deskripsi' => 'Menguasai konsep database relasional dan query SQL',
            'status' => 'aktif',
        ]);

        // Enroll Murid ke Kelas
        $kelas1->peserta()->attach($murid1->user_id, ['tanggal_daftar' => now()]);
        $kelas1->peserta()->attach($murid2->user_id, ['tanggal_daftar' => now()]);
        $kelas2->peserta()->attach($murid1->user_id, ['tanggal_daftar' => now()]);
        $kelas2->peserta()->attach($murid3->user_id, ['tanggal_daftar' => now()]);
        $kelas3->peserta()->attach($murid2->user_id, ['tanggal_daftar' => now()]);
        $kelas3->peserta()->attach($murid3->user_id, ['tanggal_daftar' => now()]);

        // Create Materi
        Materi::create([
            'kelas_id' => $kelas1->kelas_id,
            'judul' => 'Pengenalan HTML',
            'tipe' => 'video',
            'deskripsi' => 'Video pengenalan struktur dasar HTML',
        ]);

        Materi::create([
            'kelas_id' => $kelas1->kelas_id,
            'judul' => 'CSS Styling',
            'tipe' => 'pdf',
            'deskripsi' => 'Materi PDF tentang CSS styling',
        ]);

        Materi::create([
            'kelas_id' => $kelas2->kelas_id,
            'judul' => 'Instalasi Laravel',
            'tipe' => 'video',
            'deskripsi' => 'Tutorial video cara install Laravel',
        ]);

        Materi::create([
            'kelas_id' => $kelas2->kelas_id,
            'judul' => 'MVC Pattern',
            'tipe' => 'pdf',
            'deskripsi' => 'Penjelasan konsep MVC dalam Laravel',
        ]);

        Materi::create([
            'kelas_id' => $kelas3->kelas_id,
            'judul' => 'SQL Basics',
            'tipe' => 'pdf',
            'deskripsi' => 'Materi dasar-dasar SQL',
        ]);

        // Create Modul
        $modul1 = Modul::create([
            'judul' => 'E-Book: Web Development Complete Guide',
            'deskripsi' => 'Panduan lengkap web development dari nol hingga mahir',
            'tipe' => 'premium',
            'token_harga' => 500,
            'dibuat_oleh' => $pengajar1->user_id,
        ]);

        $modul2 = Modul::create([
            'judul' => 'Cheat Sheet HTML & CSS',
            'deskripsi' => 'Referensi cepat untuk HTML dan CSS',
            'tipe' => 'gratis',
            'token_harga' => 0,
            'dibuat_oleh' => $pengajar1->user_id,
        ]);

        $modul3 = Modul::create([
            'judul' => 'Advanced Laravel Tips & Tricks',
            'deskripsi' => 'Tips dan trik advanced untuk Laravel developers',
            'tipe' => 'premium',
            'token_harga' => 300,
            'dibuat_oleh' => $pengajar2->user_id,
        ]);

        // Murid beli modul gratis
        $modul2->pembeli()->attach($murid1->user_id, ['tanggal_beli' => now()]);
        $modul2->pembeli()->attach($murid2->user_id, ['tanggal_beli' => now()]);

        // Create Donasi
        Donasi::create([
            'nama' => 'PT. Teknologi Indonesia',
            'jumlah' => 5000000,
            'tanggal' => now()->subDays(7),
        ]);

        Donasi::create([
            'nama' => 'Yayasan Pendidikan Nusantara',
            'jumlah' => 2500000,
            'tanggal' => now()->subDays(3),
        ]);

        Donasi::create([
            'nama' => 'Alumni Batch 2020',
            'jumlah' => 1000000,
            'tanggal' => now()->subDay(),
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📧 Admin: admin@ngajar.id | Password: password');
        $this->command->info('📧 Pengajar: budi@ngajar.id | Password: password');
        $this->command->info('📧 Murid: ahmad@student.id | Password: password');
    }
}
