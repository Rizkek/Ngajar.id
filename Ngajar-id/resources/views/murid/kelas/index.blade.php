@extends('layouts.dashboard')

@section('title', 'Kelas & Katalog - Ngajar.ID')
@section('header_title', 'Kelas')

@section('content')
<div x-data="{ tab: '{{ request('tab') == 'katalog' ? 'katalog' : 'my-kelas' }}' }">
    
    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="tab = 'my-kelas'" 
                    :class="tab === 'my-kelas' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                <span class="material-symbols-rounded mr-2" :class="tab === 'my-kelas' ? 'text-teal-500' : 'text-gray-400 group-hover:text-gray-500'">school</span>
                Kelas Saya
                <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs" x-show="tab === 'my-kelas'">{{ $myKelas->count() }}</span>
            </button>

            <button @click="tab = 'katalog'"
                    :class="tab === 'katalog' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                <span class="material-symbols-rounded mr-2" :class="tab === 'katalog' ? 'text-teal-500' : 'text-gray-400 group-hover:text-gray-500'">explore</span>
                Jelajah Katalog
            </button>
        </nav>
    </div>

    <!-- TAB 1: KELAS SAYA -->
    <div x-show="tab === 'my-kelas'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        
        @if($myKelas->isEmpty())
            <div class="text-center py-12 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-24 h-24 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4 text-teal-200">
                    <span class="material-symbols-rounded text-5xl">folder_open</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Kelas</h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Anda belum mengikuti kelas apapun. Yuk cari kelas yang menarik di katalog!</p>
                <button @click="tab = 'katalog'" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold transition">
                    Cari Kelas Sekarang
                </button>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($myKelas as $kelas)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all overflow-hidden flex flex-col h-full group">
                        <!-- Thumbnail -->
                        <div class="h-40 bg-gray-200 relative overflow-hidden">
                            @if($kelas->thumbnail)
                                <img src="{{ asset('storage/' . $kelas->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-teal-400 to-emerald-500">
                                    <span class="material-symbols-rounded text-white text-5xl opacity-40">school</span>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-teal-700 text-xs font-bold px-2 py-1 rounded shadow-sm">
                                Terdaftar
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-slate-900 line-clamp-1 mb-1">{{ $kelas->judul }}</h3>
                                <p class="text-sm text-slate-500 line-clamp-2">{{ $kelas->deskripsi }}</p>
                            </div>
                            
                            <div class="flex items-center justify-between text-xs text-slate-400 mt-auto pt-4 border-t border-gray-50">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">person</span>
                                    {{ $kelas->pengajar->name ?? 'Pengajar' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">menu_book</span>
                                    {{ $kelas->materi_count ?? 0 }} Materi
                                </span>
                            </div>

                            <a href="{{ route('belajar.show', $kelas->kelas_id) }}" class="mt-4 block w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-center rounded-lg font-bold transition shadow-lg shadow-teal-100">
                                Lanjut Belajar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 2: KATALOG -->
    <div x-show="tab === 'katalog'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        
        <!-- Search & Filter -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <form action="{{ route('murid.kelas') }}" method="GET">
                    <input type="hidden" name="tab" value="katalog">
                    <input type="text" name="q" placeholder="Cari topik skill..." value="{{ request('q') }}"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-all">
                    <span class="material-symbols-rounded absolute left-3 top-2.5 text-gray-400">search</span>
                </form>
            </div>
            
            <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
               <a href="{{ route('murid.kelas', ['tab' => 'katalog']) }}" 
                  class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition {{ !request('kategori') ? 'bg-slate-800 text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                   Semua
               </a>
               @foreach(config('categories.kelas') as $catValue => $catLabel)
                   <a href="{{ route('murid.kelas', ['tab' => 'katalog', 'kategori' => $catValue]) }}" 
                      class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition {{ request('kategori') == $catValue ? 'bg-slate-800 text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50' }}">
                       {{ $catLabel }}
                   </a>
               @endforeach
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($catalogKelas as $kelas)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all group flex flex-col h-full relative">
                    <!-- Category Badge -->
                    @if($kelas->kategori)
                        <div class="absolute top-3 left-3 z-10">
                             <span class="bg-black/50 backdrop-blur text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                {{ $kelas->kategori }}
                            </span>
                        </div>
                    @endif

                    <div class="h-44 bg-gray-200 relative overflow-hidden rounded-t-xl">
                         @if($kelas->thumbnail)
                            <img src="{{ asset('storage/' . $kelas->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
                                <span class="material-symbols-rounded text-white text-5xl opacity-40">school</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-teal-600 transition-colors mb-1">{{ $kelas->judul }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-2">{{ $kelas->deskripsi }}</p>
                        </div>

                         <div class="flex items-center gap-3 text-sm text-slate-600 mt-auto mb-4">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-rounded text-lg text-slate-400">person</span> 
                                <span class="truncate max-w-[120px]">{{ $kelas->pengajar->name }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            @if(in_array($kelas->kelas_id, $enrolledKelasIds))
                                <button disabled class="block w-full py-2.5 bg-gray-100 text-gray-500 font-medium text-center rounded-lg cursor-not-allowed">
                                    Sudah Bergabung
                                </button>
                            @else
                                <form action="{{ route('murid.katalog.join', $kelas->kelas_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full py-2.5 bg-indigo-600 text-white font-medium text-center rounded-lg hover:bg-indigo-700 transition shadow-md shadow-indigo-200">
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
            {{ $catalogKelas->appends(['tab' => 'katalog', 'q' => request('q'), 'kategori' => request('kategori')])->links() }}
        </div>
    </div>
</div>

<!-- Alpine.js (Lightweight interactivity) -->
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
