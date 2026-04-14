<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class AssignmentDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $assignment) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'Assignment Due Soon',
            'message' => "Assignment '{$this->assignment->title}' due in 24 hours",
            'assignment_id' => $this->assignment->id,
            'url' => route('assignment.show', $this->assignment->id),
        ]);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Assignment Due: {$this->assignment->title}")
            ->line("Your assignment '{$this->assignment->title}' is due in 24 hours")
            ->action('View Assignment', route('assignment.show', $this->assignment->id))
            ->line('Thank you for using Ngajar.id!');
    }
}
