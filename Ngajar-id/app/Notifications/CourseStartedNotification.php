<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CourseStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $course) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'Course Started',
            'message' => "Course '{$this->course->judul}' has started",
            'course_id' => $this->course->kelas_id,
            'url' => route('kelas.show', $this->course->kelas_id),
        ]);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Course Started',
            'message' => "Course '{$this->course->judul}' has started",
        ]);
    }
}
