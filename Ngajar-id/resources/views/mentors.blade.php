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

    <!-- Filters (Enhanced) -->
    <div class="bg-white border-b border-gray-100 shadow-sm sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <!-- Search -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pt-2">
                        <span class="material-symbols-rounded text-gray-400">search</span>
                    </span>
                    <input type="text"
                        class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all"
                        placeholder="Cari nama pengajar atau keahlian...">
                </div>

                <!-- Dropdowns -->
                <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide">
                    <select
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-slate-600 text-sm focus:outline-none hover:border-brand-500 cursor-pointer">
                        <option>Semua Jenjang</option>
                        <option>SD</option>
                        <option>SMP</option>
                        <option>SMA</option>
                        <option>Umum</option>
                    </select>
                    <select
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-slate-600 text-sm focus:outline-none hover:border-brand-500 cursor-pointer">
                        <option>Semua Mapel</option>
                        <option>Matematika</option>
                        <option>Bahasa Inggris</option>
                        <option>Fisika</option>
                        <option>Biologi</option>
                    </select>

                    <!-- New Relevant Filters -->
                    <select
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-slate-600 text-sm focus:outline-none hover:border-brand-500 cursor-pointer">
                        <option>Ketersediaan</option>
                        <option>Senin - Rabu</option>
                        <option>Sabtu - Minggu</option>
                        <option>Malam Hari</option>
                    </select>
                    <select
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-slate-600 text-sm focus:outline-none hover:border-brand-500 cursor-pointer">
                        <option>Metode Belajar</option>
                        <option>Online Class</option>
                        <option>Tatap Muka (Offline)</option>
                        <option>Hybrid</option>
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
                    class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 flex flex-col items-center text-center group relative overflow-hidden">

                    <!-- Top Volunteer Badge -->
                    @if(isset($mentor['is_top']) && $mentor['is_top'])
                        <div class="absolute top-0 right-0">
                            <div
                                class="bg-yellow-400 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg shadow-sm flex items-center gap-1">
                                <span class="material-symbols-rounded text-sm">workspace_premium</span>
                                Top Volunteer
                            </div>
                        </div>
                    @endif

                    <!-- Profile Photo (Personal Branding) -->
                    <div class="w-24 h-24 mb-3 relative group/avatar cursor-pointer">
                        <img src="{{ $mentor['photo'] }}" alt="{{ $mentor['name'] }}"
                            class="w-full h-full rounded-full object-cover ring-4 ring-brand-50 group-hover:ring-brand-200 transition-all shadow-md group-hover/avatar:brightness-90">

                        <!-- Video Intro Overlay -->
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-opacity">
                            <div
                                class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg transform scale-75 group-hover/avatar:scale-100 transition-transform">
                                <span class="material-symbols-rounded text-brand-600 text-2xl ml-1">play_arrow</span>
                            </div>
                        </div>

                        <!-- Status Indicator -->
                        <div
                            class="absolute bottom-1 right-1 w-6 h-6 bg-white rounded-full flex items-center justify-center p-0.5 shadow-sm z-10">
                            <div class="w-full h-full bg-green-500 rounded-full border-2 border-white" title="Online Sekarang">
                            </div>
                        </div>
                    </div>

                    <!-- Name & Role -->
                    <h3 class="text-lg font-bold text-slate-900 mb-1 group-hover:text-brand-600 transition-colors line-clamp-1">
                        {{ $mentor['name'] }}
                    </h3>

                    <!-- Skill Tags (Visual Labels) -->
                    <div class="flex flex-wrap justify-center gap-1.5 mb-4 px-2">
                        @if(isset($mentor['tags']))
                            @foreach($mentor['tags'] as $tag)
                                <span
                                    class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] uppercase font-bold tracking-wide rounded-md">#{{ $tag }}</span>
                            @endforeach
                        @else
                            {{-- Fallback tags if not dynamic yet --}}
                            <span
                                class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] uppercase font-bold tracking-wide rounded-md">#Sabar</span>
                            <span
                                class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] uppercase font-bold tracking-wide rounded-md">#Friendly</span>
                        @endif
                    </div>

                    <div class="w-full border-t border-gray-100 mb-4"></div>

                    <!-- Details -->
                    <div class="w-full text-left space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="material-symbols-rounded text-base text-brand-500 w-5 text-center">menu_book</span>
                            <span class="font-medium">{{ $mentor['subjects'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="material-symbols-rounded text-base text-brand-500 w-5 text-center">school</span>
                            <span class="truncate">{{ $mentor['university'] }}</span>
                        </div>
                        <!-- New Details: Schedule & Rating -->
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="material-symbols-rounded text-base text-brand-500 w-5 text-center">schedule</span>
                            <span class="truncate">{{ $mentor['availability'] ?? 'Flexible' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="material-symbols-rounded text-base text-yellow-400 w-5 text-center">star</span>
                            <span class="font-bold text-slate-900">{{ $mentor['rating'] }}</span>
                            <span class="text-slate-400 text-xs">({{ $mentor['reviews'] }} reviews)</span>
                        </div>
                    </div>

                    <!-- Actions (Chat) -->
                    <div class="w-full mt-auto">
                        <a href="https://wa.me/{{ $mentor['whatsapp'] ?? '#' }}?text=Halo%20Kak%20{{ urlencode($mentor['name']) }},%20saya%20tertarik%20belajar%20{{ urlencode($mentor['subjects']) }}."
                            target="_blank"
                            class="w-full py-3 bg-brand-600 text-white hover:bg-brand-700 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-md shadow-brand-200 hover:shadow-lg">
                            <span class="material-symbols-rounded text-xl">chat</span>
                            Hubungi via WhatsApp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            <nav class="flex gap-2">
                <a href="#"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-slate-500 hover:bg-gray-50 bg-white transition-all">
                    <span class="material-symbols-rounded">chevron_left</span>
                </a>
                <a href="#"
                    class="w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-lg font-bold shadow-lg">1</a>
                <a href="#"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-slate-500 hover:bg-gray-50 bg-white transition-all">2</a>
                <a href="#"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-slate-500 hover:bg-gray-50 bg-white transition-all">
                    <span class="material-symbols-rounded">chevron_right</span>
                </a>
            </nav>
        </div>
    </div>
@endsection