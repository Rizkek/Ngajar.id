<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Token;
use App\Models\Donation;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Set unlimited execution time to prevent timeout
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $this->command->info('🌱 Starting database seeding...');

        // ========================================
        // 1. CREATE ADMIN
        // ========================================
        $this->command->info('👤 Creating Admin...');
        $admin = User::create([
            'name' => 'Admin Ngajar.id',
            'email' => 'admin@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // ========================================
        // 2. CREATE 5 PENGAJAR
        // ========================================
        $this->command->info('👨‍🏫 Creating 5 Pengajar...');

        $pengajar1 = User::create([
            'name' => 'Dr. Budi Santoso, M.Kom',
            'email' => 'budi@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        $pengajar2 = User::create([
            'name' => 'Siti Aminah, S.Pd., M.T',
            'email' => 'siti@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        $pengajar3 = User::create([
            'name' => 'Ir. Andi Wijaya, M.Sc',
            'email' => 'andi@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        $pengajar4 = User::create([
            'name' => 'Fitri Rahmawati, S.Kom., M.M',
            'email' => 'fitri@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        $pengajar5 = User::create([
            'name' => 'Muhammad Rizal, S.T., M.Kom',
            'email' => 'rizal@ngajar.id',
            'password' => Hash::make('password'),
            'role' => 'pengajar',
            'status' => 'aktif',
        ]);

        $pengajars = [$pengajar1, $pengajar2, $pengajar3, $pengajar4, $pengajar5];

        // ========================================
        // 3. CREATE 20 MURID (increased from 10)
        // ========================================
        $this->command->info('👨‍🎓 Creating 20 Murid...');

        $muridData = [
            ['name' => 'Ahmad Rizki Pratama', 'email' => 'ahmad@student.id'],
            ['name' => 'Dewi Lestari Putri', 'email' => 'dewi@student.id'],
            ['name' => 'Fahmi Abdullah', 'email' => 'fahmi@student.id'],
            ['name' => 'Sari Wulandari', 'email' => 'sari@student.id'],
            ['name' => 'Rudi Hermawan', 'email' => 'rudi@student.id'],
            ['name' => 'Indah Permata Sari', 'email' => 'indah@student.id'],
            ['name' => 'Teguh Prasetyo', 'email' => 'teguh@student.id'],
            ['name' => 'Lina Maryana', 'email' => 'lina@student.id'],
            ['name' => 'Yoga Aditya', 'email' => 'yoga@student.id'],
            ['name' => 'Ratna Sari Dewi', 'email' => 'ratna@student.id'],
            ['name' => 'Budi Santoso', 'email' => 'budi.s@student.id'],
            ['name' => 'Ayu Ting Ting', 'email' => 'ayu@student.id'],
            ['name' => 'Dimas Anggara', 'email' => 'dimas@student.id'],
            ['name' => 'Nina Zatulini', 'email' => 'nina@student.id'],
            ['name' => 'Reza Rahadian', 'email' => 'reza@student.id'],
            ['name' => 'Gita Savitri', 'email' => 'gita@student.id'],
            ['name' => 'Arief Muhammad', 'email' => 'arief@student.id'],
            ['name' => 'Cinta Laura', 'email' => 'cinta@student.id'],
            ['name' => 'Boy William', 'email' => 'boy@student.id'],
            ['name' => 'Chelsea Islan', 'email' => 'chelsea@student.id'],
        ];

        $murids = [];
        foreach ($muridData as $data) {
            $murid = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'murid',
                'status' => 'aktif',
            ]);
            $murids[] = $murid;

            // Create Token untuk setiap murid
            Token::create([
                'user_id' => $murid->user_id,
                'jumlah' => rand(500, 2000),
                'last_update' => now(),
            ]);
        }

        // ========================================
        // 4. CREATE KELAS (8 Kelas) - WITH KATEGORI
        // ========================================
        $this->command->info('📚 Creating Kelas...');

        $kelas1 = Course::create([
            'pengajar_id' => $pengajar1->user_id,
            'judul' => 'Pemrograman Web Dasar - HTML, CSS, JavaScript',
            'deskripsi' => 'Pelajari fundamental web development mulai dari HTML5, CSS3, hingga JavaScript ES6. Cocok untuk pemula yang ingin memulai karir sebagai web developer.',
            'kategori' => 'Programming',
            'status' => 'aktif',
        ]);

        $kelas2 = Course::create([
            'pengajar_id' => $pengajar1->user_id,
            'judul' => 'Laravel untuk Pemula - Build Modern Web Apps',
            'deskripsi' => 'Belajar framework Laravel dari nol hingga mahir. Membuat aplikasi web modern dengan MVC pattern, Eloquent ORM, dan Blade templating.',
            'kategori' => 'Programming',
            'status' => 'aktif',
        ]);

        $kelas3 = Course::create([
            'pengajar_id' => $pengajar2->user_id,
            'judul' => 'Database Management dengan MySQL & PostgreSQL',
            'deskripsi' => 'Menguasai konsep database relasional, query SQL, normalisasi, indexing, dan optimasi performa database untuk aplikasi production.',
            'kategori' => 'Data Science',
            'status' => 'aktif',
        ]);

        $kelas4 = Course::create([
            'pengajar_id' => $pengajar3->user_id,
            'judul' => 'React.js - Build Interactive User Interfaces',
            'deskripsi' => 'Menjadi React Developer profesional. Pelajari components, hooks, state management, React Router, dan best practices.',
            'kategori' => 'Programming',
            'status' => 'aktif',
        ]);

        $kelas5 = Course::create([
            'pengajar_id' => $pengajar3->user_id,
            'judul' => 'Node.js & Express - Backend Development',
            'deskripsi' => 'Bangun RESTful API dan aplikasi backend scalable menggunakan Node.js, Express.js, MongoDB, dan JWT authentication.',
            'kategori' => 'Programming',
            'status' => 'aktif',
        ]);

        $kelas6 = Course::create([
            'pengajar_id' => $pengajar4->user_id,
            'judul' => 'Mobile App Development dengan Flutter',
            'deskripsi' => 'Buat aplikasi mobile cross-platform untuk Android & iOS menggunakan Flutter dan Dart. Dari basic hingga deployment.',
            'kategori' => 'Teknologi',
            'status' => 'aktif',
        ]);

        $kelas7 = Course::create([
            'pengajar_id' => $pengajar4->user_id,
            'judul' => 'Python untuk Data Science & Machine Learning',
            'deskripsi' => 'Pelajari Python, Pandas, NumPy, Matplotlib, dan Scikit-learn untuk analisis data dan machine learning projects.',
            'kategori' => 'Data Science',
            'status' => 'aktif',
        ]);

        $kelas8 = Course::create([
            'pengajar_id' => $pengajar5->user_id,
            'judul' => 'DevOps Fundamentals - Docker, CI/CD, & Cloud',
            'deskripsi' => 'Kuasai Docker, Kubernetes, GitHub Actions, dan deployment ke cloud platforms (AWS/GCP/Azure) untuk production apps.',
            'kategori' => 'Teknologi',
            'status' => 'aktif',
        ]);

        $allKelas = [$kelas1, $kelas2, $kelas3, $kelas4, $kelas5, $kelas6, $kelas7, $kelas8];

        // ========================================
        // 5. ENROLL MURID KE KELAS
        // ========================================
        $this->command->info('📝 Enrolling Murid ke Kelas...');

        foreach ($murids as $index => $murid) {
            // Setiap murid ikut 2-4 kelas secara random
            $kelasCount = rand(2, 4);
            $selectedKelas = array_rand($allKelas, $kelasCount);

            if (!is_array($selectedKelas)) {
                $selectedKelas = [$selectedKelas];
            }

            foreach ($selectedKelas as $kelasIndex) {
                $allKelas[$kelasIndex]->peserta()->attach($murid->user_id, [
                    'tanggal_daftar' => now()->subDays(rand(1, 30))
                ]);
            }
        }

        // ========================================
        // 6. CREATE MATERI UNTUK SETIAP KELAS
        // ========================================
        $this->command->info('📖 Creating Materi...');

        // Kelas 1: Pemrograman Web Dasar
        $materiKelas1 = [
            ['judul' => 'Pengenalan HTML5 dan Semantic Elements', 'tipe' => 'video', 'deskripsi' => 'Video tutorial lengkap tentang struktur HTML5, semantic tags, dan best practices modern web development.'],
            ['judul' => 'CSS3 Fundamentals - Styling dan Layout', 'tipe' => 'pdf', 'deskripsi' => 'Materi PDF lengkap CSS3: selectors, box model, flexbox, grid, dan responsive design.'],
            ['judul' => 'JavaScript ES6+ untuk Pemula', 'tipe' => 'video', 'deskripsi' => 'Pelajari JavaScript modern: variables, functions, arrow functions, destructuring, async/await.'],
            ['judul' => 'Project: Landing Page Responsif', 'tipe' => 'pdf', 'deskripsi' => 'Panduan step-by-step membuat landing page modern yang responsive untuk semua device.'],
        ];

        foreach ($materiKelas1 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas1->kelas_id]));
        }

        // Kelas 2: Laravel
        $materiKelas2 = [
            ['judul' => 'Instalasi Laravel & Environment Setup', 'tipe' => 'video', 'deskripsi' => 'Tutorial video instalasi Laravel, Composer, dan konfigurasi development environment.'],
            ['judul' => 'MVC Pattern dan Routing di Laravel', 'tipe' => 'pdf', 'deskripsi' => 'Penjelasan konsep MVC, routing, controllers, dan request lifecycle di Laravel.'],
            ['judul' => 'Eloquent ORM dan Database Migration', 'tipe' => 'video', 'deskripsi' => 'Kuasai Eloquent ORM, relationships, query builder, dan database migrations.'],
            ['judul' => 'Blade Templating Engine', 'tipe' => 'pdf', 'deskripsi' => 'Materi lengkap Blade: layouts, components, directives, dan best practices.'],
            ['judul' => 'Authentication dengan Laravel Breeze', 'tipe' => 'video', 'deskripsi' => 'Implementasi authentication system menggunakan Laravel Breeze dan middleware.'],
        ];

        foreach ($materiKelas2 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas2->kelas_id]));
        }

        // Kelas 3: Database
        $materiKelas3 = [
            ['judul' => 'SQL Basics - SELECT, INSERT, UPDATE, DELETE', 'tipe' => 'pdf', 'deskripsi' => 'Materi dasar-dasar SQL query dan data manipulation.'],
            ['judul' => 'Database Design dan Normalisasi', 'tipe' => 'video', 'deskripsi' => 'Pelajari cara mendesain database yang efisien dengan normalization principles.'],
            ['judul' => 'Joins, Subqueries, dan Advanced SQL', 'tipe' => 'pdf', 'deskripsi' => 'Teknik SQL advanced untuk query complex data relationships.'],
            ['judul' => 'Database Indexing dan Performance Optimization', 'tipe' => 'video', 'deskripsi' => 'Optimasi performa database dengan indexing, query optimization, dan caching.'],
        ];

        foreach ($materiKelas3 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas3->kelas_id]));
        }

        // Kelas 4: React.js
        $materiKelas4 = [
            ['judul' => 'React Fundamentals - Components & JSX', 'tipe' => 'video', 'deskripsi' => 'Pengenalan React components, JSX syntax, dan component lifecycle.'],
            ['judul' => 'React Hooks - useState, useEffect, useContext', 'tipe' => 'pdf', 'deskripsi' => 'Panduan lengkap React Hooks untuk state management modern.'],
            ['judul' => 'React Router untuk SPA Navigation', 'tipe' => 'video', 'deskripsi' => 'Implementasi routing dalam Single Page Application menggunakan React Router.'],
        ];

        foreach ($materiKelas4 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas4->kelas_id]));
        }

        // Kelas 5: Node.js
        $materiKelas5 = [
            ['judul' => 'Node.js & NPM Fundamentals', 'tipe' => 'video', 'deskripsi' => 'Dasar-dasar Node.js, event loop, modules, dan package management.'],
            ['judul' => 'Building RESTful API dengan Express.js', 'tipe' => 'pdf', 'deskripsi' => 'Panduan membuat REST API dengan Express.js routing dan middleware.'],
            ['judul' => 'MongoDB & Mongoose ODM', 'tipe' => 'video', 'deskripsi' => 'Integrasi MongoDB dengan Node.js menggunakan Mongoose.'],
        ];

        foreach ($materiKelas5 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas5->kelas_id]));
        }

        // Kelas 6: Flutter
        $materiKelas6 = [
            ['judul' => 'Flutter & Dart Basics', 'tipe' => 'video', 'deskripsi' => 'Pengenalan Flutter framework dan bahasa pemrograman Dart.'],
            ['judul' => 'Flutter Widgets dan Layouts', 'tipe' => 'pdf', 'deskripsi' => 'Materi lengkap tentang Flutter widgets, layouts, dan UI components.'],
        ];

        foreach ($materiKelas6 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas6->kelas_id]));
        }

        // Kelas 7: Python Data Science
        $materiKelas7 = [
            ['judul' => 'Python Fundamentals untuk Data Science', 'tipe' => 'video', 'deskripsi' => 'Python basics, data structures, dan libraries untuk data science.'],
            ['judul' => 'Pandas & NumPy untuk Data Analysis', 'tipe' => 'pdf', 'deskripsi' => 'Mengolah dan menganalisis data menggunakan Pandas dan NumPy.'],
        ];

        foreach ($materiKelas7 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas7->kelas_id]));
        }

        // Kelas 8: DevOps
        $materiKelas8 = [
            ['judul' => 'Docker Containerization Basics', 'tipe' => 'video', 'deskripsi' => 'Pengenalan Docker, containers, images, dan Docker Compose.'],
            ['judul' => 'CI/CD dengan GitHub Actions', 'tipe' => 'pdf', 'deskripsi' => 'Implementasi continuous integration dan deployment automation.'],
        ];

        foreach ($materiKelas8 as $materi) {
            Lesson::create(array_merge($materi, ['kelas_id' => $kelas8->kelas_id]));
        }

        // ========================================
        // 7. CREATE MODUL (10 Modul)
        // ========================================
        $this->command->info('📦 Creating Modul...');

        $modulData = [
            [
                'judul' => 'E-Book: Web Development Complete Guide 2024',
                'deskripsi' => 'Panduan lengkap web development dari fundamental hingga advanced. Mencakup HTML, CSS, JavaScript, React, Node.js, dan deployment. Total 450+ halaman dengan studi kasus real-world projects.',
                'tipe' => 'premium',
                'token_harga' => 500,
                'dibuat_oleh' => $pengajar1->user_id,
            ],
            [
                'judul' => 'Cheat Sheet HTML5 & CSS3 - Quick Reference',
                'deskripsi' => 'Referensi cepat lengkap untuk HTML5 tags, attributes, dan CSS3 properties. Termasuk Flexbox, Grid, Responsive Design, dan best practices. Format PDF 50 halaman.',
                'tipe' => 'gratis',
                'token_harga' => 0,
                'dibuat_oleh' => $pengajar1->user_id,
            ],
            [
                'judul' => 'Advanced Laravel Tips & Tricks - Pro Edition',
                'deskripsi' => 'Kumpulan tips dan trik advanced untuk Laravel developers. Optimasi query, design patterns, testing, security, dan scaling aplikasi Laravel di production.',
                'tipe' => 'premium',
                'token_harga' => 300,
                'dibuat_oleh' => $pengajar2->user_id,
            ],
            [
                'judul' => 'SQL Mastery - From Basics to Advanced',
                'deskripsi' => 'Materi lengkap SQL dari dasar hingga mahir. Mencakup query optimization, complex joins, window functions, stored procedures, dan database design patterns.',
                'tipe' => 'premium',
                'token_harga' => 400,
                'dibuat_oleh' => $pengajar2->user_id,
            ],
            [
                'judul' => 'React.js Best Practices & Design Patterns',
                'deskripsi' => 'Panduan best practices React development: component architecture, state management patterns, performance optimization, testing strategies, dan production deployment.',
                'tipe' => 'premium',
                'token_harga' => 450,
                'dibuat_oleh' => $pengajar3->user_id,
            ],
            [
                'judul' => 'JavaScript ES6+ Cheat Sheet',
                'deskripsi' => 'Quick reference untuk JavaScript modern: arrow functions, destructuring, spread operators, async/await, modules, dan fitur ES2020+. Gratis untuk semua member.',
                'tipe' => 'gratis',
                'token_harga' => 0,
                'dibuat_oleh' => $pengajar3->user_id,
            ],
            [
                'judul' => 'Flutter UI Component Library - 100+ Widgets',
                'deskripsi' => 'Koleksi 100+ Flutter widget templates siap pakai. Termasuk custom buttons, cards, forms, animations, dan complete page templates dengan source code.',
                'tipe' => 'premium',
                'token_harga' => 350,
                'dibuat_oleh' => $pengajar4->user_id,
            ],
            [
                'judul' => 'Python Data Science Toolkit',
                'deskripsi' => 'E-Book komprehensif Python untuk Data Science. Pandas, NumPy, Matplotlib, Seaborn, Scikit-learn. Termasuk 50+ contoh kasus analisis data real-world.',
                'tipe' => 'premium',
                'token_harga' => 550,
                'dibuat_oleh' => $pengajar4->user_id,
            ],
            [
                'judul' => 'Git & GitHub Collaboration Guide',
                'deskripsi' => 'Panduan lengkap Git version control dan GitHub collaboration. Branch strategies, pull requests, code review, dan team workflows. Gratis!',
                'tipe' => 'gratis',
                'token_harga' => 0,
                'dibuat_oleh' => $pengajar5->user_id,
            ],
            [
                'judul' => 'DevOps Masterclass - Docker to Kubernetes',
                'deskripsi' => 'Materi komprehensif DevOps: Docker, Docker Compose, Kubernetes, CI/CD pipelines, monitoring, dan cloud deployment (AWS, GCP, Azure). 600+ halaman.',
                'tipe' => 'premium',
                'token_harga' => 600,
                'dibuat_oleh' => $pengajar5->user_id,
            ],
        ];

        $moduls = [];
        foreach ($modulData as $data) {
            $modul = Module::create($data);
            $moduls[] = $modul;
        }

        // Murid beli modul (gratis dan premium)
        $this->command->info('💳 Processing Modul purchases...');

        foreach ($murids as $murid) {
            // Setiap murid pasti download semua modul gratis
            foreach ($moduls as $modul) {
                if ($modul->tipe === 'gratis') {
                    $modul->pembeli()->attach($murid->user_id, [
                        'tanggal_beli' => now()->subDays(rand(1, 20))
                    ]);
                }
            }

            // Random beli modul premium (20% chance per modul)
            foreach ($moduls as $modul) {
                if ($modul->tipe === 'premium' && rand(1, 100) <= 20) {
                    $modul->pembeli()->attach($murid->user_id, [
                        'tanggal_beli' => now()->subDays(rand(1, 15))
                    ]);
                }
            }
        }

        // ========================================
        // 8. CREATE DONASI
        // ========================================
        $this->command->info('💰 Creating Donasi...');

        $donasiData = [
            ['nama' => 'PT. Teknologi Indonesia', 'jumlah' => 5000000, 'days_ago' => 30],
            ['nama' => 'Yayasan Pendidikan Nusantara', 'jumlah' => 2500000, 'days_ago' => 25],
            ['nama' => 'Alumni Batch 2020', 'jumlah' => 1000000, 'days_ago' => 20],
            ['nama' => 'CV. Digital Solutions', 'jumlah' => 3000000, 'days_ago' => 15],
            ['nama' => 'Bapak Hendra Wijaya', 'jumlah' => 500000, 'days_ago' => 10],
            ['nama' => 'Komunitas Developer Jakarta', 'jumlah' => 1500000, 'days_ago' => 7],
            ['nama' => 'Ibu Siti Nurhaliza', 'jumlah' => 750000, 'days_ago' => 5],
            ['nama' => 'PT. EduTech Nusantara', 'jumlah' => 10000000, 'days_ago' => 3],
            ['nama' => 'Anonymous Donor', 'jumlah' => 2000000, 'days_ago' => 1],
        ];

        foreach ($donasiData as $donasi) {
            Donation::create([
                'nama' => $donasi['nama'],
                'jumlah' => $donasi['jumlah'],
                'tanggal' => now()->subDays($donasi['days_ago']),
            ]);
        }

        // ========================================
        // 9. ADD PROGRESS DATA & COMPLETION
        // ========================================
        $this->command->info('📈 Adding Progress & Completion Data...');

        // Update murid dengan XP dan Level
        foreach ($murids as $index => $murid) {
            $xp = rand(500, 12000);
            $level = max(1, intdiv($xp, 2000) + 1);

            $murid->update([
                'xp' => $xp,
                'level' => $level,
            ]);
        }

        // Update pengajar dengan lebih banyak data
        foreach ($pengajars as $pengajar) {
            $pengajar->update([
                'xp' => rand(5000, 50000),
                'level' => rand(3, 15),
            ]);
        }

        // Add progress completion untuk kelas
        $enrollments = DB::table('kelas_peserta')->get();
        foreach ($enrollments as $enrollment) {
            $progress = rand(0, 100);
            DB::table('kelas_peserta')
                ->where('kelas_id', $enrollment->kelas_id)
                ->where('siswa_id', $enrollment->siswa_id)
                ->update([
                    'progress' => $progress,
                    'status' => $progress == 100 ? 'completed' : 'active',
                    'tanggal_daftar' => now()->subDays(rand(5, 60)),
                ]);
        }

        // ========================================
        // 9.5 CREATE ULASAN (Reviews)
        // ========================================
        $this->command->info('⭐ Creating Ulasan (Reviews)...');

        $ulasanTexts = [
            5 => [
                'Sangat luar biasa! Materi disampaikan dengan sangat jelas dan mudah dipahami. Pengajarnya sabar dan responsif. Saya bisa langsung praktek setelah belajar.',
                'Kelas terbaik yang pernah saya ikuti. Gratis tapi kualitasnya melebihi kursus berbayar manapun. Terima kasih Ngajar.id!',
                'Pengajar sangat berpengalaman dan menjelaskan dengan bahasa yang mudah dimengerti. Saya berhasil membuat project nyata setelah ikut kelas ini.',
                'Platform ini luar biasa! Saya yang awalnya tidak tahu apa-apa kini sudah bisa membuat aplikasi sendiri. Komunitas di sini juga sangat supportif.',
                'Materinya lengkap dan terstruktur dengan baik. Penjelasan langkah demi langkah sangat membantu saya yang baru mulai belajar.',
            ],
            4 => [
                'Kelas yang sangat bermanfaat. Pengajar menjelaskan konsep yang susah dengan cara yang mudah dipahami. Hanya butuh sedikit perbaikan di bagian latihan soal.',
                'Bagus sekali! Materi cukup lengkap dan pengajar sangat membantu di forum diskusi. Akan lebih sempurna kalau ada project studi kasus lebih banyak.',
                'Saya sangat puas dengan kelas ini. Kontennya up-to-date dan relevan dengan kebutuhan industri. Pengajarnya juga aktif membalas pertanyaan.',
                'Kelas yang recommended untuk pemula. Penjelasannya runtut dari dasar hingga advanced. Terima kasih sudah mau berbagi ilmu secara gratis!',
            ],
        ];

        // Ambil semua enrollment yang sudah ada
        $enrollments = DB::table('kelas_peserta')
            ->where('progress', '>=', 30) // Hanya yang sudah cukup progress
            ->inRandomOrder()
            ->limit(40)
            ->get();

        $usedCombinations = [];
        $reviewCount = 0;

        foreach ($enrollments as $enrollment) {
            $key = $enrollment->kelas_id . '_' . $enrollment->siswa_id;
            if (isset($usedCombinations[$key])) continue;
            $usedCombinations[$key] = true;

            // 70% chance untuk memberikan ulasan
            if (rand(1, 100) > 70) continue;

            $rating = rand(0, 100) <= 70 ? 5 : 4; // 70% bintang 5, 30% bintang 4
            $texts  = $ulasanTexts[$rating];

            Review::create([
                'user_id'  => $enrollment->siswa_id,
                'kelas_id' => $enrollment->kelas_id,
                'rating'   => $rating,
                'ulasan'   => $texts[array_rand($texts)],
            ]);

            $reviewCount++;
        }

        $this->command->info("   ⭐ Created {$reviewCount} ulasan");

        // ========================================
        // 10. SHOW SUMMARY
        // ========================================
        $this->command->newLine();
        $this->command->info('✅ ========================================');
        $this->command->info('✅ DATABASE SEEDED SUCCESSFULLY!');
        $this->command->info('✅ ========================================');
        $this->command->newLine();

        $this->command->info('📊 SUMMARY:');
        $this->command->info('   👤 Admin: 1');
        $this->command->info('   👨‍🏫 Pengajar: 5');
        $this->command->info('   👨‍🎓 Murid: 20');
        $this->command->info('   📚 Kelas: 8 (ALL WITH KATEGORI)');
        $this->command->info('   📖 Materi: ' . Lesson::count());
        $this->command->info('   📦 Modul: 10');
        $this->command->info('   💰 Donasi: 9');
        $this->command->newLine();

        $this->command->info('🔐 LOGIN CREDENTIALS (semua password: password):');
        $this->command->newLine();
        $this->command->info('   ADMIN:');
        $this->command->info('   📧 admin@ngajar.id');
        $this->command->newLine();
        $this->command->info('   PENGAJAR:');
        $this->command->info('   📧 budi@ngajar.id');
        $this->command->info('   📧 siti@ngajar.id');
        $this->command->info('   📧 andi@ngajar.id');
        $this->command->info('   📧 fitri@ngajar.id');
        $this->command->info('   📧 rizal@ngajar.id');
        $this->command->newLine();
        $this->command->info('   MURID:');
        $this->command->info('   📧 ahmad@student.id');
        $this->command->info('   📧 dewi@student.id');
        $this->command->info('   📧 (+ 18 murid lainnya)');
        $this->command->newLine();
    }
}


