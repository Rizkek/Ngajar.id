@extends('layouts.dashboard')

@section('title', 'Katalog Kelas - Ngajar.ID')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Jelajah Kelas</h1>
            <p class="text-slate-600">Temukan kelas baru dan tingkatkan skill Anda hari ini.</p>
        </div>

        <!-- Search Bar -->
        <div class="mb-8 max-w-2xl">
            <form action="{{ route('murid.katalog') }}" method="GET" class="relative">
                <input type="text" name="q" placeholder="Cari topik skill yang ingin dipelajari..."
                    value="{{ request('q') }}"
                    class="w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-all">
                <span class="material-symbols-rounded absolute left-4 top-3.5 text-gray-400">search</span>
                <button type="submit"
                    class="absolute right-2 top-2 px-4 py-1.5 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Filters (Example UI only for now) -->
        <div class="flex gap-2 overflow-x-auto pb-4 mb-4 no-scrollbar">
            <button
                class="px-4 py-2 bg-slate-800 text-white rounded-full text-sm font-medium whitespace-nowrap">Semua</button>
            <button
                class="px-4 py-2 bg-white border border-gray-200 text-slate-600 hover:bg-slate-50 rounded-full text-sm font-medium whitespace-nowrap transition">Programming</button>
            <button
                class="px-4 py-2 bg-white border border-gray-200 text-slate-600 hover:bg-slate-50 rounded-full text-sm font-medium whitespace-nowrap transition">Design</button>
            <button
                class="px-4 py-2 bg-white border border-gray-200 text-slate-600 hover:bg-slate-50 rounded-full text-sm font-medium whitespace-nowrap transition">Business</button>
            <button
                class="px-4 py-2 bg-white border border-gray-200 text-slate-600 hover:bg-slate-50 rounded-full text-sm font-medium whitespace-nowrap transition">Marketing</button>
        </div>

        @if($allKelas->isEmpty())
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-4xl text-gray-400">search_off</span>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Kelas tidak ditemukan</h3>
                <p class="text-gray-500">Coba kata kunci lain atau reset filter.</p>
                <a href="{{ route('murid.katalog') }}" class="inline-block mt-4 text-teal-600 font-medium hover:underline">Lihat
                    Semua Kelas</a>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($allKelas as $kelas)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all group flex flex-col h-full">
                        <!-- Thumbnail Placeholder -->
                        <div class="h-48 bg-gray-200 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            @if($kelas->thumbnail)
                                <img src="{{ asset('storage/' . $kelas->thumbnail) }}" alt="{{ $kelas->judul }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-teal-400 to-blue-500">
                                    <span class="material-symbols-rounded text-white text-6xl opacity-30">school</span>
                                </div>
                            @endif

                            <div class="absolute bottom-4 left-4 right-4">
                                <span
                                    class="bg-white/20 backdrop-blur-md text-white text-xs px-2 py-1 rounded border border-white/30">
                                    {{ $kelas->kategori ?? 'Umum' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-auto">
                                <h3
                                    class="text-lg font-bold text-slate-900 group-hover:text-teal-600 transition-colors mb-2 line-clamp-2">
                                    {{ $kelas->judul }}
                                </h3>
                                <p class="text-sm text-slate-500 line-clamp-2 mb-4">
                                    {{ $kelas->deskripsi }}
                                </p>

                                <div class="flex items-center gap-3 text-sm text-slate-600 mb-4">
                                    <div class="flex items-center gap-1">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($kelas->pengajar->name) }}&background=random"
                                                alt="{{ $kelas->pengajar->name }}">
                                        </div>
                                        <span class="truncate max-w-[120px]">{{ $kelas->pengajar->name }}</span>
                                    </div>
                                    <span class="text-gray-300">•</span>
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-rounded text-base text-amber-500">star</span>
                                        <span>4.8</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 mt-4">
                                @if(in_array($kelas->kelas_id, $enrolledKelasIds))
                                    <a href="{{ route('belajar.show', $kelas->kelas_id) }}"
                                        class="block w-full py-2.5 bg-green-600 text-white font-medium text-center rounded-lg hover:bg-green-700 transition shadow-md shadow-green-200">
                                        Lanjut Belajar
                                    </a>
                                @else
                                    <form action="{{ route('murid.katalog.join', $kelas->kelas_id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full py-2.5 bg-teal-600 text-white font-medium text-center rounded-lg hover:bg-teal-700 transition shadow-md shadow-teal-200">
                                            Gabung Kelas
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $allKelas->links() }}
            </div>
        @endif
    </div>
@endsection