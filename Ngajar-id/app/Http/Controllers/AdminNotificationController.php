<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\BroadcastLog;

class AdminNotificationController extends Controller
{
    use ApiResponse;

    /**
     * List all broadcast notifications
     * GET /admin/notifications
     */
    public function index(Request $request)
    {
        try {
            $logs = BroadcastLog::with('admin')
                ->latest()
                ->paginate($request->get('per_page', 15));

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $logs->map(fn($log) => [
                        'id' => $log->id,
                        'title' => $log->title,
                        'message' => $log->message,
                        'sent_by' => $log->admin?->name,
                        'recipient_type' => $log->recipient_type,
                        'sent_at' => $log->created_at?->toIso8601String(),
                        'recipient_count' => $log->recipient_count,
                    ]),
                    'Notifications retrieved successfully'
                );
            }

            return view('admin.notifications.index', compact('logs'));
        } catch (\Exception $e) {
            \Log::error('AdminNotificationController@index: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Send broadcast notification
     * POST /admin/notifications/send
     */
    public function send(Request $request)
    {
        try {
            $validated = $request->validate([
                'recipient_type' => 'required|in:all,murid,pengajar,kelas',
                'kelas_id' => 'required_if:recipient_type,kelas|nullable|exists:kelas,kelas_id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'action_url' => 'nullable|url',
            ]);

            // Get recipients
            $recipients = $this->getRecipients($validated);

            if ($recipients->isEmpty()) {
                throw new \Exception('No recipients found');
            }

            // Create broadcast log
            $log = BroadcastLog::create([
                'admin_id' => $request->user()->id,
                'title' => $validated['title'],
                'message' => $validated['message'],
                'recipient_type' => $validated['recipient_type'],
                'recipient_count' => $recipients->count(),
                'action_url' => $validated['action_url'] ?? null,
            ]);

            // Send notification via Laravel's notification system
            foreach ($recipients->chunk(100) as $chunk) {
                Notification::send($chunk, new \App\Notifications\AdminBroadcast(
                    $validated['title'],
                    $validated['message'],
                    $validated['action_url'] ?? null
                ));
            }

            if ($request->expectsJson()) {
                return $this->success(
                    ['log_id' => $log->id, 'sent_to' => $recipients->count()],
                    'Notification sent successfully'
                );
            }

            return back()->with('success', "Notification sent to {$recipients->count()} users");
        } catch (\Exception $e) {
            \Log::error('AdminNotificationController@send: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Send targeted broadcast
     * POST /admin/notifications/broadcast
     */
    public function broadcast(Request $request)
    {
        try {
            $validated = $request->validate([
                'recipient_ids' => 'nullable|array',
                'recipient_ids.*' => 'integer|exists:users,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $recipients = User::whereIn('id', $validated['recipient_ids'] ?? [])
                ->where('status', 'aktif')
                ->get();

            if ($recipients->isEmpty()) {
                throw new \Exception('No active recipients found');
            }

            foreach ($recipients->chunk(100) as $chunk) {
                Notification::send($chunk, new \App\Notifications\AdminBroadcast(
                    $validated['title'],
                    $validated['message']
                ));
            }

            if ($request->expectsJson()) {
                return $this->success(
                    ['sent_to' => $recipients->count()],
                    'Broadcast sent successfully'
                );
            }

            return back()->with('success', 'Broadcast sent successfully');
        } catch (\Exception $e) {
            \Log::error('AdminNotificationController@broadcast: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * View notification history
     * GET /admin/notifications/history
     */
    public function history(Request $request)
    {
        try {
            $logs = BroadcastLog::with('admin')
                ->when($request->has('days'), function ($q) use ($request) {
                    $q->where('created_at', '>=', now()->subDays($request->get('days')));
                })
                ->latest()
                ->paginate($request->get('per_page', 20));

            if ($request->expectsJson()) {
                return $this->successWithPagination(
                    $logs->map(fn($log) => [
                        'id' => $log->id,
                        'title' => $log->title,
                        'message' => $log->message,
                        'sent_by' => $log->admin?->name,
                        'recipient_type' => $log->recipient_type,
                        'recipient_count' => $log->recipient_count,
                        'sent_at' => $log->created_at?->toIso8601String(),
                    ]),
                    'Notification history retrieved successfully'
                );
            }

            return view('admin.notifications.history', compact('logs'));
        } catch (\Exception $e) {
            \Log::error('AdminNotificationController@history: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return $this->serverError($e->getMessage());
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get recipients based on type
     */
    private function getRecipients($validated)
    {
        return match ($validated['recipient_type']) {
            'all' => User::whereIn('role', ['murid', 'pengajar'])
                ->where('status', 'aktif')
                ->get(),
            'murid' => User::where('role', 'murid')
                ->where('status', 'aktif')
                ->get(),
            'pengajar' => User::where('role', 'pengajar')
                ->where('status', 'aktif')
                ->get(),
            'kelas' => Kelas::with('peserta')
                ->findOrFail($validated['kelas_id'])
                ->peserta,
            default => collect(),
        };
    }
}
