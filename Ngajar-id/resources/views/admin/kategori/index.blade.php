@extends('layouts.dashboard')

@section('title', 'Kelola Kategori - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Manajemen Kategori</h1>
            <p class="text-slate-600">Kelompokkan kelas berdasarkan topik pembelajaran</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($availableKategori as $key => $category)
                @php
                    $stat = $kategoriStats->firstWhere('kategori', $key);
                    $totalKelas = $stat ? $stat->total : 0;
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-{{ $category['color'] }}-100 flex items-center justify-center">
                            <span
                                class="material-symbols-rounded text-2xl text-{{ $category['color'] }}-600">{{ $category['icon'] }}</span>
                        </div>
                        <span class="text-2xl font-bold text-slate-800">{{ $totalKelas }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $category['name'] }}</h3>
                    <p class="text-sm text-slate-500 mb-4">{{ $totalKelas }} kelas aktif</p>

                    <a href="{{ route('admin.kategori.show', $key) }}"
                        class="inline-flex items-center text-sm font-medium text-{{ $category['color'] }}-600 hover:text-{{ $category['color'] }}-700 transition-colors">
                        Lihat Detail <span class="material-symbols-rounded text-lg ml-1">arrow_forward</span>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Bulk Update Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Update Kategori Massal</h2>
            <p class="text-slate-600 mb-6">Pilih kelas yang ingin diubah kategorinya secara bersamaan.</p>

            <form action="{{ route('admin.kategori.bulk-update') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Select Kategori Baru -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Kategori Baru</label>
                        <select name="kategori" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($availableKategori as $key => $cat)
                                <option value="{{ $key }}">{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div
                    class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 text-sm text-amber-800 flex items-start gap-2">
                    <span class="material-symbols-rounded text-amber-600 mt-0.5">info</span>
                    <p>Fitur bulk update akan tersedia di update berikutnya. Saat ini silakan update kategori melalui menu
                        Edit Kelas masing-masing.</p>
                </div>

                <button type="button" disabled
                    class="px-6 py-3 bg-gray-300 text-white font-bold rounded-lg cursor-not-allowed">
                    Update Masal (Segera Hadir)
                </button>
            </form>
        </div>
    </div>
@endsection