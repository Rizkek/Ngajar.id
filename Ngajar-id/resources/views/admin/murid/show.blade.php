@extends('layouts.dashboard')

@section('title', 'Detail Murid - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6">
            <a href="{{ route('admin.murid.index') }}"
                class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium mb-4">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali ke Daftar Murid
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Detail Murid</h1>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile & Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="text-center mb-6">
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                            {{ substr($murid->name, 0, 1) }}
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $murid->name }}</h2>
                        <p class="text-slate-600 text-sm">{{ $murid->email }}</p>

                        <div class="mt-4">
                            @if($murid->status === 'aktif')
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
                            <span class="font-medium text-slate-900">{{ $murid->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">User ID:</span>
                            <span class="font-medium text-slate-900">#{{ $murid->user_id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Token Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Kelola Token</h3>

                    <div class="mb-4 p-4 bg-amber-50 rounded-lg text-center">
                        <p class="text-sm text-amber-700 mb-1">Saldo Saat Ini</p>
                        <p class="text-3xl font-black text-amber-900">{{ number_format($murid->token?->jumlah ?? 0) }}</p>
                        <p class="text-xs text-amber-600 mt-1">Token</p>
                    </div>

                    <form action="{{ route('admin.murid.updateToken', $murid->user_id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Token</label>
                            <input type="number" name="amount" required min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Masukkan jumlah">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" name="action" value="add"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded">add</span>
                                Tambah
                            </button>
                            <button type="submit" name="action" value="subtract"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded">remove</span>
                                Kurangi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Status Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Aksi Akun</h3>
                    <div class="space-y-3">
                        @if($murid->status === 'aktif')
                            <form action="{{ route('admin.murid.updateStatus', $murid->user_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="nonaktif">
                                <button type="submit" onclick="return confirm('Suspend murid ini?')"
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">block</span>
                                    Suspend Murid
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.murid.updateStatus', $murid->user_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">check_circle</span>
                                    Aktifkan Kembali
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.murid.destroy', $murid->user_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('HAPUS PERMANEN murid ini?')"
                                class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded">delete_forever</span>
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity & Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Kelas Diikuti</p>
                                <h3 class="text-3xl font-black">{{ $murid->kelas_ikuti_count }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">school</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Modul Dimiliki</p>
                                <h3 class="text-3xl font-black">{{ $murid->modulDimiliki->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">workspace_premium</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Classes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-slate-800">Kelas yang Diikuti</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($murid->kelasIkuti as $kelas)
                            <div class="p-6 hover:bg-slate-50 transition-colors">
                                <h3 class="font-bold text-slate-900 mb-2">{{ $kelas->judul }}</h3>
                                <div class="flex items-center gap-4 text-sm text-slate-500">
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-rounded text-base">person</span>
                                        <span>{{ $kelas->pengajar->name ?? 'N/A' }}</span>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $kelas->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($kelas->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center text-slate-500">
                                <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">school_off</span>
                                <p>Belum mengikuti kelas apapun.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection