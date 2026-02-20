@extends('layouts.dashboard')

@section('title', 'Learning Paths - Ngajar.ID')
@section('header_title', 'Learning Paths')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Learning Paths</h1>
        <p class="text-slate-500">Jalur belajar terstruktur untuk menguasai skill baru.</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('murid.learning-paths.index') }}"
                class="border-teal-500 text-teal-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Progress Saya
            </a>
            <a href="{{ route('learning-paths.index') }}"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Jelajah Semua Path
            </a>
        </nav>
    </div>

    @if($ongoingPaths->isEmpty() && $completedPaths->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-rounded text-3xl text-teal-600">route</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada Learning Path diikuti</h3>
            <p class="text-slate-500 mb-6 max-w-md mx-auto">Anda belum mendaftar ke learning path manapun. Mulai perjalanan
                belajar Anda sekarang!</p>
            <a href="{{ route('learning-paths.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition">
                <span class="material-symbols-rounded">explore</span>
                Jelajah Learning Path
            </a>
        </div>
    @else
        <!-- Ongoing Paths -->
        @if($ongoingPaths->isNotEmpty())
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-rounded text-amber-500">hourglass_top</span>
                Sedang Dipelajari
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($ongoingPaths as $path)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col h-full">
                        <div class="h-32 bg-gray-200 relative">
                            @if($path->thumbnail)
                                <img src="{{ asset('storage/' . $path->thumbnail) }}" alt="{{ $path->judul }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                                    <span class="material-symbols-rounded text-white text-4xl">route</span>
                                </div>
                            @endif
                            <div class="absolute bottom-0 left-0 w-full h-1 bg-gray-200">
                                <div class="bg-amber-400 h-1 transition-all duration-500"
                                    style="width: {{ $path->pivot->progress_percentage }}%"></div>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="px-2 py-0.5 rounded textxs font-bold bg-teal-50 text-teal-700 border border-teal-100 uppercase tracking-wide text-[10px]">
                                    {{ $path->level }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2">{{ $path->judul }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-2 mb-4">{{ $path->deskripsi }}</p>

                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-600">
                                    {{ $path->pivot->progress_percentage }}% Selesai
                                </span>
                                <a href="{{ route('learning-paths.show', $path->path_id) }}"
                                    class="text-sm font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                                    Lanjutkan <span class="material-symbols-rounded text-lg">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Completed Paths -->
        @if($completedPaths->isNotEmpty())
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-rounded text-green-500">check_circle</span>
                Selesai
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($completedPaths as $path)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-green-100 overflow-hidden opacity-90 hover:opacity-100 transition">
                        <div class="bg-green-50 p-4 border-b border-green-100 flex items-center justify-between">
                            <span class="px-2 py-1 bg-green-200 text-green-800 text-xs font-bold rounded">Completed</span>
                            <span class="text-xs text-green-700 font-medium">
                                {{ \Carbon\Carbon::parse($path->pivot->completed_at)->format('d M Y') }}
                            </span>
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $path->judul }}</h3>
                            <p class="text-sm text-slate-500 mb-4">Selamat! Anda telah menyelesaikan path ini.</p>
                            <a href="{{ route('learning-paths.certificate', $path->path_id) }}"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-green-200 text-green-700 hover:bg-green-50 rounded-lg font-bold transition text-sm shadow-sm">
                                <span class="material-symbols-rounded">workspace_premium</span>
                                Lihat Sertifikat
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
@endsection