<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class TokenTransactionService
{
    /**
     * Process purchasing a premium lesson
     *
     * @param User $user The user purchasing the lesson
     * @param Lesson $materi The lesson being purchased
     * @throws \Exception
     */
    public function purchaseMaterial(User $user, Lesson $materi): void
    {
        DB::transaction(function () use ($user, $materi) {
            // Kurangi Token dari Murid
            $token = $user->token()->lockForUpdate()->first();
            if (!$token) {
                throw new \Exception('Token wallet tidak ditemukan. Silakan hubungi admin.');
            }

            $token->decrement('jumlah', $materi->harga_token);

            // Catat Log Token Murid (Pengurangan)
            \App\Models\TokenLog::create([
                'user_id' => $user->user_id,
                'jumlah' => -$materi->harga_token,
                'tipe' => 'penggunaan',
                'keterangan' => 'Membeli materi: ' . $materi->judul
            ]);

            // Transfer Token ke Pengajar (80% dari harga)
            $kelas = $materi->kelas;
            if ($kelas && $kelas->pengajar_id) {
                $pengajarShare = (int) ($materi->harga_token * 0.8); // 80% untuk pengajar
                $adminCommission = $materi->harga_token - $pengajarShare; // 20% untuk admin

                // Tambah token ke pengajar
                $pengajarToken = \App\Models\Token::firstOrCreate(
                    ['user_id' => $kelas->pengajar_id],
                    ['jumlah' => 0, 'last_update' => now()]
                );
                $pengajarToken->increment('jumlah', $pengajarShare);
                $pengajarToken->update(['last_update' => now()]);

                // Log token pengajar (Penambahan)
                \App\Models\TokenLog::create([
                    'user_id' => $kelas->pengajar_id,
                    'jumlah' => $pengajarShare,
                    'tipe' => 'pendapatan',
                    'keterangan' => 'Penjualan materi: ' . $materi->judul . ' (80%)'
                ]);

                // Log komisi admin (untuk tracking)
                \App\Models\TokenLog::create([
                    'user_id' => 1, // Assuming admin user_id is 1, adjust if needed
                    'jumlah' => $adminCommission,
                    'tipe' => 'komisi',
                    'keterangan' => 'Komisi penjualan materi: ' . $materi->judul . ' (20%)'
                ]);
            }

            // Insert Akses Materi
            DB::table('materi_akses')->insert([
                'user_id' => $user->user_id,
                'materi_id' => $materi->materi_id,
                'unlocked_at' => now(),
            ]);
        });
    }
}
