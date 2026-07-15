@extends('layouts.dashboard')

@section('title', 'Analytics Kelas ' . $kelas->judul . ' - Ngajar.ID')
@section('header_title', 'Analytics Kelas')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('teacher.kelas') }}" class="text-teal-600 hover:text-teal-700 text-sm flex items-center gap-1 mb-2">
            <span class="material-symbols-rounded text-sm">arrow_back</span> Kembali ke Kelas Saya
        </a>
        <h2 class="text-2xl font-bold text-gray-800">{{ $kelas->judul }}</h2>
        <p class="text-gray-500 text-sm mt-1">Pantau performa dan keterlibatan murid</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
            <span class="material-symbols-rounded text-3xl">groups</span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Total Murid</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_students'] }}</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-500">
            <span class="material-symbols-rounded text-3xl">task_alt</span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Penyelesaian Rata-rata</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['completion_rate'] }}%</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
            <span class="material-symbols-rounded text-3xl">star</span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Rating Kelas</p>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['average_rating'], 1) }}</p>
                <div class="flex text-amber-400 text-sm">
                    @for($i=1; $i<=5; $i++)
                        <span class="material-symbols-rounded">{{ $i <= round($stats['average_rating']) ? 'star' : 'star_border' }}</span>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Chart Placeholder 1 -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-rounded text-teal-600">show_chart</span>
            Pendaftaran 30 Hari Terakhir
        </h3>
        <div class="h-64 flex items-end justify-between px-2 pb-6 border-b border-gray-100 relative">
            <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pb-6 pointer-events-none">
                <div>20</div>
                <div>10</div>
                <div>0</div>
            </div>
            @for($i=1; $i<=10; $i++)
                <div class="w-8 bg-teal-100 rounded-t-sm hover:bg-teal-200 transition relative group cursor-pointer" style="height: {{ rand(20, 100) }}%">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 pointer-events-none transition">
                        +{{ rand(1,5) }}
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Chart Placeholder 2 -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-rounded text-amber-500">donut_large</span>
            Distribusi Penyelesaian
        </h3>
        <div class="h-64 flex items-center justify-center">
            <div class="w-48 h-48 rounded-full border-8 border-gray-100 relative overflow-hidden flex items-center justify-center">
                <!-- Simplified CSS pie chart representation -->
                <div class="absolute w-full h-full rounded-full border-8 border-green-400 border-t-transparent border-l-transparent transform rotate-45"></div>
                <div class="absolute w-full h-full rounded-full border-8 border-amber-400 border-r-transparent border-b-transparent transform rotate-45"></div>
                <div class="text-center relative z-10">
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['completion_rate'] }}%</p>
                    <p class="text-xs text-gray-500">Selesai</p>
                </div>
            </div>
        </div>
        <div class="flex justify-center gap-6 mt-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <div class="w-3 h-3 rounded-full bg-green-400"></div> Selesai
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <div class="w-3 h-3 rounded-full bg-amber-400"></div> Sedang Belajar
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <div class="w-3 h-3 rounded-full bg-gray-200"></div> Belum Mulai
            </div>
        </div>
    </div>
</div>
@endsection
