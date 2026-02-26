@extends('layouts.dashboard')

@section('title', 'Notification Center - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Notification Center</h1>
                <p class="text-slate-600">Kirim notifikasi ke pengguna platform</p>
            </div>
            <a href="{{ route('admin.notifications.create') }}"
                class="px-6 py-3 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700 transition-all shadow-lg flex items-center gap-2">
                <span class="material-symbols-rounded">campaign</span>
                Buat Broadcast Baru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions: Live Class Notification -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl shadow-lg text-white p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-rounded text-3xl">live_tv</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Live Class Alert</h2>
                        <p class="text-indigo-100 text-sm">Kirim notifikasi "Kelas Dimulai" ke siswa</p>
                    </div>
                </div>

                <form action="{{ route('admin.notifications.sendLiveClass') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-indigo-200 mb-1">Pilih
                                Kelas</label>
                            <select name="kelas_id" required
                                class="w-full px-4 py-2 bg-indigo-800 border border-indigo-400 rounded-lg text-white placeholder-indigo-300 focus:ring-2 focus:ring-white focus:bg-white focus:text-slate-900 transition-colors">
                                <option value="" class="text-slate-800">-- Pilih Kelas Aktif --</option>
                                @foreach(\App\Models\Kelas::where('status', 'aktif')->get() as $kelas)
                                    <option value="{{ $kelas->kelas_id }}" class="text-slate-800">
                                        {{ Str::limit($kelas->judul, 30) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-indigo-200 mb-1">Link
                                Meeting</label>
                            <input type="url" name="meeting_url" required placeholder="https://meet.google.com/..."
                                class="w-full px-4 py-2 bg-indigo-700/50 border border-indigo-400 rounded-lg text-white placeholder-indigo-300 focus:ring-2 focus:ring-white">
                        </div>
                        <button type="submit"
                            class="w-full py-3 bg-white text-indigo-700 font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-md mt-2">
                            Kirim Notifikasi Sekarang
                        </button>
                        <p class="text-xs text-center text-indigo-200 mt-2">Notifikasi akan dikirim ke semua peserta kelas
                            tersebut.</p>
                    </div>
                </form>
            </div>

            <!-- Recent Notifications History -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">Riwayat Broadcast Terakhir</h2>
                    <span class="text-xs text-slate-500">{{ $logs->count() }} Teraktual</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <div class="p-4 hover:bg-slate-50 transition-colors flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-blue-600">
                                    {{ $log->recipient_type === 'kelas' ? 'live_tv' : 'campaign' }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-slate-900 text-sm truncate">{{ $log->title }}</h3>
                                    <span
                                        class="text-[10px] text-slate-400 whitespace-nowrap ml-2">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-600 mt-1 line-clamp-1">{{ $log->message }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span
                                        class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded border border-gray-200">
                                        Target: {{ ucfirst($log->recipient_type) }} ({{ $log->recipient_count }} user)
                                    </span>
                                    @php
                                        $priorityColors = [
                                            'high' => 'bg-red-100 text-red-600 border-red-200',
                                            'normal' => 'bg-blue-100 text-blue-600 border-blue-200',
                                            'low' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                        $pColor = $priorityColors[$log->priority] ?? $priorityColors['normal'];
                                    @endphp
                                    <span class="px-2 py-0.5 {{ $pColor }} text-[10px] rounded border">
                                        {{ ucfirst($log->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <span class="material-symbols-rounded text-4xl mb-2 opacity-20">history</span>
                            <p class="text-sm">Belum ada riwayat pengiriman notifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection