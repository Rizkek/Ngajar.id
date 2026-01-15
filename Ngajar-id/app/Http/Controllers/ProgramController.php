<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a list of active programs/classes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Kelas::with(['pengajar:user_id,name'])
            ->withCount('peserta')
            ->where('status', 'aktif');

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $programs = $query->latest()->paginate($perPage);

        $data = $programs->map(function ($kelas) {
            return [
                'id' => $kelas->kelas_id,
                'judul' => $kelas->judul,
                'deskripsi' => $kelas->deskripsi,
                'pengajar' => $kelas->pengajar->name,
                'total_siswa' => $kelas->peserta_count,
                'status' => $kelas->status,
                'created_at' => $kelas->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $programs->currentPage(),
                'last_page' => $programs->lastPage(),
                'per_page' => $programs->perPage(),
                'total' => $programs->total(),
            ],
        ]);
    }

    /**
     * Display a single program/class details
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $kelas = Kelas::with([
            'pengajar:user_id,name,email',
            'materi:materi_id,kelas_id,judul,tipe,deskripsi',
            'peserta:user_id,name'
        ])
            ->withCount('peserta')
            ->find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $kelas->kelas_id,
                'judul' => $kelas->judul,
                'deskripsi' => $kelas->deskripsi,
                'status' => $kelas->status,
                'pengajar' => [
                    'id' => $kelas->pengajar->user_id,
                    'name' => $kelas->pengajar->name,
                    'email' => $kelas->pengajar->email,
                ],
                'total_siswa' => $kelas->peserta_count,
                'materi' => $kelas->materi->map(function ($materi) {
                    return [
                        'id' => $materi->materi_id,
                        'judul' => $materi->judul,
                        'tipe' => $materi->tipe,
                        'deskripsi' => $materi->deskripsi,
                    ];
                }),
                'created_at' => $kelas->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
