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
                        placeholder="Cari mata pelajaran atau topik...">
                </div>

                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto mask-linear-fade">
                    <button
                        class="px-4 py-2 rounded-full bg-brand-500 text-white text-sm font-medium whitespace-nowrap">Semua</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Matematika</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Bahasa
                        Indonesia</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Sains
                        (IPA)</button>
                    <button
                        class="px-4 py-2 rounded-full bg-gray-100 text-slate-600 hover:bg-gray-200 text-sm font-medium whitespace-nowrap">Bahasa
                        Inggris</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Loop Cards Here -->
            @foreach($programs as $program)
                <div
                    class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                        <img src="{{ $program['image'] }}" alt="{{ $program['title'] }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div
                            class="absolute top-3 left-3 px-2 py-1 bg-white/90 backdrop-blur text-xs font-bold text-slate-800 rounded">
                            {{ $program['level'] }}
                        </div>
                        @if(isset($program['is_premium']) && $program['is_premium'])
                            <div class="absolute top-3 right-3 px-2 py-1 bg-secondary-500 text-xs font-bold text-white rounded">
                                Premium
                            </div>
                        @else
                            <div class="absolute top-3 right-3 px-2 py-1 bg-brand-500 text-xs font-bold text-white rounded">
                                Gratis
                            </div>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="text-xs text-brand-600 font-semibold mb-2 uppercase tracking-wider">
                            {{ $program['category'] }}</div>
                        <h3
                            class="text-lg font-bold text-slate-900 mb-2 leading-tight group-hover:text-brand-600 transition-colors">
                            {{ $program['title'] }}</h3>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2 flex-grow">{{ $program['description'] }}</p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded text-yellow-400 text-base">star</span>
                                <span class="text-sm font-bold text-slate-700">{{ $program['rating'] }}</span>
                                <span class="text-xs text-slate-400">({{ $program['reviews'] }})</span>
                            </div>
                            <span class="text-sm text-slate-500">{{ $program['students'] }} Siswa</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            <nav class="flex gap-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">Previous</a>
                <a href="#" class="px-4 py-2 bg-brand-600 text-white rounded font-medium">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">3</a>
                <span class="px-4 py-2 text-slate-400">...</span>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">Next</a>
            </nav>
        </div>
    </div>
@endsection