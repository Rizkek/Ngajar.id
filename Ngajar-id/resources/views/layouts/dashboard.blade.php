<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Ngajar.ID</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://ui-avatars.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100;300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Inject Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 font-roboto">

    <div class="flex min-h-screen relative" x-data="{ sidebarOpen: false }">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-40 md:hidden glass"></div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-teal-600 text-white transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto">
            @include('partials.sidebar')
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 md:ml-0" id="main-content">
            <!-- Topbar -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="px-4 sm:px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Hamburger Menu (Mobile Only) -->
                        <button @click="sidebarOpen = !sidebarOpen" aria-label="Toggle Sidebar Menu"
                            class="md:hidden relative z-50 text-slate-500 hover:text-teal-600 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition-colors">
                            <span class="material-symbols-rounded text-2xl block">menu</span>
                        </button>
                        <h1 class="text-xl font-bold text-teal-500 truncate">@yield('header_title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-2 sm:space-x-4">
                        @if(auth()->user() && auth()->user()->role == 'murid')
                            <div
                                class="hidden md:flex items-center bg-amber-50 border border-amber-200 rounded-full px-3 py-1">
                                <span class="material-symbols-rounded text-amber-500 text-lg mr-1">monetization_on</span>
                                <span class="text-sm font-bold text-amber-700">{{ auth()->user()->getSaldoToken() }}
                                    Token</span>
                            </div>
                        @endif

                        <!-- Notifications Dropdown -->
                        <div class="relative" x-data="{ 
                            open: false, 
                            notifications: [], 
                            loading: false, 
                            loaded: false,
                            fetchNotifications() { 
                                if(this.loaded) return;
                                this.loading = true;
                                fetch('{{ route('notifications.latest') }}')
                                    .then(res => res.json())
                                    .then(data => { 
                                        this.notifications = data; 
                                        this.loading = false; 
                                        this.loaded = true;
                                    });
                            } 
                        }">
                            @php
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                            @endphp
                            <button @click="open = !open; if(open) fetchNotifications()" aria-label="Toggle Notifications"
                                class="relative p-2 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-full transition-colors focus:outline-none">
                                <span class="material-symbols-rounded text-2xl">notifications</span>
                                @if($unreadCount > 0)
                                    <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden" style="display: none;">
                                <div class="px-4 py-2 border-b border-slate-50 flex justify-between items-center">
                                    <h3 class="font-bold text-slate-800">Notifikasi</h3>
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-teal-600 hover:underline">Lihat Semua</a>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    
                                    <template x-if="loading">
                                        <div class="px-4 py-8 text-center text-slate-400">
                                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-teal-500 mx-auto"></div>
                                        </div>
                                    </template>

                                    <template x-if="!loading && notifications.length > 0">
                                        <div>
                                            <template x-for="notif in notifications" :key="notif.id">
                                                <div :class="'px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 ' + (notif.is_read ? 'opacity-60' : '')">
                                                    <div class="flex gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                                            <span class="material-symbols-rounded text-teal-600 text-lg" x-text="notif.type"></span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-bold text-slate-900 truncate" x-text="notif.title"></p>
                                                            <p class="text-xs text-slate-600 line-clamp-2" x-text="notif.message"></p>
                                                            <p class="text-[10px] text-slate-400 mt-1" x-text="notif.time"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="!loading && notifications.length === 0 && loaded">
                                        <div class="px-4 py-8 text-center text-slate-400">
                                            <span class="material-symbols-rounded text-4xl mb-2 opacity-20">notifications_off</span>
                                            <p class="text-sm italic">Belum ada notifikasi.</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Profile/User Info Placeholder -->
                        <div class="flex items-center space-x-2">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name ?? 'Pengguna' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role ?? 'Guest') }}</p>
                            </div>
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600">
                                <span class="material-symbols-rounded text-xl">person</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 flex-1 overflow-x-hidden">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t p-4 text-center text-sm text-gray-500 mt-auto">
                &copy; {{ date('Y') }} Ngajar.ID. All rights reserved.
            </footer>
        </div>
    </div>

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
                }, 200);
            }
        });
    </script>
</body>

</html>