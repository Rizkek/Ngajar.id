@extends('layouts.app')

@section('title', 'Daftar - Ngajar.ID')

@section('content')
    <div class="min-h-screen flex">
        <!-- Left Side - Illustration & Benefits (Split Screen) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-teal-500 to-teal-600 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-white/10 rounded-full -ml-48 -mt-48"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-white/10 rounded-full -mr-40 -mb-40"></div>
            
            <div class="relative z-10 flex flex-col justify-center items-center px-48 text-white">
                <!-- Logo/Brand -->
                <div class="mb-8 text-center">
                    <h2 class="text-5xl font-black mb-3">Bergabung Bersama</h2>
                    <p class="text-blue-100 text-xl">10.000+ Pelajar & Relawan</p>
                </div>

                <!-- Illustration -->
                <div class="w-full max-w-md mb-8">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800&auto=format&fit=crop" 
                         alt="Team Learning" 
                         class="w-full h-auto rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-500">
                </div>

                <!-- Benefits List -->
                <div class="space-y-4 max-w-md w-full">
                    <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-white">school</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Belajar Gratis</h3>
                            <p class="text-sm text-blue-100">Akses ribuan materi pembelajaran tanpa biaya</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-white">groups</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Komunitas Aktif</h3>
                            <p class="text-sm text-blue-100">Belajar bersama ribuan siswa lainnya</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-white">verified</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Sertifikat Resmi</h3>
                            <p class="text-sm text-blue-100">Dapatkan sertifikat setelah menyelesaikan kursus</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-gray-50">
            <div class="w-full max-w-lg">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-black text-slate-900 mb-2">Buat Akun Baru</h1>
                    <p class="text-slate-600">Mulai perjalanan belajar Anda hari ini</p>
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

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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
                                placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePassword('password', 'passwordIcon')" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <span class="material-symbols-rounded" id="passwordIcon">visibility</span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                            <span class="material-symbols-rounded text-sm">info</span>
                            Gunakan kombinasi huruf, angka, dan simbol
                        </p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Confirmation Input with Toggle -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-4 py-3.5 bg-white border-2 border-gray-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all pr-12"
                                placeholder="Ulangi password">
                            <button type="button" onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <span class="material-symbols-rounded" id="confirmPasswordIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Role Selection (Card Selection) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Daftar Sebagai</label>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Murid Card -->
                            <label class="role-card cursor-pointer">
                                <input type="radio" name="role" value="murid" class="hidden role-radio" 
                                    {{ old('role') == 'murid' ? 'checked' : '' }} required>
                                <div class="role-card-content p-5 border-2 border-gray-200 rounded-2xl transition-all hover:border-teal-300 hover:shadow-md bg-white">
                                    <div class="flex flex-col items-center text-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center">
                                            <span class="material-symbols-rounded text-3xl text-blue-600">school</span>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-900 mb-1">Murid</h3>
                                            <p class="text-xs text-slate-500">Saya ingin belajar</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Pengajar Card -->
                            <label class="role-card cursor-pointer">
                                <input type="radio" name="role" value="pengajar" class="hidden role-radio"
                                    {{ old('role') == 'pengajar' ? 'checked' : '' }} required>
                                <div class="role-card-content p-5 border-2 border-gray-200 rounded-2xl transition-all hover:border-teal-300 hover:shadow-md bg-white">
                                    <div class="flex flex-col items-center text-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-teal-50 flex items-center justify-center">
                                            <span class="material-symbols-rounded text-3xl text-teal-600">person_raised_hand</span>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-900 mb-1">Pengajar</h3>
                                            <p class="text-xs text-slate-500">Saya ingin mengajar</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button (Dynamic Text based on Role) -->
                    <button type="submit" id="submitBtn"
                        class="w-full py-4 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold text-lg rounded-2xl shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 hover:from-teal-700 hover:to-teal-600 transition-all duration-300 transform hover:-translate-y-0.5">
                        Daftar Sekarang
                    </button>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gray-50 text-slate-500 font-medium">Atau daftar dengan</span>
                        </div>
                    </div>

                    <!-- Social Login (Google) -->
                    <a href="{{ route('auth.google') }}"
                        class="w-full py-3.5 bg-white border-2 border-gray-200 text-slate-700 font-semibold rounded-2xl hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Lanjutkan dengan Google
                    </a>

                    <!-- Login Link -->
                    <p class="text-center text-sm text-slate-600">
                        Sudah punya akun? 
                        <a href="{{ url('/login') }}" class="text-teal-600 font-bold hover:text-teal-700 hover:underline">
                            Masuk Sekarang
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Role Card Selected State */
        .role-radio:checked + .role-card-content {
            border-color: #14b8a6;
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.2);
        }

        .role-radio:checked + .role-card-content h3 {
            color: #14b8a6;
        }
    </style>

    <script>
        // Password Visibility Toggle
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

        // Dynamic Submit Button Text based on Role Selection
        const roleRadios = document.querySelectorAll('input[name="role"]');
        const submitBtn = document.getElementById('submitBtn');
        
        roleRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'murid') {
                    submitBtn.innerHTML = '<span class="material-symbols-rounded mr-2">school</span>Ayo Mulai Belajar!';
                } else if (this.value === 'pengajar') {
                    submitBtn.innerHTML = '<span class="material-symbols-rounded mr-2">volunteer_activism</span>Mulai Berbagi Ilmu';
                }
            });
        });

        // Set initial button text if role is already selected (old input)
        const selectedRole = document.querySelector('input[name="role"]:checked');
        if (selectedRole) {
            if (selectedRole.value === 'murid') {
                submitBtn.innerHTML = '<span class="material-symbols-rounded mr-2">school</span>Ayo Mulai Belajar!';
            } else if (selectedRole.value === 'pengajar') {
                submitBtn.innerHTML = '<span class="material-symbols-rounded mr-2">volunteer_activism</span>Mulai Berbagi Ilmu';
            }
        }
    </script>
@endsection