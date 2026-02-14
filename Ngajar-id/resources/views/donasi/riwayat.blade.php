@extends('layouts.app')

@section('title', 'Riwayat Donasi - Ngajar.ID')

@section('content')
    <div class="bg-gradient-to-br from-teal-50 via-white to-amber-50 py-12 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">Riwayat Donasi</h1>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                Transparansi donasi yang telah diterima untuk mendukung kemajuan pendidikan di Indonesia.
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-slate-900">Donatur Terverifikasi</h3>
                <a href="{{ route('donasi') }}" class="text-sm text-teal-600 font-bold hover:underline">← Kembali Donasi</a>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($riwayat_donasi as $index => $donasi)
                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center gap-4 group">
                        <!-- Avatar -->
                        @php
                            $colors = ['bg-blue-100 text-blue-600', 'bg-pink-100 text-pink-600', 'bg-purple-100 text-purple-600', 'bg-orange-100 text-orange-600', 'bg-teal-100 text-teal-600'];
                            $colorClass = $colors[$loop->index % count($colors)];
                            $initials = collect(explode(' ', $donasi->nama ?: 'Hamba Allah'))->map(function ($segment) {
                                return strtoupper(substr($segment, 0, 1));
                            })->take(2)->join('');
                        @endphp

                        <div
                            class="w-12 h-12 rounded-full {{ $colorClass }} flex items-center justify-center font-bold text-base shadow-sm group-hover:scale-110 transition-transform flex-shrink-0">
                            {{ $initials }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                <p class="text-base font-bold text-slate-900 truncate">
                                    {{ $donasi->nama ?: 'Hamba Allah' }}
                                </p>
                                <p class="text-base font-bold text-teal-600 whitespace-nowrap">
                                    Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mt-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400 flex items-center gap-1">
                                        <span class="material-symbols-rounded text-sm">calendar_today</span>
                                        {{ \Carbon\Carbon::parse($donasi->tanggal)->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </div>

                                @if($donasi->pesan)
                                    <div class="hidden sm:block text-slate-300 mx-2">|</div>
                                    <p class="text-xs text-slate-500 italic truncate max-w-md">
                                        "{{ $donasi->pesan }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400">
                        <span class="material-symbols-rounded text-6xl mb-4 block opacity-30">savings_off</span>
                        <h4 class="text-lg font-bold text-slate-600 mb-2">Belum ada donasi terkonfirmasi</h4>
                        <p class="text-sm mb-6">Jadilah orang baik pertama hari ini!</p>
                        <a href="{{ route('donasi') }}"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-bold">
                            Donasi Sekarang
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-4 bg-gray-50/50 border-t border-gray-100">
                {{ $riwayat_donasi->links() }}
            </div>
        </div>
    </div>
@endsection