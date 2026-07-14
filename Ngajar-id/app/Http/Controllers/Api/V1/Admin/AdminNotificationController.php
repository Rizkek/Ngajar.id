<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Admin\NotificationService;

class AdminNotificationController extends Controller
{
    use ApiResponse;

    protected $notifService;

    public function __construct(NotificationService $notifService)
    {
        $this->notifService = $notifService;
    }

    /** GET /admin/notifications */
    public function index(Request $request)
    {
        try {
            $logs = $this->notifService->getLogs($request->get('per_page', 15));

            return $this->successWithPagination(
                    $logs->map(fn($log) => $this->notifService->formatLog($log)),
                    'Notifications retrieved successfully'
                );
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/notifications/send */
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

            $result = $this->notifService->sendBroadcast($request->user(), $validated);

            return $this->success($result, 'Notification sent successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** POST /admin/notifications/broadcast */
    public function broadcast(Request $request)
    {
        try {
            $validated = $request->validate([
                'recipient_ids' => 'nullable|array',
                'recipient_ids.*' => 'integer|exists:users,user_id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $result = $this->notifService->sendTargeted(
                $validated['recipient_ids'] ?? [],
                $validated['title'],
                $validated['message']
            );

            return $this->success($result, 'Broadcast sent successfully');
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }

    /** GET /admin/notifications/history */
    public function history(Request $request)
    {
        try {
            $logs = $this->notifService->getLogs(
                $request->get('per_page', 20),
                $request->has('days') ? (int) $request->get('days') : null
            );

            return $this->successWithPagination(
                    $logs->map(fn($log) => $this->notifService->formatLog($log)),
                    'Notification history retrieved successfully'
                );
            
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
            
        }
    }
}
