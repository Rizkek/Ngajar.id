<?php

namespace App\Notifications;

use App\Models\Kelas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveClassStarted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Kelas $kelas
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Bisa tambah 'mail' atau 'broadcast' nanti
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'live_class',
            'title' => 'Kelas Live Dimulai!',
            'message' => "Pengajar di kelas '{$this->kelas->judul}' sedang Live sekarang. Gabung yuk!",
            'url' => route('kelas.live.join', $this->kelas->kelas_id),
            'kelas_id' => $this->kelas->kelas_id
        ];
    }
}
