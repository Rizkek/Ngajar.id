<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Kelas;
use App\Models\LearningPath;
use App\Models\Materi;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. Buat Pengajar untuk Berbagai Kategori
        // ---------------------------------------------------------------------
        $pengajars = [
            'Design' => [
                'name' => 'Sarah Desiana',
                'email' => 'sarah.design@ngajar.id',
                'bio' => 'Senior UI/UX Designer di Unicorn. Passionate tentang user-centric design.',
                'image' => 'https://ui-avatars.com/api/?name=Sarah+Desiana&background=random'
            ],
            'Business' => [
                'name' => 'Budi Business',
                'email' => 'budi.business@ngajar.id',
                'bio' => 'Founder dari 3 startup sukses. Membantu Anda membangun bisnis dari nol.',
                'image' => 'https://ui-avatars.com/api/?name=Budi+Business&background=random'
            ],
            'Marketing' => [
                'name' => 'Maya Marketer',
                'email' => 'maya.marketing@ngajar.id',
                'bio' => 'Digital Marketing Expert dengan pengalaman 10 tahun di agency global.',
                'image' => 'https://ui-avatars.com/api/?name=Maya+Marketer&background=random'
            ],
            'Soft Skills' => [
                'name' => 'Coach Andi',
                'email' => 'andi.coach@ngajar.id',
                'bio' => 'Certified Life Coach & Public Speaking Trainer. Unlock your potential.',
                'image' => 'https://ui-avatars.com/api/?name=Coach+Andi&background=random'
            ],
        ];

        $createdPengajars = [];

        foreach ($pengajars as $kategori => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'pengajar',
                    'avatar' => $data['image'],
                    'bio' => $data['bio'],
                    // Default values
                    'level' => 5,
                    'xp' => 1000,
                    'status' => 'aktif'
                ]
            );
            $createdPengajars[$kategori] = $user;
            $this->command->info("Created Pengajar: {$data['name']} ($kategori)");
        }

        // ---------------------------------------------------------------------
        // 2. Buat Kelas-Kelas Baru (Tiap Kategori minimal 3 kelas)
        // ---------------------------------------------------------------------
        $newClasses = [
            'Design' => [
                ['judul' => 'Dasar Desain Grafis', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'Intro to UI Design', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'UX Research Methods', 'level' => 'Intermediate', 'harga' => 100000],
            ],
            'Business' => [
                ['judul' => 'Mindset Entrepreneur', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'Membuat Business Model Canvas', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'Strategi Mencari Investor', 'level' => 'Advanced', 'harga' => 150000],
            ],
            'Marketing' => [
                ['judul' => 'Digital Marketing 101', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'Dasar Copywriting', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'Facebook Ads Mastery', 'level' => 'Intermediate', 'harga' => 120000],
            ],
            'Soft Skills' => [
                ['judul' => 'Public Speaking Dasar', 'level' => 'Beginner', 'harga' => 0],
                ['judul' => 'Leadership Essentials', 'level' => 'Intermediate', 'harga' => 0],
                ['judul' => 'Negotiation Skills', 'level' => 'Advanced', 'harga' => 100000],
            ],
        ];

        $kelasIds = [];

        foreach ($newClasses as $kategori => $kelasArray) {
            $pengajar = $createdPengajars[$kategori];

            foreach ($kelasArray as $kelasData) {
                $kelas = Kelas::create([
                    'judul' => $kelasData['judul'],
                    'deskripsi' => "Pelajari " . $kelasData['judul'] . " secara mendalam bersama praktisi berpengalaman.",
                    'pengajar_id' => $pengajar->user_id,
                    'kategori' => $kategori,
                    'level' => $kelasData['level'],
                    'harga' => $kelasData['harga'],
                    'rating' => rand(45, 50) / 10,
                    'total_siswa' => rand(10, 100),
                    'durasi' => rand(60, 180) . ' menit',
                    'thumbnail' => null, // Placeholder handled in blade
                    'status' => 'aktif'
                ]);

                // Simpan ID untuk Learning Path nanti
                $kelasIds[$kategori][] = $kelas->kelas_id;

                // Tambahkan dummy materi (wajib ada untuk progress)
                Materi::create([
                    'kelas_id' => $kelas->kelas_id,
                    'judul' => 'Pendahuluan: ' . $kelasData['judul'],
                    'tipe' => 'video'
                ]);

                $this->command->info("Created Kelas: {$kelasData['judul']}");
            }
        }

        // ---------------------------------------------------------------------
        // 3. Buat Learning Paths (Bundling Kelas)
        // ---------------------------------------------------------------------
        // Kita bundling kelas-kelas yang baru dibuat tadi

        $paths = [
            'Design' => [
                'judul' => 'Menjadi UI/UX Designer',
                'level' => 'Beginner',
            ],
            'Business' => [
                'judul' => 'Membangun Startup dari Ide',
                'level' => 'Beginner',
            ],
            'Marketing' => [
                'judul' => 'Digital Marketing Specialist',
                'level' => 'Beginner',
            ],
            'Soft Skills' => [
                'judul' => 'Career Acceleration Program',
                'level' => 'Intermediate',
            ]
        ];

        foreach ($paths as $kategori => $pathData) {
            if (!isset($kelasIds[$kategori]))
                continue;

            // Tentukan Pengajar (ambil creator dari kelas pertama di kategori ini)
            $kelasPertama = Kelas::find($kelasIds[$kategori][0]);
            $creatorId = $kelasPertama ? $kelasPertama->pengajar_id : 1;

            // Create Path
            $path = LearningPath::create([
                'judul' => $pathData['judul'],
                'deskripsi' => "Jalur belajar terstruktur untuk menguasai {$kategori} dari dasar hingga mahir.",
                'kategori' => $kategori,
                'level' => $pathData['level'],
                'estimated_hours' => rand(10, 50),
                'total_enrolled' => rand(5, 50),
                'is_active' => true,
                'is_free' => true, // Default free path
                'created_by' => $creatorId
            ]);

            // Attach Classes to Path
            foreach ($kelasIds[$kategori] as $index => $kelasId) {
                $path->kelas()->attach($kelasId, [
                    'urutan' => $index + 1,
                    'is_required' => true
                ]);
            }

            $this->command->info("Created Path: {$pathData['judul']} with " . count($kelasIds[$kategori]) . " classes");
        }

    }
}
