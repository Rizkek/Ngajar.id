@extends('layouts.app')

@section('title', 'Donasi - Ngajar.ID')

@push('head-scripts')
    <!-- Script Midtrans (Untuk Payment Gateway) -->
    <script type="text/javascript"
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@section('content')
    <!-- Bagian Hero: Judul & Gambar Utama -->
    <!-- Hero Section with Human Connection -->
    <div class="relative bg-white overflow-hidden border-b border-gray-100">
        <div
            class="absolute inset-0 z-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/notebook.png')]">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Konten Teks -->
                <div class="flex-1 text-center md:text-left">
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

                    <!-- Tombol Floating (Desktop Only) -->
                    <div class="hidden md:flex gap-4">
                        <button onclick="document.getElementById('donasi-section').scrollIntoView({behavior: 'smooth'})"
                            class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-lg rounded-2xl shadow-xl shadow-teal-600/20 hover:shadow-teal-600/30 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 group">
                            <span class="material-symbols-rounded group-hover:animate-bounce">volunteer_activism</span>
                            Donasi Sekarang
                        </button>
                    </div>
                </div>

                <!-- Gambar Ilustrasi Emosional -->
                <div class="flex-1 w-full relative">
                    <div
                        class="relative rounded-4xl overflow-hidden shadow-2xl border-8 border-white transform rotate-2 hover:rotate-0 transition-all duration-500 group">
                        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=1000&auto=format&fit=crop"
                            alt="Anak-anak belajar dengan bahagia"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>

                        <div class="absolute bottom-8 left-8 right-8">
                            <div class="bg-white/90 backdrop-blur-md p-4 rounded-xl shadow-lg border border-white/50">
                                <blockquote class="text-slate-800 italic text-base font-medium mb-2">
                                    "Terima kasih Kakak Donatur, sekarang aku bisa beli buku paket matematika!"
                                </blockquote>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center text-xs font-bold text-teal-600">
                                        B</div>
                                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Budi, Siswa SDN 1
                                        Harapan</p>
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

    <!-- Area Konten Utama -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">

        <!-- Kartu Total Donasi (Tanpa Target) -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-10 mb-16 relative overflow-hidden group">
            <!-- Decorative background elements (Subtle) -->
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-teal-50 to-blue-50 rounded-full -mr-32 -mt-32 opacity-70 group-hover:scale-105 transition-transform duration-1000">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-orange-50 to-amber-50 rounded-full -ml-20 -mb-20 opacity-70">
            </div>

            <div class="relative z-10">
                <div class="text-center mb-12">
                    <div
                        class="inline-flex items-center gap-2 mb-4 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-green-700 uppercase tracking-widest">Dana Tersalurkan</span>
                    </div>

                    <h2 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight mb-2">
                        Rp {{ number_format($total_donasi, 0, ',', '.') }}
                    </h2>
                    <p class="text-lg text-slate-500 font-medium">Telah digunakan untuk membiayai pendidikan anak Indonesia
                    </p>
                </div>

                <!-- Grid Visual Dampak Donasi -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors duration-300 border border-transparent hover:border-slate-100">
                        <div
                            class="w-12 h-12 rounded-2xl bg-teal-100/50 text-teal-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-rounded text-2xl">school</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Rp 150rb</p>
                            <p class="text-sm text-slate-500 leading-snug">Menyekolahkan <strong>1 siswa</strong> selama
                                satu bulan penuh.</p>
                        </div>
                    </div>

                    <div
                        class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors duration-300 border border-transparent hover:border-slate-100">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-100/50 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-rounded text-2xl">menu_book</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Rp 50rb</p>
                            <p class="text-sm text-slate-500 leading-snug">Satu paket <strong>alat tulis & buku</strong>
                                lengkap.</p>
                        </div>
                    </div>

                    <div
                        class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition-colors duration-300 border border-transparent hover:border-slate-100">
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-100/50 text-indigo-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-rounded text-2xl">wifi</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">Rp 25rb</p>
                            <p class="text-sm text-slate-500 leading-snug">Subsidi <strong>kuota internet</strong> 10GB
                                untuk belajar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12" id="donasi-section">
            <!-- Kolom Kiri: Cerita & Info Transparansi -->
            <div class="lg:col-span-2 space-y-12">

                <!-- Info Transparansi Penyaluran -->
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

                <!-- Kartu Cerita Founder -->
                <div class="bg-indigo-900 rounded-3xl p-8 md:p-12 relative overflow-hidden text-white">
                    <img src="https://images.unsplash.com/photo-1577896334614-201967484a3d?q=80&w=1000&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay" alt="Background">

                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center">
                        <div
                            class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white/30 overflow-hidden shrink-0">
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

            <!-- Kolom Kanan: Form Donasi & Riwayat -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Card Form Donasi -->
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 text-center top-24">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Mari Berkontribusi</h3>
                    <p class="text-sm text-slate-500 mb-6">Pilih nominal donasi terbaikmu</p>

                    <!-- Pilihan Tombol Nominal -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <button onclick="selectAmount(25000)" data-amount="25000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-teal-500 hover:text-teal-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">Kuota</div>
                            Rp 25rb
                        </button>
                        <button onclick="selectAmount(50000)" data-amount="50000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-teal-500 hover:text-teal-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">Alat Tulis</div>
                            Rp 50rb
                        </button>
                        <button onclick="selectAmount(100000)" data-amount="100000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-teal-500 hover:text-teal-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">Makanan</div>
                            Rp 100rb
                        </button>
                        <button onclick="selectAmount(150000)" data-amount="150000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-teal-500 hover:text-teal-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">1 Bulan</div>
                            Rp 150rb
                        </button>
                    </div>

                    <!-- Input Nominal Manual -->
                    <div class="mb-4">
                        <label class="text-xs text-slate-500 mb-2 block text-left">Atau nominal lainnya:</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium">Rp</span>
                            <input type="number" id="customAmount" placeholder="100.000"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl font-bold text-slate-700 focus:border-teal-500 focus:outline-none transition-all"
                                oninput="selectCustomAmount(this.value)">
                        </div>
                    </div>

                    <button onclick="openDonationModal()"
                        class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 active:scale-95 transition-all mb-4 flex items-center justify-center gap-2"
                        id="donateButton" disabled>
                        <span class="material-symbols-rounded">volunteer_activism</span>
                        <span id="buttonText">Pilih Nominal Dulu</span>
                    </button>

                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400">
                        <span class="material-symbols-rounded text-base">lock</span>
                        Pembayaran Aman & Terverifikasi
                    </div>
                </div>

                <!-- List Riwayat Donatur -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden ">
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
                                    $initials = collect(explode(' ', $donasi->nama))->map(function ($segment) {
                                        return strtoupper(substr($segment, 0, 1));
                                    })->take(2)->join('');
                                @endphp

                                <div
                                    class="w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center font-bold text-sm shadow-sm group-hover:scale-110 transition-transform">
                                    {{ $initials }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm font-bold text-slate-900 truncate">
                                            {{ htmlspecialchars($donasi->nama) }}
                                        </p>
                                        <p class="text-sm font-bold text-brand-600 whitespace-nowrap">Rp
                                            {{ number_format($donasi->jumlah, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($donasi->tanggal)->diffForHumans() }}</span>
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
                        <a href="{{ route('donasi.riwayat') }}"
                            class="inline-block text-sm text-teal-600 font-bold hover:text-teal-700 hover:underline">Lihat
                            Semua
                            Riwayat</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Popup Donasi (Multi-Step) -->
    <div id="donationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <!-- Header Modal -->
            <div
                class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                <h2 class="text-xl font-bold text-slate-900" id="modalTitle">Konfirmasi Donasi</h2>
                <button onclick="closeDonationModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-rounded text-2xl">close</span>
                </button>
            </div>

            <!-- Indikator Step (1-2-3) -->
            <div class="px-6 py-4 border-b border-gray-50">
                <div class="flex items-center justify-between max-w-md mx-auto">
                    <div class="flex items-center gap-2" id="step1Indicator">
                        <div
                            class="w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-sm">
                            1</div>
                        <span class="text-sm font-medium text-teal-600">Konfirmasi</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
                    <div class="flex items-center gap-2" id="step2Indicator">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">
                            2</div>
                        <span class="text-sm font-medium text-gray-400">Pembayaran</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
                    <div class="flex items-center gap-2" id="step3Indicator">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">
                            3</div>
                        <span class="text-sm font-medium text-gray-400">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Step 1: Form Data Diri -->
            <div id="step1" class="p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-teal-50 mx-auto mb-4 flex items-center justify-center">
                        <span class="material-symbols-rounded text-4xl text-teal-600">volunteer_activism</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-2">Nominal donasi Anda</p>
                    <p class="text-4xl font-black text-slate-900" id="selectedAmountDisplay">Rp 0</p>
                </div>

                <div class="bg-teal-50 border border-teal-100 rounded-2xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-teal-600">info</span>
                        <div class="text-sm text-teal-800">
                            <p class="font-bold mb-1">Donasi Anda akan disalurkan untuk:</p>
                            <ul class="list-disc list-inside space-y-1 text-teal-700">
                                <li>Beasiswa pendidikan siswa tidak mampu</li>
                                <li>Bantuan fasilitas belajar</li>
                                <li>Subsidi kuota internet</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 mb-6">
                    {{-- Name Field with Floating Label --}}
                    <div class="relative">
                        <input type="text" id="donorName"
                            class="peer w-full px-4 py-3 pt-6 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none transition-all"
                            placeholder=" " required>
                        <label for="donorName"
                            class="absolute left-4 top-3.5 text-gray-400 text-sm transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:text-xs peer-focus:top-1.5 peer-focus:text-teal-600 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                    </div>

                    {{-- Email Field with Floating Label --}}
                    <div class="relative">
                        <input type="email" id="donorEmail"
                            class="peer w-full px-4 py-3 pt-6 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none transition-all"
                            placeholder=" " required>
                        <label for="donorEmail"
                            class="absolute left-4 top-3.5 text-gray-400 text-sm transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:text-xs peer-focus:top-1.5 peer-focus:text-teal-600 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-1.5">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-slate-500 mt-2 ml-1 flex items-center gap-1">
                            <span class="material-symbols-rounded text-sm">info</span>
                            Untuk menerima bukti donasi & invoice
                        </p>
                    </div>

                    {{-- Message Field with Floating Label --}}
                    <div class="relative">
                        <textarea id="donorMessage" rows="4"
                            class="peer w-full px-4 py-3 pt-6 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none transition-all resize-none"
                            placeholder=" "></textarea>
                        <label for="donorMessage"
                            class="absolute left-4 top-3.5 text-gray-400 text-sm transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:text-xs peer-focus:top-1.5 peer-focus:text-teal-600 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-1.5">
                            Pesan / Doa (Opsional)
                        </label>
                    </div>

                    <div class="flex items-start gap-2 bg-teal-50 border border-teal-100 rounded-xl p-3">
                        <span class="material-symbols-rounded text-teal-600 text-sm mt-0.5">privacy_tip</span>
                        <p class="text-xs text-teal-700">
                            <strong>Data Anda aman.</strong> Kami hanya menggunakan email untuk mengirim bukti donasi. Tidak
                            akan dishare ke pihak ketiga.
                        </p>
                    </div>
                </div>

                <button onclick="goToStep2()"
                    class="w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg transition-all">
                    Lanjut ke Pembayaran
                </button>
            </div>

            <!-- Step 2: Metode Pembayaran -->
            <div id="step2" class="p-6 hidden">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Pilih Metode Pembayaran</h3>

                <div class="space-y-3 mb-6">
                    <!-- Transfer Bank -->
                    <button onclick="selectPaymentMethod('bank')" data-payment="bank"
                        class="payment-method-btn w-full p-4 border-2 border-gray-200 rounded-xl hover:border-teal-500 transition-all text-left flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                <span class="material-symbols-rounded text-blue-600">account_balance</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">Transfer Bank</p>
                                <p class="text-sm text-slate-500">BCA, Mandiri, BNI, BRI</p>
                            </div>
                        </div>
                        <span class="material-symbols-rounded text-gray-400">chevron_right</span>
                    </button>

                    <!-- E-Wallet -->
                    <button onclick="selectPaymentMethod('ewallet')" data-payment="ewallet"
                        class="payment-method-btn w-full p-4 border-2 border-gray-200 rounded-xl hover:border-teal-500 transition-all text-left flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                                <span class="material-symbols-rounded text-purple-600">wallet</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">E-Wallet</p>
                                <p class="text-sm text-slate-500">GoPay, OVO, DANA, ShopeePay</p>
                            </div>
                        </div>
                        <span class="material-symbols-rounded text-gray-400">chevron_right</span>
                    </button>

                    <!-- QRIS -->
                    <button onclick="selectPaymentMethod('qris')" data-payment="qris"
                        class="payment-method-btn w-full p-4 border-2 border-gray-200 rounded-xl hover:border-teal-500 transition-all text-left flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                                <span class="material-symbols-rounded text-teal-600">qr_code</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">QRIS</p>
                                <p class="text-sm text-slate-500">Semua aplikasi pembayaran</p>
                            </div>
                        </div>
                        <span class="material-symbols-rounded text-gray-400">chevron_right</span>
                    </button>
                </div>

                <div class="flex gap-3">
                    <button onclick="goToStep1()"
                        class="flex-1 py-3 border-2 border-gray-200 text-slate-700 font-bold rounded-xl hover:bg-gray-50 transition-all">
                        Kembali
                    </button>
                    <button onclick="goToStep3()" id="confirmPaymentBtn" disabled
                        class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Konfirmasi
                    </button>
                </div>
            </div>

            <!-- Step 3: Sukses -->
            <div id="step3" class="p-6 text-center hidden">
                <div class="w-24 h-24 rounded-full bg-green-50 mx-auto mb-6 flex items-center justify-center">
                    <span class="material-symbols-rounded text-6xl text-green-600">check_circle</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">Terima Kasih! 🙏</h3>
                <p class="text-slate-600 mb-6">Donasi Anda sedang diproses. Kami akan mengirimkan instruksi pembayaran ke
                    email Anda.</p>

                <div class="bg-gray-50 rounded-2xl p-6 mb-6 text-left">
                    <h4 class="font-bold text-slate-900 mb-4">Detail Donasi:</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nominal:</span>
                            <span class="font-bold text-slate-900" id="finalAmount">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Metode:</span>
                            <span class="font-bold text-slate-900" id="finalPayment">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nama:</span>
                            <span class="font-bold text-slate-900" id="finalName">-</span>
                        </div>
                    </div>
                </div>

                <button onclick="closeDonationModal()"
                    class="w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg transition-all">
                    Selesai
                </button>
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

        .donation-amount-btn.selected {
            border-color: #14b8a6;
            background-color: #f0fdfa;
            color: #14b8a6;
        }

        .payment-method-btn.selected {
            border-color: #14b8a6;
            background-color: #f0fdfa;
        }
    </style>

    <script>
        let selectedAmount = 0;
        let selectedPaymentMethod = '';

        // Pilih nominal donasi
        function selectAmount(amount) {
            selectedAmount = amount;

            // Update UI tombol nominal
            document.querySelectorAll('.donation-amount-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            document.querySelector(`[data-amount="${amount}"]`).classList.add('selected');

            // Clear custom input
            document.getElementById('customAmount').value = '';

            // Aktifkan tombol donasi
            enableDonateButton();
        }

        // Pilih nominal custom
        function selectCustomAmount(value) {
            if (value && value > 0) {
                selectedAmount = parseInt(value);

                // Remove selection dari tombol preset
                document.querySelectorAll('.donation-amount-btn').forEach(btn => {
                    btn.classList.remove('selected');
                });

                enableDonateButton();
            } else {
                selectedAmount = 0;
                document.getElementById('donateButton').disabled = true;
                document.getElementById('buttonText').textContent = 'Pilih Nominal Dulu';
            }
        }

        // Aktifkan tombol donasi
        function enableDonateButton() {
            const btn = document.getElementById('donateButton');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            document.getElementById('buttonText').textContent = `Donasi Rp ${formatRupiah(selectedAmount)}`;
        }

        // Buka modal
        function openDonationModal() {
            if (selectedAmount === 0) return;

            document.getElementById('donationModal').classList.remove('hidden');
            document.getElementById('donationModal').classList.add('flex');
            document.getElementById('selectedAmountDisplay').textContent = `Rp ${formatRupiah(selectedAmount)}`;
            document.body.style.overflow = 'hidden';
        }

        // Tutup modal
        function closeDonationModal() {
            document.getElementById('donationModal').classList.add('hidden');
            document.getElementById('donationModal').classList.remove('flex');
            document.body.style.overflow = 'auto';

            // Reset ke step 1
            goToStep1();
        }

        // Navigasi step
        function goToStep1() {
            showStep(1);
        }

        function goToStep2() {
            // Validasi nama dan email wajib diisi
            const nameInput = document.getElementById('donorName');
            const emailInput = document.getElementById('donorEmail');

            const nameValue = nameInput.value.trim();
            const emailValue = emailInput.value.trim();

            if (!nameValue) {
                alert('❌ Nama wajib diisi!\n\nSilakan masukkan nama Anda untuk melanjutkan.');
                nameInput.focus();
                return;
            }

            if (!emailValue) {
                alert('❌ Email wajib diisi!\n\nEmail diperlukan untuk mengirimkan bukti donasi dan invoice.');
                emailInput.focus();
                return;
            }

            if (!isValidEmail(emailValue)) {
                alert('❌ Format email tidak valid!\n\nContoh email yang benar: nama@example.com');
                emailInput.focus();
                return;
            }

            showStep(2);
        }

        function goToStep3() {
            // Validasi metode pembayaran dipilih
            if (!selectedPaymentMethod) {
                alert('Pilih metode pembayaran terlebih dahulu!');
                return;
            }

            // Double check validasi (seharusnya sudah divalidasi di step 1)
            const nameValue = document.getElementById('donorName').value.trim();
            const emailValue = document.getElementById('donorEmail').value.trim();

            if (!nameValue || !emailValue) {
                alert('⚠️ Data tidak lengkap! Mohon kembali ke step 1.');
                goToStep1();
                return;
            }

            // Disable button dan tampilkan loading
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Memproses...';

            // Kirim data ke backend (email null jika kosong)
            const formData = {
                jumlah: selectedAmount,
                nama: document.getElementById('donorName').value || 'Hamba Allah',
                email: emailValue || null,  // Kirim null jika kosong
                pesan: document.getElementById('donorMessage').value,
                metode_pembayaran: selectedPaymentMethod,
            };

            fetch('{{ route("donasi.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData)
            })
                .then(response => {
                    // Check if response is ok
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Gagal memproses donasi');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data.invoice_url) {
                        // Reset button
                        confirmBtn.disabled = false;
                        confirmBtn.textContent = originalText;

                        // Tutup modal
                        closeDonationModal();

                        // Redirect ke Xendit Invoice URL
                        window.location.href = data.data.invoice_url;
                    } else {
                        throw new Error(data.message || 'Gagal menyimpan donasi');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                    console.error('Error:', error);

                    // Reset button
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = originalText;
                });
        }

        // Helper function untuk validasi email
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showStep(step) {
            // Hide semua step
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');

            // Show step yang dipilih
            document.getElementById(`step${step}`).classList.remove('hidden');

            // Update indicator
            updateStepIndicator(step);

            // Update title
            const titles = ['', 'Konfirmasi Donasi', 'Pilih Pembayaran', 'Donasi Berhasil'];
            document.getElementById('modalTitle').textContent = titles[step];
        }

        function updateStepIndicator(activeStep) {
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`step${i}Indicator`);
                const circle = indicator.querySelector('div');
                const text = indicator.querySelector('span');

                if (i <= activeStep) {
                    circle.className = 'w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-sm';
                    text.className = 'text-sm font-medium text-teal-600';
                } else {
                    circle.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm';
                    text.className = 'text-sm font-medium text-gray-400';
                }
            }
        }

        // Pilih metode pembayaran
        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;

            // Update UI
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            document.querySelector(`[data-payment="${method}"]`).classList.add('selected');

            // Aktifkan tombol konfirmasi
            document.getElementById('confirmPaymentBtn').disabled = false;
        }

        function getPaymentMethodName(method) {
            const methods = {
                'bank': 'Transfer Bank',
                'ewallet': 'E-Wallet',
                'qris': 'QRIS'
            };
            return methods[method] || method;
        }

        // Format rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Close modal ketika klik di luar
        document.getElementById('donationModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeDonationModal();
            }
        });
    </script>
@endsection