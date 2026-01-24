@extends('layouts.dashboard')

@section('title', 'Kelas Saya - Pengajar')

@section('dashboard-content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Kelas Saya</h1>
                <p class="text-slate-600">Daftar kelas yang kamu ajar</p>
            </div>
            <a href="{{ route('pengajar.kelas.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition-colors">
                <span class="material-symbols-rounded">add</span>
                <span>Tambah Kelas Baru</span>
            </a>
        </div>

        @if($kelasList->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="material-symbols-rounded text-slate-300 text-6xl mb-4">school</span>
                <h3 class="text-xl font-semibold text-slate-700 mb-2">Belum Ada Kelas</h3>
                <p class="text-slate-500 mb-4">Mulai mengajar dengan membuat kelas pertamamu.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kelasList as $kelas)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-6">
                            <h3 class="text-xl font-bold text-white mb-2">{{ $kelas['judul'] }}</h3>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $kelas['status'] === 'aktif' ? 'bg-white text-amber-700' : 'bg-amber-700 text-white' }}">
                                {{ ucfirst($kelas['status']) }}
                            </span>
                        </div>

                        <div class="p-6">
                            <p class="text-slate-600 mb-4 line-clamp-3">{{ $kelas['deskripsi'] }}</p>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center gap-2 text-slate-500">
                                    <span class="material-symbols-rounded text-lg">groups</span>
                                    <span class="text-sm">{{ $kelas['total_siswa'] }} Siswa</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-500">
                                    <span class="material-symbols-rounded text-lg">menu_book</span>
                                    <span class="text-sm">{{ $kelas['total_materi'] }} Materi</span>
                                </div>
                            </div>

                            <div class="text-xs text-slate-400 mb-4">
                                Dibuat: {{ \Carbon\Carbon::parse($kelas['created_at'])->format('d M Y') }}
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('pengajar.kelas.edit', $kelas['kelas_id']) }}"
                                    class="flex-1 py-2 bg-teal-600 hover:bg-teal-700 text-white text-center rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded text-lg">edit</span>
                                    Edit
                                </a>
                                <a href="{{ route('kelas.live', $kelas['kelas_id']) }}" target="_blank"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-center rounded-lg font-medium transition-colors flex items-center justify-center"
                                    title="Mulai Kelas Live">
                                    <span class="material-symbols-rounded text-lg">videocam</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection