@extends('layouts.dashboard')

@section('title', 'Edit Materi')
@section('header_title', 'Edit Materi')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <div class="mb-6 border-b pb-4 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Materi</h2>
            <p class="text-gray-500 text-sm">Perbarui detail materi atau ganti file.</p>
        </div>

        <!-- Delete Button -->
        <form action="{{ route('pengajar.materi.destroy', $materi->materi_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini? File akan dihapus permanen.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold flex items-center">
                <span class="material-symbols-rounded text-lg mr-1">delete</span>
                Hapus Materi
            </button>
        </form>
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

    <form action="{{ route('pengajar.materi.update', $materi->materi_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Pilih Kelas -->
        <div class="mb-6">
            <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-2">Untuk Kelas</label>
            <select name="kelas_id" id="kelas_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors">
                @foreach($kelas as $k)
                    <option value="{{ $k->kelas_id }}" {{ old('kelas_id', $materi->kelas_id) == $k->kelas_id ? 'selected' : '' }}>
                        {{ $k->judul }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Judul Materi -->
        <div class="mb-6">
            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Materi</label>
            <input type="text" name="judul" id="judul" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                placeholder="Contoh: Pengenalan Algoritma" required value="{{ old('judul', $materi->judul) }}">
        </div>

        <!-- Tipe Materi -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Materi</label>
            <div class="flex items-center space-x-6">
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="radio" name="tipe" value="pdf" class="text-teal-600 focus:ring-teal-500" 
                        {{ old('tipe', $materi->tipe) == 'pdf' ? 'checked' : '' }}>
                    <div class="ml-2 flex items-center gap-2 group-hover:text-teal-700">
                        <span class="material-symbols-rounded text-red-500">picture_as_pdf</span>
                        <span class="text-gray-700 font-medium">Dokumen (PDF)</span>
                    </div>
                </label>
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="radio" name="tipe" value="video" class="text-teal-600 focus:ring-teal-500"
                        {{ old('tipe', $materi->tipe) == 'video' ? 'checked' : '' }}>
                    <div class="ml-2 flex items-center gap-2 group-hover:text-teal-700">
                        <span class="material-symbols-rounded text-blue-500">videocam</span>
                        <span class="text-gray-700 font-medium">Video</span>
                    </div>
                </label>
                 <label class="inline-flex items-center cursor-pointer group">
                    <input type="radio" name="tipe" value="soal" class="text-teal-600 focus:ring-teal-500"
                        {{ old('tipe', $materi->tipe) == 'soal' ? 'checked' : '' }}>
                    <div class="ml-2 flex items-center gap-2 group-hover:text-teal-700">
                        <span class="material-symbols-rounded text-purple-500">quiz</span>
                        <span class="text-gray-700 font-medium">Latihan Soal</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Upload File (Opsional saat Edit) -->
        <div class="mb-6">
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Ganti File (Opsional)</label>
            
            @if($materi->file_url)
                <div class="mb-3 flex items-center gap-2 text-sm text-green-600 bg-green-50 p-2 rounded-lg border border-green-100">
                    <span class="material-symbols-rounded">check_circle</span>
                    <span>File saat ini: <strong>{{ basename($materi->file_url) }}</strong></span>
                </div>
            @endif

            <div class="flex items-center justify-center w-full">
                <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-teal-500 transition-all">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <span class="material-symbols-rounded text-gray-400 text-3xl mb-2">cloud_upload</span>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk ganti file</span> atau drag and drop</p>
                        <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah file</p>
                    </div>
                    <input id="file" name="file" type="file" class="hidden" />
                </label>
            </div>
             <!-- Filename preview -->
            <div id="file-preview" class="mt-2 text-sm text-teal-600 font-medium hidden flex items-center gap-2">
                <span class="material-symbols-rounded">check_circle</span>
                <span id="filename"></span>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="mb-8">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Tambahan</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                placeholder="Berikan instruksi atau penjelasan singkat...">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('pengajar.materi') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md transition-colors flex items-center">
                <span class="material-symbols-rounded text-xl mr-2">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const preview = document.getElementById('file-preview');
        const nameSpan = document.getElementById('filename');
        
        if (fileName) {
            nameSpan.textContent = fileName;
            preview.classList.remove('hidden');
            preview.classList.add('flex');
        } else {
            preview.classList.add('hidden');
            preview.classList.remove('flex');
        }
    });
</script>
@endsection
