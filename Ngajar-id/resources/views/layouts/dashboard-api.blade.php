<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-url" data-api-url="{{ env('API_URL') ?? url('/api/v1') }}">

    <title>@yield('title', 'Dashboard') - Ngajar.ID</title>

    <!-- Styles -->
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
                        }
                    }
                }
            }
        }
    </script>

    @yield('styles')
</head>

<body class="bg-gray-50 font-roboto">
    <!-- Load API Client -->
    <script src="{{ asset('js/api-client.js') }}"></script>

    <!-- Initialize authentication if token exists -->
    <script>
        // Check for stored auth token and set it in API client
        const storedToken = localStorage.getItem('auth_token');
        if (storedToken) {
            api.authToken = storedToken;
        }

        // Handle logout
        window.handleLogout = async function() {
            try {
                await api.logout();
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout error:', error);
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
            }
        };

        // Toast notification helper
        window.showToast = function(message, type = 'info') {
            console.log(`[${type.toUpperCase()}] ${message}`);
            // Can be enhanced with a toast library later
        };

        // Handle API errors globally
        window.handleApiError = function(error, message = 'An error occurred') {
            console.error('API Error:', error);
            showToast(`${message}: ${error.message}`, 'error');
        };
    </script>

    <!-- Inject Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="flex min-h-screen relative" x-data="{ sidebarOpen: false }">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-40 md:hidden glass"></div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-teal-600 text-white transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto shadow-lg">
            <!-- Logo -->
            <div class="p-6 border-b border-teal-500/30">
                <h1 class="text-2xl font-bold">Ngajar.ID</h1>
                <p class="text-sm text-teal-100 mt-1">Student Portal</p>
            </div>

            <!-- Menu -->
            <nav class="mt-6 px-3 space-y-2">
                <a href="/dashboard" class="flex items-center px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    <span class="material-symbols-rounded mr-3">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <a href="/student/courses" class="flex items-center px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    <span class="material-symbols-rounded mr-3">menu_book</span>
                    <span>Cari Kursus</span>
                </a>

                <a href="/student/my-courses" class="flex items-center px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    <span class="material-symbols-rounded mr-3">my_library_books</span>
                    <span>Kursus Saya</span>
                </a>

                <a href="/student/learning-paths" class="flex items-center px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    <span class="material-symbols-rounded mr-3">collection_bookmark</span>
                    <span>Learning Path</span>
                </a>

                <a href="/student/certificates" class="flex items-center px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    <span class="material-symbols-rounded mr-3">card_membership</span>
                    <span>Sertifikat</span>
                </a>

                <a href="/student/leaderboard" class="flex items-center px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    <span class="material-symbols-rounded mr-3">emoji_events</span>
                    <span>Leaderboard</span>
                </a>
            </nav>

            <!-- User section at bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-teal-500/30 bg-teal-700/50">
                <button onclick="handleLogout()" class="w-full flex items-center px-4 py-2 text-teal-100 hover:text-white transition-colors rounded-lg hover:bg-teal-800">
                    <span class="material-symbols-rounded mr-2">logout</span>
                    <span class="text-sm">Logout</span>
                </button>
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="px-4 sm:px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Hamburger Menu (Mobile Only) -->
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="md:hidden text-slate-500 hover:text-teal-600 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition-colors">
                            <span class="material-symbols-rounded text-2xl">menu</span>
                        </button>
                        <h1 class="text-xl font-bold text-teal-600">@yield('header_title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ notifOpen: false }">
                            <button @click="notifOpen = !notifOpen"
                                class="relative p-2 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-full transition-colors">
                                <span class="material-symbols-rounded">notifications</span>
                                <span id="unread-count" class="hidden absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
                            </button>

                            <div x-show="notifOpen" @click.away="notifOpen = false"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-100 z-50">
                                <div class="p-4 border-b border-slate-100">
                                    <h3 class="font-bold">Notifikasi</h3>
                                </div>
                                <div id="notifications-list" class="max-h-80 overflow-y-auto">
                                    <div class="p-4 text-center text-slate-500 text-sm">Memuat...</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile -->
                        <div class="flex items-center space-x-3">
                            <div class="hidden sm:text-right">
                                <p id="user-name" class="text-sm font-semibold text-slate-800">User</p>
                                <p class="text-xs text-slate-500">Student</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold cursor-pointer">
                                <span id="user-avatar" class="text-lg">U</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="p-4 sm:p-6 lg:p-8">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 mt-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-slate-600">
                    <p>&copy; {{ date('Y') }} Ngajar.ID. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Initialize User Data -->
    <script>
        (async function initPage() {
            try {
                const userResponse = await api.getCurrentUser();
                if (userResponse.success && userResponse.data) {
                    const user = userResponse.data;
                    document.getElementById('user-name').textContent = user.name || 'User';
                    document.getElementById('user-avatar').textContent = (user.name || 'U')[0].toUpperCase();
                }

                // Load unread notification count
                const notifCount = await api.getUnreadCount();
                if (notifCount.success && notifCount.data?.unread_count > 0) {
                    const badge = document.getElementById('unread-count');
                    badge.classList.remove('hidden');
                    badge.textContent = notifCount.data.unread_count;
                }

                // Load notifications
                const notifs = await api.getNotifications(1);
                if (notifs.success && notifs.data?.data) {
                    const notifList = document.getElementById('notifications-list');
                    if (notifs.data.data.length > 0) {
                        notifList.innerHTML = notifs.data.data.map(n => `
                            <div class="p-4 border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors">
                                <p class="font-semibold text-sm text-slate-800">${n.title || n.type}</p>
                                <p class="text-xs text-slate-600 mt-1">${n.message}</p>
                                <p class="text-xs text-slate-400 mt-1">${new Date(n.created_at).toLocaleDateString('id-ID')}</p>
                            </div>
                        `).join('');
                    } else {
                        notifList.innerHTML = '<div class="p-4 text-center text-slate-500 text-sm">Tidak ada notifikasi</div>';
                    }
                }
            } catch (error) {
                console.error('Failed to initialize page:', error);
            }
        })();
    </script>

    @yield('scripts')
</body>

</html>
