@extends('layouts.app')

@section('title', 'Verifikasi Email - Ngajar.ID')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl text-center border border-gray-100">
        <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-rounded text-4xl text-teal-600">mark_email_unread</span>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 font-robotoSlab">Verifikasi Email</h2>
        
        <p class="mt-4 text-sm text-gray-600 leading-relaxed">
            Terima kasih telah bergabung dengan Ngajar.ID! Sebelum memulai, silakan periksa kotak masuk email Anda dan klik tautan verifikasi yang baru saja kami kirimkan.
        </p>
        
        @if (session('status') == 'verification-link-sent')
            <div class="mt-6 mb-4 font-medium text-sm text-teal-700 bg-teal-50 p-4 rounded-xl border border-teal-100">
                Tautan verifikasi baru telah dikirim ke alamat email Anda.
            </div>
        @endif

        <div class="mt-8 space-y-4">
            <form method="POST" action="{{ route('auth.resend-verification') }}">
                @csrf
                <x-buttons.primary type="submit" fullWidth="true">
                    Kirim Ulang Tautan Verifikasi
                </x-buttons.primary>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-buttons.secondary type="submit" fullWidth="true">
                    Logout
                </x-buttons.secondary>
            </form>
        </div>
    </div>
</div>
@endsection
