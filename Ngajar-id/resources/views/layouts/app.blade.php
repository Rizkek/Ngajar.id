<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Ngajar.ID')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('img/Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
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
                            500: '#f59e0b', // Amber 500 for accents
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
    </style>
</head>

<body
    class="bg-gray-50 text-slate-800 font-sans flex flex-col min-h-screen antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header -->
    <header
        class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <!-- Placeholder logo if image not loaded, or use text -->
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-brand-500/30 group-hover:scale-110 transition-transform">
                            N
                        </div>
                        <span
                            class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-brand-900">
                            Ngajar.id
                        </span>
                    </a>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ url('/') }}"
                        class="text-slate-600 hover:text-brand-600 font-medium transition-colors">Beranda</a>
                    <a href="{{ route('programs') }}"
                        class="text-slate-600 hover:text-brand-600 font-medium transition-colors">Program Belajar</a>
                    <a href="{{ route('mentors') }}"
                        class="text-slate-600 hover:text-brand-600 font-medium transition-colors">Cari Pengajar</a>
                    <a href="{{ url('/donasi') }}"
                        class="text-slate-600 hover:text-brand-600 font-medium transition-colors">Donasi</a>
                </nav>

                <!-- Auth Buttons / User Menu -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <!-- Authenticated User Menu -->
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-slate-600">Halo, <strong>{{ auth()->user()->name }}</strong></span>

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

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-full shadow-lg shadow-red-500/30 hover:bg-red-600 hover:shadow-red-500/40 transition-all transform hover:-translate-y-0.5">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest Buttons -->
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

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-500 hover:text-brand-600 p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white font-bold text-xl">
                            N
                        </div>
                        <span class="text-2xl font-bold text-white">
                            Ngajar.id
                        </span>
                    </div>
                    <p class="text-slate-400 leading-relaxed mb-6">
                        Platform pendidikan inklusif yang menghubungkan semangat relawan dengan mimpi pelajar Indonesia.
                    </p>
                    <div class="flex space-x-4">
                        <!-- Facebook Icon -->
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <!-- Twitter Icon -->
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <!-- Instagram Icon -->
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
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
                        <li><a href="#" class="hover:text-brand-500 transition-colors">Untuk Pelajar</a></li>
                        <li><a href="#" class="hover:text-brand-500 transition-colors">Untuk Pengajar</a></li>
                        <li><a href="#" class="hover:text-brand-500 transition-colors">Sekolah Mitra</a></li>
                        <li><a href="#" class="hover:text-brand-500 transition-colors">Karir Relawan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6">Hubungi Kami</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 text-teal-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <a href="mailto:halo@ngajar.id"
                                class="hover:text-brand-500 transition-colors">halo@ngajar.id</a>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 text-teal-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <a href="tel:+6281234567890" class="hover:text-brand-500 transition-colors">+62
                                812-3456-7890</a>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 text-teal-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
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

</body>

</html>