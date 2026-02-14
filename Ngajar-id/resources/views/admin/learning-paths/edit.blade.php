@extends('layouts.dashboard')

@section('title', 'Edit Learning Path - Admin')

@section('content')
    <div class="container-fluid px-4 max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('admin.learning-paths.index') }}"
                class="text-brand-600 hover:text-brand-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-slate-900 mb-2 mt-4">Edit Learning Path</h1>
            <p class="text-slate-600">Update informasi jalur pembelajaran</p>
        </div>

        <form action="{{ route('admin.learning-paths.update', $learningPath->learning_path_id) }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nama -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Learning Path *</label>
                    <input type="text" name="nama" value="{{ old('nama', $learningPath->nama) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('nama')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Slug (URL-friendly) *</label>
                    <input type="text" name="slug" value="{{ old('slug', $learningPath->slug) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('slug')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">{{ old('deskripsi', $learningPath->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Urutan Tampilan *</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $learningPath->urutan) }}" min="1" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @error('urutan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Icon (Material Symbols)</label>
                    <div class="flex gap-4">
                        <input type="text" name="icon" value="{{ old('icon', $learningPath->icon) }}"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-300">
                            <span
                                class="material-symbols-rounded text-2xl text-slate-600">{{ $learningPath->icon ?? 'help' }}</span>
                        </div>
                    </div>
                    @error('icon')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warna -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Warna (Tailwind)</label>
                    <div class="flex gap-4">
                        <select name="warna"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                            @foreach(['blue', 'teal', 'green', 'amber', 'orange', 'red', 'purple', 'pink', 'indigo'] as $color)
                                <option value="{{ $color }}" {{ old('warna', $learningPath->warna) == $color ? 'selected' : '' }}>
                                    {{ ucfirst($color) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="w-12 h-12 rounded-lg bg-{{ $learningPath->warna }}-500 border border-gray-300"></div>
                    </div>
                    @error('warna')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.learning-paths.index') }}"
                    class="px-6 py-3 border border-gray-300 text-slate-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 transition-all shadow-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection