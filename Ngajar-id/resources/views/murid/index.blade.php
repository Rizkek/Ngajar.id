@extends('layouts.dashboard')

@section('title', 'Dashboard Murid')
@section('header_title', 'Dashboard Murid')

@section('content')
    <section class="mb-8">
        <div class="mb-4">
            <h3 class="inline-flex items-center text-3xl font-bold text-teal-500">
                <span>Materi Pembelajaran</span>
            </h3>
        </div>

        <div class="border-l-4 border-r-4 border-b-4 border-[#003F4A] shadow-lg rounded-xl p-6 bg-white">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($materiList as $materi)
                    <a href="#" class="block hover:shadow-lg transition-shadow border border-gray-200 rounded-lg p-4 group">
                        <p class="text-lg font-bold text-teal-600 group-hover:text-teal-700">
                            {{ $materi['judul'] }}
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            Kelas: {{ $materi['kelas'] }}
                        </p>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 col-span-full text-center py-4">Belum ada materi yang tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section>
        <div class="mb-4">
            <h3 class="text-3xl font-bold text-teal-500 mb-6"> Modul Ngajar.ID</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($modulList as $modul)
                    <div class="rounded-xl shadow-md border border-teal-500 hover:shadow-lg transition duration-300">
                        <div class="bg-white rounded-xl p-5 relative group h-full flex flex-col">
                            <h2 class="text-lg font-bold text-teal-700 mb-2">
                                {{ $modul['judul'] }}
                            </h2>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">
                                {{ $modul['deskripsi'] }}
                            </p>

                            <div class="mt-auto">
                                @if($modul['sudah_dibeli'])
                                    <button
                                        class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full cursor-default">
                                        Sudah Dibeli
                                    </button>
                                @else
                                    <button
                                        class="flex items-center gap-2 bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full hover:bg-yellow-200 transition">
                                        <span class="material-symbols-rounded text-yellow-600 text-sm">token</span> Beli
                                        {{ $modul['harga'] }} Token
                                    </button>
                                @endif
                            </div>

                            <div
                                class="absolute top-2 right-2 bg-teal-500 text-white text-xs font-bold px-2 py-1 rounded-lg opacity-90">
                                MODUL
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-full text-center py-4">Belum ada modul yang tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection