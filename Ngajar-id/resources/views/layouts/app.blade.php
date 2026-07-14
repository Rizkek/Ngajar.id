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
    <link rel="preconnect" href="https://ui-avatars.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Google Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0&display=swap"
        rel="stylesheet">

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        
        /* A11y: Skip to content */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #0f766e;
            color: white;
            padding: 8px 16px;
            z-index: 9999;
            transition: top 0.2s;
            font-weight: bold;
        }
        .skip-link:focus {
            top: 0;
        }

        /* A11y: Focus visible global */
        *:focus-visible {
            outline: 2px solid #0f766e !important;
            outline-offset: 2px !important;
        }

        /* A11y: Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
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
    <a href="#main-content" class="skip-link">Lanjut ke konten utama</a>

    <!-- Header / Navigasi Utama -->
    @include('partials.navbar')

    <!-- Konten Halaman (Dynamic Content) -->
    <main id="main-content" class="grow pt-20">
        @yield('content')
    </main>

    <!-- Footer / Kaki Halaman -->
    @include('partials.footer')

    @include('partials.ai-support')



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

    <!-- Global Toast Notifications -->
    @if(session('success'))
        <x-alerts.toast type="success" :message="session('success')" />
    @elseif(session('error'))
        <x-alerts.toast type="error" :message="session('error')" />
    @elseif(session('warning'))
        <x-alerts.toast type="warning" :message="session('warning')" />
    @endif

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
                // Reduced to 300ms — enough to prevent flash without adding perceived latency
                setTimeout(() => {
                    loader.style.opacity = '0';
                    loader.style.pointerEvents = 'none';

                    setTimeout(() => {
                        loader.remove();
                    }, 500); // Wait for transition to finish
                }, 300);
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
