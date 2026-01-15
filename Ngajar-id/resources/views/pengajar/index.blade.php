@extends('layouts.dashboard')

@section('title', 'Dashboard Pengajar')
@section('header_title', 'Dashboard Relawan')

@section('content')
    <div class="bg-teal-500 text-white p-6 sm:p-8 rounded-xl shadow-lg mb-10 flex items-center space-x-6">
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white flex items-center justify-center text-teal-500">
            <i class="fas fa-user text-3xl"></i>
        </div>
        <div>
            <h2 class="text-2xl sm:text-3xl font-semibold mb-1">Terimakasih, Anda Hebat!</h2>
            <p class="text-sm sm:text-base opacity-90">"Idealisme adalah kemewahan terakhir yang hanya dimiliki oleh
                pemuda."</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Card 1 -->
        <div
            class="bg-teal-500 text-white p-5 rounded-xl shadow-md flex items-center space-x-4 hover:shadow-lg hover:-translate-y-1 transition-all">
            <i class="fas fa-graduation-cap text-3xl opacity-80"></i>
            <div>
                <p class="text-sm font-medium opacity-90">Total Kelas Dibina</p>
                <p class="text-2xl font-bold">{{ $stats['total_kelas'] ?? 0 }}</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div
            class="bg-teal-500 text-white p-5 rounded-xl shadow-md flex items-center space-x-4 hover:shadow-lg hover:-translate-y-1 transition-all">
            <i class="fas fa-book text-3xl opacity-80"></i>
            <div>
                <p class="text-sm font-medium opacity-90">Materi Dibuat</p>
                <p class="text-2xl font-bold">{{ $stats['total_materi'] ?? 0 }}</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div
            class="bg-teal-500 text-white p-5 rounded-xl shadow-md flex items-center space-x-4 hover:shadow-lg hover:-translate-y-1 transition-all">
            <i class="fas fa-users text-3xl opacity-80"></i>
            <div>
                <p class="text-sm font-medium opacity-90">Siswa Mengikuti</p>
                <p class="text-2xl font-bold">{{ $stats['total_siswa'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-7 rounded-xl shadow-md">
        <h3 class="text-xl font-semibold text-teal-500 mb-6">Kelas Yang Dibina</h3>
        <div class="space-y-4">
            @forelse($kelasList as $kelas)
                <a href="#" class="block hover:shadow-lg transition-shadow">
                    <div
                        class="border border-gray-200 rounded-lg p-4 flex items-center space-x-4 hover:shadow-sm hover:border-teal-500 transition-all">
                        <div class="p-3 bg-teal-100 rounded-lg flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-2xl text-teal-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-base text-gray-800">{{ $kelas['judul'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $kelas['total_siswa'] }} siswa aktif</p>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 text-center py-4">Belum ada kelas yang dibina.</p>
            @endforelse
        </div>
    </div>
@endsection