@extends('layouts.app')

@section('title', 'Cari Pengajar - Ngajar.ID')

@section('content')
    <!-- Header Hero -->
    <div class="bg-slate-900 py-16 lg:py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl lg:text-5xl font-extrabold mb-6">Belajar dari yang Terbaik</h1>
            <p class="text-xl text-slate-300 max-w-2xl mx-auto">
                Temukan mentor berpengalaman yang siap membantumu memahami materi pelajaran dengan lebih mudah.
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-100 shadow-sm sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <!-- Search -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pt-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text"
                        class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                        placeholder="Cari nama pengajar atau keahlian...">
                </div>

                <!-- Dropdowns -->
                <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                    <select
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-slate-600 text-sm focus:outline-none hover:border-brand-500">
                        <option>Semua Jenjang</option>
                        <option>SD</option>
                        <option>SMP</option>
                        <option>SMA</option>
                        <option>Umum</option>
                    </select>
                    <select
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-slate-600 text-sm focus:outline-none hover:border-brand-500">
                        <option>Semua Mapel</option>
                        <option>Matematika</option>
                        <option>Bahasa Inggris</option>
                        <option>Fisika</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Mentors Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($mentors as $mentor)
                <div
                    class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 p-6 flex flex-col items-center text-center group">
                    <div class="w-24 h-24 mb-4 relative">
                        <img src="{{ $mentor['photo'] }}" alt="{{ $mentor['name'] }}"
                            class="w-full h-full rounded-full object-cover ring-4 ring-brand-50 group-hover:ring-brand-200 transition-all">
                        <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 border-2 border-white rounded-full"
                            title="Online"></div>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-1 group-hover:text-brand-600 transition-colors">
                        {{ $mentor['name'] }}</h3>
                    <p class="text-sm text-brand-600 font-medium mb-3">{{ $mentor['role'] }}</p>

                    <div class="w-full border-t border-gray-100 my-4"></div>

                    <div class="w-full text-left space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="w-5 text-center">📚</span>
                            <span>{{ $mentor['subjects'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="w-5 text-center">🎓</span>
                            <span class="truncate">{{ $mentor['university'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="w-5 text-center">⭐</span>
                            <span>{{ $mentor['rating'] }} ({{ $mentor['reviews'] }} reviews)</span>
                        </div>
                    </div>

                    <button
                        class="w-full py-2 bg-brand-50 text-brand-700 hover:bg-brand-600 hover:text-white rounded-lg font-bold transition-all duration-300">
                        Lihat Profil
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            <nav class="flex gap-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">Previous</a>
                <a href="#" class="px-4 py-2 bg-slate-900 text-white rounded font-medium">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">2</a>
                <span class="px-4 py-2 text-slate-400">...</span>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded text-slate-500 hover:bg-gray-50">Next</a>
            </nav>
        </div>
    </div>
@endsection