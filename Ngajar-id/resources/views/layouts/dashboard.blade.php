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
</body>

</html>