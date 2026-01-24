@extends('layouts.app')

@section('title', 'Program Belajar - Ngajar.ID')

@section('content')
    <!-- Header Hero -->
    <div class="bg-brand-600 py-16 lg:py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl lg:text-5xl font-extrabold mb-6">Program Belajar Unggulan</h1>
            <p class="text-xl text-brand-100 max-w-2xl mx-auto">
                Temukan ribuan materi pelajaran, video interaktif, dan latihan soal untuk membantumu meraih prestasi
                terbaik.
            </p>
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

    <!-- Search & Filters -->
    <div class="bg-white border-b border-gray-100 sticky top-20 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="relative w-full md:max-w-lg">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pt-2">
                        <span class="material-symbols-rounded text-gray-400">search</span>
                    </span>
                    <input type="text"
                        class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                        placeholder="Cari kelas yang kamu mau...">
                </div>

                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto mask-linear-fade">
                    <button
                        class="px-4 py-2 rounded-full bg-brand-500 text-white text-sm font-medium whitespace-nowrap">Semua</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Sains</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Sosial</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Bahasa</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Teknologi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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
                    <div
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
                            <p class="text-sm text-slate-500 mb-4 line-clamp-2 flex-grow">{{ $program->deskripsi }}</p>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-yellow-400 text-base">star</span>
                                    <span class="text-sm font-bold text-slate-700">4.9</span>
                                    <span class="text-xs text-slate-400">({{ $program->peserta_count + 10 }} Ulasan)</span>
                                </div>
                                <span class="text-sm text-slate-500">{{ $program->peserta_count }} Siswa</span>
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
        @endif
    </div>
@endsection