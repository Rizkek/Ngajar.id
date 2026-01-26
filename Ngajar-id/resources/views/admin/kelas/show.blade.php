@extends('layouts.dashboard')

@section('title', 'Review Kelas - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6">
            <a href="{{ route('admin.kelas.index') }}"
                class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium mb-4">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali ke Daftar Kelas
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Review Kelas</h1>
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
            <!-- Admin Actions Panel -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Status Kelas</h3>

                    <div class="text-center mb-6">
                        @if($kelas->status === 'aktif')
                            <div class="w-20 h-20 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-3">
                                <span class="material-symbols-rounded text-green-600 text-4xl">check_circle</span>
                            </div>
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">✓ Aktif &
                                Dipublikasi</span>
                        @elseif($kelas->status === 'selesai')
                            <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <span class="material-symbols-rounded text-gray-600 text-4xl">inventory</span>
                            </div>
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">📦
                                Diarsipkan</span>
                        @elseif($kelas->status === 'ditolak')
                            <div class="w-20 h-20 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-3">
                                <span class="material-symbols-rounded text-red-600 text-4xl">cancel</span>
                            </div>
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">✖ Ditolak</span>
                        @endif
                    </div>

                    <div class="space-y-3 text-sm border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Dibuat:</span>
                            <span class="font-medium text-slate-900">{{ $kelas->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Kelas ID:</span>
                            <span class="font-medium text-slate-900">#{{ $kelas->kelas_id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Moderation Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Aksi Moderasi</h3>
                    <div class="space-y-3">
                        @if($kelas->status !== 'aktif')
                            <form action="{{ route('admin.kelas.updateStatus', $kelas->kelas_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit"
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">check_circle</span>
                                    Approve & Publikasikan
                                </button>
                            </form>
                        @endif

                        @if($kelas->status !== 'selesai')
                            <form action="{{ route('admin.kelas.updateStatus', $kelas->kelas_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="selesai">
                                <button type="submit" onclick="return confirm('Arsipkan kelas ini?')"
                                    class="w-full px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">inventory</span>
                                    Arsipkan Kelas
                                </button>
                            </form>
                        @endif

                        @if($kelas->status !== 'ditolak')
                            <form action="{{ route('admin.kelas.updateStatus', $kelas->kelas_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="ditolak">
                                <button type="submit"
                                    onclick="return confirm('Tolak kelas ini? Kelas tidak akan tampil di platform.')"
                                    class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded">cancel</span>
                                    Reject/Tolak Kelas
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.kelas.destroy', $kelas->kelas_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('HAPUS PERMANEN kelas ini? Tindakan tidak bisa dibatalkan!')"
                                class="w-full px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded">delete_forever</span>
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="font-bold mb-4">💡 Tips Moderasi</h3>
                    <ul class="text-xs space-y-2 opacity-90">
                        <li>✓ Approve jika konten berkualitas & sesuai guideline</li>
                        <li>📦 Arsipkan jika sudah selesai/tidak aktif</li>
                        <li>✖ Reject jika melanggar ToS atau spam</li>
                        <li>🗑️ Hapus hanya jika benar-benar perlu</li>
                    </ul>
                </div>
            </div>

            <!-- Kelas Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $kelas->judul }}</h2>
                        <p class="text-slate-600">{{ $kelas->deskripsi }}</p>
                    </div>

                    <!-- Pengajar Info -->
                    <div class="border-t pt-6">
                        <h3 class="font-bold text-slate-900 mb-4">Pengajar</h3>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-r from-teal-400 to-teal-500 flex items-center justify-center text-white font-bold text-2xl">
                                {{ substr($kelas->pengajar->name ?? 'N', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-900">{{ $kelas->pengajar->name ?? 'N/A' }}</h4>
                                <p class="text-sm text-slate-600">{{ $kelas->pengajar->email ?? 'N/A' }}</p>
                                <a href="{{ route('admin.pengajar.show', $kelas->pengajar_id) }}"
                                    class="text-xs text-teal-600 hover:underline">
                                    Lihat Profil Pengajar →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Total Peserta</p>
                                <h3 class="text-3xl font-black">{{ $kelas->peserta_count }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">groups</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">Total Materi</p>
                                <h3 class="text-3xl font-black">{{ $kelas->materi_count }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl">menu_book</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Materi List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-slate-800">Materi Pembelajaran</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($kelas->materi as $materi)
                            <div class="p-6 hover:bg-slate-50 transition-colors flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    @if($materi->tipe === 'video')
                                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-rounded text-red-600">videocam</span>
                                        </div>
                                    @elseif($materi->tipe === 'pdf')
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-rounded text-blue-600">picture_as_pdf</span>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-rounded text-gray-600">quiz</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-900">{{ $materi->judul }}</h4>
                                    @if($materi->deskripsi)
                                        <p class="text-sm text-slate-600 mt-1">{{ Str::limit($materi->deskripsi, 100) }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-700">
                                            {{ strtoupper($materi->tipe) }}
                                        </span>
                                        @if($materi->file_url)
                                            <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                                ✓ File Uploaded
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center text-slate-500">
                                <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">menu_book_off</span>
                                <p>Belum ada materi diupload.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection