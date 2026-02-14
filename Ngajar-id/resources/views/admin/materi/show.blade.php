@extends('layouts.dashboard')

@section('title', 'Detail Materi - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <a href="{{ route('admin.materi.index') }}"
                class="text-brand-600 hover:text-brand-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-slate-900 mt-4 break-words">{{ $materi->judul }}</h1>
            <p class="text-slate-600">ID Modul: {{ $materi->modul_id }} • Tipe: {{ ucfirst($materi->tipe) }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Preview Konten -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Preview Box -->
                <div
                    class="bg-slate-900 rounded-xl overflow-hidden shadow-lg relative aspect-video flex items-center justify-center">
                    @if($materi->tipe == 'video')
                        @if($materi->video_url)
                            {{-- Embed YouTube/Vimeo logic here --}}
                            <iframe src="{{ str_replace('youtu.be/', 'youtube.com/embed/', $materi->video_url) }}"
                                class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        @else
                            <div class="text-slate-400 text-center">
                                <span class="material-symbols-rounded text-6xl mb-2">videocam_off</span>
                                <p>Video URL tidak tersedia</p>
                            </div>
                        @endif
                    @elseif($materi->tipe == 'artikel')
                        <div class="p-8 bg-white text-slate-800 w-full h-full overflow-y-auto prose max-w-none">
                            {!! nl2br(e($materi->konten ?? 'Tidak ada konten artikel.')) !!}
                        </div>
                    @elseif($materi->tipe == 'dokumen')
                        <div class="bg-slate-800 text-white text-center p-8">
                            <span class="material-symbols-rounded text-6xl mb-4 text-slate-400">description</span>
                            <h3 class="text-xl font-bold mb-2">{{ basename($materi->file_path ?? 'Dokumen') }}</h3>
                            <a href="{{ Storage::url($materi->file_path) }}" target="_blank"
                                class="inline-block px-6 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors mt-4">
                                Download / Lihat Dokumen
                            </a>
                        </div>
                    @else
                        <div class="text-slate-400 text-center">
                            <span class="material-symbols-rounded text-6xl mb-2">help_outline</span>
                            <p>Preview tidak tersedia untuk tipe ini</p>
                        </div>
                    @endif
                </div>

                <!-- Detail Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-gray-100 pb-2">Deskripsi</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $materi->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="block text-slate-400 mb-1">Durasi Estimasi</span>
                            <span class="font-medium text-slate-800">{{ $materi->durasi_menit ?? 0 }} Menit</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 mb-1">Urutan</span>
                            <span class="font-medium text-slate-800">Modul ke-{{ $materi->urutan }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Aksi Admin -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Aksi Admin</h3>

                    <form action="{{ route('admin.materi.update', $materi->modul_id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Judul Materi</label>
                            <input type="text" name="judul" value="{{ old('judul', $materi->judul) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Urutan</label>
                            <input type="number" name="urutan" value="{{ old('urutan', $materi->urutan) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-500 text-sm">
                        </div>

                        <button type="submit"
                            class="w-full py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition-colors shadow-sm">
                            Update Data
                        </button>
                    </form>

                    <hr class="my-6 border-slate-100">

                    <form action="{{ route('admin.materi.destroy', $materi->modul_id) }}" method="POST"
                        onsubmit="return confirm('Hapus materi ini? Pengajar pemilik materi akan kehilangan akses.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full py-2 bg-white text-red-600 border border-red-200 font-medium rounded hover:bg-red-50 transition-colors">
                            Hapus Materi
                        </button>
                    </form>
                </div>

                <!-- Info Kelas & Pengajar -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-slate-500 uppercase mb-4">Informasi Kelas</h3>
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded bg-gray-200 overflow-hidden flex-shrink-0">
                            @if($materi->kelas->thumbnail)
                                <img src="{{ Storage::url($materi->kelas->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-rounded">image</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('admin.kelas.show', $materi->kelas_id) }}"
                                class="text-brand-600 font-bold hover:underline line-clamp-2">
                                {{ $materi->kelas->nama_kelas }}
                            </a>
                            <p class="text-xs text-slate-500 mt-1">Status: {{ ucfirst($materi->kelas->status) }}</p>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-slate-500 uppercase mb-2 mt-6">Dibuat Oleh</h3>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-xs">
                            {{ substr($materi->kelas->pengajar->name ?? 'U', 0, 2) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $materi->kelas->pengajar->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $materi->kelas->pengajar->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection