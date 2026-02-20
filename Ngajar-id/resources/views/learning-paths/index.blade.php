@extends('layouts.dashboard')

@section('title', 'Jelajah Learning Paths - Ngajar.ID')
@section('header_title', 'Semua Learning Paths')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Jalur Belajar Terstruktur</h1>
        <p class="text-slate-500">Pilih learning path sesuai tujuan karir Anda.</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('murid.learning-paths.index') }}"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Progress Saya
            </a>
            <a href="{{ route('learning-paths.index') }}"
                class="border-teal-500 text-teal-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Jelajah Semua Path
            </a>
        </nav>
    </div>

    <!-- Filters -->
    <div class="mb-8 p-4 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center">
        <form action="{{ route('learning-paths.index') }}" method="GET" class="flex flex-wrap gap-4 w-full">
            <select name="kategori"
                class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <select name="level"
                class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="">Semua Level</option>
                @foreach($levels as $lvl)
                    <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold transition text-sm">
                Filter
            </button>

            @if(request('kategori') || request('level'))
                <a href="{{ route('learning-paths.index') }}" class="text-sm text-red-500 hover:underline">Reset Filter</a>
            @endif
        </form>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($paths as $path)
            <a href="{{ route('learning-paths.show', $path->path_id) }}"
                class="group block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition hover:-translate-y-1">
                <div class="h-40 bg-gray-100 relative overflow-hidden">
                    @if($path->thumbnail)
                        <img src="{{ asset('storage/' . $path->thumbnail) }}" alt="{{ $path->judul }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                            <span class="material-symbols-rounded text-white text-5xl opacity-80">route</span>
                        </div>
                    @endif
                    <div
                        class="absolute top-2 right-2 bg-white/90 backdrop-blur text-xs font-bold px-2 py-1 rounded text-slate-800 shadow-sm">
                        {{ $path->level }}
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2 text-xs text-slate-500">
                        <span class="bg-slate-100 px-2 py-0.5 rounded">{{ $path->kategori ?? 'Umum' }}</span>
                        <span>•</span>
                        <span>{{ $path->estimated_hours ?? 10 }} jam</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2 group-hover:text-teal-600 transition">
                        {{ $path->judul }}</h3>
                    <p class="text-sm text-slate-500 line-clamp-3 mb-4">{{ $path->deskripsi }}</p>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="material-symbols-rounded text-base text-teal-500">school</span>
                            <span>{{ $path->kelas->count() }} Kelas</span>
                        </div>
                        <span class="text-teal-600 font-bold text-sm flex items-center gap-1">
                            Detail <span class="material-symbols-rounded text-lg">arrow_forward</span>
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <span class="material-symbols-rounded text-4xl">search_off</span>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Tidak ada Learning Path ditemukan</h3>
                <p class="text-slate-500">Coba ubah filter pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $paths->withQueryString()->links() }}
    </div>
@endsection