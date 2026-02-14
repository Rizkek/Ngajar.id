@extends('layouts.dashboard')

@section('title', 'Moderasi Kelas - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium mb-2 text-sm">
                    <span class="material-symbols-rounded text-lg">arrow_back</span>
                    Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Moderasi Kelas</h1>
                <p class="text-slate-600">Review, approve, dan kelola semua kelas di platform</p>
            </div>
        </div>

        <!-- Filters & Search - Modern Design -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex flex-wrap gap-4">
                <!-- Search Input with Icon -->
                <div class="flex-1 min-w-[250px] relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span
                            class="material-symbols-rounded text-gray-400 group-focus-within:text-teal-500 transition-colors text-xl">search</span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul kelas atau nama pengajar..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-200 text-slate-700 placeholder-slate-400">
                </div>

                <!-- Status Filter -->
                <div class="relative min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="material-symbols-rounded text-gray-400 text-xl">visibility</span>
                    </div>
                    <select name="status"
                        class="w-full pl-12 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50/50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-200 text-slate-700 font-medium appearance-none cursor-pointer">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>📊 Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>📦 Selesai/Arsip
                        </option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Filter Button -->
                <button type="submit"
                    class="px-6 py-3 bg-linear-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md active:scale-95">
                    <span class="material-symbols-rounded text-xl">tune</span>
                    <span>Filter</span>
                </button>

                <!-- Reset Button -->
                @if(request('search') || request('status') != 'all')
                    <a href="{{ route('admin.kelas.index') }}"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2">
                        <span class="material-symbols-rounded text-xl">refresh</span>
                        <span>Reset</span>
                    </a>
                @endif
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
            <div class="bg-linear-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
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

            <div class="bg-linear-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
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

            <div class="bg-linear-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white">
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

            <div class="bg-linear-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
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
                                            class="w-8 h-8 rounded-full bg-linear-to-r from-teal-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
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