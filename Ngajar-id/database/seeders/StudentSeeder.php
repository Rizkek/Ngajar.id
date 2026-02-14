<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. Buat 5 Murid Baru
        // ---------------------------------------------------------------------
        $students = [
            ['name' => 'Rina Designer', 'email' => 'rina.design@gmail.com', 'bio' => 'Belajar UI/UX untuk switch career.'],
            ['name' => 'Anton Startups', 'email' => 'anton.bisnis@gmail.com', 'bio' => 'Ingin membangun bisnis F&B.'],
            ['name' => 'Siti Marketing', 'email' => 'siti.sosmed@gmail.com', 'bio' => 'Admin olshop yang mau belajar ads.'],
            ['name' => 'Budi Belajar', 'email' => 'budi.rajin@gmail.com', 'bio' => 'Lifelong learner.'],
            ['name' => 'Dewi Code', 'email' => 'dewi.frontend@gmail.com', 'bio' => 'Suka coding dan desain.'],
        ];

        $createdStudents = [];

        foreach ($students as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'), // password default
                    'role' => 'murid',
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&background=random',
                    'bio' => $data['bio'],
                    'level' => rand(1, 5),
                    'xp' => rand(100, 2000),
                    'status' => 'aktif'
                ]
            );
            $createdStudents[] = $user;
            $this->command->info("Created Student: {$user->name}");
        }

        // ---------------------------------------------------------------------
        // 2. Enroll Students (New & Existing) to Classes
        // ---------------------------------------------------------------------

        // Ambil semua murid (termasuk yang baru dibuat dan yang sudah ada sebelumnya)
        $allStudents = User::where('role', 'murid')->get();

        // Ambil semua kelas yang aktif
        $allClasses = Kelas::where('status', 'aktif')->get();

        if ($allClasses->isEmpty()) {
            $this->command->error("No classes found! Please run ContentSeeder first.");
            return;
        }

        foreach ($allStudents as $student) {
            // Randomly enroll each student to 2-5 classes
            $classesToEnroll = $allClasses->random(min(rand(2, 5), $allClasses->count()));

            foreach ($classesToEnroll as $kelas) {
                // Check enrollment first to avoid duplicates
                $exists = DB::table('kelas_peserta')
                    ->where('siswa_id', $student->user_id)
                    ->where('kelas_id', $kelas->kelas_id)
                    ->exists();

                if (!$exists) {
                    // Enroll via pivot table
                    DB::table('kelas_peserta')->insert([
                        'siswa_id' => $student->user_id,
                        'kelas_id' => $kelas->kelas_id,
                        'tanggal_daftar' => now()->subDays(rand(1, 30)), // Random daftar 1-30 hari lalu
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update total siswa di kelas
                    $kelas->increment('total_siswa');

                    $this->command->info("Enrolled {$student->name} to {$kelas->judul} ({$kelas->kategori})");
                }
            }
        }
    }
}
