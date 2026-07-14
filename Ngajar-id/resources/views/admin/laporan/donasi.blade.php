@extends('layouts.dashboard')

@section('title', 'Laporan Donasi - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Laporan Donasi</h1>
                <p class="text-slate-600">Rekapitulasi donasi dan export data</p>
            </div>
            <div>
                <x-buttons.primary type="button" icon="download" class="bg-green-600 hover:bg-green-700" onclick="window.location.href='{{ route('admin.laporan.donasi.export', request()->query()) }}'">
                    Export CSV
                </x-buttons.primary>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Total Donasi</p>
                        <h3 class="text-3xl font-black">Rp {{ number_format($totalDonasi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">payments</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Jumlah Transaksi</p>
                        <h3 class="text-3xl font-black">{{ number_format($countDonasi) }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">receipt_long</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Rata-rata Donasi</p>
                        <h3 class="text-3xl font-black">Rp {{ number_format($avgDonasi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">trending_up</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <!-- Filter - Modern Design -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <!-- Start Date -->
                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-rounded text-teal-600 text-lg">calendar_today</span>
                        Dari Tanggal
                    </label>
                    <x-form.input type="date" name="start_date" value="{{ request('start_date') }}" />
                </div>

                <!-- End Date -->
                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-rounded text-teal-600 text-lg">event</span>
                        Sampai Tanggal
                    </label>
                    <x-form.input type="date" name="end_date" value="{{ request('end_date') }}" />
                </div>

                <!-- Filter Button -->
                <x-buttons.primary type="submit" icon="filter_alt">
                    Filter
                </x-buttons.primary>

                <!-- Reset Button -->
                <x-buttons.secondary type="button" icon="refresh" onclick="window.location.href='{{ route('admin.laporan.donasi') }}'">
                    Reset
                </x-buttons.secondary>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Donatur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($donations as $donasi)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $donasi->tanggal->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-slate-700">{{ $donasi->nama ?: 'Hamba Allah' }}</span>
                                    @if(!$donasi->nama)
                                        <span class="text-xs text-slate-400 italic ml-2">(Anonim)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-green-600 font-bold">Rp
                                        {{ number_format($donasi->jumlah, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    Transfer Bank
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">search_off</span>
                                    <p>Tidak ada data donasi pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($donations->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $donations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection