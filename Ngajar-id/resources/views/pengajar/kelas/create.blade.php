@extends('layouts.dashboard')

@section('title', 'Buat Kelas Baru')
@section('header_title', 'Buat Kelas Baru')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-gray-800">Formulir Kelas Baru</h2>
            <p class="text-gray-500 text-sm">Bagikan ilmu Anda dengan membuat kelas baru.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengajar.kelas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Judul Kelas -->
                <div class="col-span-2">
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Kelas</label>
                    <input type="text" name="judul" id="judul"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                        placeholder="Contoh: Pemrograman Dasar Python" required value="{{ old('judul') }}">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="kategori" id="kategori"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(config('categories.kelas') as $value => $label)
                            <option value="{{ $value }}" {{ old('kategori') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <!-- Pindah status ke sidebar grid biar rapih atau keep below, tapi karena grid 2 row, mending sejajar kategori kalo muat, atau separate row -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Publikasi</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 bg-white">
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif (Tampil di Katalog)
                        </option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Draft (Disembunyikan)
                        </option>
                    </select>
                </div>
            </div>

            <!-- Thumbnail Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Kelas (Optional)</label>
                <div class="flex items-center justify-center w-full">
                    <label for="thumbnail"
                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <span class="material-symbols-rounded text-gray-400 text-4xl mb-2">add_photo_alternate</span>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span></p>
                            <p class="text-xs text-gray-500">JPG, PNG (Max. 2MB)</p>
                        </div>
                        <input id="thumbnail" name="thumbnail" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mb-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kelas</label>
                <textarea name="deskripsi" id="deskripsi" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                    placeholder="Jelaskan apa yang akan dipelajari di kelas ini..."
                    required>{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('pengajar.kelas') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md transition-colors flex items-center">
                    <span class="material-symbols-rounded text-xl mr-2">save</span>
                    Simpan Efek
                </button>
            </div>
        </form>
    </div>
@endsection