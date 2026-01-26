@extends('layouts.dashboard')

@section('title', 'Detail Pengajar - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6">
            <a href="{{ route('admin.pengajar.index') }}"
                class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium mb-4">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali ke Daftar Pengajar
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Detail Pengajar</h1>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl flex items-center gap-3">
                <span class="material-symbols-rounded text-green-600">check_circle</span>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="text-center mb-6">
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-r from-teal-400 to-teal-500 flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                            {{ substr($pengajar->name, 0, 1) }}
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $pengajar->name }}</h2>
                        <p class="text-slate-600 text-sm">{{ $pengajar->email }}</p>

                        <div class="mt-4">
                            @if($pengajar->status === 'aktif')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">✓
                                    Aktif</span>
                            @else
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">⊗
                                    Suspend</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3 text-sm border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Terdaftar:</span>
                            <span class="font-medium text-slate-900">{{ $pengajar->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">User ID:</span>
                            <span class="font-medium text-slate-900">#{{ $pengajar->user_id }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 space-y-3">
                        @if($pengajar->status === 'aktif')
                            <form action="{{ route('admin.pengajar.updateStatus', $pengajar->user_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="nonaktif">
                                <button type="submit" onclick="return confirm('Suspend pengajar ini?')"
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">block</span>
                                    Suspend Pengajar
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.pengajar.updateStatus', $pengajar->user_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">check_circle</span>
                                    Aktifkan Kembali
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.pengajar.destroy', $pengajar->user_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('HAPUS PERMANEN pengajar ini? Tindakan tidak dapat dibatalkan!')"
                                class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded">delete_forever</span>
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats & Classes -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Total Kelas</p>
                                <h3 class="text-3xl font-black">{{ $pengajar->kelas_ajar_count }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">class</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Total Siswa</p>
                                <h3 class="text-3xl font-black">{{ $totalSiswa }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">groups</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Total Materi</p>
                                <h3 class="text-3xl font-black">{{ $totalMateri }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">menu_book</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kelas List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-slate-800">Kelas yang Diajar</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($pengajar->kelasAjar as $kelas)
                            <div class="p-6 hover:bg-slate-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-slate-900 mb-2">{{ $kelas->judul }}</h3>
                                        <p class="text-sm text-slate-600 mb-3">{{ Str::limit($kelas->deskripsi, 150) }}</p>
                                        <div class="flex items-center gap-4 text-sm text-slate-500">
                                            <div class="flex items-center gap-1">
                                                <span class="material-symbols-rounded text-base">groups</span>
                                                <span>{{ $kelas->peserta->count() }} siswa</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="material-symbols-rounded text-base">menu_book</span>
                                                <span>{{ $kelas->materi->count() }} materi</span>
                                            </div>
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $kelas->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($kelas->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center text-slate-500">
                                <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">school_off</span>
                                <p>Belum membuat kelas.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection