@extends('layouts.app')

@section('title', 'Donasi - Ngajar.ID')

@section('content')
    <!-- Hero Section with Human Connection -->
    <!-- Hero Section with Human Connection -->
    <div class="relative bg-white overflow-hidden border-b border-gray-100">
        <div class="absolute inset-0 z-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/notebook.png')]">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Text Content -->
                <div class="flex-1 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 text-orange-600 rounded-full text-sm font-bold mb-6 animate-fade-in-down border border-orange-100">
                        <span class="material-symbols-rounded text-lg">favorite</span>
                        Gerakan #OrangBaik Indonesia
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-800 leading-tight mb-6">
                        Satu Donasi, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-blue-500">Sejuta
                            Mimpi.</span>
                    </h1>
                    <p class="text-lg text-slate-500 mb-8 max-w-xl mx-auto md:mx-0 leading-relaxed font-medium">
                        Bergabunglah dengan <span
                            class="font-extrabold text-slate-800">{{ number_format($donatur_count) }}</span> donatur lainnya
                        untuk membantu pelajar Indonesia mendapatkan hak pendidikan yang layak.
                    </p>

                    <!-- Floating CTA for Desktop -->
                    <div class="hidden md:flex gap-4">
                        <button onclick="document.getElementById('donasi-section').scrollIntoView({behavior: 'smooth'})"
                            class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-lg rounded-2xl shadow-xl shadow-teal-600/20 hover:shadow-teal-600/30 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 group">
                            <span class="material-symbols-rounded group-hover:animate-bounce">volunteer_activism</span>
                            Donasi Sekarang
                        </button>
                    </div>
                </div>

                <!-- Emotional Image -->
                <div class="flex-1 w-full relative">
                    <div
                        class="relative rounded-[2rem] overflow-hidden shadow-2xl border-8 border-white transform rotate-2 hover:rotate-0 transition-all duration-500 group">
                        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=1000&auto=format&fit=crop"
                            alt="Anak-anak belajar dengan bahagia" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>

                        <div
                            class="absolute bottom-8 left-8 right-8">
                            <div class="bg-white/90 backdrop-blur-md p-4 rounded-xl shadow-lg border border-white/50">
                                <blockquote class="text-slate-800 italic text-base font-medium mb-2">
                                    "Terima kasih Kakak Donatur, sekarang aku bisa beli buku paket matematika!"
                                </blockquote>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center text-xs font-bold text-teal-600">B</div>
                                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Budi, Siswa SDN 1 Harapan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Decoration element -->
                    <div
                        class="absolute -top-6 -left-6 w-24 h-24 bg-teal-400 rounded-full blur-3xl opacity-20 animate-pulse">
                    </div>
                    <div
                        class="absolute -bottom-6 -right-6 w-32 h-32 bg-orange-400 rounded-full blur-3xl opacity-20 animate-pulse delay-700">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">

        <!-- Donation Goal Card (Refined) -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-10 mb-16 relative overflow-hidden group">
            <!-- Decorative background elements (Subtle) -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-teal-50 to-blue-50 rounded-full -mr-32 -mt-32 opacity-70 group-hover:scale-105 transition-transform duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-orange-50 to-amber-50 rounded-full -ml-20 -mb-20 opacity-70"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-end mb-8">
                    <div>
                        <div class="inline-flex items-center gap-2 mb-2">
                             <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                             <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Live Campaign</h2>
                        </div>
                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <span class="text-4xl md:text-6xl font-black text-slate-800 tracking-tight">Rp
                                {{ number_format($total_donasi, 0, ',', '.') }}</span>
                            <span class="text-lg text-slate-400 font-medium">terkumpul dari Target</span>
                        </div>
                    </div>
                    <div class="text-right mt-6 md:mt-0 bg-white/80 backdrop-blur-sm p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="text-4xl font-extrabold text-teal-500">{{ number_format($progress_percentage, 1) }}%</span>
                        <p class="text-xs text-slate-400 font-bold uppercase mt-1">Target Tercapai</p>
                    </div>
                </div>

                <!-- Progress Bar (Sleek) -->
                <div class="relative w-full h-4 bg-gray-100 rounded-full overflow-hidden mb-10 ring-1 ring-gray-200/50">
                    <div class="absolute inset-0 bg-gray-50/50"></div>
                    <div class="h-full bg-gradient-to-r from-teal-400 via-teal-500 to-blue-500 rounded-full relative overflow-hidden transition-all duration-1000 ease-out"
                        style="width: {{ $progress_percentage }}%">
                        <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                    </div>
                </div>

                <!-- Impact Visuals (Clean Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors duration-300 border border-transparent hover:border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-teal-100/50 text-teal-600 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-2xl">school</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Rp 150rb</p>
                            <p class="text-sm text-slate-500 leading-snug">Menyekolahkan <strong>1 siswa</strong> selama satu bulan penuh.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors duration-300 border border-transparent hover:border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100/50 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-2xl">menu_book</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Rp 50rb</p>
                            <p class="text-sm text-slate-500 leading-snug">Satu paket <strong>alat tulis & buku</strong> lengkap.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors duration-300 border border-transparent hover:border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100/50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-2xl">wifi</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Rp 25rb</p>
                            <p class="text-sm text-slate-500 leading-snug">Subsidi <strong>kuota internet</strong> 10GB untuk belajar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12" id="donasi-section">
            <!-- Left Column: Story & Transparency -->
            <div class="lg:col-span-2 space-y-12">

                <!-- Transparansi Penyaluran (Icons) -->
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-1 bg-brand-500 rounded-full"></div>
                        <h3 class="text-2xl font-bold text-slate-900">Transparansi Penyaluran</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all text-center group">
                            <div
                                class="mx-auto w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-rounded text-3xl text-teal-600">school</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">40% Beasiswa</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Bantuan biaya sekolah & SPP bagi siswa prasejahtera berprestasi.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all text-center group">
                            <div
                                class="mx-auto w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-rounded text-3xl text-amber-600">auto_stories</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">35% Fasilitas Belajar</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Pengadaan modul, buku paket, dan alat tulis untuk kegiatan belajar.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all text-center group">
                            <div
                                class="mx-auto w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-rounded text-3xl text-indigo-600">wifi_tethering</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">25% Operasional Digital</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Subsidi kuota internet santri & pemeliharaan server platform.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Human Story Card -->
                <div class="bg-indigo-900 rounded-3xl p-8 md:p-12 relative overflow-hidden text-white">
                    <img src="https://images.unsplash.com/photo-1577896334614-201967484a3d?q=80&w=1000&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay" alt="Background">

                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center">
                        <div
                            class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white/30 overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?auto=format&fit=crop&q=80&w=200"
                                class="w-full h-full object-cover" alt="Founder">
                        </div>
                        <div>
                            <span class="material-symbols-rounded text-4xl text-white/50 mb-4 block">format_quote</span>
                            <p class="text-xl md:text-2xl font-medium leading-relaxed mb-6 font-serif">
                                "Pendidikan adalah tiket masa depan. Di Ngajar.ID, kami menyatukan niat baik Anda dengan
                                semangat belajar mereka yang tak pernah padam."
                            </p>
                            <div>
                                <h4 class="font-bold text-lg">Ahmad Fauzi</h4>
                                <p class="text-indigo-200 text-sm">Founder Ngajar.ID</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Donation History & CTA -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Floating Mobile CTA (Simulated for layout) -->
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 text-center sticky top-24 z-30">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Mari Berkontribusi</h3>
                    <p class="text-sm text-slate-500 mb-6">Pilih nominal donasi terbaikmu</p>

                    <button
                        class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 active:scale-95 transition-all mb-4 flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded">volunteer_activism</span>
                        Donasi Sekarang
                    </button>

                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400">
                        <span class="material-symbols-rounded text-base">lock</span>
                        Pembayaran Aman & Terverifikasi
                    </div>
                </div>

                <!-- History List (Activity Feed) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-slate-900">Donatur Terbaru</h3>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md">Live Update</span>
                    </div>

                    <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto scrollbar-hide">
                        @forelse($riwayat_donasi as $index => $donasi)
                            <div class="p-4 hover:bg-gray-50 transition-colors flex items-center gap-4 group">
                                <!-- Avatar Initials -->
                                @php
                                    $colors = ['bg-blue-100 text-blue-600', 'bg-pink-100 text-pink-600', 'bg-purple-100 text-purple-600', 'bg-orange-100 text-orange-600', 'bg-teal-100 text-teal-600'];
                                    $colorClass = $colors[$index % count($colors)];
                                    $initials = collect(explode(' ', $donasi['nama']))->map(function ($segment) {
                                        return strtoupper(substr($segment, 0, 1)); })->take(2)->join('');
                                @endphp

                                <div
                                    class="w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center font-bold text-sm shadow-sm group-hover:scale-110 transition-transform">
                                    {{ $initials }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm font-bold text-slate-900 truncate">
                                            {{ htmlspecialchars($donasi['nama']) }}</p>
                                        <p class="text-sm font-bold text-brand-600 whitespace-nowrap">Rp
                                            {{ number_format($donasi['jumlah'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($donasi['tanggal'])->diffForHumans() }}</span>
                                        @if($index < 3)
                                            <span
                                                class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                Baru Donasi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400">
                                <span class="material-symbols-rounded text-4xl mb-2 block opacity-50">savings</span>
                                <p>Belum ada donasi hari ini.</p>
                                <p class="text-sm">Jadilah yang pertama!</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="p-4 bg-gray-50/50 text-center border-t border-gray-100">
                        <button class="text-sm text-brand-600 font-bold hover:text-brand-700 hover:underline">Lihat Semua
                            Riwayat</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .stripe-animation {
            background-image: linear-gradient(45deg,
                    rgba(255, 255, 255, 0.15) 25%,
                    transparent 25%,
                    transparent 50%,
                    rgba(255, 255, 255, 0.15) 50%,
                    rgba(255, 255, 255, 0.15) 75%,
                    transparent 75%,
                    transparent);
            background-size: 1rem 1rem;
        }
    </style>
@endsection