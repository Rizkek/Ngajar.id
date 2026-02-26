<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ngajar.ID')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('img/Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Google Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        secondary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316', // Orange 500
                            600: '#ea580c', // Orange 600
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Hide Scrollbar Utility */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        }
    </style>

    <!-- Flatpickr Custom Styles -->
    <style>
        /* Custom Flatpickr styling to match our theme */
        .flatpickr-calendar {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            font-family: inherit !important;
        }

        .flatpickr-months {
            border-radius: 12px 12px 0 0 !important;
            background: linear-gradient(to right, #059669, #10b981) !important;
        }

        .flatpickr-current-month {
            color: white !important;
        }

        .flatpickr-weekday {
            color: #6b7280 !important;
            font-weight: 600 !important;
        }

        .flatpickr-day {
            color: #1e293b !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }

        .flatpickr-day:hover:not(.flatpickr-disabled):not(.selected) {
            background: #f0fdf4 !important;
            border-color: #34d399 !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #10b981 !important;
            border-color: #10b981 !important;
            color: white !important;
        }

        .flatpickr-day.today {
            border-color: #10b981 !important;
            color: #10b981 !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today:hover {
            background: #f0fdf4 !important;
        }

        .flatpickr-months .flatpickr-prev-month:hover svg,
        .flatpickr-months .flatpickr-next-month:hover svg {
            fill: white !important;
        }

        /* Style for the alternative input */
        input.flatpickr-input[readonly] {
            cursor: pointer !important;
            background-color: rgb(249 250 251) !important;
        }

        input.flatpickr-input[readonly]:focus {
            background-color: white !important;
        }
    </style>

    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Swiper.js (Slider) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- AOS (Animate on Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head-scripts')
</head>

<body
    class="bg-gray-50 text-slate-800 font-sans flex flex-col min-h-screen antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header / Navigasi Utama -->
    <header
        class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <span
                            class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-brand-900">
                            Ngajar.id
                        </span>
                    </a>
                </div>

                <!-- Navigasi Desktop -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ url('/') }}"
                        class="{{ request()->is('/') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('programs') }}"
                        class="{{ request()->is('programs') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                        Program Belajar
                    </a>
                    <a href="{{ route('mentors') }}"
                        class="{{ request()->is('mentors') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                        Cari Pengajar
                    </a>
                    <a href="{{ url('/donasi') }}"
                        class="{{ request()->is('donasi') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                        Donasi
                    </a>
                </nav>

                <!-- Tombol Otentikasi / Menu User -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <!-- Menu User yang Sudah Login -->
                        <!-- Menu User yang Sudah Login -->
                        <div class="flex items-center gap-4">
                            <!-- Gamification Badge -->
                            <div class="hidden md:flex flex-col items-end mr-2">
                                <span
                                    class="text-xs font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full border border-brand-100">
                                    {{ auth()->user()->xp }} XP
                                </span>
                                <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold mt-0.5">
                                    Level {{ auth()->user()->level }}
                                </span>
                            </div>

                            <div class="h-8 w-px bg-slate-200 hidden md:block"></div>

                            <span class="text-sm text-slate-600 hidden md:inline">Halo,
                                <strong>{{ auth()->user()->name }}</strong></span>

                            @if(auth()->user()->isMurid())
                                <a href="{{ route('murid.dashboard') }}"
                                    class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                                    Dashboard
                                </a>
                            @elseif(auth()->user()->isPengajar())
                                <a href="{{ route('pengajar.dashboard') }}"
                                    class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                                    Dashboard
                                </a>
                            @elseif(auth()->user()->isAdmin())
                                <a href="/admin" class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                                    Admin Panel
                                </a>
                            @endif

                            <a href="{{ route('profile') }}"
                                class="p-2 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-full transition-all"
                                title="Profil Saya">
                                <span class="material-symbols-rounded text-xl">account_circle</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-all"
                                    title="Logout">
                                    <span class="material-symbols-rounded text-xl">logout</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Tombol untuk Tamu (Belum Login) -->
                        <a href="{{ url('/login') }}"
                            class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                            Masuk
                        </a>
                        <a href="{{ url('/register') }}"
                            class="px-5 py-2.5 bg-brand-600 text-white font-medium rounded-full shadow-lg shadow-brand-500/30 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all transform hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Tombol Menu Mobile (Hamburger) -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-500 hover:text-brand-600 p-2">
                        <span class="material-symbols-rounded text-2xl">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Konten Halaman (Dynamic Content) -->
    <main class="grow pt-20">
        @yield('content')
    </main>

    <!-- Footer / Kaki Halaman -->
    @unless(request()->is('login') || request()->is('register') || request()->is('password/*'))
        <footer class="bg-slate-900 text-white pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <div class="col-span-1 md:col-span-1">
                        <div class="flex items-center gap-2 mb-6">
                            <span class="text-2xl font-bold text-white">
                                Ngajar.id
                            </span>
                        </div>
                        <p class="text-slate-400 leading-relaxed mb-6">
                            Platform pendidikan inklusif yang menghubungkan semangat relawan dengan mimpi pelajar Indonesia.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <!-- Facebook -->
                            <a href="https://facebook.com/ngajarid" target="_blank" aria-label="Facebook"
                                class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#1877F2] hover:text-white transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.248h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                            <!-- X (Twitter) -->
                            <a href="https://twitter.com/ngajarid" target="_blank" aria-label="X (Twitter)"
                                class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-black hover:text-white transition-all duration-300 hover:scale-110">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </a>
                            <!-- Instagram -->
                            <a href="https://instagram.com/ngajarid" target="_blank" aria-label="Instagram"
                                class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:text-white transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.332 3.608 1.308.975.975 1.245 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.332 2.633-1.308 3.608-.975.975-2.242 1.245-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.332-3.608-1.308-.975-.975-1.245-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.245 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948s.014 3.667.072 4.947c.2 4.337 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072s3.667-.014 4.947-.072c4.358-.201 6.78-2.618 6.98-6.98.058-1.281.072-1.689.072-4.948s-.014-3.667-.072-4.947c-.2-4.358-2.618-6.78-6.98-6.98-1.281-.059-1.689-.073-4.948-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </a>
                            <!-- TikTok -->
                            <a href="https://tiktok.com/@ngajarid" target="_blank" aria-label="TikTok"
                                class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-black hover:text-white transition-all duration-300 hover:scale-110">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.9-.32-1.98-.23-2.81.3-.75.47-1.21 1.25-1.28 2.13-.09 1.07.5 2.12 1.41 2.6 1 .53 2.24.4 3.13-.34.61-.5.95-1.22.99-2.01.03-3.24.03-6.48.01-9.72z" />
                                </svg>
                            </a>
                            <!-- YouTube -->
                            <a href="https://youtube.com/@ngajarid" target="_blank" aria-label="YouTube"
                                class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#FF0000] hover:text-white transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-6">Jelajahi</h4>
                        <ul class="space-y-4 text-slate-400">
                            <li><a href="{{ url('/') }}" class="hover:text-brand-500 transition-colors">Beranda</a></li>
                            <li><a href="{{ route('programs') }}" class="hover:text-brand-500 transition-colors">Program
                                    Belajar</a></li>
                            <li><a href="{{ route('mentors') }}" class="hover:text-brand-500 transition-colors">Cari
                                    Pengajar</a></li>
                            <li><a href="{{ url('/donasi') }}" class="hover:text-brand-500 transition-colors">Donasi</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-6">Layanan</h4>
                        <ul class="space-y-4 text-slate-400">
                            <li><a href="{{ url('/register?role=murid') }}"
                                    class="hover:text-brand-500 transition-colors">Untuk Pelajar</a></li>
                            <li><a href="{{ url('/register?role=pengajar') }}"
                                    class="hover:text-brand-500 transition-colors">Untuk Pengajar</a></li>
                            <li><a href="{{ route('tentang-kami') }}" class="hover:text-brand-500 transition-colors">Sekolah
                                    Mitra</a></li>
                            <li><a href="{{ route('tentang-kami') }}" class="hover:text-brand-500 transition-colors">Karir
                                    Relawan</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-6">Hubungi Kami</h4>
                        <ul class="space-y-4 text-slate-400">
                            <li class="flex items-start gap-4 p-2 rounded-xl hover:bg-slate-800 transition-colors group">
                                <span
                                    class="material-symbols-rounded text-brand-500 text-2xl group-hover:scale-110 transition-transform">mail</span>
                                <a href="mailto:halo@ngajar.id"
                                    class="text-slate-300 hover:text-white transition-colors flex flex-col">
                                    <span class="text-xs text-slate-500 font-medium">Email Kami</span>
                                    <span class="font-bold">halo@ngajar.id</span>
                                </a>
                            </li>
                            <li class="flex items-start gap-4 p-2 rounded-xl hover:bg-slate-800 transition-colors group">
                                <span
                                    class="material-symbols-rounded text-brand-500 text-2xl group-hover:scale-110 transition-transform">chat</span>
                                <a href="https://wa.me/6281234567890" target="_blank"
                                    class="text-slate-300 hover:text-white transition-colors flex flex-col">
                                    <span class="text-xs text-slate-500 font-medium">WhatsApp Admin</span>
                                    <span class="font-bold">+62 812-3456-7890</span>
                                </a>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-teal-500 mt-0.5 text-xl">location_on</span>
                                <span>Jl. Pendidikan No. 10,<br>Bandung, Indonesia</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-slate-500 text-sm">
                        © {{ date('Y') }} Ngajar.ID. All rights reserved.
                    </p>
                    <div class="flex gap-6 text-sm text-slate-500">
                        <a href="{{ route('privacy-policy') }}" class="hover:text-brand-500">Privacy Policy</a>
                        <a href="{{ route('terms-of-service') }}" class="hover:text-brand-500">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    @endunless

    {{-- Floating AI Support Widget --}}
    <div class="fixed bottom-6 right-6 z-40 flex flex-col items-end gap-3">

        {{-- Chat Panel --}}
        <div id="support-panel"
            class="hidden w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex-col"
            style="max-height: 560px;">

            {{-- Header --}}
            <div
                class="bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-4 text-white flex items-center gap-3 shrink-0">
                <div
                    class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md shrink-0">
                    <span class="material-symbols-rounded text-xl">smart_toy</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm leading-none">Ngaji - Asisten Ngajar.id</h3>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <p class="text-xs text-brand-100">Online • Siap membantu</p>
                    </div>
                </div>
                <button onclick="toggleSupport()" class="text-white/70 hover:text-white shrink-0">
                    <span class="material-symbols-rounded text-xl">close</span>
                </button>
            </div>

            {{-- Messages Area --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50"
                style="min-height: 240px; max-height: 320px;">
                {{-- Pesan sambutan awal --}}
                <div class="flex items-end gap-2">
                    <div class="w-7 h-7 bg-brand-600 rounded-full flex items-center justify-center shrink-0 mb-0.5">
                        <span class="material-symbols-rounded text-white text-sm">smart_toy</span>
                    </div>
                    <div
                        class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm max-w-[80%]">
                        <p class="text-sm text-slate-700 leading-relaxed">Halo! Saya <strong>Ngaji</strong>, asisten
                            virtual Ngajar.id 👋<br>Ada yang bisa saya bantu?</p>
                    </div>
                </div>
            </div>

            {{-- Quick Topics --}}
            <div id="quick-topics" class="px-4 py-2 border-t border-gray-100 bg-white shrink-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Pertanyaan Cepat</p>
                <div class="flex flex-wrap gap-1.5">
                    <button onclick="sendQuickMessage('Bagaimana cara daftar sebagai Murid?')"
                        class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                        Cara daftar Murid
                    </button>
                    <button onclick="sendQuickMessage('Bagaimana cara reset password yang lupa?')"
                        class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                        Lupa Password
                    </button>
                    <button onclick="sendQuickMessage('Apa itu sistem token di Ngajar.id?')"
                        class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                        Sistem Token
                    </button>
                    <button onclick="sendQuickMessage('Bagaimana jika donasi saya sudah berhasil didaftarkan?')"
                        class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                        Donasi Berhasil?
                    </button>
                </div>
            </div>

            {{-- Input Area --}}
            <div class="px-4 py-3 border-t border-gray-100 bg-white shrink-0">
                <div class="flex items-end gap-2">
                    <textarea id="chat-input"
                        class="flex-1 resize-none border border-gray-200 rounded-2xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-all leading-relaxed"
                        placeholder="Ketik pertanyaanmu..." rows="1" maxlength="500"
                        onkeydown="handleChatKeydown(event)" oninput="autoResizeTextarea(this)"></textarea>
                    <button id="send-btn" onclick="sendChatMessage()"
                        class="w-10 h-10 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <span class="material-symbols-rounded text-xl">send</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 text-center mt-2">Ngaji dapat membuat kesalahan. Selalu verifikasi
                    info penting.</p>
            </div>
        </div>

        {{-- Toggle Button --}}
        <button onclick="toggleSupport()" id="support-toggle-btn"
            class="group bg-brand-600 hover:bg-brand-700 text-white w-14 h-14 rounded-2xl shadow-xl shadow-brand-600/30 flex items-center justify-center transition-all hover:scale-110 active:scale-95 relative">
            <span id="support-icon" class="material-symbols-rounded text-3xl transition-all">question_answer</span>
            <span id="support-badge"
                class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 border-2 border-white rounded-full animate-pulse"></span>
        </button>
    </div>

    <script>
        // ===== State =====
        let chatHistory = []; // Array of {role: 'user'|'model', text: '...'}
        let isChatLoading = false;

        // ===== Toggle Widget =====
        function toggleSupport() {
            const panel = document.getElementById('support-panel');
            const icon = document.getElementById('support-icon');
            const badge = document.getElementById('support-badge');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                panel.classList.add('flex');
                icon.textContent = 'close';
                badge.classList.add('hidden'); // Sembunyikan badge saat dibuka
                scrollChatToBottom();
            } else {
                panel.classList.add('hidden');
                panel.classList.remove('flex');
                icon.textContent = 'question_answer';
            }
        }

        // ===== Kirim Pesan =====
        async function sendChatMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();

            if (!message || isChatLoading) return;

            // Tampilkan pesan user
            appendMessage('user', message);
            input.value = '';
            autoResizeTextarea(input);

            // Tampilkan loading indicator
            const loadingId = showLoadingBubble();
            isChatLoading = true;
            document.getElementById('send-btn').disabled = true;

            try {
                const response = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        history: chatHistory.slice(-10), // Kirim max 10 history
                    }),
                });

                const data = await response.json();
                removeLoadingBubble(loadingId);

                if (data.success) {
                    appendMessage('model', data.reply);
                    // Simpan ke history
                    chatHistory.push({ role: 'user', text: message });
                    chatHistory.push({ role: 'model', text: data.reply });
                    // Trim history ke 20 entry (10 percakapan)
                    if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);
                } else {
                    appendMessage('model', data.message || 'Maaf, terjadi kesalahan. Silakan coba lagi. 🙏');
                }
            } catch (err) {
                removeLoadingBubble(loadingId);
                appendMessage('model', 'Koneksi bermasalah. Pastikan internet kamu aktif ya! 🌐');
            } finally {
                isChatLoading = false;
                document.getElementById('send-btn').disabled = false;
            }
        }

        // ===== Kirim via Quick Topic Button =====
        function sendQuickMessage(text) {
            const input = document.getElementById('chat-input');
            input.value = text;
            sendChatMessage();
        }

        // ===== Append Message Bubble =====
        function appendMessage(role, text) {
            const container = document.getElementById('chat-messages');
            const isUser = role === 'user';

            const wrapper = document.createElement('div');
            wrapper.className = `flex items-end gap-2 ${isUser ? 'flex-row-reverse' : ''}`;

            // Avatar
            const avatar = document.createElement('div');
            avatar.className = `w-7 h-7 rounded-full flex items-center justify-center shrink-0 mb-0.5 ${isUser ? 'bg-slate-200' : 'bg-brand-600'}`;
            avatar.innerHTML = isUser
                ? '<span class="material-symbols-rounded text-slate-500 text-sm">person</span>'
                : '<span class="material-symbols-rounded text-white text-sm">smart_toy</span>';

            // Bubble
            const bubble = document.createElement('div');
            bubble.className = isUser
                ? 'bg-brand-600 text-white rounded-2xl rounded-br-sm px-4 py-3 max-w-[80%] shadow-sm'
                : 'bg-white border border-gray-100 text-slate-700 rounded-2xl rounded-bl-sm px-4 py-3 max-w-[80%] shadow-sm';
            bubble.innerHTML = `<p class="text-sm leading-relaxed">${escapeHtml(text).replace(/\n/g, '<br>')}</p>`;

            wrapper.appendChild(avatar);
            wrapper.appendChild(bubble);
            container.appendChild(wrapper);
            scrollChatToBottom();
        }

        // ===== Loading Bubble =====
        function showLoadingBubble() {
            const id = 'loading-' + Date.now();
            const container = document.getElementById('chat-messages');
            const wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.className = 'flex items-end gap-2';
            wrapper.innerHTML = `
                <div class="w-7 h-7 bg-brand-600 rounded-full flex items-center justify-center shrink-0 mb-0.5">
                    <span class="material-symbols-rounded text-white text-sm">smart_toy</span>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1 items-center">
                        <div class="w-2 h-2 bg-brand-400 rounded-full animate-bounce" style="animation-delay:0s"></div>
                        <div class="w-2 h-2 bg-brand-500 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                        <div class="w-2 h-2 bg-brand-600 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                    </div>
                </div>`;
            container.appendChild(wrapper);
            scrollChatToBottom();
            return id;
        }

        function removeLoadingBubble(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        // ===== Helpers =====
        function scrollChatToBottom() {
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        }

        function autoResizeTextarea(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 96) + 'px';
        }

        function handleChatKeydown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendChatMessage();
            }
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>



    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- Swiper.js -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize AOS
            AOS.init({
                duration: 800,
                once: true,
                offset: 100
            });

            // Initialize all date inputs with Flatpickr
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                flatpickr(input, {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true,
                    theme: "material_blue"
                });
            });
        });
    </script>

    @stack('scripts')

    <!-- Global Loader -->
    <div id="global-loader"
        class="fixed inset-0 z-100 bg-white flex flex-col items-center justify-center transition-opacity duration-500">
        <div class="relative w-24 h-24 mb-4">
            <!-- Pulsing Background -->
            <div class="absolute inset-0 bg-brand-100 rounded-full animate-ping opacity-75"></div>
            <!-- Logo/Icon Container -->
            <div
                class="relative bg-white rounded-full p-4 shadow-xl border border-brand-100 flex items-center justify-center w-full h-full">
                <!-- Using the same gradient as the logo text -->
                <span
                    class="material-symbols-rounded text-5xl bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-brand-900 animate-pulse">
                    school
                </span>
            </div>
        </div>

        <div class="flex flex-col items-center gap-2">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                Ngajar<span class="text-brand-600">.id</span>
            </h2>
            <div class="flex gap-1">
                <div class="w-2 h-2 bg-brand-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-2 h-2 bg-brand-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-brand-800 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('global-loader');
            if (loader) {
                // Minimum loading time to prevent flash
                setTimeout(() => {
                    loader.style.opacity = '0';
                    loader.style.pointerEvents = 'none';

                    setTimeout(() => {
                        loader.remove();
                    }, 500); // Wait for transition to finish
                }, 800);
            }
        });

        // Fallback if DOMContentLoaded already fired or fails
        window.addEventListener('load', () => {
            const loader = document.getElementById('global-loader');
            if (loader && loader.style.opacity !== '0') {
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 500);
            }
        });
    </script>
</body>

</html>