@extends('layouts.dashboard')

@section('title', 'Kelas Saya - Pengajar')

@section('content')
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

        <!-- Quick Tutorial Tip (Dismissible) -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start gap-4 relative">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 text-blue-600">
                <span class="material-symbols-rounded">lightbulb</span>
            </div>
            <div class="flex-1 pr-8">
                <h4 class="font-bold text-blue-900 text-sm mb-1">Tips untuk Pengajar Baru</h4>
                <p class="text-blue-800 text-sm leading-relaxed">
                    Bingung mulai dari mana? Buat <strong>Kelas</strong> terlebih dahulu, lalu tambahkan
                    <strong>Materi</strong> (Video/PDF) di dalamnya. Jangan lupa bagikan link kelas ke siswa Anda!
                </p>
            </div>
            <button class="absolute top-4 right-4 text-blue-400 hover:text-blue-600">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        @if($kelasList->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center flex flex-col items-center">
                <!-- Custom SVG Illustration -->
                <div class="w-48 h-48 bg-teal-50 rounded-full flex items-center justify-center mb-6 relative">
                    <div class="absolute inset-0 border-4 border-white rounded-full shadow-lg"></div>
                    <span class="material-symbols-rounded text-teal-200 text-9xl">school</span>
                    <div class="absolute bottom-4 right-4 bg-white p-2 rounded-xl shadow-md transform rotate-12">
                        <span class="material-symbols-rounded text-amber-400 text-3xl">add</span>
                    </div>
                </div>

                <h3 class="text-2xl font-bold text-slate-800 mb-2">Belum Ada Kelas</h3>
                <p class="text-slate-500 mb-8 max-w-md mx-auto">
                    Mulai perjalanan mengajar Anda dengan membuat kelas pertama. Bagikan ilmu Anda dan inspirasi ribuan siswa.
                </p>

                <a href="{{ route('pengajar.kelas.create') }}"
                    class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 transform hover:-translate-y-1 transition-all flex items-center gap-3">
                    <span class="material-symbols-rounded">add_circle</span>
                    Buat Kelas Pertama
                </a>
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