<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveClassController extends Controller
{
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
        $roomName = "NgajarID-Live-" . $kelas->kelas_id; // Room name unik

        $jitsiConfig = [
            'roomName' => $roomName,
            'width' => '100%',
            'height' => '100%',
            'userInfo' => [
                'displayName' => $user->name . ($isPengajar ? ' (Pengajar)' : ''),
                'email' => $user->email
            ],
            'configOverwrite' => [
                'startWithAudioMuted' => true,
                'startWithVideoMuted' => true,
                'prejoinPageEnabled' => false // Langsung masuk tanpa halaman pre-join
            ],
            'interfaceConfigOverwrite' => [
                'TOOLBAR_BUTTONS' => [
                    'microphone',
                    'camera',
                    'closedcaptions',
                    'desktop',
                    'fullscreen',
                    'fodeviceselection',
                    'hangup',
                    'profile',
                    'chat',
                    'recording',
                    'livestreaming',
                    'etherpad',
                    'sharedvideo',
                    'settings',
                    'raisehand',
                    'videoquality',
                    'filmstrip',
                    'invite',
                    'feedback',
                    'stats',
                    'shortcuts',
                    'tileview',
                    'videobackgroundblur',
                    'download',
                    'help',
                    'mute-everyone',
                    'security'
                ],
            ]
        ];

        return view('live-class.room', compact('kelas', 'jitsiConfig', 'user'));
    }
}
