@extends('layouts.dashboard')

@section('title', 'Upload Materi Baru')
@section('header_title', 'Upload Materi')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Upload Materi Baru</h2>
        <p class="text-gray-500 text-sm">Bagikan materi pembelajaran untuk siswa Anda.</p>
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

    <form action="{{ route('pengajar.materi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Pilih Kelas -->
        <div class="mb-6">
            <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-2">Untuk Kelas</label>
            <select name="kelas_id" id="kelas_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors">
                <option value="" disabled selected>Pilih Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->kelas_id }}" {{ (old('kelas_id', $selectedKelasId ?? '') == $k->kelas_id) ? 'selected' : '' }}>
                        {{ $k->judul }}
                    </option>
                @endforeach
            </select>
            @if($kelas->isEmpty())
                <p class="text-red-500 text-xs mt-1">Anda belum memiliki kelas aktif. <a href="{{ route('pengajar.kelas.create') }}" class="underline font-bold">Buat kelas dulu</a>.</p>
            @endif
        </div>

        <!-- Judul Materi -->
        <div class="mb-6">
            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Materi</label>
            <input type="text" name="judul" id="judul" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                placeholder="Contoh: Pengenalan Algoritma" required value="{{ old('judul') }}">
        </div>

        <!-- Pengaturan Premium -->
        <div class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
            <span class="block text-sm font-bold text-slate-700 mb-2">Tipe Akses Materi</span>
            <p class="text-xs text-slate-500 mb-3">Tentukan apakah materi ini gratis atau berbayar menggunakan Token.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 cursor-pointer rounded-lg border bg-white hover:border-teal-500 transition-all has-[:checked]:border-teal-500 has-[:checked]:ring-1 has-[:checked]:ring-teal-500">
                    <input type="radio" name="is_premium" value="0" class="peer hidden" checked onclick="togglePremium(false)">
                    <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center">
                            <span class="material-symbols-rounded">lock_open</span>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-700">Akses Gratis</span>
                            <span class="block text-xs text-slate-500">Dapat diakses semua siswa</span>
                        </div>
                    </div>
                     <div class="absolute top-4 right-4 text-teal-600 opacity-0 peer-checked:opacity-100">
                        <span class="material-symbols-rounded">check_circle</span>
                    </div>
                </label>

                <label class="relative flex items-center p-4 cursor-pointer rounded-lg border bg-white hover:border-amber-500 transition-all has-[:checked]:border-amber-500 has-[:checked]:ring-1 has-[:checked]:ring-amber-500">
                    <input type="radio" name="is_premium" value="1" class="peer hidden" onclick="togglePremium(true)">
                    <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                            <span class="material-symbols-rounded">lock</span>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-700">Berbayar (Premium)</span>
                            <span class="block text-xs text-slate-500">Perlu Token untuk membuka</span>
                        </div>
                    </div>
                    <div class="absolute top-4 right-4 text-amber-600 opacity-0 peer-checked:opacity-100">
                        <span class="material-symbols-rounded">check_circle</span>
                    </div>
                </label>
            </div>

            <!-- Input Harga Token (Hidden by default) -->
            <div id="hargaTokenContainer" class="mt-4 hidden">
                <label class="block text-sm font-bold text-slate-700 mb-2">Harga Token</label>
                <div class="relative max-w-xs">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-rounded text-amber-500 text-xl">token</span>
                    <input type="number" name="harga_token" id="hargaTokenInput" min="0" value="0"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Jumlah Token">
                </div>
                <p class="text-xs text-amber-600 mt-1 font-medium">*Minimal 1 token</p>
            </div>
        </div>

        <!-- Tipe Materi -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Materi</label>
            <div class="flex items-center space-x-6">
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="radio" name="tipe" value="pdf" class="text-teal-600 focus:ring-teal-500" 
                        {{ old('tipe', 'pdf') == 'pdf' ? 'checked' : '' }}>
                    <div class="ml-2 flex items-center gap-2 group-hover:text-teal-700">
                        <span class="material-symbols-rounded text-red-500">picture_as_pdf</span>
                        <span class="text-gray-700 font-medium">Dokumen (PDF)</span>
                    </div>
                </label>
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="radio" name="tipe" value="video" class="text-teal-600 focus:ring-teal-500"
                        {{ old('tipe') == 'video' ? 'checked' : '' }}>
                    <div class="ml-2 flex items-center gap-2 group-hover:text-teal-700">
                        <span class="material-symbols-rounded text-blue-500">videocam</span>
                        <span class="text-gray-700 font-medium">Video</span>
                    </div>
                </label>
                 <label class="inline-flex items-center cursor-pointer group">
                    <input type="radio" name="tipe" value="soal" class="text-teal-600 focus:ring-teal-500"
                        {{ old('tipe') == 'soal' ? 'checked' : '' }}>
                    <div class="ml-2 flex items-center gap-2 group-hover:text-teal-700">
                        <span class="material-symbols-rounded text-purple-500">quiz</span>
                        <span class="text-gray-700 font-medium">Latihan Soal</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Upload File -->
        <div class="mb-6">
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Upload File (Video/PDF/ZIP)</label>
            <div class="flex items-center justify-center w-full">
                <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-teal-500 transition-all">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <span class="material-symbols-rounded text-gray-400 text-3xl mb-2">cloud_upload</span>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                        <p class="text-xs text-gray-500">Max. 50MB (Boleh dikosongkan dulu)</p>
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
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Tambahan (Opsional)</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors"
                placeholder="Berikan instruksi atau penjelasan singkat tentang materi ini...">{{ old('deskripsi') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('pengajar.materi') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md transition-colors flex items-center">
                <span class="material-symbols-rounded text-xl mr-2">upload</span>
                Simpan Materi
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

    function togglePremium(isPremium) {
        const container = document.getElementById('hargaTokenContainer');
        const input = document.getElementById('hargaTokenInput');
        
        if (isPremium) {
            container.classList.remove('hidden');
            input.value = input.value || 50; // Default price suggestion
        } else {
            container.classList.add('hidden');
            input.value = 0;
        }
    }
</script>
@endsection
