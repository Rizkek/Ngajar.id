<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tampilkan semua notifikasi user (inbox)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua notifikasi dan paginate
        $notifications = collect(); // Default kosong jika tidak pakai database notification bawaan
        
        if (method_exists($user, 'notifications')) {
            $notifications = $user->notifications()->paginate(20);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi dibaca
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        
        if (method_exists($user, 'notifications')) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return back()->with('success', 'Notifikasi ditandai dibaca.');
    }

    /**
     * Tandai semua dibaca
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        if (method_exists($user, 'unreadNotifications')) {
            $user->unreadNotifications->markAsRead();
        }

        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    /**
     * Get latest notifications via AJAX
     */
    public function latestJson()
    {
        $user = Auth::user();
        $notifications = [];

        if (method_exists($user, 'notifications')) {
            $notifications = $user->notifications()->latest()->take(5)->get()->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->data['type'] ?? 'notifications',
                    'title' => $notif->data['title'] ?? 'Notifikasi Baru',
                    'message' => $notif->data['message'] ?? $notif->data['judul_materi'] ?? '',
                    'time' => $notif->created_at->diffForHumans(),
                    'is_read' => $notif->read_at !== null
                ];
            });
        }

        return response()->json($notifications);
    }
}
