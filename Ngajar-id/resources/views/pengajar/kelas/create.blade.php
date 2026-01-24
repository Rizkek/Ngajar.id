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

        <form action="{{ route('pengajar.kelas.store') }}" method="POST">
            @csrf

            <!-- Judul Kelas -->
            <div class="mb-6">
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Kelas</label>
                <input type="text" name="judul" id="judul"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                    placeholder="Contoh: Pemrograman Dasar Python" required value="{{ old('judul') }}">
            </div>

            <!-- Deskripsi -->
            <div class="mb-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kelas</label>
                <textarea name="deskripsi" id="deskripsi" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                    placeholder="Jelaskan apa yang akan dipelajari di kelas ini..."
                    required>{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Publikasi</label>
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="aktif" class="text-teal-600 focus:ring-teal-500" checked>
                        <span class="ml-2 text-gray-700">Aktif (Dapat dilihat murid)</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="nonaktif" class="text-teal-600 focus:ring-teal-500">
                        <span class="ml-2 text-gray-700">Draft (Sembunyikan dulu)</span>
                    </label>
                </div>
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