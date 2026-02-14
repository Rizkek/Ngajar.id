@extends('layouts.dashboard')

@section('title', 'Detail Donasi - Admin')

@section('content')
    <div class="container-fluid px-4 max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('admin.donasi.index') }}"
                class="text-brand-600 hover:text-brand-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <div class="flex justify-between items-start mt-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-1">Donasi #{{ $donasi->id }}</h1>
                    <p class="text-slate-500 font-mono text-sm">{{ $donasi->nomor_transaksi }}</p>
                </div>

                @php
                    $statusColor = match ($donasi->status) {
                        'paid', 'settlement', 'capture' => 'bg-green-100 text-green-700 border-green-200',
                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'failed', 'cancel', 'deny' => 'bg-red-100 text-red-700 border-red-200',
                        'expired' => 'bg-gray-100 text-gray-700 border-gray-200',
                        'refunded' => 'bg-purple-100 text-purple-700 border-purple-200',
                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                    };
                @endphp
                <span class="px-4 py-2 rounded-lg text-sm font-bold border {{ $statusColor }} uppercase tracking-wider">
                    {{ $donasi->status }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <span class="material-symbols-rounded">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <span class="material-symbols-rounded">error</span>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informasi Donatur -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-gray-100 pb-2">Data Donatur</h3>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase">Nama</span>
                        <span class="text-lg font-medium text-slate-900">{{ $donasi->nama ?: 'Hamba Allah' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase">Email</span>
                        <span class="text-base text-slate-700">{{ $donasi->email }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase">Pesan / Doa</span>
                        <p class="text-slate-600 italic bg-slate-50 p-3 rounded-lg mt-1 border border-slate-100">
                            "{{ $donasi->pesan ?: '-' }}"
                        </p>
                    </div>
                </div>
            </div>

            <!-- Rincian Pembayaran -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-gray-100 pb-2">Rincian Transaksi</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Metode Pembayaran</span>
                        <span
                            class="font-bold text-slate-800 uppercase">{{ str_replace('_', ' ', $donasi->metode_pembayaran) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Waktu Transaksi</span>
                        <span class="text-slate-800">{{ $donasi->tanggal->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <hr class="border-dashed border-gray-200 my-4">

                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-slate-800">Total Donasi</span>
                        <span class="text-2xl font-black text-brand-600">Rp
                            {{ number_format($donasi->jumlah, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($donasi->catatan_admin)
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <span class="block text-xs font-bold text-yellow-800 uppercase mb-1">Catatan Admin</span>
                        <p class="text-yellow-900 text-sm">{{ $donasi->catatan_admin }}</p>
                    </div>
                @endif
            </div>

            <!-- Admin Actions -->
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Aksi Konfirmasi Manual</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Update Status Form -->
                    <div>
                        <form action="{{ route('admin.donasi.updateStatus', $donasi->id) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Update Status</label>
                                <select name="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-500">
                                    <option value="paid" {{ $donasi->status == 'paid' ? 'selected' : '' }}>Paid (Lunas)
                                    </option>
                                    <option value="pending" {{ $donasi->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="failed" {{ $donasi->status == 'failed' ? 'selected' : '' }}>Failed (Gagal)
                                    </option>
                                    <option value="expired" {{ $donasi->status == 'expired' ? 'selected' : '' }}>Expired
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan
                                    (Opsional)</label>
                                <textarea name="admin_note" rows="2"
                                    class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-500"
                                    placeholder="Alasan perubahan status...">{{ $donasi->catatan_admin }}</textarea>
                            </div>
                            <button type="submit"
                                class="px-6 py-2 bg-slate-800 text-white font-bold rounded hover:bg-slate-900 transition-colors">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <!-- Refund Form (Only if Paid) -->
                    @if(in_array($donasi->status, ['paid', 'settlement', 'capture']))
                        <div class="border-l border-gray-200 pl-8">
                            <h4 class="text-sm font-bold text-red-600 uppercase mb-4 flex items-center gap-2">
                                <span class="material-symbols-rounded">warning</span> Area Berbahaya
                            </h4>

                            <p class="text-xs text-slate-500 mb-4">
                                Refund akan mengubah status menjadi 'Refunded' dan mencatat alasan pengembalian dana. Dana harus
                                dikembalikan manual via Midtrans Dashboard.
                            </p>

                            <form action="{{ route('admin.donasi.refund', $donasi->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin memproses refund? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alasan Refund</label>
                                    <input type="text" name="refund_reason" required
                                        class="w-full px-4 py-2 border border-red-300 rounded focus:ring-2 focus:ring-red-500 placeholder-red-200"
                                        placeholder="Contoh: Double transfer / Request user">
                                </div>
                                <button type="submit"
                                    class="w-full px-6 py-2 bg-white border border-red-500 text-red-600 font-bold rounded hover:bg-red-50 transition-colors">
                                    Proses Refund Manual
                                </button>
                            </form>
                        </div>
                    @else
                        <div
                            class="flex items-center justify-center border-l border-gray-200 pl-8 text-slate-400 text-sm italic">
                            Menu Refund hanya tersedia untuk transaksi sukses.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection