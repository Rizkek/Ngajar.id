<?php

namespace App\Jobs;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCourseCompletionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Kelas $kelas
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulasi proses berat (misal: generate PDF sertifikat, kirim email via SMTP eksternal)
        // Dalam production, ini akan memakan waktu 2-5 detik.
        // Dengan Queue, user tidak perlu menunggu proses ini selesai.

        Log::info("Memulai proses pengiriman sertifikat untuk user: {$this->user->name} di kelas: {$this->kelas->judul}");

        // Simulasi delay (misal: generate PDF)
        sleep(2);

        // Logika kirim email
        // Mail::to($this->user->email)->send(new CourseCertificateMail($this->kelas));

        Log::info("Email sertifikat berhasil dikirim ke: {$this->user->email}");
    }
}
