@extends('layouts.app')

@section('title', 'Masuk - Ngajar.ID')

@section('content')
    <div class="flex flex-col items-center justify-start pt-20 mb-20 px-4">
        <h1 class="text-3xl font-bold mb-2">Masuk</h1>
        <p class="mb-6 text-center text-gray-600">Silahkan Masukkan Akun Ngajar.ID</p>

        @if ($errors->any())
            <div class="w-full max-w-sm mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="w-full max-w-sm mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
        @endif

        <form class="w-full max-w-sm space-y-4" method="POST" action="{{ route('login') }}">
            @csrf
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
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-teal-600 text-white font-medium rounded-md hover:bg-teal-700 transition-colors">
                Masuk
            </button>

            <p class="text-center text-sm">Belum memiliki akun? <a href="{{ url('/register') }}"
                    class="text-teal-600 font-medium hover:underline">Daftar</a></p>
        </form>
    </div>
@endsection