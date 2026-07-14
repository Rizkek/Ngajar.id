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
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center shrink-0 z-10">
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

    <!-- Welcome & Stats Section (Professional) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Profile & Welcome (Simplified) -->
        <div
            class="lg:col-span-2 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl shadow-lg p-6 sm:p-8 text-white h-full relative overflow-hidden flex flex-col justify-center">
            <!-- Subtle Mesh Gradient -->
            <div
                class="absolute top-0 right-0 w-[500px] h-[500px] bg-gradient-to-br from-teal-50 via-blue-50 to-white rounded-full opacity-70 blur-3xl -mr-32 -mt-64 z-0 pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-start gap-8">
                <!-- Profile Summary (No Gamification) -->
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-4">
                        <div>
                            <h2 class="text-3xl font-black text-white">Halo, {{ Auth::user()?->name ?? 'Pengajar' }}! 👋
                            </h2>
                            <p class="text-teal-50 mt-2 max-w-lg leading-relaxed font-medium">
                                Dashboard relawan. Kelola kelas, pantau siswa, dan bagikan dampakmu.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Stats Row -->
                    <div class="grid grid-cols-3 gap-3 mt-6">
                        <div class="bg-white/10 rounded-lg p-3 text-center backdrop-blur-sm">
                            <p class="text-xs text-teal-100 font-semibold">Kelas Aktif</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_kelas'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3 text-center backdrop-blur-sm">
                            <p class="text-xs text-teal-100 font-semibold">Siswa</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_siswa'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/10 rounded-lg p-3 text-center backdrop-blur-sm">
                            <p class="text-xs text-teal-100 font-semibold">Rating</p>
                            <p class="text-2xl font-bold text-white">4.8⭐</p>
                        </div>
                    </div>
                </div>

                <!-- Wallet / Earnings Summary -->
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 w-full md:w-48">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-teal-100 mb-2">Pendapatan Bulan Ini</p>
                    <div class="text-2xl font-black text-white mb-3">Rp 0</div>
                    <button
                        class="w-full py-2 bg-white text-teal-600 hover:bg-teal-50 text-xs font-bold rounded-lg transition-all">
                        Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Class Analytics Card -->
        <div
            class="bg-white rounded-xl shadow-lg p-6 sm:p-8 h-full border border-gray-100 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-teal-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>

            <h3 class="text-slate-600 text-xs font-bold uppercase tracking-wider mb-4 relative z-10">Analitik Kelas</h3>

            <div class="flex-1 flex flex-col items-center justify-center text-center mb-4 relative z-10">
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                    <span class="material-symbols-rounded text-3xl">trending_up</span>
                </div>
                <p class="text-xs text-slate-400 font-medium mb-2">Completion Rate</p>
                <p class="text-2xl font-black text-blue-600">72%</p>
            </div>

            <a href="{{ route('teacher.analytics') }}"
                class="w-full py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-rounded text-lg">analytics</span>
                Lihat Analitik
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Card 1 -->
        <div data-aos="fade-up" data-aos-delay="100"
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
        <div data-aos="fade-up" data-aos-delay="200"
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
        <div data-aos="fade-up" data-aos-delay="300"
            class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                <span class="material-symbols-rounded text-2xl">groups</span>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Siswa Mengikuti</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_siswa'] ?? 0 }}</p>
            </div>
        </div>
        <!-- Card 4 (Token Earnings) -->
        <div data-aos="fade-up" data-aos-delay="400"
            class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                <span class="material-symbols-rounded text-2xl">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Saldo Token</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['token_balance'] ?? 0) }}</p>
                <p class="text-xs text-green-600 font-medium">+{{ number_format($stats['token_earnings'] ?? 0) }} total
                    pendapatan</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-7 rounded-xl shadow-md" data-aos="fade-up" data-aos-delay="500">
        <h3 class="text-xl font-semibold text-teal-500 mb-6">Kelas Yang Dibina</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kelasList as $kelas)
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <x-cards.course-card 
                        title="{{ $kelas['judul'] }}"
                        author="{{ $kelas['total_siswa'] }} siswa aktif"
                        category="Kelola"
                        image="https://ui-avatars.com/api/?name={{ urlencode($kelas['judul']) }}&background=0d9488&color=fff&size=400"
                        url="{{ route('teacher.courses.edit', $kelas['kelas_id'] ?? 1) }}"
                    >
                        <x-slot name="footer">
                            <x-buttons.secondary href="{{ route('teacher.courses.edit', $kelas['kelas_id'] ?? 1) }}" fullWidth="true" class="mt-2 border-teal-200 text-teal-700 hover:bg-teal-50">
                                <x-icons.material name="edit" size="sm" class="mr-2" />
                                Kelola Kelas
                            </x-buttons.secondary>
                        </x-slot>
                    </x-cards.course-card>
                </div>
            @empty
                <div class="col-span-full">
                    <x-empty-state 
                        icon="co_present" 
                        title="Belum ada kelas yang dibina" 
                        description="Mulai buat kelas pertamamu dan bagikan ilmumu."
                        actionLabel="Buat Kelas Baru"
                        actionUrl="{{ route('teacher.courses.create') }}"
                        actionIcon="add_circle"
                    />
                </div>
            @endforelse
        </div>
    </div>
@endsection

