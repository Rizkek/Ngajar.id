<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\BroadcastLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Exception;

class NotificationService
{
    /**
     * Get paginated broadcast logs.
     */
    public function getLogs(int $perPage = 15, ?int $days = null)
    {
        return BroadcastLog::with('admin')
            ->when($days, fn($q) => $q->where('created_at', '>=', now()->subDays($days)))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Send a broadcast notification to a group of users.
     *
     * @param User $admin
     * @param array $data validated fields: recipient_type, kelas_id?, title, message, action_url?
     */
    public function sendBroadcast(User $admin, array $data): array
    {
        $recipients = $this->resolveRecipients($data);

        if ($recipients->isEmpty()) {
            throw new Exception('No recipients found');
        }

        $log = BroadcastLog::create([
            'admin_id' => $admin->user_id,
            'title' => $data['title'],
            'message' => $data['message'],
            'recipient_type' => $data['recipient_type'],
            'recipient_count' => $recipients->count(),
            'action_url' => $data['action_url'] ?? null,
        ]);

        foreach ($recipients->chunk(100) as $chunk) {
            Notification::send($chunk, new \App\Notifications\AdminBroadcast(
                $data['title'],
                $data['message'],
                $data['action_url'] ?? null
            ));
        }

        return [
            'log_id' => $log->id,
            'sent_to' => $recipients->count(),
        ];
    }

    /**
     * Send a targeted notification to specific user IDs.
     */
    public function sendTargeted(array $userIds, string $title, string $message): array
    {
        $recipients = User::whereIn('user_id', $userIds)
            ->where('status', 'aktif')
            ->get();

        if ($recipients->isEmpty()) {
            throw new Exception('No active recipients found');
        }

        foreach ($recipients->chunk(100) as $chunk) {
            Notification::send($chunk, new \App\Notifications\AdminBroadcast($title, $message));
        }

        return ['sent_to' => $recipients->count()];
    }

    /**
     * Resolve a set of recipients based on recipient_type.
     */
    private function resolveRecipients(array $data): Collection
    {
        return match ($data['recipient_type']) {
            'all' => User::whereIn('role', ['murid', 'pengajar'])->where('status', 'aktif')->get(),
            'murid' => User::where('role', 'murid')->where('status', 'aktif')->get(),
            'pengajar' => User::where('role', 'pengajar')->where('status', 'aktif')->get(),
            'kelas' => Course::with('peserta')->findOrFail($data['kelas_id'])->peserta,
            default => collect(),
        };
    }

    /**
     * Format a broadcast log for API response.
     */
    public function formatLog($log): array
    {
        return [
            'id' => $log->id,
            'title' => $log->title,
            'message' => $log->message,
            'sent_by' => $log->admin?->name,
            'recipient_type' => $log->recipient_type,
            'sent_at' => $log->created_at?->toIso8601String(),
            'recipient_count' => $log->recipient_count,
        ];
    }
}


