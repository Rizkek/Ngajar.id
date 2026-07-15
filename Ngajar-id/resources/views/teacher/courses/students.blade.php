@extends('layouts.dashboard')

@section('title', 'Murid Kelas ' . $kelas->judul . ' - Ngajar.ID')
@section('header_title', 'Daftar Murid')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('teacher.kelas') }}" class="text-teal-600 hover:text-teal-700 text-sm flex items-center gap-1 mb-2">
            <span class="material-symbols-rounded text-sm">arrow_back</span> Kembali ke Kelas Saya
        </a>
        <h2 class="text-2xl font-bold text-gray-800">{{ $kelas->judul }}</h2>
        <p class="text-gray-500 text-sm mt-1">Daftar murid yang terdaftar di kelas ini</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-bold text-gray-700 flex items-center gap-2">
            <span class="material-symbols-rounded text-teal-500">group</span>
            Total Murid: {{ $students->total() ?? 0 }}
        </h3>
        <div class="relative">
            <input type="text" placeholder="Cari murid..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 w-64">
            <span class="material-symbols-rounded absolute left-3 top-2.5 text-gray-400 text-sm">search</span>
        </div>
    </div>

    @if($students->isEmpty())
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <span class="material-symbols-rounded text-4xl text-gray-300">sentiment_dissatisfied</span>
            </div>
            <h4 class="text-lg font-bold text-gray-800">Belum Ada Murid</h4>
            <p class="text-gray-500 text-sm mt-1 max-w-sm mx-auto">Kelas ini belum memiliki murid yang terdaftar. Bagikan link kelas Anda untuk mulai mengajar!</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-100 text-sm text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-4 font-bold">Nama Murid</th>
                        <th class="px-6 py-4 font-bold">Tanggal Bergabung</th>
                        <th class="px-6 py-4 font-bold text-center">Progress</th>
                        <th class="px-6 py-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($students as $siswa)
                        <tr class="hover:bg-teal-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold">
                                        {{ substr($siswa->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $siswa->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $siswa->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($siswa->pivot->created_at ?? now())->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-teal-600 h-2.5 rounded-full" style="width: {{ rand(10, 100) }}%"></div>
                                </div>
                                <p class="text-xs text-center text-gray-500 mt-1">{{ rand(10, 100) }}% Selesai</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-teal-600 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-3 py-1 rounded text-sm transition font-medium">
                                    Pesan
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
