@extends('layouts.dashboard')

@section('title', 'Dashboard Pengajar')
@section('header_title', 'Dashboard Relawan')

@section('content')
    <!-- Donation Notification (Delightful Interaction) -->
    <!-- Mock Logic: In real app, check session('new_donation') or latest donation timestamp -->
    @if(rand(0, 1))
        <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl flex items-center gap-4 relative overflow-hidden animate-fade-in-down"
            role="alert">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-rounded text-green-600 text-6xl">volunteer_activism</span>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 z-10">
                <span class="material-symbols-rounded text-green-600 text-2xl animate-bounce">celebration</span>
            </div>
            <div class="z-10">
                <h4 class="font-bold text-green-800 text-sm">Hore! Dukungan Baru Diterima!</h4>
                <p class="text-green-700 text-sm">
                    Seseorang baru saja mendonasikan <strong>Rp 50.000</strong> untuk mendukung operasional kelasmu. Terus
                    semangat mengajar!
                </p>
            </div>
            <button class="absolute top-4 right-4 text-green-400 hover:text-green-600 z-10"
                onclick="this.parentElement.remove()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
    @endif

    <!-- Welcome & Gamification Section -->
    <!-- Welcome & Gamification Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Profile & Welcome (Gamified - Light) -->
        <div
            class="lg:col-span-2 bg-white text-slate-800 p-8 rounded-2xl shadow-lg border border-teal-50 relative overflow-hidden group">
            <!-- Subtle Mesh Gradient -->
            <div
                class="absolute top-0 right-0 w-[500px] h-[500px] bg-gradient-to-br from-teal-50 via-blue-50 to-white rounded-full opacity-70 blur-3xl -mr-32 -mt-64 z-0 pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <!-- Circular Level Indicator -->
                <div class="relative w-28 h-28 flex-shrink-0">
                    <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 100 100">
                        <!-- Track -->
                        <circle cx="50" cy="50" r="45" fill="none" class="stroke-slate-100" stroke-width="8" />

                        @php
                            $percentage = min(100, ($gamification['poin'] / $gamification['next_target']) * 100);
                            $circumference = 2 * 3.14159 * 45;
                            $strokeDashoffset = $circumference - ($percentage / 100) * $circumference;
                        @endphp

                        <!-- Progress -->
                        <!-- Defined gradient in CSS or inline defs below -->
                        <defs>
                            <linearGradient id="gradientTeal" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#14b8a6;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#0d9488;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="url(#gradientTeal)" stroke-width="8"
                            stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $strokeDashoffset }}"
                            stroke-linecap="round" class="transition-all duration-1000 ease-out shadow-lg" />
                    </svg>

                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Level</span>
                        <span class="text-2xl font-black text-teal-600">{{ substr($gamification['level'], 8, 1) }}</span>
                    </div>
                </div>

                <div class="text-center md:text-left flex-1">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-3 mb-3">
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Halo,
                            {{ Auth::user()?->name ?? 'Pengajar' }}!</h2>
                        <span
                            class="px-3 py-1 bg-teal-50 text-teal-700 border border-teal-100 rounded-full text-xs font-extrabold uppercase tracking-wide">
                            {{ $gamification['level'] }}
                        </span>
                    </div>

                    <p class="text-slate-500 mb-6 max-w-lg leading-relaxed font-medium">
                        Terima kasih telah berbagi ilmu! Semangatmu adalah pelita bagi mereka yang membutuhkan.
                    </p>

                    <div
                        class="inline-flex items-center gap-3 bg-slate-50 px-5 py-3 rounded-xl border border-slate-100 shadow-sm transition-all hover:bg-white hover:shadow-md">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-rounded text-lg">lock_open</span>
                        </div>
                        <span class="text-sm text-slate-600">
                            Butuh <span class="text-amber-600 font-black">{{ $gamification['points_needed'] }} Poin</span>
                            lagi untuk sertifikat
                        </span>
                    </div>
                </div>

                <!-- Mini Leaderboard (Desktop) -->
                <div
                    class="hidden xl:block bg-gradient-to-b from-slate-50 to-white rounded-2xl p-5 border border-slate-100 w-64 flex-shrink-0 shadow-sm">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4 text-center">Top
                        Volunteer</h4>
                    <div class="space-y-4">
                        @foreach($leaderboard->take(3) as $index => $leader)
                            <div class="flex items-center gap-3 group/leader cursor-default">
                                <span
                                    class="flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-[10px] font-bold text-slate-500 group-hover/leader:bg-teal-50 group-hover/leader:text-teal-600 transition-colors">{{ $index + 1 }}</span>
                                <img src="{{ $leader['avatar'] }}" class="w-8 h-8 rounded-full ring-2 ring-white shadow-sm"
                                    alt="">
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-xs font-bold text-slate-700 truncate group-hover/leader:text-teal-700 transition-colors">
                                        {{ $leader['name'] }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $leader['poin'] }} Poin</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Status Card -->
        <div
            class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-teal-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>

            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-4 relative z-10">Status Sertifikat</h3>

            <div class="flex-1 flex flex-col items-center justify-center text-center mb-4 relative z-10">
                <div
                    class="w-16 h-16 rounded-full {{ $gamification['poin'] >= 1000 ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center mb-3 {{ $gamification['poin'] >= 1000 ? 'animate-bounce' : '' }}">
                    <span class="material-symbols-rounded text-3xl">workspace_premium</span>
                </div>
                <!-- Progress Line -->
                <div class="w-full bg-gray-100 h-2 rounded-full mb-2 overflow-hidden">
                    <div class="bg-teal-500 h-full rounded-full transition-all duration-1000"
                        style="width: {{ min(100, ($gamification['poin'] / 1000) * 100) }}%"></div>
                </div>
                <p class="text-xs text-slate-400">{{ $gamification['poin'] }} / 1000 Poin Total</p>
            </div>

            @if($gamification['poin'] < 1000)
                <button disabled
                    class="w-full py-2.5 bg-gray-100 text-gray-400 rounded-xl text-sm font-bold cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200">
                    <span class="material-symbols-rounded text-lg">lock</span>
                    Butuh {{ 1000 - $gamification['poin'] }} Poin Lagi
                </button>
            @else
                <a href="{{ route('pengajar.sertifikat.download') }}"
                    class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-teal-500/30 flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded text-lg">download</span>
                    Download Sertifikat
                </a>
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