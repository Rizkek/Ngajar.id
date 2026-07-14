<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $verificationUrl = '',
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di Ngajar.ID! 🎓',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'dashboardUrl' => $this->getDashboardUrl(),
            ],
        );
    }

    private function getDashboardUrl(): string
    {
        return match ($this->user->role) {
            'pengajar' => route('teacher.dashboard'),
            default => route('student.dashboard'),
        };
    }

    public function attachments(): array
    {
        return [];
    }
}
