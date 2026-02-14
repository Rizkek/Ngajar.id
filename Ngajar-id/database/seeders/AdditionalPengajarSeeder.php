<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Support\Facades\Hash;

class AdditionalPengajarSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Adding 10 new Pengajar and their Classes...');

        $categories = [
            'Programming',
            'Design',
            'Business',
            'Marketing',
            'Data Science',
            'Sains',
            'Sosial',
            'Bahasa',
            'Teknologi',
            'Seni'
        ];

        // 10 Names for new teachers
        $teacherData = [
            ['name' => 'Dr. Rina Kusuma, M.Pd', 'expertise' => 'Sains'],
            ['name' => 'Prof. Hendra Gunawan, Ph.D', 'expertise' => 'Teknologi'],
            ['name' => 'Maya Indah, S.Sn., M.Ds', 'expertise' => 'Seni'],
            ['name' => 'Bambang Susilo, M.B.A', 'expertise' => 'Business'],
            ['name' => 'Citra Lestari, S.E., M.M', 'expertise' => 'Marketing'],
            ['name' => 'Dr. Eko Prasetyo, M.T', 'expertise' => 'Programming'],
            ['name' => 'Sarah Wijaya, M.A', 'expertise' => 'Bahasa'],
            ['name' => 'Dedi Kurniawan, S.Si., M.Kom', 'expertise' => 'Data Science'],
            ['name' => 'Lina Marlina, S.Hum', 'expertise' => 'Sosial'],
            ['name' => 'Rizky Pratama, M.Sn', 'expertise' => 'Design']
        ];

        $existingMurids = User::where('role', 'murid')->get();

        foreach ($teacherData as $data) {
            $name = $data['name'];
            $category = $data['expertise']; // Ensure we map specific teachers to specific categories for realism if desired, or just rotate.

            // Generate email based on name
            $emailName = strtolower(explode(' ', $name)[1] ?? 'user');
            $email = $emailName . rand(100, 999) . '@ngajar.id';

            // Create User (Idempotent)
            $pengajar = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'pengajar',
                    'status' => 'aktif',
                ]
            );

            // Check if class already exists for this pengajar
            $existingKelas = Kelas::where('pengajar_id', $pengajar->user_id)->first();

            if (!$existingKelas) {
                // Create Kelas based on category
                $judulKelas = $this->getClassTitle($category);

                $kelas = Kelas::create([
                    'pengajar_id' => $pengajar->user_id,
                    'judul' => $judulKelas,
                    'deskripsi' => "Kelas intensif $category yang dirancang oleh $name. Membahas konsep fundamental hingga implementasi praktis di dunia nyata. Cocok untuk semua tingkat kemampuan.",
                    'kategori' => $category,
                    'status' => 'aktif',
                    // Use a random image from unsplash based on category keywords could be cool but we don't have that logic easily, let's leave thumbnail null or handled by frontend view
                ]);

                // Create Materi
                $materis = [
                    ['judul' => "Pengenalan $category: Apa dan Mengapa?", 'tipe' => 'video', 'deskripsi' => "Video pembuka yang menjelaskan roadman belajar di kelas $category ini."],
                    ['judul' => "Konsep Dasar & Teori Utama", 'tipe' => 'pdf', 'deskripsi' => "Buku panduan digital yang berisi rangkuman materi teori $category."],
                    ['judul' => "Tutorial Praktis: Studi Kasus 1", 'tipe' => 'video', 'deskripsi' => "Demonstrasi penerapan ilmu $category dalam menyelesaikan masalah sederhana."],
                    ['judul' => "Quiz & Evaluasi Tahap 1", 'tipe' => 'pdf', 'deskripsi' => "Soal-soal latihan untuk menguji pemahaman materi dasar."],
                    ['judul' => "Project Akhir & Sertifikasi", 'tipe' => 'pdf', 'deskripsi' => "Panduan pengerjaan project akhir sebagai syarat kelulusan kelas ini."]
                ];

                foreach ($materis as $materi) {
                    Materi::create(array_merge($materi, ['kelas_id' => $kelas->kelas_id]));
                }

                // Enroll random students (3-8 students per class)
                if ($existingMurids->count() > 0) {
                    $numStudents = rand(3, 8);
                    $randomStudents = $existingMurids->random(min($numStudents, $existingMurids->count()));
                    foreach ($randomStudents as $student) {
                        $kelas->peserta()->attach($student->user_id, [
                            'tanggal_daftar' => now()->subDays(rand(1, 60))
                        ]);
                    }
                }

                $this->command->info("✅ Created: $name ($category) - " . $kelas->judul . " with " . count($materis) . " modules.");
            } else {
                $this->command->info("ℹ️ Skipped: $name already has a class.");
            }
        }
    }

    private function getClassTitle($category)
    {
        $titles = [
            'Programming' => 'Advanced Algorithm & Data Structures',
            'Design' => 'Visual Identity & Branding Masterclass',
            'Business' => 'Manajemen Startup & Kewirausahaan Digital',
            'Marketing' => 'SEO & Content Marketing Strategy 2026',
            'Data Science' => 'Deep Learning dengan TensorFlow',
            'Sains' => 'Astronomi Dasar: Menjelajah Semesta',
            'Sosial' => 'Psikologi Komunikasi Interpersonal',
            'Bahasa' => 'IELTS Preparation Course Intensive',
            'Teknologi' => 'Cybersecurity Awareness & Defense',
            'Seni' => 'Fotografi Komersial & Editing Lightroom'
        ];
        return $titles[$category] ?? "Kelas Profesional $category";
    }
}
