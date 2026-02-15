@extends('layouts.dashboard')

@section('title', 'Moderasi Materi - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Moderasi Materi</h1>
                <p class="text-slate-600">Review & kelola konten pembelajaran dari pengajar</p>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form action="{{ route('admin.materi.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cari Materi</label>
                    <div class="relative">
                        <span class="material-symbols-rounded absolute left-3 top-2.5 text-slate-400">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-500"
                            placeholder="Judul materi...">
                    </div>
                </div>

                <div class="w-full md:w-64">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filter Kelas</label>
                    <select name="kelas_id"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Kelas</option>
                        @foreach($allKelas as $k)
                            <option value="{{ $k->kelas_id }}" {{ request('kelas_id') == $k->kelas_id ? 'selected' : '' }}>
                                {{ Str::limit($k->judul, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-48">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipe</label>
                    <select name="tipe"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Tipe</option>
                        <option value="video" {{ request('tipe') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="artikel" {{ request('tipe') == 'artikel' ? 'selected' : '' }}>Artikel</option>
                        <option value="quiz" {{ request('tipe') == 'quiz' ? 'selected' : '' }}>Kuis</option>
                        <option value="dokumen" {{ request('tipe') == 'dokumen' ? 'selected' : '' }}>Dokumen</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="px-6 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Materi Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Materi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pengajar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal Upload</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($materi as $item)
                                        <tr class="hover:bg-slate-50 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-slate-900">{{ $item->judul }}</div>
                                                <div class="text-xs text-slate-500 mt-1">ID: {{ $item->materi_id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                            {{ $item->tipe == 'video' ? 'bg-red-100 text-red-700' :
                            ($item->tipe == 'artikel' ? 'bg-blue-100 text-blue-700' :
                                ($item->tipe == 'quiz' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700')) }}">
                                                    <span class="material-symbols-rounded text-[14px]">
                                                        {{ $item->tipe == 'video' ? 'play_circle' :
                            ($item->tipe == 'artikel' ? 'article' :
                                ($item->tipe == 'quiz' ? 'quiz' : 'description')) }}
                                                    </span>
                                                    {{ ucfirst($item->tipe) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                <a href="{{ route('admin.kelas.show', $item->kelas_id) }}"
                                                    class="hover:text-brand-600 hover:underline">
                                                    {{ Str::limit($item->kelas->judul ?? 'Tanpa Kelas', 25) }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $item->kelas->pengajar->name ?? 'Unknown' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                {{ $item->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <a href="{{ route('admin.materi.show', $item->materi_id) }}"
                                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Lihat/Review">
                                                        <span class="material-symbols-rounded text-lg">visibility</span>
                                                    </a>
                                                    <form action="{{ route('admin.materi.destroy', $item->materi_id) }}" method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('Hapus materi ini? Tindakan tidak bisa dibatalkan.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded"
                                                            title="Hapus Konten">
                                                            <span class="material-symbols-rounded text-lg">delete</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 block mb-4">folder_open</span>
                                    <p class="text-lg font-medium">Tidak ada materi ditemukan</p>
                                    <p class="text-sm mt-1">Coba ubah filter pencarian Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($materi->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $materi->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection