<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\NotificationService;

class AdminNotificationController extends Controller
{

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

            return view('admin.notifications.index', compact('logs'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return back()->with('success', "Notification sent to {$result['sent_to']} users");
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return back()->with('success', 'Broadcast sent successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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

            return view('admin.notifications.index', compact('logs'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /admin/notifications/send-live */
    public function sendLiveClass(Request $request)
    {
        if (!auth()->user()->isAdmin())
            abort(403);
        $kelas = \App\Models\Course::findOrFail($request->kelas_id);

        // Kirim notifikasi ke semua murid yang ikut kelas ini (via database notification)
        $muridIds = \Illuminate\Support\Facades\DB::table('kelas_peserta')
            ->where('kelas_id', $kelas->kelas_id)
            ->pluck('siswa_id');

        $murids = \App\Models\User::whereIn('user_id', $muridIds)->get();
        \Illuminate\Support\Facades\Notification::send($murids, new \App\Notifications\LiveClassStarted($kelas));

        return back()->with('success', 'Notifikasi Live Class dikirim!');
    }
}
