@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Ngajar.ID')

@section('dashboard-content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Admin Dashboard</h1>
            <p class="text-slate-600">Ringkasan statistik platform Ngajar.ID</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Murid -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-4">
                    <span class="material-symbols-rounded text-2xl">school</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Murid</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($totalMurid) }}</h3>
                </div>
            </div>

            <!-- Total Pengajar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 mr-4">
                    <span class="material-symbols-rounded text-2xl">person_book</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pengajar</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($totalPengajar) }}</h3>
                </div>
            </div>

            <!-- Total Kelas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 mr-4">
                    <span class="material-symbols-rounded text-2xl">class</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Kelas Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ number_format($totalKelas) }}</h3>
                </div>
            </div>

            <!-- Total Donasi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-4">
                    <span class="material-symbols-rounded text-2xl">volunteer_activism</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Donasi</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalDonasi, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- New Users Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">Pengguna Terbaru</h2>
                    <a href="#" class="text-teal-600 text-sm hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($latestUsers as $user)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="font-medium text-slate-700">{{ $user->name }}</span>
                                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="px-2 py-1 text-xs font-semibold rounded-full 
                                                            {{ $user->role == 'admin' ? 'bg-red-100 text-red-700' :
                                ($user->role == 'pengajar' ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-700') }}">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                        {{ $user->created_at->diffForHumans() }}
                                                    </td>
                                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- System Status / Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-2 gap-4">
                    <button
                        class="p-4 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-rounded text-amber-500 text-3xl mb-2">verified</span>
                        <span class="text-sm font-medium text-slate-700">Verifikasi Pengajar</span>
                    </button>
                    <button
                        class="p-4 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-rounded text-red-500 text-3xl mb-2">campaign</span>
                        <span class="text-sm font-medium text-slate-700">Buat Pengumuman</span>
                    </button>
                    <button
                        class="p-4 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-rounded text-green-500 text-3xl mb-2">payments</span>
                        <span class="text-sm font-medium text-slate-700">Laporan Keuangan</span>
                    </button>
                    <button
                        class="p-4 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-rounded text-blue-500 text-3xl mb-2">settings</span>
                        <span class="text-sm font-medium text-slate-700">Pengaturan Sistem</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection