@extends('layouts.dashboard')

@section('title', 'Kelola Learning Paths - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Learning Paths Management</h1>
                <p class="text-slate-600">Kelola jalur pembelajaran terstruktur untuk siswa</p>
            </div>
            <a href="{{ route('admin.learning-paths.create') }}"
                class="px-6 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition-all shadow-lg flex items-center gap-2">
                <span class="material-symbols-rounded">add</span>
                Tambah Learning Path
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-slate-800">Semua Learning Paths ({{ $learningPaths->total() }})</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Urutan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama Path</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Jumlah Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Icon/Warna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($learningPaths as $path)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-2xl font-black text-slate-800">{{ $path->urutan }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $path->nama }}</div>
                                        <div class="text-sm text-slate-500 mt-1">{{ Str::limit($path->deskripsi, 60) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="text-xs bg-slate-100 px-2 py-1 rounded">{{ $path->slug }}</code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                                        <span class="material-symbols-rounded text-sm">class</span>
                                        {{ $path->kelas_count }} kelas
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($path->icon)
                                            <span
                                                class="material-symbols-rounded text-{{ $path->warna ?? 'slate' }}-600">{{ $path->icon }}</span>
                                        @endif
                                        @if($path->warna)
                                            <span class="inline-block w-6 h-6 rounded-full bg-{{ $path->warna }}-500"></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.learning-paths.show', $path->learning_path_id) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Detail">
                                            <span class="material-symbols-rounded text-xl">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.learning-paths.edit', $path->learning_path_id) }}"
                                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <span class="material-symbols-rounded text-xl">edit</span>
                                        </a>
                                        <form action="{{ route('admin.learning-paths.destroy', $path->learning_path_id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus learning path ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus">
                                                <span class="material-symbols-rounded text-xl">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <span class="material-symbols-rounded text-6xl text-slate-300 block mb-4">route</span>
                                    <p class="text-lg font-medium">Belum ada Learning Path</p>
                                    <p class="text-sm mt-2">Buat learning path pertama untuk membimbing siswa</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($learningPaths->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $learningPaths->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection