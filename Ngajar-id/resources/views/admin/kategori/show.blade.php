@extends('layouts.dashboard')

@section('title', 'Detail Kategori  - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <a href="{{ route('admin.kategori.index') }}"
                class="text-brand-600 hover:text-brand-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <div class="flex items-center gap-4 mt-4">
                <h1 class="text-3xl font-bold text-slate-900 capitalize">{{ str_replace('-', ' ', $kategori) }}</h1>
                <span class="px-3 py-1 bg-brand-100 text-brand-700 font-bold rounded-full text-sm">
                    {{ $kelas->total() }} Kelas
                </span>
            </div>
            <p class="text-slate-600 mt-2">Daftar kelas dalam kategori ini</p>
        </div>

        <!-- Kelas Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pengajar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($kelas as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded bg-gray-200 overflow-hidden shrink-0">
                                            @if($item->thumbnail)
                                                <img src="{{ Storage::url($item->thumbnail) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <span class="material-symbols-rounded">image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900 hover:text-brand-600 transition-colors">
                                                <a href="{{ route('admin.kelas.show', $item->kelas_id) }}">
                                                    {{ Str::limit($item->judul, 40) }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5">ID: {{ $item->kelas_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    {{ $item->pengajar->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium border rounded bg-slate-50 text-slate-600 border-slate-200">
                                        {{ ucfirst($item->level) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">
                                    {{ $item->harga > 0 ? 'Rp ' . number_format($item->harga, 0, ',', '.') : 'Gratis' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                                            {{ $item->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('admin.kelas.show', $item->kelas_id) }}"
                                        class="text-brand-600 hover:text-brand-800 font-bold transition-colors">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 block mb-4">category</span>
                                    <p class="text-lg font-medium">Belum ada kelas dalam kategori ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kelas->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $kelas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection