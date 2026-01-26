@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Ngajar.ID')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Admin Dashboard</h1>
            <p class="text-slate-600">Ringkasan statistik dan kontrol platform Ngajar.ID</p>
        </div>

        <!-- Stats Cards with Growth Indicators -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Murid -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">school</span>
                    </div>
                    <div class="text-right">
                        @if($muridGrowth > 0)
                            <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
                                <span class="material-symbols-rounded text-xs align-middle">trending_up</span>
                                +{{ $muridGrowth }}%
                            </span>
                        @endif
                    </div>
                </div>
                <p class="text-sm font-medium opacity-90">Total Murid</p>
                <h3 class="text-3xl font-black">{{ number_format($totalMurid) }}</h3>
            </div>

            <!-- Total Pengajar -->
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">person_book</span>
                    </div>
                    <div class="text-right">
                        @if($pengajarGrowth > 0)
                            <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
                                <span class="material-symbols-rounded text-xs align-middle">trending_up</span>
                                +{{ $pengajarGrowth }}%
                            </span>
                        @endif
                    </div>
                </div>
                <p class="text-sm font-medium opacity-90">Total Pengajar</p>
                <h3 class="text-3xl font-black">{{ number_format($totalPengajar) }}</h3>
            </div>

            <!-- Total Kelas -->
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">class</span>
                    </div>
                </div>
                <p class="text-sm font-medium opacity-90">Kelas Aktif</p>
                <h3 class="text-3xl font-black">{{ number_format($totalKelas) }}</h3>
            </div>

            <!-- Total Donasi -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-2xl">volunteer_activism</span>
                    </div>
                </div>
                <p class="text-sm font-medium opacity-90">Total Donasi</p>
                <h3 class="text-2xl font-black">Rp {{ number_format($totalDonasi / 1000000, 1) }}Jt</h3>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Growth Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Pertumbuhan Pengguna (6 Bulan Terakhir)</h2>
                <canvas id="userGrowthChart" height="250"></canvas>
            </div>

            <!-- Donation Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Tren Donasi (6 Bulan Terakhir)</h2>
                <canvas id="donationTrendChart" height="250"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Recent Activity Feed -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">Aktivitas Terbaru</h2>
                    <span class="text-xs text-slate-500">Real-time</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    @foreach($recentActivity as $activity)
                        <div class="p-4 hover:bg-slate-50 transition-colors flex items-start gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="material-symbols-rounded text-{{ $activity['color'] }}-600 text-xl">{{ $activity['icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700">{{ $activity['message'] }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Aksi Cepat</h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.pengajar.index') }}"
                        class="block p-4 rounded-lg bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 transition-all text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-rounded text-2xl">person_book</span>
                            <div class="text-left">
                                <div class="text-sm font-bold">Kelola Pengajar</div>
                                <div class="text-xs opacity-90">Verifikasi & manage</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.murid.index') }}"
                        class="block p-4 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-rounded text-2xl">school</span>
                            <div class="text-left">
                                <div class="text-sm font-bold">Kelola Murid</div>
                                <div class="text-xs opacity-90">Token & akses kontrol</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.kelas.index') }}"
                        class="block p-4 rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 transition-all text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-rounded text-2xl">class</span>
                            <div class="text-left">
                                <div class="text-sm font-bold">Moderasi Kelas</div>
                                <div class="text-xs opacity-90">Review & quality control</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.laporan.donasi') }}"
                        class="block p-4 rounded-lg bg-emerald-500 hover:bg-emerald-600 transition-all text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-rounded text-2xl">volunteer_activism</span>
                            <div class="text-left">
                                <div class="text-sm font-bold">Laporan Donasi</div>
                                <div class="text-xs opacity-90">Rekap & export</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.laporan.revenue') }}"
                        class="block p-4 rounded-lg bg-indigo-500 hover:bg-indigo-600 transition-all text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-rounded text-2xl">monetization_on</span>
                            <div class="text-left">
                                <div class="text-sm font-bold">Revenue Report</div>
                                <div class="text-xs opacity-90">Token sales analytics</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Latest Donations Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800">Donasi Terbaru</h2>
                <a href="#" class="text-teal-600 text-sm hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Donatur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($latestDonations as $donation)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-teal-500 flex items-center justify-center text-white font-bold">
                                            {{ substr($donation->nama ?: 'H', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $donation->nama ?: 'Hamba Allah' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-green-600 font-bold">Rp
                                        {{ number_format($donation->jumlah, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $donation->tanggal->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($userGrowthData['labels']),
                datasets: [
                    {
                        label: 'Murid',
                        data: @json($userGrowthData['murid']),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pengajar',
                        data: @json($userGrowthData['pengajar']),
                        borderColor: 'rgb(20, 184, 166)',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Donation Trend Chart
        const donationTrendCtx = document.getElementById('donationTrendChart').getContext('2d');
        new Chart(donationTrendCtx, {
            type: 'bar',
            data: {
                labels: @json($donationTrendData['labels']),
                datasets: [{
                    label: 'Total Donasi (Rp)',
                    data: @json($donationTrendData['amounts']),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection