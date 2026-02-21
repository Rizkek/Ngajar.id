@extends('layouts.app')

@section('title', 'Program Belajar - Ngajar.ID')

@section('content')
    <!-- Header Hero -->
    <!-- Header Hero -->
    <div class="relative bg-brand-600 overflow-hidden">
        <!-- Abstract Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white blur-3xl mix-blend-overlay"></div>
            <div class="absolute top-1/2 right-0 w-64 h-64 rounded-full bg-yellow-300 blur-3xl mix-blend-overlay"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24 lg:pt-32 lg:pb-32 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <!-- Text Content -->
                <div class="flex-1 text-center lg:text-left" data-aos="fade-right">
                    <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight mb-6 tracking-tight">
                        Eksplorasi Ilmu <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-amber-400">Tanpa<br />
                            Batas</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-brand-100 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light">
                        Akses ribuan kelas interaktif, mentor berpengalaman, dan komunitas belajar yang suportif untuk masa
                        depan yang lebih cerah.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <button
                            onclick="document.querySelector('.grid').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            class="px-8 py-4 bg-white text-brand-600 font-bold rounded-full shadow-[0_4px_14px_0_rgba(255,255,255,0.39)] hover:shadow-[0_6px_20px_rgba(255,255,255,0.23)] hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto">
                            Jelajahi Kelas
                        </button>
                        <a href="{{ route('tentang-kami') }}"
                            class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white/10 transition-all duration-300 w-full sm:w-auto flex items-center justify-center gap-2 group">
                            <span
                                class="material-symbols-rounded group-hover:translate-x-1 transition-transform">info</span>
                            Tentang Kami
                        </a>
                    </div>
                </div>

                <!-- Hero Image / Visual -->
                <div class="flex-1 w-full max-w-lg lg:max-w-xl relative" data-aos="fade-left" data-aos-delay="200">
                    <!-- Blob Decoration -->
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-brand-500 to-purple-500 rounded-full blur-[60px] opacity-40 animate-pulse">
                    </div>

                    <div
                        class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white/20 backdrop-blur-sm transform rotate-2 hover:rotate-0 transition-all duration-500 cursor-pointer group">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000&auto=format&fit=crop"
                            alt="Students Learning"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">

                        <!-- Floating Badge -->
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="bg-white/90 backdrop-blur-md p-4 rounded-xl shadow-lg flex items-center gap-4">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <span class="material-symbols-rounded text-green-600">verified_user</span>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Bergabung bersama</p>
                                    <p class="text-slate-900 font-bold">10,000+ Siswa Berprestasi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Info:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif
    </div>

    <!-- Search & Filters (Glassmorphism) -->
    <div class="sticky top-16 z-40 transition-all duration-300" id="sticky-bar" x-data="{ 
                                                                searchQuery: '{{ request('search') }}',
                                                                selectedCategory: '{{ request('kategori') ?? '' }}'
                                                            }">
        <div class="absolute inset-0 bg-white/80 backdrop-blur-md border-b border-white/20 shadow-sm"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col gap-6">
                <!-- Search Input -->
                <form x-ref="filterForm" method="GET" action="{{ route('programs') }}" class="w-full">
                    <div class="w-full relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span
                                class="material-symbols-rounded text-brand-400 group-focus-within:text-brand-600 transition-colors">search</span>
                        </div>
                        <input type="text" name="search" x-model="searchQuery"
                            class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white/60 border border-gray-200 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100 focus:border-brand-300 transition-all shadow-sm text-slate-700 placeholder-slate-400 font-medium"
                            placeholder="Cari topik pembelajaran (contoh: Aljabar, Sejarah Indonesia...)">
                        <input type="hidden" name="kategori" x-model="selectedCategory">
                    </div>
                </form>

                <!-- Visual Categories -->
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
                    <!-- Category: Semua -->
                    <a href="{{ route('programs') }}"
                        @click.prevent="selectedCategory = ''; $nextTick(() => $refs.filterForm.submit())"
                        class="flex flex-col items-center gap-2 min-w-[80px] group transition-all duration-300 hover:-translate-y-1">
                        <div
                            class="w-14 h-14 rounded-2xl {{ !request('kategori') ? 'bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-300' : 'bg-white border border-gray-100 text-slate-500' }} flex items-center justify-center shadow-md transition-all">
                            <span class="material-symbols-rounded text-2xl">grid_view</span>
                        </div>
                        <span
                            class="text-xs font-semibold {{ !request('kategori') ? 'text-brand-600' : 'text-slate-500' }}">Semua</span>
                    </a>

                    @foreach(config('categories.kelas') as $catValue => $catLabel)
                        @php
                            $catConfig = config('categories.icons')[$catValue] ?? ['icon' => 'folder', 'color' => 'gray'];
                            $icon = $catConfig['icon'];
                            $color = $catConfig['color'];
                        @endphp
                        <a href="{{ route('programs', ['kategori' => $catValue]) }}"
                            @click.prevent="selectedCategory = '{{ $catValue }}'; $nextTick(() => $refs.filterForm.submit())"
                            class="flex flex-col items-center gap-2 min-w-[80px] group transition-all duration-300 hover:-translate-y-1">
                            <div
                                class="w-14 h-14 rounded-2xl {{ request('kategori') == $catValue ? 'bg-' . $color . '-50 border-2 border-' . $color . '-500 text-' . $color . '-600' : 'bg-white border border-gray-100 text-' . $color . '-500' }} flex items-center justify-center shadow-sm group-hover:bg-{{ $color }}-50 group-hover:border-{{ $color }}-200 transition-all">
                                <span class="material-symbols-rounded text-2xl">{{ $icon }}</span>
                            </div>
                            <span
                                class="text-xs font-medium {{ request('kategori') == $catValue ? 'text-' . $color . '-600 font-bold' : 'text-slate-500' }} group-hover:text-{{ $color }}-600">{{ $catLabel }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Program Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Filter indicator -->
        @if(request('search') || request('kategori'))
            <div class="mb-6 flex items-center gap-3 flex-wrap">
                <span class="text-sm text-slate-600">
                    Menampilkan <strong class="text-brand-600">{{ $programs->total() }}</strong> hasil
                    @if(request('search'))
                        untuk "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if(request('kategori'))
                        di kategori <strong class="text-brand-600">{{ request('kategori') }}</strong>
                    @endif
                </span>
                <a href="{{ route('programs') }}"
                    class="text-xs px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-full transition flex items-center gap-1">
                    <span class="material-symbols-rounded text-sm">close</span>
                    Hapus Filter
                </a>
            </div>
        @else
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Semua Program Belajar</h2>
                <p class="text-sm text-slate-500">{{ $programs->total() }} kelas tersedia</p>
            </div>
        @endif

        @if($programs->isEmpty())
            <div class="text-center py-12">
                <span class="material-symbols-rounded text-gray-300 text-8xl mb-4">school</span>
                <h3 class="text-2xl font-bold text-gray-500">Belum ada kelas tersedia saat ini.</h3>
                <p class="text-gray-400">Silakan kembali lagi nanti atau hubungi kami.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Loop Cards Here -->
                @foreach($programs as $program)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}"
                        class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                        <div class="relative h-48 bg-gray-200 overflow-hidden">
                            <!-- Placeholder Image for now as DB doesn't have image column yet -->
                            <img src="https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&w=600&q=80"
                                alt="{{ $program->judul }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                            <div
                                class="absolute top-3 left-3 px-2 py-1 bg-white/90 backdrop-blur text-xs font-bold text-slate-800 rounded">
                                Semua Level
                            </div>

                            <div class="absolute top-3 right-3 px-2 py-1 bg-brand-500 text-xs font-bold text-white rounded">
                                Gratis
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="text-xs text-brand-600 font-semibold mb-2 uppercase tracking-wider">
                                {{ $program->pengajar->name ?? 'Pengajar Ngajar.ID' }}
                            </div>
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 leading-tight group-hover:text-brand-600 transition-colors">
                                {{ $program->judul }}
                            </h3>
                            <p class="text-sm text-slate-500 mb-4 line-clamp-2 grow">{{ $program->deskripsi }}</p>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto mb-4">
                                <div class="flex items-center gap-1.5 bg-yellow-50 px-2 py-1 rounded-md">
                                    <span class="material-symbols-rounded text-yellow-400 text-base">star</span>
                                    <span class="text-sm font-bold text-slate-700">4.9</span>
                                </div>
                                <div class="flex items-center gap-1 text-slate-500">
                                    <span class="material-symbols-rounded text-base">group</span>
                                    <span class="text-sm">{{ $program->peserta->count() }} Siswa</span>
                                </div>
                            </div>

                            <form action="{{ route('programs.join', $program->kelas_id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-rounded text-sm">login</span>
                                    Daftar Kelas
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
@endsection