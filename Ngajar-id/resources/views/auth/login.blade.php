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
                    <img src="{{ asset('img/auth-bg.jpg') }}"
                        fetchpriority="high"
                        alt="Students Learning"
                        class="w-full h-auto rounded-2xl shadow-2xl transition-transform duration-500">
                </div>

                <!-- Testimonial -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 max-w-md border border-white/20">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-rounded text-4xl text-white/80">format_quote</span>
                        <div>
                            <p class="text-white/90 mb-3 italic">
                                "Ngajar.ID membuat saya lebih mudah belajar matematika. Pengajarnya sabar dan materinya
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
                                <p class="font-bold text-red-900 mb-1">Gagal Masuk</p>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif



                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <x-inputs.text 
                        name="email" 
                        label="Email" 
                        type="email" 
                        placeholder="nama@email.com" 
                        icon="mail" 
                        required="true" 
                    />

                    <!-- Password Input with Toggle -->
                    <div x-data="{ show: false }" class="space-y-1 w-full">
                        <label for="password" class="block text-sm font-bold text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icons.material name="lock" size="sm" class="text-gray-400" />
                            </div>
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password" 
                                id="password" 
                                placeholder="Masukkan password"
                                required
                                class="block w-full rounded-xl border-gray-300 focus:ring-teal-500 focus:border-teal-500 sm:text-sm pl-10 pr-10 @error('password') border-red-500 @enderror"
                            >
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <x-icons.material x-text="show ? 'visibility_off' : 'visibility'" size="sm" />
                            </button>
                        </div>
                        @error('password')
                            <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                <x-icons.material name="error" size="sm" /> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end text-sm">
                        <a href="{{ route('password.request') }}"
                            class="text-teal-600 font-bold hover:text-teal-700 hover:underline">Lupa password?</a>
                    </div>

                    <!-- Submit Button -->
                    <x-buttons.primary type="submit" fullWidth="true" size="lg" class="shadow-lg shadow-teal-600/30 w-full mt-2">
                        Masuk Sekarang
                    </x-buttons.primary>

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
                        Masuk dengan Google
                    </a>

                    <!-- Register Link -->
                    <p class="text-center text-sm text-slate-600">
                        Belum bergabung dengan komunitas?
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

        // Login Loading Handler
        document.querySelector('form').addEventListener('submit', function (e) {
            const btn = this.querySelector('button[type="submit"]');

            // Update Button State only — no fullscreen overlay
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            btn.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Sedang Masuk...</span>
                    </div>
                `;
        });
    </script>
@endsection