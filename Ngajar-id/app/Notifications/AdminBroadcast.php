<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AdminBroadcast extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $actionUrl;
    protected $priority;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $actionUrl = null, $priority = 'normal')
    {
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->priority = $priority;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'priority' => $this->priority,
            'type' => 'admin_broadcast',
        ];
    }
}
