<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Services\LiveClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveClassController extends Controller
{
    public function __construct(private LiveClassService $liveClassService) {}

    /**
     * Join ke ruang kelas live
     */
    public function join($kelasId)
    {
        $user = Auth::user();
        $kelas = Kelas::findOrFail($kelasId);

        // Keamanan: hanya pengajar pemilik kelas atau murid terdaftar yang boleh masuk
        $isPengajar = $kelas->pengajar_id == $user->user_id;

        // Cek apakah murid terdaftar (jika bukan pengajar)
        if (!$isPengajar) {
            $isMuridTerdaftar = $kelas->peserta()->where('kelas_peserta.siswa_id', $user->user_id)->exists();
            if (!$isMuridTerdaftar) {
                return redirect()->back()->with('error', 'Anda tidak terdaftar di kelas ini.');
            }
        }

        // Setup konfigurasi Jitsi
        $roomName = "NgajarID-Live-" . $kelas->kelas_id;

        $jitsiConfig = [
            'roomName' => $roomName,
            'width' => '100%',
            'height' => '100%',
            'userInfo' => [
                'displayName' => $user->name . ($isPengajar ? ' 🎓 (Pengajar)' : ''),
                'email' => $user->email,
                'moderator' => $isPengajar
            ],
            'configOverwrite' => [
                'prejoinPageEnabled' => true,
                'startWithAudioMuted' => !$isPengajar,
                'startWithVideoMuted' => !$isPengajar,
                'disableModeratorIndicator' => false,
                'startSilent' => false,
                'enableWelcomePage' => false,
                'enableClosePage' => false,
                'enableInsecureRoomNameWarning' => false,
                'requireDisplayName' => true
            ],
            'interfaceConfigOverwrite' => [
                'TOOLBAR_BUTTONS' => $isPengajar ?
                    ['microphone', 'camera', 'desktop', 'fullscreen', 'hangup', 'chat', 'recording', 'livestreaming', 'raisehand', 'tileview', 'mute-everyone', 'security', 'invite', 'settings'] :
                    ['microphone', 'camera', 'desktop', 'fullscreen', 'hangup', 'chat', 'raisehand', 'tileview'],
                'SHOW_JITSI_WATERMARK' => false,
                'SHOW_WATERMARK_FOR_GUESTS' => false,
                'DEFAULT_BACKGROUND' => '#1a1a1a',
                'DISABLE_JOIN_LEAVE_NOTIFICATIONS' => false
            ]
        ];

        return view('live-class.room', compact('kelas', 'jitsiConfig', 'user', 'isPengajar'));
    }
}
