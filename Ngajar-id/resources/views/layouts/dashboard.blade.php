<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Ngajar.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100;300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        roboto: ['"Roboto Slab"', 'serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-roboto">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col ml-64 transition-all duration-300" id="main-content">
            <!-- Topbar -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="px-6 py-3 flex items-center justify-between">
                    <h1 class="text-xl font-bold text-teal-500">@yield('header_title', 'Dashboard')</h1>

                    <div class="flex items-center space-x-4">
                        @if(auth()->user() && auth()->user()->role == 'murid')
                            <div
                                class="hidden md:flex items-center bg-amber-50 border border-amber-200 rounded-full px-3 py-1">
                                <span class="material-symbols-rounded text-amber-500 text-lg mr-1">monetization_on</span>
                                <span class="text-sm font-bold text-amber-700">{{ auth()->user()->getSaldoToken() }}
                                    Token</span>
                            </div>
                        @endif

                        <!-- Profile/User Info Placeholder -->
                        <div class="flex items-center space-x-2">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name ?? 'Pengguna' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role ?? 'Guest') }}</p>
                            </div>
                            <div
                                class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600">
                                <span class="material-symbols-rounded text-xl">person</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t p-4 text-center text-sm text-gray-500 mt-auto">
                &copy; {{ date('Y') }} Ngajar.ID. All rights reserved.
            </footer>
        </div>
    </div>
    <!-- Global Loader -->
    <div id="global-loader"
        class="fixed inset-0 z-100 bg-white flex flex-col items-center justify-center transition-opacity duration-500">
        <div class="relative w-24 h-24 mb-4">
            <!-- Pulsing Background -->
            <div class="absolute inset-0 bg-teal-100 rounded-full animate-ping opacity-75"></div>
            <!-- Logo/Icon Container -->
            <div
                class="relative bg-white rounded-full p-4 shadow-xl border border-teal-100 flex items-center justify-center w-full h-full">
                <span class="material-symbols-rounded text-5xl text-teal-600 animate-pulse">
                    school
                </span>
            </div>
        </div>

        <div class="flex flex-col items-center gap-2">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                Ngajar<span class="text-teal-600">.id</span>
            </h2>
            <div class="flex gap-1">
                <div class="w-2 h-2 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-2 h-2 bg-teal-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-teal-800 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            const loader = document.getElementById('global-loader');
            if (loader) {
                setTimeout(() => {
                    loader.style.opacity = '0';
                    loader.style.pointerEvents = 'none';
                    setTimeout(() => {
                        loader.remove();
                    }, 500);
                }, 800);
            }
        });
    </script>
</body>

</html>