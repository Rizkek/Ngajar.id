<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle Global Search
     */
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->back();
        }

        // Search Kelas
        $kelasResults = Kelas::where('judul', 'like', "%{$query}%")
            ->where('status', 'aktif') // Hanya kelas aktif
            ->with('pengajar') // Eager load pengajar
            ->limit(10)
            ->get();

        // Search Materi (Optional, bisa berat kalau banyak banget)
        $materiResults = Materi::where('judul', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        return view('search.index', compact('query', 'kelasResults', 'materiResults'));
    }
}
