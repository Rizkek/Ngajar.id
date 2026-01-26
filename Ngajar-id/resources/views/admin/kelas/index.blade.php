@extends('layouts.dashboard')

@section('title', 'Moderasi Kelas - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Moderasi Kelas</h1>
                <p class="text-slate-600">Review, approve, dan kelola semua kelas di platform</p>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[250px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul kelas atau nama pengajar..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                </div>

                <select name="status"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>✓ Aktif</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>📦 Selesai/Arsip</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>✖ Ditolak</option>
                </select>

                <button type="submit"
                    class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                    <span class="material-symbols-rounded">search</span>
                    Filter
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl flex items-center gap-3">
                <span class="material-symbols-rounded text-green-600">check_circle</span>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center gap-3">
                <span class="material-symbols-rounded text-red-600">error</span>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Total Kelas</p>
                        <h3 class="text-3xl font-black">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">class</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Aktif</p>
                        <h3 class="text-3xl font-black">{{ $stats['aktif'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">check_circle</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Arsip</p>
                        <h3 class="text-3xl font-black">{{ $stats['selesai'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">inventory</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Ditolak</p>
                        <h3 class="text-3xl font-black">{{ $stats['ditolak'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">cancel</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Pengajar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Materi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($kelasList as $kelas)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <h4 class="font-bold text-slate-900 mb-1">{{ $kelas->judul }}</h4>
                                        <p class="text-xs text-slate-500">{{ Str::limit($kelas->deskripsi, 80) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-r from-teal-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr($kelas->pengajar->name ?? 'N', 0, 1) }}
                                        </div>
                                        <span class="text-sm text-slate-700">{{ $kelas->pengajar->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $kelas->peserta_count }} siswa
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                        {{ $kelas->materi_count }} materi
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($kelas->status === 'aktif')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓
                                            Aktif</span>
                                    @elseif($kelas->status === 'selesai')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">📦
                                            Arsip</span>
                                    @elseif($kelas->status === 'ditolak')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">✖
                                            Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.kelas.show', $kelas->kelas_id) }}"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg font-medium transition-colors">
                                            Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">search_off</span>
                                    <p>Tidak ada kelas ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kelasList->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $kelasList->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection