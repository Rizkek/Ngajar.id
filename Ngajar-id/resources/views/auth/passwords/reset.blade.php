@extends('layouts.app')

@section('title', 'Reset Password - Ngajar.ID')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-black text-slate-900">Ubah Password Baru</h1>
                <p class="text-slate-600 mt-2 text-sm">Pastikan password baru Anda aman dan kuat.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl flex items-start gap-3">
                    <span class="material-symbols-rounded text-green-600">check_circle</span>
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all font-medium text-slate-900"
                        placeholder="nama@email.com" readonly>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all pr-12 placeholder-slate-400"
                            placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('password', 'passwordIcon')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="material-symbols-rounded" id="passwordIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi
                        Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all pr-12 placeholder-slate-400"
                            placeholder="Ulangi password">
                        <button type="button" onclick="togglePassword('password_confirmation', 'confirmIcon')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="material-symbols-rounded" id="confirmIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 hover:from-teal-700 hover:to-teal-600 transition-all transform hover:-translate-y-0.5">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                passwordIcon.textContent = 'visibility';
            }
        }
    </script>
@endsection