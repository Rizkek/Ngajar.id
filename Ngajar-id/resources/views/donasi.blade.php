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
                <div class="flex-1 text-center md:text-left" data-aos="fade-right">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-800 leading-tight mb-6">
                        Satu Donasi, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-secondary-500">Sejuta
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
                            class="px-8 py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-lg rounded-2xl shadow-xl shadow-brand-600/20 hover:shadow-brand-600/30 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 group">
                            <span class="material-symbols-rounded group-hover:animate-bounce">volunteer_activism</span>
                            Donasi Sekarang
                        </button>
                    </div>
                </div>

                <!-- Gambar Ilustrasi Emosional -->
                <div class="flex-1 w-full relative" data-aos="fade-left" data-aos-delay="200">
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
                                        class="w-6 h-6 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-600">
                                        B</div>
                                    <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Budi, Siswa SDN 1
                                        Harapan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Decoration element -->
                    <div
                        class="absolute -top-6 -left-6 w-24 h-24 bg-brand-400 rounded-full blur-3xl opacity-20 animate-pulse">
                    </div>
                    <div
                        class="absolute -bottom-6 -right-6 w-32 h-32 bg-secondary-400 rounded-full blur-3xl opacity-20 animate-pulse delay-700">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Konten Utama -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">

        <!-- Kartu Total Donasi (Tanpa Target) -->
        <div data-aos="zoom-in" data-aos-delay="400"
            class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-10 mb-16 relative overflow-hidden group">
            <!-- Decorative background elements (Subtle) -->
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-brand-50 to-secondary-50 rounded-full -mr-32 -mt-32 opacity-70 group-hover:scale-105 transition-transform duration-1000">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-secondary-50 to-brand-50 rounded-full -ml-20 -mb-20 opacity-70">
            </div>

            <div class="relative z-10">
                <div class="text-center mb-12">
                    <div
                        class="inline-flex items-center gap-2 mb-4 bg-brand-50 px-3 py-1 rounded-full border border-brand-100">
                        <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-brand-700 uppercase tracking-widest">Dana Tersalurkan</span>
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
                            class="w-12 h-12 rounded-2xl bg-brand-100/50 text-brand-600 flex items-center justify-center shrink-0">
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
                            class="w-12 h-12 rounded-2xl bg-secondary-100/50 text-secondary-600 flex items-center justify-center shrink-0">
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
                            class="w-12 h-12 rounded-2xl bg-brand-100/50 text-brand-600 flex items-center justify-center shrink-0">
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
                <section data-aos="fade-up">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-1 bg-brand-500 rounded-full"></div>
                        <h3 class="text-2xl font-bold text-slate-900">Transparansi Penyaluran</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all text-center group">
                            <div
                                class="mx-auto w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-rounded text-3xl text-brand-600">school</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">40% Beasiswa</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Bantuan biaya sekolah & SPP bagi siswa prasejahtera berprestasi.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all text-center group">
                            <div
                                class="mx-auto w-16 h-16 bg-secondary-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-rounded text-3xl text-secondary-600">auto_stories</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">35% Fasilitas Belajar</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Pengadaan modul, buku paket, dan alat tulis untuk kegiatan belajar.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all text-center group">
                            <div
                                class="mx-auto w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-rounded text-3xl text-brand-600">wifi_tethering</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">25% Operasional Digital</h4>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Subsidi kuota internet santri & pemeliharaan server platform.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Kartu Cerita Founder -->
                <div class="bg-brand-900 rounded-3xl p-8 md:p-12 relative overflow-hidden text-white" data-aos="fade-up">
                    <img src="https://images.unsplash.com/photo-1577896334614-201967484a3d?q=80&w=1000&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay" alt="Background">

                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center">
                        <div
                            class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white/30 overflow-hidden shrink-0">
                            <img src="{{ asset('azis.jpg') }}" class="w-full h-full object-cover" alt="Muhammad Abdul Azis">
                        </div>
                        <div>
                            <span class="material-symbols-rounded text-4xl text-white/50 mb-4 block">format_quote</span>
                            <p class="text-xl md:text-2xl font-medium leading-relaxed mb-6 font-serif">
                                "Pendidikan adalah tiket masa depan. Di Ngajar.ID, kami menyatukan niat baik Anda dengan
                                semangat belajar mereka yang tak pernah padam."
                            </p>
                            <div>
                                <h4 class="font-bold text-lg">Muhammad Abdul Azis</h4>
                                <p class="text-secondary-200 text-sm">Project Manager</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Form Donasi & Riwayat -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Card Form Donasi -->
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 text-center top-24"
                    data-aos="fade-left">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Mari Berkontribusi</h3>
                    <p class="text-sm text-slate-500 mb-6">Pilih nominal donasi terbaikmu</p>

                    <!-- Pilihan Tombol Nominal -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <button onclick="selectAmount(25000)" data-amount="25000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-brand-500 hover:text-brand-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">Kuota</div>
                            Rp 25rb
                        </button>
                        <button onclick="selectAmount(50000)" data-amount="50000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-brand-500 hover:text-brand-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">Alat Tulis</div>
                            Rp 50rb
                        </button>
                        <button onclick="selectAmount(100000)" data-amount="100000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-brand-500 hover:text-brand-600 transition-all">
                            <div class="text-xs text-slate-400 mb-1">Makanan</div>
                            Rp 100rb
                        </button>
                        <button onclick="selectAmount(150000)" data-amount="150000"
                            class="donation-amount-btn px-4 py-3 border-2 border-gray-200 rounded-xl text-slate-700 font-bold hover:border-brand-500 hover:text-brand-600 transition-all">
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
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl font-bold text-slate-700 focus:border-brand-500 focus:outline-none transition-all"
                                oninput="selectCustomAmount(this.value)">
                        </div>
                    </div>

                    <button onclick="openDonationModal()"
                        class="w-full py-4 bg-secondary-500 hover:bg-secondary-600 text-white font-bold rounded-xl shadow-lg shadow-secondary-500/20 active:scale-95 transition-all mb-4 flex items-center justify-center gap-2"
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
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" data-aos="fade-left"
                    data-aos-delay="200">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-slate-900">Donatur Terbaru</h3>
                        <span class="px-2 py-1 bg-brand-50 text-brand-600 text-xs font-bold rounded-md">Live Update</span>
                    </div>

                    <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto scrollbar-hide">
                        @forelse($riwayat_donasi as $index => $donasi)
                            <div class="p-4 hover:bg-gray-50 transition-colors flex items-center gap-4 group">
                                <!-- Avatar Initials -->
                                @php
                                    $colors = ['bg-brand-50 text-brand-600', 'bg-secondary-50 text-secondary-600', 'bg-brand-100 text-brand-700', 'bg-secondary-100 text-secondary-700', 'bg-brand-50 text-brand-500'];
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
                                                class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-secondary-50 text-secondary-600 border border-secondary-100">
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
                            class="inline-block text-sm text-brand-600 font-bold hover:text-brand-700 hover:underline">Lihat
                            Semua
                            Riwayat</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('partials.donation-modal')

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
    </style>

    <script>
        let selectedAmount = 0;

        // Buka modal (sudah ada di partial, tapi kita panggil dari sini)
        // Fungsi selectAmount tetap di sini karena tombolnya ada di halaman ini
        function selectAmount(amount) {
            selectedAmount = amount;
            document.querySelectorAll('.donation-amount-btn').forEach(btn => btn.classList.remove('selected'));
            document.querySelector(`[data-amount="${amount}"]`).classList.add('selected');
            document.getElementById('customAmount').value = '';
            enableDonateButton();
        }

        function selectCustomAmount(value) {
            if (value && value > 0) {
                selectedAmount = parseInt(value);
                document.querySelectorAll('.donation-amount-btn').forEach(btn => btn.classList.remove('selected'));
                enableDonateButton();
            } else {
                selectedAmount = 0;
                document.getElementById('donateButton').disabled = true;
                document.getElementById('buttonText').textContent = 'Pilih Nominal Dulu';
            }
        }

        function enableDonateButton() {
            const btn = document.getElementById('donateButton');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            document.getElementById('buttonText').textContent = `Donasi Rp ${formatRupiah(selectedAmount)}`;
        }

        // Initialize from URL if nominal exists
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const nominal = urlParams.get('nominal');
            if (nominal) {
                document.getElementById('customAmount').value = nominal;
                selectCustomAmount(nominal);
            }
        });
    </script>
@endsection