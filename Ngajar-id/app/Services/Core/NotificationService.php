<?php

namespace App\Services\Core;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CourseStartedNotification;
use App\Notifications\AssignmentDueNotification;
use App\Notifications\MessageNotification;

class NotificationService
{
    /**
     * Send course started notification
     */
    public function notifyCourseStarted(User $user, $course): void
    {
        Notification::send($user, new CourseStartedNotification($course));
    }

    /**
     * Send assignment due notification
     */
    public function notifyAssignmentDue(User $user, $assignment): void
    {
        Notification::send($user, new AssignmentDueNotification($assignment));
    }

    /**
     * Send message notification
     */
    public function notifyNewMessage(User $user, $message): void
    {
        Notification::send($user, new MessageNotification($message));
    }

    /**
     * Batch notify students about course update
     */
    public function notifyCourseUpdate($course, $message)
    {
        $students = $course->peserta;
        foreach ($students as $student) {
            Notification::queue($student, new MessageNotification([
                'title' => 'Course Update: ' . $course->judul,
                'message' => $message,
                'url' => route('kelas.show', $course->kelas_id),
            ]));
        }
    }

    /**
     * Get user notifications with pagination
     */
    public function getUserNotifications(User $user, int $perPage = 20)
    {
        return $user->notifications()->paginate($perPage);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(User $user, $notificationId): bool
    {
        return $user->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]) > 0;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->whereNull('read_at')->count();
    }
}
