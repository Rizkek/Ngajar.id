{{-- @var \App\Models\User $user --}}
@extends('layouts.dashboard')

@section('title', 'Profil Saya')
@section('header_title', 'Pengaturan Profil')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Sidebar (Navigation/Quick Info) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <div class="relative w-32 h-32 mx-auto mb-4">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                class="w-full h-full object-cover rounded-full border-4 border-white shadow-md">
                        @else
                            <div
                                class="w-full h-full rounded-full bg-linear-to-br from-teal-500 to-blue-500 flex items-center justify-center text-white text-4xl font-bold shadow-md">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <!-- Rank Badge (Optional) -->
                        <div class="absolute bottom-0 right-0 bg-amber-400 text-white p-2 rounded-full border-4 border-white shadow-sm"
                            title="Level {{ $user->level }}">
                            <span class="material-symbols-rounded text-xl">military_tech</span>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-slate-500 text-sm mb-4">{{ ucfirst($user->role) }}</p>

                    <div class="flex items-center justify-center gap-2 mb-6">
                        <span
                            class="px-3 py-1 bg-teal-50 text-teal-700 text-xs font-bold rounded-full border border-teal-100">
                            Level {{ $user->level ?? 1 }}
                        </span>
                        <span
                            class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-100">
                            {{ $user->xp ?? 0 }} XP
                        </span>
                    </div>

                    <div class="border-t border-gray-100 pt-6 text-left">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Statistik</p>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Bergabung</span>
                                <span class="font-medium text-slate-900">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            @if($user->isMurid())
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">Kelas Diikuti</span>
                                    <span class="font-medium text-slate-900">{{ $user->kelasIkuti->count() }}</span>
                                </div>
                            @endif
                            @if($user->isPengajar())
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">Kelas Dibuat</span>
                                    <span class="font-medium text-slate-900">{{ $user->kelasAjar->count() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content (Forms) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Session Messages -->
                @if (session('success'))
                    <div
                        class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <span class="material-symbols-rounded text-green-600">check_circle</span>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Edit Profile Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                            <span class="material-symbols-rounded text-blue-600">person</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Informasi Pribadi</h3>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-900">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-900">
                            </div>

                            <!-- Avatar -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Foto Profil</label>
                                <div class="flex items-center gap-4">
                                    <div class="relative group cursor-pointer w-full">
                                        <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*"
                                            onchange="previewImage(this)">
                                        <label for="avatar"
                                            class="flex items-center justify-center w-full px-4 py-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer gap-2 text-slate-500 group-hover:text-blue-600">
                                            <span class="material-symbols-rounded">cloud_upload</span>
                                            <span class="font-medium text-sm">Klik untuk upload foto baru</span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 flex items-center gap-2">
                                    <span class="material-symbols-rounded">save</span>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-teal-50 flex items-center justify-center">
                            <span class="material-symbols-rounded text-teal-600">lock_reset</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Ubah Password</h3>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <!-- Current Password -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Password Saat Ini</label>
                                <div class="relative">
                                    <input type="password" name="current_password" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium text-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- New Password -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
                                    <input type="password" name="password" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium text-slate-900">
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi
                                        Password</label>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium text-slate-900">
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-teal-600/20 hover:shadow-teal-600/30 flex items-center gap-2">
                                    <span class="material-symbols-rounded">key</span>
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    // Update preview logic here if needed, or rely on page reload after save
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection