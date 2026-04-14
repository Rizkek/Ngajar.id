<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class MessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $message) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => $this->message['title'] ?? 'New Message',
            'message' => $this->message['message'] ?? '',
            'url' => $this->message['url'] ?? '#',
        ]);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->message['title'] ?? 'New Message',
            'message' => $this->message['message'] ?? '',
        ]);
    }
}
