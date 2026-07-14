<?php

namespace App\Services\Course;

use App\Models\User;
use Illuminate\Support\Str;

class LiveClassService
{
    /**
     * Generate Jitsi room for live class
     */
    public function generateJitsiRoom(int $classId, User $instructor): array
    {
        $roomName = 'ngajar_' . $classId . '_' . Str::random(8);
        $jitsiUrl = config('jitsi.server_url', 'https://meet.jit.si');

        return [
            'room' => $roomName,
            'url' => "{$jitsiUrl}/{$roomName}",
            'instructor_id' => $instructor->user_id,
            'class_id' => $classId,
            'created_at' => now(),
            'expires_at' => now()->addHours(2),
        ];
    }

    /**
     * Get active live classes
     */
    public function getActiveLiveClasses(): array
    {
        return \DB::table('live_sessions')
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with(['class', 'instructor'])
            ->get()
            ->toArray();
    }

    /**
     * Join live class
     */
    public function joinLiveClass(User $user, int $classId): array
    {
        $session = \DB::table('live_sessions')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            throw new \Exception('Live session not found or inactive');
        }

        // Record attendance
        \DB::table('live_attendance')->insertOrIgnore([
            'session_id' => $session->id,
            'user_id' => $user->user_id,
            'joined_at' => now(),
        ]);

        return [
            'room' => $session->room,
            'url' => $session->url,
            'user_name' => $user->name,
        ];
    }

    /**
     * End live session
     */
    public function endLiveSession(int $sessionId): bool
    {
        return \DB::table('live_sessions')
            ->where('id', $sessionId)
            ->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]) > 0;
    }

    /**
     * Get session attendance
     */
    public function getSessionAttendance(int $sessionId): array
    {
        return \DB::table('live_attendance')
            ->join('users', 'live_attendance.user_id', '=', 'users.user_id')
            ->where('session_id', $sessionId)
            ->select('users.user_id', 'users.name', 'Live_attendance.joined_at', 'live_attendance.left_at')
            ->get()
            ->toArray();
    }
}
