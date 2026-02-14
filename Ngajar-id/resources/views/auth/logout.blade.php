<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logout - Ngajar.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-teal-600 border-t-transparent rounded-full animate-spin mx-auto mb-4">
        </div>
        <p class="text-gray-600 text-lg">Logging out...</p>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        // Auto submit setelah halaman load
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('logout-form').submit();
            }, 500);
        });
    </script>
</body>

</html>