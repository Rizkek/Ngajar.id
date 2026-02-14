@extends('layouts.dashboard')

@section('title', 'Tambah Learning Path - Admin')

@section('content')
    <div class="container-fluid px-4 max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('admin.learning-paths.index') }}"
                class="text-brand-600 hover:text-brand-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-slate-900 mb-2 mt-4">Tambah Learning Path Baru</h1>
            <p class="text-slate-600">Buat jalur pembelajaran terstruktur untuk siswa</p>
        </div>

        <form action="{{ route('admin.learning-paths.store') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf

            <div class="space-y-6">
                <!-- Nama -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Learning Path *</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                        placeholder="Contoh: Web Development Fundamentals">
                    @error('nama')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Slug (URL-friendly) *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                        placeholder="web-development-fundamentals">
                    <p class="text-xs text-slate-500 mt-1">Gunakan huruf kecil, angka, dan tanda hubung. Contoh:
                        web-dev-fundamentals</p>
                    @error('slug')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                        placeholder="Jelaskan tujuan dan manfaat learning path ini...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Urutan Tampilan *</label>
                    <input type="number" name="urutan" value="{{ old('urutan', 1) }}" min="1" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <p class="text-xs text-slate-500 mt-1">Urutan tampilan di halaman utama (1 = paling atas)</p>
                    @error('urutan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Icon (Material Symbols)</label>
                    <input type="text" name="icon" value="{{ old('icon') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                        placeholder="code, design_services, analytics">
                    <p class="text-xs text-slate-500 mt-1">
                        Lihat: <a href="https://fonts.google.com/icons" target="_blank"
                            class="text-brand-600 hover:underline">Google Material Symbols</a>
                    </p>
                    @error('icon')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warna -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Warna (Tailwind)</label>
                    <select name="warna"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        <option value="">Pilih Warna</option>
                        <option value="blue" {{ old('warna') == 'blue' ? 'selected' : '' }}>Blue</option>
                        <option value="teal" {{ old('warna') == 'teal' ? 'selected' : '' }}>Teal</option>
                        <option value="green" {{ old('warna') == 'green' ? 'selected' : '' }}>Green</option>
                        <option value="amber" {{ old('warna') == 'amber' ? 'selected' : '' }}>Amber</option>
                        <option value="orange" {{ old('warna') == 'orange' ? 'selected' : '' }}>Orange</option>
                        <option value="red" {{ old('warna') == 'red' ? 'selected' : '' }}>Red</option>
                        <option value="purple" {{ old('warna') == 'purple' ? 'selected' : '' }}>Purple</option>
                        <option value="pink" {{ old('warna') == 'pink' ? 'selected' : '' }}>Pink</option>
                        <option value="indigo" {{ old('warna') == 'indigo' ? 'selected' : '' }}>Indigo</option>
                    </select>
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
                    Simpan Learning Path
                </button>
            </div>
        </form>
    </div>
@endsection