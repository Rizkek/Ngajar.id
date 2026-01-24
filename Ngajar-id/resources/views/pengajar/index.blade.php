@extends('layouts.dashboard')

@section('title', 'Dashboard Pengajar')
@section('header_title', 'Dashboard Relawan')

@section('content')
    <!-- Welcome & Gamification Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Profile & Welcome -->
        <div
            class="lg:col-span-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white p-8 rounded-xl shadow-lg flex items-center gap-6">
            <div
                class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/30">
                <span class="material-symbols-rounded text-5xl text-white">person</span>
            </div>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold">Terima Kasih, Relawan!</h2>
                    <span
                        class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase tracking-wide backdrop-blur-sm border border-white/20">
                        {{ $gamification['level'] }}
                    </span>
                </div>
                <p class="text-lg opacity-90 italic">"Idealisme adalah kemewahan terakhir yang hanya dimiliki oleh pemuda."
                </p>
                <div class="mt-4 flex items-center gap-4">
                    <div class="flex-1 bg-black/20 rounded-full h-3 max-w-xs overflow-hidden">
                        @php
                            $percentage = min(100, ($gamification['poin'] / $gamification['next_target']) * 100);
                        @endphp
                        <div class="bg-yellow-400 h-full rounded-full transition-all duration-1000"
                            style="width: {{ $percentage }}%"></div>
                    </div>
                    <span class="text-sm font-medium">{{ $gamification['poin'] }} / {{ $gamification['next_target'] }}
                        Poin</span>
                </div>
            </div>
        </div>

        <!-- Level Stats/Certificate -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-center">
            <h3 class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-4">Pencapaian Anda</h3>

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-{{ $gamification['badge_color'] }}-100 flex items-center justify-center text-{{ $gamification['badge_color'] }}-600">
                        <span class="material-symbols-rounded">military_tech</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Level Saat Ini</p>
                        <p class="font-bold text-slate-800">{{ $gamification['level'] }}</p>
                    </div>
                </div>
            </div>

            @if($gamification['level'] == 'Relawan Tunas')
                <button disabled
                    class="mt-auto w-full py-2 bg-gray-100 text-gray-400 rounded-lg text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded text-lg">lock</span>
                    Sertifikat Terkunci
                </button>
            @else
                <button
                    class="mt-auto w-full py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded text-lg">workspace_premium</span>
                    Klaim Sertifikat
                </button>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Card 1 -->
        <div
            class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                <span class="material-symbols-rounded text-2xl">school</span>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Kelas Dibina</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_kelas'] ?? 0 }}</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div
            class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
                <span class="material-symbols-rounded text-2xl">menu_book</span>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Materi Dibuat</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_materi'] ?? 0 }}</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div
            class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                <span class="material-symbols-rounded text-2xl">groups</span>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Siswa Mengikuti</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_siswa'] ?? 0 }}</p>
            </div>
        </div>
        <!-- Card 4 (Poin) -->
        <div
            class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                <span class="material-symbols-rounded text-2xl">star</span>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Total Poin</p>
                <p class="text-2xl font-bold text-slate-800">{{ $gamification['poin'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-7 rounded-xl shadow-md">
        <h3 class="text-xl font-semibold text-teal-500 mb-6">Kelas Yang Dibina</h3>
        <div class="space-y-4">
            @forelse($kelasList as $kelas)
                <a href="#" class="block hover:shadow-lg transition-shadow">
                    <div
                        class="border border-gray-200 rounded-lg p-4 flex items-center space-x-4 hover:shadow-sm hover:border-teal-500 transition-all">
                        <div class="p-3 bg-teal-100 rounded-lg flex-shrink-0">
                            <span class="material-symbols-rounded text-3xl text-teal-600">co_present</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-base text-gray-800">{{ $kelas['judul'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $kelas['total_siswa'] }} siswa aktif</p>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 text-center py-4">Belum ada kelas yang dibina.</p>
            @endforelse
        </div>
    </div>
@endsection