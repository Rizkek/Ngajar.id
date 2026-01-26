@extends('layouts.dashboard')

@section('title', 'Materi - Pengajar')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Materi Pembelajaran</h1>
                <p class="text-slate-600">Semua materi dari kelas yang kamu ajar</p>
            </div>
            <a href="{{ route('pengajar.materi.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition-colors">
                <span class="material-symbols-rounded">add</span>
                <span>Upload Materi Baru</span>
            </a>
        </div>

        <!-- Quick Tip -->
        <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 mb-6 flex items-start gap-4 relative">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 text-purple-600">
                <span class="material-symbols-rounded">tips_and_updates</span>
            </div>
            <div class="flex-1 pr-8">
                <h4 class="font-bold text-purple-900 text-sm mb-1">Materi yang Menarik</h4>
                <p class="text-purple-800 text-sm leading-relaxed">
                    Siswa lebih suka materi visual! Cobalah kombinasikan <strong>Video</strong> pendek dengan
                    <strong>PDF</strong> ringkasan untuk hasil belajar maksimal.
                </p>
            </div>
            <button class="absolute top-4 right-4 text-purple-400 hover:text-purple-600">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        @if(empty($materiList))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center flex flex-col items-center">
                <!-- Custom SVG Illustration -->
                <div class="w-48 h-48 bg-purple-50 rounded-full flex items-center justify-center mb-6 relative">
                    <div class="absolute inset-0 border-4 border-white rounded-full shadow-lg"></div>
                    <span class="material-symbols-rounded text-purple-200 text-9xl">menu_book</span>
                    <div class="absolute bottom-4 right-4 bg-white p-2 rounded-xl shadow-md transform -rotate-6">
                        <span class="material-symbols-rounded text-teal-400 text-3xl">upload_file</span>
                    </div>
                </div>

                <h3 class="text-2xl font-bold text-slate-800 mb-2">Belum Ada Materi</h3>
                <p class="text-slate-500 mb-8 max-w-md mx-auto">
                    Kelas tanpa materi ibarat buku tanpa tulisan. Segera upload video atau dokumen untuk mulai mengajar.
                </p>
                <a href="{{ route('pengajar.materi.create') }}"
                    class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 transform hover:-translate-y-1 transition-all flex items-center gap-3">
                    <span class="material-symbols-rounded">upload</span>
                    Upload Materi Baru
                </a>
            </div>
        @else
            <div class="grid gap-4">
                @foreach($materiList as $materi)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($materi['tipe'] === 'video')
                                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-rounded text-red-600 text-3xl">videocam</span>
                                    </div>
                                @elseif($materi['tipe'] === 'pdf')
                                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-rounded text-blue-600 text-3xl">picture_as_pdf</span>
                                    </div>
                                @else
                                    <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-rounded text-gray-600 text-3xl">description</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $materi['judul'] }}</h3>
                                <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                                    <span class="material-symbols-rounded text-base">school</span>
                                    <span>{{ $materi['kelas_judul'] }}</span>
                                </div>
                                @if(!empty($materi['deskripsi']))
                                    <p class="text-slate-600 text-sm mb-3">{{ $materi['deskripsi'] }}</p>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                        {{ strtoupper($materi['tipe']) }}
                                    </span>
                                    @if($materi['file_url'])
                                        <a href="{{ $materi['file_url'] }}" target="_blank"
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 hover:bg-green-200 transition-colors flex items-center gap-1">
                                            <span class="material-symbols-rounded text-sm">download</span>
                                            Lihat File
                                        </a>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Belum Upload
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-shrink-0 flex gap-2">
                                <a href="{{ route('pengajar.materi.edit', $materi['materi_id']) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    <span class="material-symbols-rounded text-lg">edit</span>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('pengajar.materi.destroy', $materi['materi_id']) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                        <span class="material-symbols-rounded text-lg">delete</span>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection