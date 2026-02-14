@extends('layouts.dashboard')

@section('title', 'Kelola Pengajar - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium mb-2 text-sm">
                    <span class="material-symbols-rounded text-lg">arrow_back</span>
                    Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Kelola Pengajar</h1>
                <p class="text-slate-600">Manajemen pengajar dan kontrol kualitas platform</p>
            </div>
        </div>

        <!-- Filters & Search - Modern Design -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" action="{{ route('admin.pengajar.index') }}" class="flex flex-wrap gap-4">
                <!-- Search Input with Icon -->
                <div class="flex-1 min-w-[250px] relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span
                            class="material-symbols-rounded text-gray-400 group-focus-within:text-teal-500 transition-colors text-xl">search</span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau email pengajar..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-200 text-slate-700 placeholder-slate-400">
                </div>

                <!-- Status Filter - Custom Select -->
                <div class="relative min-w-[180px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="material-symbols-rounded text-gray-400 text-xl">filter_list</span>
                    </div>
                    <select name="status"
                        class="w-full pl-12 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50/50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-200 text-slate-700 font-medium appearance-none cursor-pointer">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>📊 Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>🚫 Suspend</option>
                    </select>
                    <!-- Custom Dropdown Arrow -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Filter Button -->
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md active:scale-95">
                    <span class="material-symbols-rounded text-xl">tune</span>
                    <span>Filter</span>
                </button>

                <!-- Reset Button (if filters are active) -->
                @if(request('search') || request('status') != 'all')
                    <a href="{{ route('admin.pengajar.index') }}"
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

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-linear-to-br from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Total Pengajar</p>
                        <h3 class="text-3xl font-black">{{ $pengajars->total() }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">person_book</span>
                    </div>
                </div>
            </div>

            <div class="bg-linear-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Pengajar Aktif</p>
                        <h3 class="text-3xl font-black">
                            {{ \App\Models\User::pengajar()->where('status', 'aktif')->count() }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">verified</span>
                    </div>
                </div>
            </div>

            <div class="bg-linear-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Disuspend</p>
                        <h3 class="text-3xl font-black">
                            {{ \App\Models\User::pengajar()->where('status', 'nonaktif')->count() }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">block</span>
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
                                Pengajar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Terdaftar</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pengajars as $pengajar)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-linear-to-r from-teal-400 to-teal-500 flex items-center justify-center text-white font-bold">
                                            {{ substr($pengajar->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $pengajar->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $pengajar->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                        {{ $pengajar->kelas_ajar_count }} Kelas
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($pengajar->status === 'aktif')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Suspend</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $pengajar->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.pengajar.show', $pengajar->user_id) }}"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg font-medium transition-colors">
                                            Detail
                                        </a>

                                        @if($pengajar->status === 'aktif')
                                            <form action="{{ route('admin.pengajar.updateStatus', $pengajar->user_id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="nonaktif">
                                                <button type="submit" onclick="return confirm('Suspend pengajar ini?')"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg font-medium transition-colors">
                                                    Suspend
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.pengajar.updateStatus', $pengajar->user_id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="aktif">
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium transition-colors">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">search_off</span>
                                    <p>Tidak ada pengajar ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pengajars->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $pengajars->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection