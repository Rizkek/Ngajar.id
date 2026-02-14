@extends('layouts.app')

@section('title', 'Lupa Password - Ngajar.ID')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-3xl text-teal-600">lock_reset</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900">Lupa Password?</h1>
                <p class="text-slate-600 mt-2 text-sm">Masukkan email Anda untuk menerima link reset password.</p>
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

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Terdaftar</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-50 transition-all placeholder-slate-400"
                        placeholder="nama@email.com">
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 hover:from-teal-700 hover:to-teal-600 transition-all transform hover:-translate-y-0.5">
                    Kirim Link Reset
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}"
                        class="text-sm text-slate-500 hover:text-teal-600 font-medium flex items-center justify-center gap-1 transition-colors">
                        <span class="material-symbols-rounded text-lg">arrow_back</span>
                        Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection