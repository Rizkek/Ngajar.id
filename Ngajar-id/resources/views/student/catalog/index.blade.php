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
            <form action="{{ route('student.katalog') }}" method="GET" class="relative">
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
            <x-empty-state 
                icon="search_off" 
                title="Kelas tidak ditemukan" 
                description="Coba kata kunci lain atau reset filter."
                actionLabel="Lihat Semua Kelas"
                actionUrl="{{ route('student.katalog') }}"
            />
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($allKelas as $kelas)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <x-cards.course-card 
                            title="{{ $kelas->judul }}"
                            author="{{ $kelas->pengajar->name }}"
                            category="{{ $kelas->kategori ?? 'Umum' }}"
                            image="{{ $kelas->thumbnail ? asset('storage/' . $kelas->thumbnail) : 'https://ui-avatars.com/api/?name='.urlencode($kelas->judul).'&background=0d9488&color=fff&size=400' }}"
                            url="{{ in_array($kelas->kelas_id, $enrolledKelasIds) ? route('belajar.show', $kelas->kelas_id) : '#' }}"
                        >
                            <x-slot name="footer">
                                @if(in_array($kelas->kelas_id, $enrolledKelasIds))
                                    <x-buttons.primary href="{{ route('belajar.show', $kelas->kelas_id) }}" fullWidth="true" color="green" class="shadow-md shadow-green-200">
                                        Lanjut Belajar
                                    </x-buttons.primary>
                                @else
                                    <form action="{{ route('student.katalog.join', $kelas->kelas_id) }}" method="POST">
                                        @csrf
                                        <x-buttons.primary type="submit" fullWidth="true" color="teal" class="shadow-md shadow-teal-200">
                                            Gabung Kelas
                                        </x-buttons.primary>
                                    </form>
                                @endif
                            </x-slot>
                        </x-cards.course-card>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $allKelas->links() }}
            </div>
        @endif
    </div>
@endsection
