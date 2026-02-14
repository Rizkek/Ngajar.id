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
                            500: '#14b8a6', // Teal 500
                            600: '#0d9488', // Teal 600
                            900: '#134e4a',
                        },
                        secondary: {
                            500: '#f59e0b', // Amber 500
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
            background: linear-gradient(to right, #0d9488, #14b8a6) !important;
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
            background: #f0fdfa !important;
            border-color: #5eead4 !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #14b8a6 !important;
            border-color: #14b8a6 !important;
            color: white !important;
        }

        .flatpickr-day.today {
            border-color: #14b8a6 !important;
            color: #14b8a6 !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today:hover {
            background: #f0fdfa !important;
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
                            class="text-2xl font-bold bg-clip-text text-transparent bg-linear-to-r from-brand-600 to-brand-900">
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
                        <div class="flex space-x-4">
                            <!-- Facebook Icon -->
                            <a href="#" aria-label="Facebook"
                                class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                                <span class="material-symbols-rounded text-xl">facebook</span>
                            </a>
                            <!-- X (Twitter) Icon -->
                            <a href="#" aria-label="X (Twitter)"
                                class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </a>
                            <!-- Instagram Icon -->
                            <a href="#" aria-label="Instagram"
                                class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                                <span class="material-symbols-rounded text-xl">photo_camera</span>
                            </a>
                            <!-- YouTube Icon -->
                            <a href="#" aria-label="YouTube"
                                class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                                <span class="material-symbols-rounded text-xl">play_circle</span>
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
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-teal-500 mt-0.5 text-xl">mail</span>
                                <a href="mailto:halo@ngajar.id"
                                    class="hover:text-brand-500 transition-colors">halo@ngajar.id</a>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-teal-500 mt-0.5 text-xl">call</span>
                                <a href="tel:+6281234567890" class="hover:text-brand-500 transition-colors">+62
                                    812-3456-7890</a>
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
                        <a href="#" class="hover:text-brand-500">Privacy Policy</a>
                        <a href="#" class="hover:text-brand-500">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    @endunless

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize all date inputs with Flatpickr
            const dateInputs = document.querySelectorAll('input[type="date"]');

            dateInputs.forEach(input => {
                flatpickr(input, {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true, // Force flatpickr even on mobile
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
                    class="material-symbols-rounded text-5xl bg-clip-text text-transparent bg-linear-to-r from-brand-600 to-brand-900 animate-pulse">
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