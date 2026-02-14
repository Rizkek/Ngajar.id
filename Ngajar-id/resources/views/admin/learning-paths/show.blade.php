@extends('layouts.dashboard')

@section('title', 'Detail Learning Path - Admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-8">
            <a href="{{ route('admin.learning-paths.index') }}"
                class="text-brand-600 hover:text-brand-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <div class="flex justify-between items-center mt-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ $learningPath->nama }}</h1>
                    <p class="text-slate-600">{{ $learningPath->deskripsi }}</p>
                </div>
                <a href="{{ route('admin.learning-paths.edit', $learningPath->learning_path_id) }}"
                    class="px-4 py-2 bg-amber-500 text-white font-medium rounded-lg hover:bg-amber-600 transition-all flex items-center gap-2">
                    <span class="material-symbols-rounded">edit</span>
                    Edit Detail
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Stats & Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="text-center mb-6">
                        <div
                            class="w-20 h-20 mx-auto bg-{{ $learningPath->warna ?? 'slate' }}-100 rounded-full flex items-center justify-center mb-4">
                            <span
                                class="material-symbols-rounded text-4xl text-{{ $learningPath->warna ?? 'slate' }}-600">{{ $learningPath->icon ?? 'school' }}</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ $learningPath->kelas->count() }}</div>
                        <div class="text-sm text-slate-500">Total Kelas</div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Slug</span>
                            <code
                                class="text-slate-700 bg-gray-100 px-2 py-0.5 rounded text-sm">{{ $learningPath->slug }}</code>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Urutan</span>
                            <span class="font-medium text-slate-900">#{{ $learningPath->urutan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Warna</span>
                            <span
                                class="capitalize text-{{ $learningPath->warna }}-600 font-medium">{{ $learningPath->warna }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Kelas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Tambahkan Kelas</h3>
                    <form action="{{ route('admin.learning-paths.attach', $learningPath->learning_path_id) }}"
                        method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Kelas</label>
                            <select name="kelas_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($availableKelas as $kelas)
                                    <option value="{{ $kelas->kelas_id }}">
                                        {{ Str::limit($kelas->nama_kelas, 30) }}
                                        ({{ $kelas->pengajar->name ?? 'No Mentor' }})
                                    </option>
                                @endforeach
                            </select>
                            @if($availableKelas->isEmpty())
                                <p class="text-xs text-amber-600 mt-2">
                                    <span class="material-symbols-rounded text-sm align-middle">warning</span>
                                    Tidak ada kelas tersedia (semua sudah masuk learning path ini atau tidak aktif).
                                </p>
                            @endif
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $availableKelas->isEmpty() ? 'disabled' : '' }}>
                            Tambahkan ke Path
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Daftar Kelas -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-slate-800">Daftar Kelas dalam Path Ini</h2>
                        <span class="text-sm text-slate-500">{{ $learningPath->kelas->count() }} Kelas</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pengajar
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($learningPath->kelas as $kelas)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded bg-gray-200 overflow-hidden flex-shrink-0">
                                                    @if($kelas->thumbnail)
                                                        <img src="{{ Storage::url($kelas->thumbnail) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                            <span class="material-symbols-rounded">image</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium text-slate-900">
                                                        {{ Str::limit($kelas->nama_kelas, 30) }}</div>
                                                    <div class="text-xs text-slate-500">ID: {{ $kelas->kelas_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            {{ $kelas->pengajar->name ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full 
                                                    {{ $kelas->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst($kelas->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form
                                                action="{{ route('admin.learning-paths.detach', ['id' => $learningPath->learning_path_id, 'kelasId' => $kelas->kelas_id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Lepaskan kelas ini dari Learning Path?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    title="Remove from Path">
                                                    <span class="material-symbols-rounded">remove_circle_outline</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                            <p>Belum ada kelas di learning path ini.</p>
                                            <p class="text-sm mt-1">Gunakan form di sebelah kiri untuk menambahkan kelas.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection