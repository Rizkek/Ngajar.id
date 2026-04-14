<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Donasi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EnhancedDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Enhanced Data Seeding...');

        // Get existing users
        $admin = User::where('role', 'admin')->first();
        $pengajars = User::where('role', 'pengajar')->get();
        $murids = User::where('role', 'murid')->get();
        $kelas = Kelas::all();

        // ====== STEP 1: Add Materi for each Kelas ======
        $this->command->info('📝 Adding Materi to Kelas...');
        $materiPerKelas = [
            1 => [
                'Pengenalan HTML5 - Struktur Web',
                'CSS3 Styling - Colors, Fonts, Layout',
                'JavaScript Basics - Variables & Functions',
                'DOM Manipulation & Events',
                'Responsive Design - Mobile First'
            ],
            2 => [
                'Laravel Setup & Project Structure',
                'Routing & Controllers - Request/Response',
                'Blade Templating Engine',
                'Eloquent ORM - Database Models',
                'Authentication & Authorization'
            ],
            3 => [
                'Database Design & Normalization',
                'SQL Queries - SELECT, JOIN, Aggregate',
                'Indexing & Query Optimization',
                'Transactions & Data Integrity',
                'PostgreSQL Advanced Features'
            ],
            4 => [
                'React Components & JSX',
                'Hooks - useState, useEffect, Custom Hooks',
                'State Management - Context API, Redux',
                'React Router - Navigation',
                'API Integration & Axios'
            ],
            5 => [
                'Express.js Fundamentals',
                'RESTful API Design Patterns',
                'Middleware & Authentication',
                'MongoDB Integration',
                'Error Handling & Logging'
            ],
            6 => [
                'Flutter Setup & Widgets',
                'State Management - Provider',
                'Navigation & Routing',
                'API Integration',
                'Publishing to App Stores'
            ],
            7 => [
                'Python Basics & Data Types',
                'Pandas - Data Manipulation',
                'Data Visualization - Matplotlib',
                'Machine Learning Intro',
                'Real-world ML Projects'
            ],
            8 => [
                'Docker Containers & Images',
                'Docker Compose',
                'CI/CD Pipelines - GitHub Actions',
                'Kubernetes Basics',
                'Cloud Deployment - AWS/GCP'
            ]
        ];

        foreach ($kelas as $k) {
            $materi = $materiPerKelas[$k->kelas_id] ?? [];
            foreach ($materi as $index => $title) {
                Materi::create([
                    'kelas_id' => $k->kelas_id,
                    'judul' => $title,
                    'deskripsi' => "Materi pembelajaran tentang: {$title}",
                    'tipe' => $index % 2 == 0 ? 'video' : 'artikel',
                    'status' => 'aktif',
                ]);
            }
        }

        // ====== STEP 2: Enroll Murids to Kelas ======
        $this->command->info('✅ Enrolling Murids to Kelas...');
        foreach ($murids as $murid) {
            // Setiap murid enroll ke 3-5 kelas random
            $selectedKelas = $kelas->random(rand(3, 5));
            foreach ($selectedKelas as $k) {
                DB::table('kelas_peserta')->updateOrInsert(
                    [
                        'kelas_id' => $k->kelas_id,
                        'user_id' => $murid->user_id,
                    ],
                    [
                        'tanggal_bergabung' => now()->subDays(rand(1, 60)),
                        'progress' => rand(0, 100),
                        'status' => 'aktif',
                    ]
                );
            }
        }

        // ====== STEP 3: Add Donasi Data ======
        $this->command->info('💰 Adding Donation Data...');
        $donorNames = [
            'PT Telkom Indonesia', 'PT Bank Mandiri', 'Yayasan Pendidikan Indonesia',
            'Korporasi Indonesia Maju', 'Astra International', 'Pupuk Indonesia',
            'Pertamina Foundation', 'Semen Indonesia Foundation', 'Panin Bank',
            'BRI Foundation'
        ];

        foreach ($donorNames as $index => $name) {
            $amount = rand(1000000, 50000000);
            Donasi::create([
                'nama' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@bigcorp.id',
                'jumlah' => $amount,
                'metode_pembayaran' => collect(['transfer', 'kartu_kredit', 'e-wallet'])->random(),
                'status' => collect(['pending', 'settlement', 'paid'])->random(),
                'tanggal' => now()->subDays(rand(1, 90)),
                'catatan' => "Donasi untuk pengembangan platform edukasi Ngajar.id",
            ]);
        }

        // ====== STEP 4: Add XP & Level ======
        $this->command->info('⭐ Adding XP & Levels...');
        foreach ($murids as $murid) {
            $xp = rand(100, 10000);
            $level = max(1, intdiv($xp, 1000) + 1);
            $murid->update([
                'xp' => $xp,
                'level' => $level,
            ]);
        }

        foreach ($pengajars as $pengajar) {
            $pengajar->update([
                'xp' => rand(1000, 50000),
                'level' => rand(1, 10),
            ]);
        }

        $this->command->info('✨ Enhanced data seeding completed!');
        $this->command->info("📊 Stats:");
        $this->command->info("   • Teachers: " . $pengajars->count());
        $this->command->info("   • Students: " . $murids->count());
        $this->command->info("   • Courses: " . $kelas->count());
        $this->command->info("   • Materials: " . Materi::count());
        $this->command->info("   • Donations: " . Donasi::count());
        $this->command->info("   • Total Enrolled: " . DB::table('kelas_peserta')->count());
    }
}
