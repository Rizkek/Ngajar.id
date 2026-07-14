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

        <form action="{{ route('teacher.kelas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Judul Kelas -->
                <div class="col-span-2">
                    <x-form.input name="judul" label="Judul Kelas" placeholder="Contoh: Pemrograman Dasar Python" required :value="old('judul')" />
                </div>

                <!-- Kategori -->
                <div>
                    <x-form.select name="kategori" label="Kategori">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(config('categories.kelas') as $value => $label)
                            <option value="{{ $value }}" {{ old('kategori') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <!-- Status -->
                <div>
                    <x-form.select name="status" label="Status Publikasi">
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif (Tampil di Katalog)</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Draft (Disembunyikan)</option>
                    </x-form.select>
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
                <x-form.textarea name="deskripsi" label="Deskripsi Kelas" rows="5" placeholder="Jelaskan apa yang akan dipelajari di kelas ini..." required>{{ old('deskripsi') }}</x-form.textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <x-buttons.secondary href="{{ route('teacher.kelas') }}" class="px-6 py-2">
                    Batal
                </x-buttons.secondary>
                <x-buttons.primary type="submit" class="px-6 py-2">
                    <x-icons.material name="save" size="sm" class="mr-2 -ml-1" />
                    Simpan Kelas
                </x-buttons.primary>
            </div>
        </form>
    </div>
@endsection
