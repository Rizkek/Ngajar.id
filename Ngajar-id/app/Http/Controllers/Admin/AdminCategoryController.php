<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCategoryController extends Controller
{
    /**
     * Display kategori statistics and management
     */
    public function index()
    {
        // Get all unique categories from kelas table
        $kategoriStats = Course::select('kategori', DB::raw('count(*) as total'))
            ->whereNotNull('kategori')
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->get();

        // Define available categories (can be moved to config later)
        $availableKategori = [
            'programming' => ['name' => 'Programming', 'icon' => 'code', 'color' => 'blue'],
            'design' => ['name' => 'Design', 'icon' => 'palette', 'color' => 'purple'],
            'business' => ['name' => 'Business', 'icon' => 'business_center', 'color' => 'green'],
            'marketing' => ['name' => 'Marketing', 'icon' => 'campaign', 'color' => 'orange'],
            'data-science' => ['name' => 'Data Science', 'icon' => 'analytics', 'color' => 'teal'],
            'soft-skills' => ['name' => 'Soft Skills', 'icon' => 'psychology', 'color' => 'pink'],
            'bahasa' => ['name' => 'Bahasa', 'icon' => 'translate', 'color' => 'indigo'],
            'sertifikasi' => ['name' => 'Sertifikasi', 'icon' => 'workspace_premium', 'color' => 'amber'],
        ];

        return view('admin.categories.index', compact('kategoriStats', 'availableKategori'));
    }

    /**
     * Update bulk kategori for kelas
     */
    public function updateBulk(Request $request)
    {
        $validated = $request->validate([
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,kelas_id',
            'kategori' => 'required|string|max:100',
        ]);

        Course::whereIn('kelas_id', $validated['kelas_ids'])
            ->update(['kategori' => $validated['kategori']]);

        return back()->with('success', count($validated['kelas_ids']) . ' kelas berhasil diupdate kategorinya!');
    }

    /**
     * Show kelas by kategori
     */
    public function showByKategori($kategori)
    {
        $kelas = Course::where('kategori', $kategori)
            ->with('pengajar')
            ->paginate(20);

        return view('admin.categories.show', compact('kelas', 'kategori'));
    }
}




