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
                    <p class="text-blue-100 text-xl">10.000+ Murid & Relawan</p>
                </div>

                <!-- Illustration -->
                <div class="w-full max-w-md mb-8">
                    <img src="{{ asset('img/register-bg.jpg') }}"
                         fetchpriority="high"
                         alt="Team Learning"
                         class="w-full h-auto rounded-2xl shadow-2xl transition-transform duration-500">
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
                            <p class="text-sm text-blue-100">Belajar bersama ribuan murid lainnya</p>
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
                                <p class="font-bold text-red-900 mb-1">Gagal Mendaftar</p>
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
                <form method="POST" action="{{ route('register') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf

                    <!-- Name Input -->
                    <x-inputs.text 
                        name="name" 
                        label="Nama Lengkap" 
                        placeholder="Masukkan nama lengkap" 
                        icon="person" 
                        required="true" 
                    />

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
                                placeholder="Minimal 8 karakter"
                                required
                                class="block w-full rounded-xl border-gray-300 focus:ring-teal-500 focus:border-teal-500 sm:text-sm pl-10 pr-10 @error('password') border-red-500 @enderror"
                            >
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <x-icons.material x-text="show ? 'visibility_off' : 'visibility'" size="sm" />
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                            <x-icons.material name="info" size="sm" /> Gunakan kombinasi huruf, angka, dan simbol
                        </p>
                        @error('password')
                            <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                <x-icons.material name="error" size="sm" /> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Confirmation Input with Toggle -->
                    <div x-data="{ show: false }" class="space-y-1 w-full">
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icons.material name="lock_clock" size="sm" class="text-gray-400" />
                            </div>
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                placeholder="Ulangi password"
                                required
                                class="block w-full rounded-xl border-gray-300 focus:ring-teal-500 focus:border-teal-500 sm:text-sm pl-10 pr-10"
                            >
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <x-icons.material x-text="show ? 'visibility_off' : 'visibility'" size="sm" />
                            </button>
                        </div>
                    </div>

                    <!-- Role Selection (Card Selection) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Daftar Sebagai <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Murid Card -->
                            <label class="role-card cursor-pointer">
                                <input type="radio" name="role" value="murid" class="sr-only role-radio"
                                    {{ old('role') == 'murid' ? 'checked' : '' }} required>
                                <div class="role-card-content p-5 border-2 border-gray-200 rounded-2xl transition-all hover:border-teal-300 hover:shadow-md bg-white">
                                    <div class="flex flex-col items-center text-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center">
                                            <x-icons.material name="school" class="text-3xl text-blue-600" />
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
                                <input type="radio" name="role" value="pengajar" class="sr-only role-radio"
                                    {{ old('role') == 'pengajar' ? 'checked' : '' }} required>
                                <div class="role-card-content p-5 border-2 border-gray-200 rounded-2xl transition-all hover:border-teal-300 hover:shadow-md bg-white">
                                    <div class="flex flex-col items-center text-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-teal-50 flex items-center justify-center">
                                            <x-icons.material name="person_raised_hand" class="text-3xl text-teal-600" />
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
                                <x-icons.material name="error" size="sm" /> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Optional Fields Accordion (Phone, Avatar, Referral) -->
                    <div x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between py-3.5 px-4 bg-slate-50 hover:bg-slate-100 rounded-2xl border-2 border-dashed border-gray-200 hover:border-teal-300 transition-all"
                            :aria-expanded="open">
                            <span class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                                <span class="material-symbols-rounded text-base text-teal-500">tune</span>
                                Info Tambahan
                                <span class="text-xs text-slate-400 font-normal">(opsional — bisa diisi nanti)</span>
                            </span>
                            <span class="material-symbols-rounded text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-4 space-y-5 pt-4 border-t border-dashed border-gray-200">

                            <!-- Phone Number Input -->
                            <x-inputs.text 
                                name="phone" 
                                label="Nomor Telepon" 
                                type="tel" 
                                placeholder="+62 8xx xxxx xxxx" 
                                icon="call" 
                            />



                            <!-- Avatar Upload -->
                            <div>
                                <label for="avatar" class="block text-sm font-bold text-slate-700 mb-2">Foto Profil</label>
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden"
                                            onchange="previewAvatar(this)">
                                        <label for="avatar"
                                            class="flex items-center justify-center w-24 h-24 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:border-teal-500 transition-all group">
                                            <img id="avatarPreview" src="" alt="Preview" class="hidden w-24 h-24 object-cover rounded-xl">
                                            <div id="avatarPlaceholder" class="flex flex-col items-center justify-center">
                                                <span class="material-symbols-rounded text-3xl text-slate-400 group-hover:text-teal-600">cloud_upload</span>
                                                <p class="text-xs text-slate-500 mt-1 text-center">Pilih Foto</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex-1 text-xs text-slate-500">
                                        <p class="font-semibold text-slate-700 mb-1">Format: JPG, PNG (Max 2MB)</p>
                                        <p>Foto akan digunakan sebagai profil Anda di platform</p>
                                    </div>
                                </div>
                                @error('avatar')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <span class="material-symbols-rounded text-base">error</span>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Referral Code -->
                            <x-inputs.text 
                                name="referral_code" 
                                label="Kode Referral" 
                                placeholder="Masukkan kode referral dari teman Anda" 
                                icon="loyalty" 
                            />
                            <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                                <x-icons.material name="gift" size="sm" />
                                Dapatkan 500 token sebagai bonus jika menggunakan kode teman!
                            </p>
                        </div><!-- end x-show -->
                    </div><!-- end accordion -->

                    <!-- Email Notifications Preference -->
                    <div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-lg">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1"
                                {{ old('email_notifications', true) ? 'checked' : '' }}
                                class="mt-1.5 w-5 h-5 text-teal-600 rounded focus:ring-2 focus:ring-teal-500">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-900">Terima Email Pemberitahuan</p>
                                <p class="text-xs text-slate-600 mt-0.5">Kami akan mengirim update tentang kursus, sertifikat, dan tawaran khusus</p>
                            </div>
                        </label>
                    </div>

                    <!-- Terms & Conditions -->
                    <div>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms" required
                                {{ old('terms') ? 'checked' : '' }}
                                class="mt-1.5 w-5 h-5 text-teal-600 rounded focus:ring-2 focus:ring-teal-500 @error('terms') border-red-500 @enderror">
                            <div class="flex-1">
                                <p class="text-sm text-slate-700">
                                    Saya menyetujui
                                    <a href="{{ route('terms-of-service') }}" target="_blank" class="text-teal-600 font-semibold hover:underline">Syarat & Ketentuan</a>,
                                    <a href="{{ route('privacy-policy') }}" target="_blank" class="text-teal-600 font-semibold hover:underline">Kebijakan Privasi</a>
                                </p>
                            </div>
                        </label>
                        @error('terms')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <x-buttons.primary type="submit" id="submitBtn" fullWidth="true" size="lg" class="shadow-lg shadow-teal-600/30 w-full mt-2">
                        Daftar Sekarang
                    </x-buttons.primary>

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
                        Daftar dengan Google
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

        // Avatar Preview Function
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    const placeholder = document.getElementById('avatarPlaceholder');

                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
