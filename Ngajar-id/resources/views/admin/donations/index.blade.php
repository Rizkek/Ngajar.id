@extends('layouts.dashboard')

@section('title', 'Kelola Donasi - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Manajemen Donasi</h1>
            <p class="text-slate-600">Pantau dan kelola donasi yang masuk</p>
        </div>


        <!-- Stats Review -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Total Terkumpul</div>
                <div class="text-2xl font-black text-green-600">Rp {{ number_format($stats['total'], 0, ',', '.') }}</div>
                <div class="text-xs text-slate-400 mt-1">Status Paid/Settled</div>
            </div>
            <!-- Pending -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Menunggu</div>
                <div class="text-2xl font-black text-amber-500">{{ number_format($stats['pending']) }}</div>
                <div class="text-xs text-slate-400 mt-1">Transaksi Pending</div>
            </div>
            <!-- Sukses -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Berhasil</div>
                <div class="text-2xl font-black text-blue-600">{{ number_format($stats['success']) }}</div>
                <div class="text-xs text-slate-400 mt-1">Transaksi Selesai</div>
            </div>
            <!-- Gagal -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Gagal/Expire</div>
                <div class="text-2xl font-black text-red-500">{{ number_format($stats['failed']) }}</div>
                <div class="text-xs text-slate-400 mt-1">Transaksi Batal</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form action="{{ route('admin.donations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cari Donatur/ID</label>
                    <x-form.input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, Email, atau ID..." />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                    <x-form.select name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid/Success</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </x-form.select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Mulai</label>
                    <x-form.input type="date" name="start_date" value="{{ request('start_date') }}" />
                </div>

                <div class="flex items-end">
                    <x-buttons.primary type="submit" class="w-full justify-center">
                        Filter Data
                    </x-buttons.primary>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Donatur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($donasi as $d)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $d->nama ?: 'Hamba Allah' }}</div>
                                    <div class="text-xs text-slate-500">{{ $d->email }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $d->nomor_transaksi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">Rp {{ number_format($d->jumlah, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColor = match ($d->status) {
                                            'paid', 'settlement', 'capture' => 'bg-green-100 text-green-700',
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'failed', 'cancel', 'deny' => 'bg-red-100 text-red-700',
                                            'expired' => 'bg-gray-100 text-gray-700',
                                            'refunded' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst($d->status) }}
                                    </span>
                                    @if($d->catatan_admin)
                                        <span class="ml-1 text-slate-400 material-symbols-rounded text-sm align-middle"
                                            title="Ada catatan admin">note</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    {{ strtoupper(str_replace('_', ' ', $d->metode_pembayaran)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $d->tanggal->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('admin.donations.show', $d->donasi_id) }}"
                                        class="text-brand-600 hover:text-brand-800 font-medium">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12">
                                    <x-empty-state 
                                        icon="volunteer_activism" 
                                        title="Belum ada data donasi" 
                                        description="Data donasi tidak ditemukan atau belum ada transaksi donasi yang masuk." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($donasi->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $donasi->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
