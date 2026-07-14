<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/notifications
     * Get user notifications
     */
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();

            $query = DB::table('notifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc');

            // Filter by type
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            // Filter unread only
            if ($request->input('unread_only', false)) {
                $query->where('is_read', false);
            }

            $notifications = $query->paginate($request->input('per_page', 20));

            // Unread count
            $unreadCount = DB::table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            return $this->successWithPagination(
                    $notifications->items(),
                    'Notifications retrieved',
                    $notifications->total(),
                    $notifications->per_page(),
                    $notifications->current_page(),
                    ['unread_count' => $unreadCount]
                );

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve notifications: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/notifications/unread-count
     * Get unread notification count
     */
    public function unreadCount(Request $request)
    {
        try {
            $count = DB::table('notifications')
                ->where('user_id', auth()->id())
                ->where('is_read', false)
                ->count();

            return $this->success(['unread_count' => $count], 'Unread notification count retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to get unread count: ' . $e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/v1/notifications/{id}/read
     * Mark notification as read
     */
    public function markAsRead($notificationId, Request $request)
    {
        try {
            $notification = DB::table('notifications')
                ->where('id', $notificationId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return $this->error('Notification not found', 404);
            }

            DB::table('notifications')
                ->where('id', $notificationId)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return $this->success([], 'Notification marked as read');

        } catch (\Exception $e) {
            return $this->error('Failed to mark as read: ' . $e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/v1/notifications/mark-all-read
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            DB::table('notifications')
                ->where('user_id', auth()->id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return $this->success([], 'All notifications marked as read');

        } catch (\Exception $e) {
            return $this->error('Failed to mark all as read: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/notifications/{id}
     * Delete notification
     */
    public function destroy($notificationId, Request $request)
    {
        try {
            $notification = DB::table('notifications')
                ->where('id', $notificationId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return $this->error('Notification not found', 404);
            }

            DB::table('notifications')->where('id', $notificationId)->delete();

            return $this->success([], 'Notification deleted');

        } catch (\Exception $e) {
            return $this->error('Failed to delete notification: ' . $e->getMessage(), 400);
        }
    }

    /**
     * DELETE /api/v1/notifications/clear-all
     * Delete all notifications
     */
    public function clearAll(Request $request)
    {
        try {
            DB::table('notifications')
                ->where('user_id', auth()->id())
                ->delete();

            return $this->success([], 'All notifications cleared');

        } catch (\Exception $e) {
            return $this->error('Failed to clear notifications: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/notifications/{id}
     * Get single notification
     */
    public function show($notificationId, Request $request)
    {
        try {
            $notification = DB::table('notifications')
                ->where('id', $notificationId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return $this->error('Notification not found', 404);
            }

            // Mark as read
            if (!$notification->is_read) {
                DB::table('notifications')
                    ->where('id', $notificationId)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }

            return $this->success($notification, 'Notification retrieved');

        } catch (\Exception $e) {
            return $this->error('Failed to retrieve notification: ' . $e->getMessage(), 400);
        }
    }
}
