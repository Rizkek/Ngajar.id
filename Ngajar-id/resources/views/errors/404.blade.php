@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - Ngajar.ID')

@section('content')
    <div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md w-full text-center">
            <!-- Illustration -->
            <div class="mb-8 relative mx-auto w-64 h-64">
                <div class="absolute inset-0 bg-brand-50 rounded-full animate-pulse blur-xl opacity-70"></div>
                <svg class="relative w-full h-full text-brand-600 drop-shadow-xl" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"
                        fill="currentColor" fill-opacity="0.2" />
                    <path
                        d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-5h2v2h-2zm0-8h2v6h-2z"
                        fill="currentColor" />
                </svg>
                <div
                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-4 px-4 py-1 bg-white rounded-full border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 tracking-widest">ERROR 404</span>
                </div>
            </div>

            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl mb-4">
                Oops! Halaman Hilang
            </h1>
            <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                Sepertinya halaman yang kamu cari sedang bolos atau sudah pindah kelas. Yuk, kita kembali ke jalan yang
                benar!
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-brand-600 hover:bg-brand-700 hover:shadow-lg hover:shadow-brand-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                    <span class="material-symbols-rounded mr-2">home</span>
                    Kembali ke Beranda
                </a>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 text-base font-medium rounded-full text-slate-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-300">
                    <span class="material-symbols-rounded mr-2">arrow_back</span>
                    Kembali Halaman Sebelumnya
                </a>
            </div>
        </div>
    </div>
@endsection