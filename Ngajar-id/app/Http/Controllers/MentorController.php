<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    /**
     * Display a list of active mentors/teachers
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'pengajar')
            ->where('status', 'aktif')
            ->withCount(['kelasAjar as total_kelas'])
            ->with([
                'kelasAjar' => function ($q) {
                    $q->withCount('peserta');
                }
            ]);

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $mentors = $query->latest()->paginate($perPage);

        $data = $mentors->map(function ($mentor) {
            $totalSiswa = $mentor->kelasAjar->sum('peserta_count');

            return [
                'id' => $mentor->user_id,
                'name' => $mentor->name,
                'email' => $mentor->email,
                'total_kelas' => $mentor->total_kelas,
                'total_siswa' => $totalSiswa,
                'status' => $mentor->status,
                'joined_at' => $mentor->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $mentors->currentPage(),
                'last_page' => $mentors->lastPage(),
                'per_page' => $mentors->perPage(),
                'total' => $mentors->total(),
            ],
        ]);
    }

    /**
     * Display a single mentor profile
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $mentor = User::where('role', 'pengajar')
            ->with([
                'kelasAjar' => function ($q) {
                    $q->withCount('peserta')
                        ->with('materi:materi_id,kelas_id,judul,tipe');
                },
                'modulDibuat:modul_id,judul,tipe,token_harga,dibuat_oleh'
            ])
            ->find($id);

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor tidak ditemukan.',
            ], 404);
        }

        // Calculate stats
        $totalSiswa = $mentor->kelasAjar->sum('peserta_count');
        $totalMateri = $mentor->kelasAjar->sum(function ($kelas) {
            return $kelas->materi->count();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $mentor->user_id,
                'name' => $mentor->name,
                'email' => $mentor->email,
                'status' => $mentor->status,
                'stats' => [
                    'total_kelas' => $mentor->kelasAjar->count(),
                    'total_siswa' => $totalSiswa,
                    'total_materi' => $totalMateri,
                    'total_modul' => $mentor->modulDibuat->count(),
                ],
                'kelas' => $mentor->kelasAjar->map(function ($kelas) {
                    return [
                        'id' => $kelas->kelas_id,
                        'judul' => $kelas->judul,
                        'total_siswa' => $kelas->peserta_count,
                        'total_materi' => $kelas->materi->count(),
                    ];
                }),
                'joined_at' => $mentor->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
