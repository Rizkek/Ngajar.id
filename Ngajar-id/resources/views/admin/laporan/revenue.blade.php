@extends('layouts.dashboard')

@section('title', 'Revenue Report - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Revenue Report</h1>
                <p class="text-slate-600">Laporan penjualan token dan pendapatan platform</p>
            </div>
            <div>
                <a href="{{ route('admin.laporan.revenue.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors shadow-lg shadow-green-600/30">
                    <span class="material-symbols-rounded">download</span>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>

        <!-- Filter - Modern Design -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <!-- Start Date -->
                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-rounded text-indigo-600 text-lg">calendar_today</span>
                        Dari Tanggal
                    </label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-slate-700">
                </div>

                <!-- End Date -->
                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-rounded text-indigo-600 text-lg">event</span>
                        Sampai Tanggal
                    </label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-slate-700">
                </div>

                <!-- Filter Button -->
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md active:scale-95">
                    <span class="material-symbols-rounded text-xl">filter_alt</span>
                    <span>Filter</span>
                </button>

                <!-- Reset Button -->
                <a href="{{ route('admin.laporan.revenue') }}"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2">
                    <span class="material-symbols-rounded text-xl">refresh</span>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Total Revenue</p>
                        <h3 class="text-3xl font-black">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">monetization_on</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Token Terjual</p>
                        <h3 class="text-3xl font-black">{{ number_format($totalTokenSold) }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">token</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Total Transaksi</p>
                        <h3 class="text-3xl font-black">{{ number_format($countTransactions) }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">shopping_cart</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Grafik Pendapatan Harian</h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-slate-800">Riwayat Transaksi Topup</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Token</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Total Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topups as $topup)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $topup->tanggal->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs">
                                            {{ substr($topup->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-700">{{ $topup->user->name ?? 'User Terhapus' }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $topup->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        +{{ number_format($topup->jumlah_token) }} Token
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-slate-900 font-bold">Rp
                                        {{ number_format($topup->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Success</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 mb-2">search_off</span>
                                    <p>Tidak ada data transaksi pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($topups->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $topups->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: @json($chartData['data']),
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection