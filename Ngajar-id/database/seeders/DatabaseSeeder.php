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
        // 3. CREATE 10 MURID
        // ========================================
        $this->command->info('👨‍🎓 Creating 10 Murid...');

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
        // 4. CREATE KELAS (8 Kelas)
        // ========================================
        $this->command->info('📚 Creating Kelas...');

        $kelas1 = Kelas::create([
            'pengajar_id' => $pengajar1->user_id,
            'judul' => 'Pemrograman Web Dasar - HTML, CSS, JavaScript',
            'deskripsi' => 'Pelajari fundamental web development mulai dari HTML5, CSS3, hingga JavaScript ES6. Cocok untuk pemula yang ingin memulai karir sebagai web developer.',
            'status' => 'aktif',
        ]);

        $kelas2 = Kelas::create([
            'pengajar_id' => $pengajar1->user_id,
            'judul' => 'Laravel untuk Pemula - Build Modern Web Apps',
            'deskripsi' => 'Belajar framework Laravel dari nol hingga mahir. Membuat aplikasi web modern dengan MVC pattern, Eloquent ORM, dan Blade templating.',
            'status' => 'aktif',
        ]);

        $kelas3 = Kelas::create([
            'pengajar_id' => $pengajar2->user_id,
            'judul' => 'Database Management dengan MySQL & PostgreSQL',
            'deskripsi' => 'Menguasai konsep database relasional, query SQL, normalisasi, indexing, dan optimasi performa database untuk aplikasi production.',
            'status' => 'aktif',
        ]);

        $kelas4 = Kelas::create([
            'pengajar_id' => $pengajar3->user_id,
            'judul' => 'React.js - Build Interactive User Interfaces',
            'deskripsi' => 'Menjadi React Developer profesional. Pelajari components, hooks, state management, React Router, dan best practices.',
            'status' => 'aktif',
        ]);

        $kelas5 = Kelas::create([
            'pengajar_id' => $pengajar3->user_id,
            'judul' => 'Node.js & Express - Backend Development',
            'deskripsi' => 'Bangun RESTful API dan aplikasi backend scalable menggunakan Node.js, Express.js, MongoDB, dan JWT authentication.',
            'status' => 'aktif',
        ]);

        $kelas6 = Kelas::create([
            'pengajar_id' => $pengajar4->user_id,
            'judul' => 'Mobile App Development dengan Flutter',
            'deskripsi' => 'Buat aplikasi mobile cross-platform untuk Android & iOS menggunakan Flutter dan Dart. Dari basic hingga deployment.',
            'status' => 'aktif',
        ]);

        $kelas7 = Kelas::create([
            'pengajar_id' => $pengajar4->user_id,
            'judul' => 'Python untuk Data Science & Machine Learning',
            'deskripsi' => 'Pelajari Python, Pandas, NumPy, Matplotlib, dan Scikit-learn untuk analisis data dan machine learning projects.',
            'status' => 'aktif',
        ]);

        $kelas8 = Kelas::create([
            'pengajar_id' => $pengajar5->user_id,
            'judul' => 'DevOps Fundamentals - Docker, CI/CD, & Cloud',
            'deskripsi' => 'Kuasai Docker, Kubernetes, GitHub Actions, dan deployment ke cloud platforms (AWS/GCP/Azure) untuk production apps.',
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
            Materi::create(array_merge($materi, ['kelas_id' => $kelas1->kelas_id]));
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
            Materi::create(array_merge($materi, ['kelas_id' => $kelas2->kelas_id]));
        }

        // Kelas 3: Database
        $materiKelas3 = [
            ['judul' => 'SQL Basics - SELECT, INSERT, UPDATE, DELETE', 'tipe' => 'pdf', 'deskripsi' => 'Materi dasar-dasar SQL query dan data manipulation.'],
            ['judul' => 'Database Design dan Normalisasi', 'tipe' => 'video', 'deskripsi' => 'Pelajari cara mendesain database yang efisien dengan normalization principles.'],
            ['judul' => 'Joins, Subqueries, dan Advanced SQL', 'tipe' => 'pdf', 'deskripsi' => 'Teknik SQL advanced untuk query complex data relationships.'],
            ['judul' => 'Database Indexing dan Performance Optimization', 'tipe' => 'video', 'deskripsi' => 'Optimasi performa database dengan indexing, query optimization, dan caching.'],
        ];

        foreach ($materiKelas3 as $materi) {
            Materi::create(array_merge($materi, ['kelas_id' => $kelas3->kelas_id]));
        }

        // Kelas 4: React.js
        $materiKelas4 = [
            ['judul' => 'React Fundamentals - Components & JSX', 'tipe' => 'video', 'deskripsi' => 'Pengenalan React components, JSX syntax, dan component lifecycle.'],
            ['judul' => 'React Hooks - useState, useEffect, useContext', 'tipe' => 'pdf', 'deskripsi' => 'Panduan lengkap React Hooks untuk state management modern.'],
            ['judul' => 'React Router untuk SPA Navigation', 'tipe' => 'video', 'deskripsi' => 'Implementasi routing dalam Single Page Application menggunakan React Router.'],
        ];

        foreach ($materiKelas4 as $materi) {
            Materi::create(array_merge($materi, ['kelas_id' => $kelas4->kelas_id]));
        }

        // Kelas 5: Node.js
        $materiKelas5 = [
            ['judul' => 'Node.js & NPM Fundamentals', 'tipe' => 'video', 'deskripsi' => 'Dasar-dasar Node.js, event loop, modules, dan package management.'],
            ['judul' => 'Building RESTful API dengan Express.js', 'tipe' => 'pdf', 'deskripsi' => 'Panduan membuat REST API dengan Express.js routing dan middleware.'],
            ['judul' => 'MongoDB & Mongoose ODM', 'tipe' => 'video', 'deskripsi' => 'Integrasi MongoDB dengan Node.js menggunakan Mongoose.'],
        ];

        foreach ($materiKelas5 as $materi) {
            Materi::create(array_merge($materi, ['kelas_id' => $kelas5->kelas_id]));
        }

        // Kelas 6: Flutter
        $materiKelas6 = [
            ['judul' => 'Flutter & Dart Basics', 'tipe' => 'video', 'deskripsi' => 'Pengenalan Flutter framework dan bahasa pemrograman Dart.'],
            ['judul' => 'Flutter Widgets dan Layouts', 'tipe' => 'pdf', 'deskripsi' => 'Materi lengkap tentang Flutter widgets, layouts, dan UI components.'],
        ];

        foreach ($materiKelas6 as $materi) {
            Materi::create(array_merge($materi, ['kelas_id' => $kelas6->kelas_id]));
        }

        // Kelas 7: Python Data Science
        $materiKelas7 = [
            ['judul' => 'Python Fundamentals untuk Data Science', 'tipe' => 'video', 'deskripsi' => 'Python basics, data structures, dan libraries untuk data science.'],
            ['judul' => 'Pandas & NumPy untuk Data Analysis', 'tipe' => 'pdf', 'deskripsi' => 'Mengolah dan menganalisis data menggunakan Pandas dan NumPy.'],
        ];

        foreach ($materiKelas7 as $materi) {
            Materi::create(array_merge($materi, ['kelas_id' => $kelas7->kelas_id]));
        }

        // Kelas 8: DevOps
        $materiKelas8 = [
            ['judul' => 'Docker Containerization Basics', 'tipe' => 'video', 'deskripsi' => 'Pengenalan Docker, containers, images, dan Docker Compose.'],
            ['judul' => 'CI/CD dengan GitHub Actions', 'tipe' => 'pdf', 'deskripsi' => 'Implementasi continuous integration dan deployment automation.'],
        ];

        foreach ($materiKelas8 as $materi) {
            Materi::create(array_merge($materi, ['kelas_id' => $kelas8->kelas_id]));
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
            $modul = Modul::create($data);
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
            Donasi::create([
                'nama' => $donasi['nama'],
                'jumlah' => $donasi['jumlah'],
                'tanggal' => now()->subDays($donasi['days_ago']),
            ]);
        }

        // ========================================
        // 9. SHOW SUMMARY
        // ========================================
        $this->command->newLine();
        $this->command->info('✅ ========================================');
        $this->command->info('✅ DATABASE SEEDED SUCCESSFULLY!');
        $this->command->info('✅ ========================================');
        $this->command->newLine();

        $this->command->info('📊 SUMMARY:');
        $this->command->info('   👤 Admin: 1');
        $this->command->info('   👨‍🏫 Pengajar: 5');
        $this->command->info('   👨‍🎓 Murid: 10');
        $this->command->info('   📚 Kelas: 8');
        $this->command->info('   📖 Materi: ' . Materi::count());
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
        $this->command->info('   📧 (+ 8 murid lainnya)');
        $this->command->newLine();
    }
}
