@extends('layouts.dashboard')

@section('title', 'Kelas Saya - Murid')

@section('dashboard-content')
    <div class="container-fluid px-4">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Kelas Saya</h1>
            <p class="text-slate-600">Daftar kelas yang sedang kamu ikuti</p>
        </div>

        @if($kelasList->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="material-symbols-rounded text-slate-300 text-6xl mb-4">school</span>
                <h3 class="text-xl font-semibold text-slate-700 mb-2">Belum Ada Kelas</h3>
                <p class="text-slate-500 mb-4">Kamu belum terdaftar di kelas manapun.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kelasList as $kelas)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-6">
                            <h3 class="text-xl font-bold text-white mb-2">{{ $kelas['judul'] }}</h3>
                            <div class="flex items-center gap-2 text-teal-100">
                                <span class="material-symbols-rounded text-sm">person</span>
                                <span class="text-sm">{{ $kelas['pengajar_name'] }}</span>
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-slate-600 mb-4 line-clamp-3">{{ $kelas['deskripsi'] }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2 text-slate-500">
                                    <span class="material-symbols-rounded text-lg">menu_book</span>
                                    <span class="text-sm">{{ $kelas['total_materi'] }} Materi</span>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $kelas['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($kelas['status']) }}
                                </span>
                            </div>

                            <div class="text-xs text-slate-400 mb-4">
                                Bergabung: {{ \Carbon\Carbon::parse($kelas['tanggal_daftar'])->format('d M Y') }}
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('belajar.show', ['kelas_id' => $kelas['kelas_id']]) }}"
                                    class="flex-1 py-2 bg-teal-600 hover:bg-teal-700 text-white text-center rounded-lg font-medium transition-colors">
                                    Mulai Belajar
                                </a>
                                <a href="{{ route('kelas.live', $kelas['kelas_id']) }}" target="_blank"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-center rounded-lg font-medium transition-colors flex items-center justify-center"
                                    title="Gabung Live Class">
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