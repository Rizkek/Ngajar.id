@extends('layouts.dashboard')

@section('title', 'Dashboard Murid - Ngajar.ID')
@section('header_title', 'Dashboard')

@section('content')
    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();
    @endphp
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Halo, {{ $user->name }}! 👋</h1>
        <p class="text-slate-600 mt-1">"Pendidikan adalah senjata paling ampuh untuk mengubah dunia." - Nelson Mandela</p>
    </div>

    <!-- Stats & Gamification Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- XP & Level Card -->
        <div
            class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-rounded text-9xl">military_tech</span>
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-indigo-200 text-sm font-medium uppercase tracking-wider">Level Saat Ini</p>
                        <h2 class="text-4xl font-black mt-1">{{ $userStats['level'] }}</h2>
                    </div>
                    <div class="bg-indigo-500/30 p-2 rounded-lg backdrop-blur-sm">
                        <span class="material-symbols-rounded text-yellow-300 text-3xl">workspace_premium</span>
                    </div>
                </div>

                <div class="mb-2 flex justify-between text-sm">
                    <span class="font-bold text-indigo-100">{{ $userStats['xp'] }} XP</span>
                    <span class="text-indigo-300">Target: {{ $userStats['xp_next_level'] }} XP</span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-indigo-900/50 rounded-full h-3">
                    <div class="bg-yellow-400 h-3 rounded-full transition-all duration-1000"
                        style="width: {{ ($userStats['xp'] / $userStats['xp_next_level']) * 100 }}%"></div>
                </div>
                <p class="text-xs text-indigo-300 mt-2">Dapatkan +50 XP setiap menyelesaikan materi!</p>
            </div>
        </div>

        <!-- Token Wallet Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-rounded text-amber-500 text-3xl">token</span>
                    <h3 class="text-lg font-bold text-slate-700">Saldo Token</h3>
                </div>
                <h2 class="text-4xl font-black text-slate-900">{{ number_format($userStats['token_balance']) }}</h2>
                <p class="text-slate-500 text-sm mt-1">Gunakan untuk membeli modul premium.</p>
            </div>
            <button onclick="openTopupModal()"
                class="mt-4 w-full py-2 bg-amber-50 text-amber-700 font-bold rounded-xl hover:bg-amber-100 transition text-center text-sm border border-amber-200 flex items-center justify-center gap-2">
                <span class="material-symbols-rounded text-lg">add_circle</span>
                Top Up Token
            </button>
        </div>

        <!-- Weekly Activity Chart (Simplified) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative">
            <h3 class="text-lg font-bold text-slate-700 mb-4">Aktivitas Belajar</h3>
            <div class="flex items-end justify-between h-32 gap-2">
                @foreach($activityChart['data'] as $index => $val)
                    <div class="w-full bg-teal-100 rounded-t-lg relative group transition-all hover:bg-teal-200"
                        style="height: {{ $val * 8 }}%">
                        <div
                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                            {{ $val }} Jam
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-xs text-slate-400 font-medium">
                @foreach($activityChart['labels'] as $label)
                    <span>{{ $label }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column: Continue Learning -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Continue Learning Card -->
            @if($lastClass)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-teal-50/50">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span class="material-symbols-rounded text-teal-600">history</span>
                            Lanjutkan Belajar
                        </h2>
                        <span class="text-xs bg-teal-100 text-teal-700 px-2 py-1 rounded font-bold">Terakhir Diakses</span>
                    </div>
                    <div class="p-6 md:flex gap-6 items-center">
                        <!-- Class Thumbnail -->
                        <div class="w-full md:w-48 h-32 bg-gray-200 rounded-xl shrink-0 relative overflow-hidden mb-4 md:mb-0">
                            @if($lastClass->thumbnail)
                                <img src="{{ asset('storage/' . $lastClass->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300">
                                    <span class="material-symbols-rounded text-slate-400 text-4xl">school</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition"></div>
                        </div>

                        <!-- Class Info -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-900 mb-1 group-hover:text-teal-600 transition">
                                {{ $lastClass->judul }}
                            </h3>
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ $lastClass->deskripsi }}</p>

                            <div class="flex items-center gap-4 mb-4 text-sm text-slate-600">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-lg text-slate-400">person</span>
                                    {{ $lastClass->pengajar->name }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-lg text-slate-400">menu_book</span>
                                    12 Materi
                                </span>
                            </div>

                            <a href="{{ route('belajar.show', ['kelas_id' => $lastClass->kelas_id]) }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition shadow-lg shadow-teal-200">
                                <span>Lanjut Belajar</span>
                                <span class="material-symbols-rounded text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State for New Users -->
                <div class="bg-indigo-50 rounded-2xl p-8 text-center border-2 border-dashed border-indigo-200">
                    <div
                        class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-600">
                        <span class="material-symbols-rounded text-4xl">rocket_launch</span>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-900 mb-2">Mulai Petualangan Belajarmu!</h3>
                    <p class="text-indigo-700 mb-6 max-w-md mx-auto">Anda belum mengikuti kelas apapun. Yuk jelajahi katalog
                        kelas kami dan temukan skill baru.</p>
                    <a href="{{ route('murid.katalog') }}"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg transition inline-flex items-center gap-2">
                        <span class="material-symbols-rounded">manage_search</span>
                        Jelajah Kelas
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Column: Recommendations -->
        <div class="space-y-6">
            <!-- Category Filter Tabs -->
            @if($availableCategories->isNotEmpty())
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <h4 class="text-sm font-semibold text-slate-700 mb-3">Filter Kategori:</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('murid.dashboard') }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ !$selectedKategori ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua
                        </a>
                        @foreach($availableCategories as $cat)
                            <a href="{{ route('murid.dashboard', ['kategori' => $cat]) }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $selectedKategori == $cat ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $cat }}
                                @if(isset($categoryStats[$cat]))
                                    <span class="ml-1 opacity-75">({{ $categoryStats[$cat]['total'] }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- My Classes Section -->
            @if($myClasses->isNotEmpty())
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-800 text-lg">
                            Kelas Saya
                            @if($selectedKategori)
                                <span class="text-sm font-normal text-slate-500">- {{ $selectedKategori }}</span>
                            @endif
                        </h3>
                        <span class="text-xs text-slate-500">{{ $myClasses->count() }} kelas</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($myClasses->take(3) as $kelas)
                            <a href="{{ route('belajar.show', ['kelas_id' => $kelas->kelas_id]) }}"
                                class="block bg-white rounded-lg p-3 shadow-sm border border-gray-100 hover:shadow-md hover:border-teal-500 transition group">
                                <div class="flex gap-3">
                                    @php
                                        $bgClass = 'bg-gray-100 text-gray-500';
                                        $icon = 'school';
                                        switch ($kelas->kategori) {
                                            case 'Programming':
                                                $bgClass = 'bg-teal-100 text-teal-600';
                                                $icon = 'code';
                                                break;
                                            case 'Design':
                                                $bgClass = 'bg-pink-100 text-pink-600';
                                                $icon = 'palette';
                                                break;
                                            case 'Business':
                                                $bgClass = 'bg-blue-100 text-blue-600';
                                                $icon = 'trending_up';
                                                break;
                                            case 'Marketing':
                                                $bgClass = 'bg-orange-100 text-orange-600';
                                                $icon = 'campaign';
                                                break;
                                            case 'Soft Skills':
                                                $bgClass = 'bg-purple-100 text-purple-600';
                                                $icon = 'psychology';
                                                break;
                                        }
                                    @endphp
                                    <div class="w-14 h-14 {{ $bgClass }} rounded-lg shrink-0 flex items-center justify-center">
                                        @if($kelas->thumbnail)
                                            <img src="{{ asset('storage/' . $kelas->thumbnail) }}"
                                                class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <span class="material-symbols-rounded text-2xl">{{ $icon }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="font-semibold text-slate-900 text-sm truncate group-hover:text-teal-600 transition">
                                            {{ $kelas->judul }}
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $kelas->pengajar->name }}</p>
                                        @if($kelas->kategori)
                                            <span
                                                class="inline-block mt-1 px-2 py-0.5 bg-teal-50 text-teal-700 text-xs rounded font-medium">
                                                {{ $kelas->kategori }}
                                            </span>
                                        @endif
                                    </div>
                                    <span
                                        class="material-symbols-rounded text-slate-300 group-hover:text-teal-500 transition">chevron_right</span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if($myClasses->count() > 3)
                        <a href="{{ route('murid.kelas') }}" class="block mt-3 text-center text-sm text-teal-600 hover:underline">
                            Lihat semua kelas saya ({{ $myClasses->count() }})
                        </a>
                    @endif
                </div>
            @endif

            <!-- Recommendations -->
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-slate-800 text-lg">
                    Rekomendasi Kelas
                    @if($selectedKategori)
                        <span class="text-sm font-normal text-slate-500">- {{ $selectedKategori }}</span>
                    @endif
                </h3>
                <a href="{{ route('murid.katalog', $selectedKategori ? ['kategori' => $selectedKategori] : []) }}"
                    class="text-sm text-teal-600 hover:underline">Lihat Semua</a>
            </div>

            @forelse($recommendedClasses->take(3) as $kelas)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition group relative">
                    @if($kelas->kategori)
                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-teal-50 text-teal-700 text-xs rounded font-medium">
                            {{ $kelas->kategori }}
                        </span>
                    @endif
                    <div class="flex gap-4">
                        @php
                            $bgClass = 'bg-gray-100 text-gray-400';
                            $icon = 'image';
                            switch ($kelas->kategori) {
                                case 'Programming':
                                    $bgClass = 'bg-teal-100 text-teal-600';
                                    $icon = 'code';
                                    break;
                                case 'Design':
                                    $bgClass = 'bg-pink-100 text-pink-600';
                                    $icon = 'palette';
                                    break;
                                case 'Business':
                                    $bgClass = 'bg-blue-100 text-blue-600';
                                    $icon = 'trending_up';
                                    break;
                                case 'Marketing':
                                    $bgClass = 'bg-orange-100 text-orange-600';
                                    $icon = 'campaign';
                                    break;
                                case 'Soft Skills':
                                    $bgClass = 'bg-purple-100 text-purple-600';
                                    $icon = 'psychology';
                                    break;
                            }
                        @endphp
                        <div class="w-16 h-16 {{ $bgClass }} rounded-lg shrink-0 flex items-center justify-center">
                            @if($kelas->thumbnail)
                                <img src="{{ asset('storage/' . $kelas->thumbnail) }}"
                                    class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="material-symbols-rounded text-3xl">{{ $icon }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-900 text-sm truncate group-hover:text-teal-600 transition">
                                {{ $kelas->judul }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-1 mb-2">{{ $kelas->pengajar->name }}</p>

                            <form action="{{ route('murid.katalog.join', $kelas->kelas_id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">add</span> Gabung
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-slate-50 rounded-lg p-6 text-center">
                    <span class="material-symbols-rounded text-slate-300 text-4xl mb-2">search_off</span>
                    <p class="text-sm text-slate-500">
                        @if($selectedKategori)
                            Tidak ada kelas {{ $selectedKategori }} yang tersedia saat ini.
                        @else
                            Belum ada rekomendasi kelas tersedia.
                        @endif
                    </p>
                </div>
            @endforelse

            <!-- Quick Link to Modules -->
            <a href="{{ route('murid.materi') }}"
                class="block p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 hover:border-amber-300 transition group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 group-hover:scale-110 transition">
                        <span class="material-symbols-rounded">library_books</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-900">Modul Tambahan</h4>
                        <p class="text-xs text-amber-700">Explorasi e-book & cheat sheet</p>
                    </div>
                    <span
                        class="material-symbols-rounded text-amber-400 ml-auto group-hover:translate-x-1 transition">chevron_right</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Topup Modal -->
    <div id="topupModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-5 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Top Up Token</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Pilih nominal token yang ingin dibeli</p>
                </div>
                <button onclick="closeTopupModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <!-- Current Balance -->
            <div class="bg-slate-50 border-b border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600 mb-1">Saldo Token Saat Ini</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($userStats['token_balance']) }} <span
                                class="text-base font-normal text-slate-500">Token</span></p>
                    </div>
                    <span class="material-symbols-rounded text-amber-500 text-4xl">account_balance_wallet</span>
                </div>
            </div>

            <!-- Token Packages -->
            <div class="p-5">
                <h3 class="font-semibold text-slate-700 mb-3 text-sm">Pilih Paket Token:</h3>

                <div class="space-y-3">
                    <!-- Package 1: Starter -->
                    <div class="border-2 border-gray-200 hover:border-teal-500 rounded-lg p-4 cursor-pointer transition group"
                        onclick="selectPackage(10000, 50)">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-2xl font-bold text-slate-900">50</span>
                                    <span class="text-sm text-slate-500">Token</span>
                                </div>
                                <p class="text-xs text-slate-500">Paket Starter</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-teal-600">Rp 10.000</p>
                                <p class="text-xs text-slate-400">Rp 200/token</p>
                            </div>
                        </div>
                    </div>

                    <!-- Package 2: Pro (Popular) -->
                    <div class="border-2 border-teal-500 bg-teal-50/50 rounded-lg p-4 cursor-pointer transition group relative"
                        onclick="selectPackage(25000, 150)">
                        <span
                            class="absolute -top-2 -right-2 bg-teal-500 text-white text-xs font-semibold px-2 py-0.5 rounded">POPULER</span>
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-2xl font-bold text-slate-900">150</span>
                                    <span class="text-sm text-slate-500">Token</span>
                                    <span class="text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded font-medium">+25
                                        Bonus</span>
                                </div>
                                <p class="text-xs text-slate-500">Paket Pro - Hemat 20%</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-teal-600">Rp 25.000</p>
                                <p class="text-xs text-slate-400">Rp 167/token</p>
                            </div>
                        </div>
                    </div>

                    <!-- Package 3: Premium -->
                    <div class="border-2 border-gray-200 hover:border-purple-500 rounded-lg p-4 cursor-pointer transition group"
                        onclick="selectPackage(50000, 350)">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-2xl font-bold text-slate-900">350</span>
                                    <span class="text-sm text-slate-500">Token</span>
                                    <span
                                        class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-medium">+50
                                        Bonus</span>
                                </div>
                                <p class="text-xs text-slate-500">Paket Premium - Hemat 30%</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-purple-600">Rp 50.000</p>
                                <p class="text-xs text-slate-400">Rp 143/token</p>
                            </div>
                        </div>
                    </div>

                    <!-- Package 4: Ultimate -->
                    <div class="border-2 border-gray-200 hover:border-amber-500 rounded-lg p-4 cursor-pointer transition group"
                        onclick="selectPackage(100000, 800)">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-2xl font-bold text-slate-900">800</span>
                                    <span class="text-sm text-slate-500">Token</span>
                                    <span class="text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-medium">+100
                                        Bonus</span>
                                </div>
                                <p class="text-xs text-slate-500">Paket Ultimate - Hemat 40%</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-amber-600">Rp 100.000</p>
                                <p class="text-xs text-slate-400">Rp 125/token</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Package Summary -->
                <div id="selectedPackageInfo" class="hidden bg-slate-50 border border-slate-200 rounded-lg p-4 mt-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-600 mb-1">Total Token</p>
                            <p class="text-lg font-bold text-slate-900" id="selectedTokens">-</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-600 mb-1">Total Pembayaran</p>
                            <p class="text-lg font-bold text-teal-600" id="selectedPrice">Rp 0</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <button id="payButton" disabled onclick="processPayment()"
                    class="w-full py-3 bg-gray-300 text-gray-600 rounded-lg font-semibold cursor-not-allowed flex items-center justify-center gap-2 mt-4 transition">
                    <span class="material-symbols-rounded text-lg">lock</span>
                    Pilih Paket Terlebih Dahulu
                </button>

                <!-- Payment Info -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mt-4">
                    <div class="flex gap-2">
                        <span class="material-symbols-rounded text-blue-600 text-lg">info</span>
                        <div class="text-xs text-blue-900">
                            <p class="font-semibold mb-1">Metode Pembayaran</p>
                            <p class="text-blue-700">Pembayaran melalui Xendit. Tersedia Transfer Bank, E-Wallet (GoPay,
                                OVO, Dana), dan Kartu Kredit.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedAmount = 0;
        let selectedTokens = 0;

        function openTopupModal() {
            const modal = document.getElementById('topupModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeTopupModal() {
            const modal = document.getElementById('topupModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            selectedAmount = 0;
            selectedTokens = 0;
            document.getElementById('selectedPackageInfo').classList.add('hidden');
            document.getElementById('payButton').disabled = true;
            document.getElementById('payButton').className = 'w-full py-3 bg-gray-300 text-gray-600 rounded-lg font-semibold cursor-not-allowed flex items-center justify-center gap-2 mt-4 transition';
            document.getElementById('payButton').innerHTML = '<span class="material-symbols-rounded text-lg">lock</span> Pilih Paket Terlebih Dahulu';
        }

        function selectPackage(amount, tokens) {
            selectedAmount = amount;
            selectedTokens = tokens;

            // Update UI
            document.getElementById('selectedTokens').textContent = tokens + ' Token';
            document.getElementById('selectedPrice').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('selectedPackageInfo').classList.remove('hidden');

            // Enable pay button
            const payButton = document.getElementById('payButton');
            payButton.disabled = false;
            payButton.className = 'w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold flex items-center justify-center gap-2 mt-4 transition';
            payButton.innerHTML = '<span class="material-symbols-rounded text-lg">payment</span> Bayar Sekarang - Rp ' + amount.toLocaleString('id-ID');
        }

        function processPayment() {
            if (selectedAmount === 0 || selectedTokens === 0) {
                alert('Silakan pilih paket terlebih dahulu!');
                return;
            }

            const payButton = document.getElementById('payButton');
            const originalText = payButton.innerHTML;
            payButton.disabled = true;
            payButton.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

            fetch('{{ route("topup.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    amount: selectedAmount,
                    tokens: selectedTokens
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.invoice_url) {
                        // Redirect ke Xendit Invoice
                        window.location.href = data.invoice_url;
                    } else {
                        alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                        payButton.disabled = false;
                        payButton.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.innerHTML = originalText;
                });
        }

        // Close modal on outside click
        document.getElementById('topupModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeTopupModal();
            }
        });
    </script>


@endsection