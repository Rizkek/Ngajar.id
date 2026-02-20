<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\UserPathProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningPathController extends Controller
{
    /**
     * Browse/Explore Learning Paths
     */
    public function index(Request $request)
    {
        $kategori = $request->get('kategori');
        $level = $request->get('level');
        $sort = $request->get('sort', 'popular'); // popular, newest, title

        $query = LearningPath::with(['creator', 'kelas'])
            ->where('is_active', true);

        // Filter by kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        // Filter by level
        if ($level) {
            $query->where('level', $level);
        }

        // Sorting
        switch ($sort) {
            case 'newest':
                $query->latest();
                break;
            case 'title':
                $query->orderBy('judul');
                break;
            case 'popular':
            default:
                $query->orderBy('total_enrolled', 'desc');
                break;
        }

        $paths = $query->paginate(12);

        // Get available categories and levels
        $categories = LearningPath::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori');

        $levels = ['Beginner', 'Intermediate', 'Advanced'];

        return view('learning-paths.index', compact('paths', 'categories', 'levels', 'kategori', 'level', 'sort'));
    }

    /**
     * Show Learning Path Detail
     */
    public function show($pathId)
    {
        $path = LearningPath::with(['creator', 'kelas.pengajar'])
            ->findOrFail($pathId);

        $user = auth()->user();
        $progress = null;
        $isEnrolled = false;

        if ($user) {
            $isEnrolled = $path->isEnrolledBy($user);
            $progress = $path->getProgressFor($user);
        }

        return view('learning-paths.show', compact('path', 'isEnrolled', 'progress'));
    }

    /**
     * Enroll user to a learning path
     */
    /**
     * Enroll user to a learning path
     */
    public function enroll(Request $request, $pathId)
    {
        $user = $request->user();

        // 0. Cek Role
        if (!$user->isMurid()) {
            return back()->with('error', 'Hanya akun Murid yang bisa mendaftar learning path.');
        }

        $path = LearningPath::findOrFail($pathId);

        // 1. Check if already enrolled
        if ($path->isEnrolledBy($user)) {
            return back()->with('info', 'Anda sudah terdaftar di learning path ini.');
        }

        // 2. Logic Pembayaran Token
        $harga = $path->harga_token ?? 0;

        // Bypass jika free atau beasiswa
        if ($path->is_free || $user->hasBeasiswa()) {
            $harga = 0;
        }

        if ($harga > 0) {
            // Cek saldo
            $userToken = $user->token;
            if (!$userToken || !$userToken->cukup($harga)) {
                return redirect()->route('topup.create')
                    ->with('error', "Token tidak mencukupi untuk daftar Learning Path ini ({$harga} Token). Saldo Anda: " . ($userToken->jumlah ?? 0));
            }
        }

        try {
            DB::transaction(function () use ($user, $path, $harga) {
                // Potong Token jika berbayar
                if ($harga > 0) {
                    $user->token->kurang($harga);

                    // Catat Log
                    \App\Models\TokenLog::create([
                        'user_id' => $user->user_id,
                        'jumlah' => $harga,
                        'aksi' => 'kurang',
                        'tipe' => 'pembelian_path',
                        'keterangan' => "Membeli Learning Path: {$path->judul}",
                        'tanggal' => now(),
                    ]);
                }

                // Create progress record (Enrollment)
                UserPathProgress::create([
                    'user_id' => $user->user_id,
                    'path_id' => $path->path_id,
                    'progress_percentage' => 0,
                    'started_at' => now(),
                ]);

                // Increment total enrolled
                $path->increment('total_enrolled');

                // Auto-enroll to ALL classes in this path ? 
                // Opsional: Bisa enroll ke semua kelas sekaligus supaya tidak perlu bayar per kelas lagi (jika kelasnya berbayar)
                // Atau enroll ke kelas pertama saja.
                // Best Practice: Jika Path sudah dibeli, maka semua kelas di dalamnya dianggap sudah dibeli aksesnya via Path.
                // Kita enroll ke semua kelas di path ini.

                $kelasIds = $path->kelas()->pluck('kelas.kelas_id');
                // Filter yang belum di enroll
                $existingEnrollments = $user->kelasIkuti()->whereIn('kelas_peserta.kelas_id', $kelasIds)->pluck('kelas_peserta.kelas_id')->toArray();

                $newKelasIds = $kelasIds->diff($existingEnrollments);

                if ($newKelasIds->isNotEmpty()) {
                    $user->kelasIkuti()->attach($newKelasIds, ['tanggal_daftar' => now()]);
                }
            });

            // Redirect back to Path Detail Page to show "Enrolled" state
            // User prefers to see details first, not jump to lesson immediately
            return redirect()->route('learning-paths.show', $pathId)
                ->with('success', $harga > 0
                    ? "Berhasil membeli akses Learning Path seharga {$harga} Token!"
                    : ($user->hasBeasiswa() ? "Fasilitas Beasiswa: Berhasil mendaftar GRATIS!" : "Berhasil mendaftar gratis!"));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * My Learning Paths (User's enrolled paths)
     */
    public function myPaths(Request $request)
    {
        $user = $request->user();

        $enrolledPaths = $user->learningPathsEnrolled()
            ->with(['kelas'])
            ->withPivot('progress_percentage', 'started_at', 'completed_at')
            ->get();

        // Separate ongoing and completed
        $ongoingPaths = $enrolledPaths->filter(function ($path) {
            return $path->pivot->completed_at === null;
        });

        $completedPaths = $enrolledPaths->filter(function ($path) {
            return $path->pivot->completed_at !== null;
        });

        return view('learning-paths.my-paths', compact('ongoingPaths', 'completedPaths'));
    }

    /**
     * Mark a class as completed in a path
     */
    public function markKelasCompleted(Request $request, $pathId, $kelasId)
    {
        $user = $request->user();

        $progress = UserPathProgress::where('user_id', $user->user_id)
            ->where('path_id', $pathId)
            ->firstOrFail();

        $progress->markKelasCompleted($kelasId);

        return response()->json([
            'success' => true,
            'progress_percentage' => $progress->progress_percentage,
            'is_completed' => $progress->isCompleted(),
            'message' => 'Kelas berhasil ditandai selesai!'
        ]);
    }

    /**
     * Set current class user is taking
     */
    public function setCurrentKelas(Request $request, $pathId, $kelasId)
    {
        $user = $request->user();

        $progress = UserPathProgress::where('user_id', $user->user_id)
            ->where('path_id', $pathId)
            ->firstOrFail();

        $progress->setCurrentKelas($kelasId);

        return response()->json([
            'success' => true,
            'message' => 'Kelas saat ini berhasil diupdate!'
        ]);
    }

    /**
     * Download certificate (if path completed)
     */
    public function downloadCertificate($pathId)
    {
        $user = auth()->user();
        $path = LearningPath::findOrFail($pathId);

        $progress = UserPathProgress::where('user_id', $user->user_id)
            ->where('path_id', $pathId)
            ->firstOrFail();

        if (!$progress->isCompleted()) {
            return back()->with('error', 'Anda belum menyelesaikan learning path ini.');
        }

        // TODO: Generate PDF certificate
        // For now, just return a view
        return view('learning-paths.certificate', compact('path', 'progress', 'user'));
    }
}
