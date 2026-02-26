<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\BroadcastLog;
use App\Notifications\AdminBroadcast;

class AdminNotificationController extends Controller
{
    /**
     * Display notification management page
     */
    public function index()
    {
        // Get recent notifications sent from database
        $logs = BroadcastLog::with('admin')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.notifications.index', compact('logs'));
    }

    /**
     * Show form to create new broadcast notification
     */
    public function create()
    {
        $allKelas = Kelas::where('status', 'aktif')->get();
        return view('admin.notifications.create', compact('allKelas'));
    }

    /**
     * Send broadcast notification to selected users
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => 'required|in:all,murid,pengajar,kelas',
            'kelas_id' => 'required_if:recipient_type,kelas|nullable|exists:kelas,kelas_id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'action_url' => 'nullable|url',
            'priority' => 'required|in:low,normal,high',
        ]);

        // Determine recipients
        $recipients = collect();

        switch ($validated['recipient_type']) {
            case 'all':
                $recipients = User::whereIn('role', ['murid', 'pengajar'])->where('status', 'aktif')->get();
                break;

            case 'murid':
                $recipients = User::murid()->aktif()->get();
                break;

            case 'pengajar':
                $recipients = User::pengajar()->aktif()->get();
                break;

            case 'kelas':
                $kelas = Kelas::with('peserta')->findOrFail($validated['kelas_id']);
                $recipients = $kelas->peserta;
                break;
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No recipients found!');
        }

        // Send notification
        Notification::send($recipients, new AdminBroadcast(
            $validated['title'],
            $validated['message'],
            $validated['action_url'] ?? null,
            $validated['priority']
        ));

        // Log the broadcast
        BroadcastLog::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'recipient_type' => $validated['recipient_type'],
            'kelas_id' => $validated['kelas_id'] ?? null,
            'action_url' => $validated['action_url'],
            'priority' => $validated['priority'],
            'recipient_count' => $recipients->count(),
            'sent_by' => auth()->id(),
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Notification sent to ' . $recipients->count() . ' users!');
    }

    /**
     * Quick send live class notification
     */
    public function sendLiveClass(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'meeting_url' => 'required|url',
        ]);

        $kelas = Kelas::with('peserta')->findOrFail($validated['kelas_id']);

        if ($kelas->peserta->isEmpty()) {
            return back()->with('error', 'No students enrolled in this class!');
        }

        // Send live class notification
        Notification::send($kelas->peserta, new \App\Notifications\LiveClassStarted($kelas));

        // Log the broadcast as a special broadcast
        BroadcastLog::create([
            'title' => 'Live Class started: ' . $kelas->nama_kelas,
            'message' => 'Kelas live sudah dimulai. Klik untuk bergabung.',
            'recipient_type' => 'kelas',
            'kelas_id' => $kelas->kelas_id,
            'action_url' => $validated['meeting_url'],
            'priority' => 'high',
            'recipient_count' => $kelas->peserta->count(),
            'sent_by' => auth()->id(),
        ]);

        return back()->with('success', 'Live class notification sent to ' . $kelas->peserta->count() . ' students!');
    }
}
