@extends('layouts.app')

@section('title', 'Daftar - Ngajar.ID')

@section('content')
    <div class="flex flex-col items-center justify-start pt-20 mb-20 px-4">
        <h1 class="text-3xl font-bold mb-2">Daftar Akun Baru</h1>
        <p class="mb-6 text-center text-gray-600">Bergabunglah dengan Ngajar.ID</p>

        @if ($errors->any())
            <div class="w-full max-w-sm mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="w-full max-w-sm space-y-4" method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror" />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-red-500 @enderror" />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-red-500 @enderror" />
                <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi
                    Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500" />
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Daftar Sebagai</label>
                <select id="role" name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 @error('role') border-red-500 @enderror">
                    <option value="">-- Pilih Role --</option>
                    <option value="murid" {{ old('role') == 'murid' ? 'selected' : '' }}>Murid (Pelajar)</option>
                    <option value="pengajar" {{ old('role') == 'pengajar' ? 'selected' : '' }}>Pengajar (Guru/Relawan)
                    </option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 transition-colors">
                Daftar
            </button>

            <p class="text-center text-sm">Sudah memiliki akun? <a href="{{ url('/login') }}"
                    class="text-teal-600 font-medium hover:underline">Masuk</a></p>
        </form>
    </div>
@endsection