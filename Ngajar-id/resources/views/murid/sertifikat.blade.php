@extends('layouts.dashboard')

@section('title', 'Sertifikat Saya - Ngajar.ID')
@section('header_title', 'Sertifikat & Pencapaian')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Koleksi Sertifikat</h1>
        <p class="text-slate-500">Bukti kompetensi dan dedikasi belajar Anda.</p>
    </div>

    @if($sertifikatPath->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100 max-w-2xl mx-auto">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <span class="material-symbols-rounded text-5xl">workspace_premium</span>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Sertifikat</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">
                Selesaikan minimal satu Learning Path atau Kelas Bersertifikat untuk mendapatkan sertifikat digital.
            </p>
            <a href="{{ route('learning-paths.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition shadow-lg shadow-teal-200">
                <span class="material-symbols-rounded">rocket_launch</span>
                Mulai Belajar Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($sertifikatPath as $path)
                <div
                    class="bg-white rounded-xl shadow-lg border border-yellow-100 overflow-hidden relative group hover:-translate-y-1 transition duration-300">
                    <!-- Decorative Pattern -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-50 rounded-full -mr-16 -mt-16 opacity-50 z-0"></div>

                    <div class="p-6 relative z-10 flex flex-col h-full">
                        <div
                            class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 mb-4 shadow-inner">
                            <span class="material-symbols-rounded text-3xl">verified</span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 mb-1 leading-snug">
                            Certificate of Completion
                        </h3>
                        <p class="text-sm font-semibold text-teal-600 mb-4">
                            {{ $path->judul }}
                        </p>

                        <div class="space-y-2 text-xs text-slate-500 mb-6 flex-1">
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span>Tanggal Selesai</span>
                                <span class="font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($path->pivot->completed_at)->format('d F Y') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <span>ID Sertifikat</span>
                                <span class="font-mono text-slate-700 select-all">
                                    CRT-{{ str_pad($path->path_id, 4, '0', STR_PAD_LEFT) }}-{{ \Carbon\Carbon::parse($path->pivot->completed_at)->format('ymd') }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('learning-paths.certificate', $path->path_id) }}" target="_blank"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 text-white hover:bg-slate-800 rounded-lg font-bold transition text-sm">
                            <span class="material-symbols-rounded">download</span>
                            Unduh PDF
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection