@extends('layouts.app')

@section('title', 'Masuk - Ngajar.ID')

@section('content')
    <div class="min-h-screen flex">
        <!-- Left Side - Illustration & Testimonial (Split Screen) -->
        <div
            class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-teal-600 via-teal-500 to-blue-600 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32"></div>

            <div class="relative z-10 flex flex-col justify-center items-center px-48 text-white">
                <!-- Logo/Brand -->
                <div class="mb-8">
                    <h2 class="text-5xl font-black mb-3">Ngajar.ID</h2>
                    <p class="text-teal-100 text-lg">Platform Edukasi Terpercaya</p>
                </div>

                <!-- Illustration -->
                <div class="w-full max-w-md mb-8">
                    <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800&auto=format&fit=crop"
                        alt="Students Learning"
                        class="w-full h-auto rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-500">
                </div>

                <!-- Testimonial -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 max-w-md border border-white/20">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-rounded text-4xl text-white/80">format_quote</span>
                        <div>
                            <p class="text-white/90 mb-3 italic">
                                "Ngajar.ID membuat saya lebih mudah belajar matematika. Guru-gurunya sabar dan materinya
                                mudah dipahami!"
                            </p>
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">
                                    S
                                </div>
                                <div>
                                    <p class="font-bold text-sm">Siti Nurhaliza</p>
                                    <p class="text-xs text-teal-100">Siswa SMP Kelas 8</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-black text-slate-900 mb-2">Selamat Datang!</h1>
                    <p class="text-slate-600">Masuk untuk melanjutkan pembelajaran Anda</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-red-600">error</span>
                            <div class="flex-1">
                                <p class="font-bold text-red-900 mb-1">Terjadi Kesalahan</p>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-green-600">check_circle</span>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Input with Toggle -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all pr-12 @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <span class="material-symbols-rounded" id="passwordIcon">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <span class="text-slate-600">Ingat saya</span>
                        </label>
                        <a href="#" class="text-teal-600 font-semibold hover:text-teal-700 hover:underline">Lupa
                            password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold text-lg rounded-2xl shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 hover:from-teal-700 hover:to-teal-600 transition-all duration-300 transform hover:-translate-y-0.5">
                        Masuk Sekarang
                    </button>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gray-50 text-slate-500 font-medium">Atau masuk dengan</span>
                        </div>
                    </div>

                    <!-- Social Login (Google) -->
                    <a href="{{ route('auth.google') }}"
                        class="w-full py-3.5 bg-white border-2 border-gray-200 text-slate-700 font-semibold rounded-2xl hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Lanjutkan dengan Google
                    </a>

                    <!-- Register Link -->
                    <p class="text-center text-sm text-slate-600">
                        Belum punya akun?
                        <a href="{{ url('/register') }}"
                            class="text-teal-600 font-bold hover:text-teal-700 hover:underline">
                            Daftar Sekarang
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

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