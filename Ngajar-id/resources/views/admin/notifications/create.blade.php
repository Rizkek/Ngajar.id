@extends('layouts.dashboard')

@section('title', 'Buat Broadcast - Admin')

@section('content')
    <div class="container-fluid px-4 max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('admin.notifications.index') }}"
                class="text-teal-600 hover:text-teal-700 font-medium mb-4 inline-flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-slate-900 mb-2 mt-4">Buat Broadcast Baru</h1>
            <p class="text-slate-600">Kirim pesan massal ke pengguna aplikasi</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.notifications.send') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf

            <div class="space-y-6">
                <!-- Target Audience -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Target Penerima</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-data="{ selectedType: 'all' }">
                        <label
                            class="cursor-pointer border border-gray-200 rounded-lg p-4 hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center gap-2 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-100 has-[:checked]:text-teal-700">
                            <input type="radio" name="recipient_type" value="all" class="sr-only" checked
                                x-model="selectedType">
                            <span class="material-symbols-rounded text-2xl">groups</span>
                            <span class="text-sm font-bold">Semua User</span>
                        </label>

                        <label
                            class="cursor-pointer border border-gray-200 rounded-lg p-4 hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center gap-2 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-100 has-[:checked]:text-teal-700">
                            <input type="radio" name="recipient_type" value="murid" class="sr-only" x-model="selectedType">
                            <span class="material-symbols-rounded text-2xl">school</span>
                            <span class="text-sm font-bold">Hanya Murid</span>
                        </label>

                        <label
                            class="cursor-pointer border border-gray-200 rounded-lg p-4 hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center gap-2 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-100 has-[:checked]:text-teal-700">
                            <input type="radio" name="recipient_type" value="pengajar" class="sr-only"
                                x-model="selectedType">
                            <span class="material-symbols-rounded text-2xl">person_book</span>
                            <span class="text-sm font-bold">Hanya Pengajar</span>
                        </label>

                        <label
                            class="cursor-pointer border border-gray-200 rounded-lg p-4 hover:border-teal-500 hover:bg-teal-50 transition-all flex flex-col items-center gap-2 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-100 has-[:checked]:text-teal-700">
                            <input type="radio" name="recipient_type" value="kelas" class="sr-only" x-model="selectedType">
                            <span class="material-symbols-rounded text-2xl">class</span>
                            <span class="text-sm font-bold">Peserta Kelas</span>
                        </label>

                        <!-- Kelas Select (Conditional) -->
                        <div class="col-span-2 md:col-span-4 mt-4" x-show="selectedType === 'kelas'" x-transition>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Kelas *</label>
                            <select name="kelas_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($allKelas as $kelas)
                                    <option value="{{ $kelas->kelas_id }}">
                                        {{ Str::limit($kelas->judul, 50) }} ({{ $kelas->peserta->count() ?? 0 }} peserta)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 my-6">

                <!-- Konten Pesan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Notifikasi *</label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                            placeholder="Contoh: Pemeliharaan Sistem">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Isi Pesan *</label>
                        <textarea name="message" rows="4" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                            placeholder="Tulis pesan lengkap di sini..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Link Tujuan (Opsional)</label>
                        <input type="url" name="action_url"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                            placeholder="https://ngajar.id/promo">
                        <p class="text-xs text-slate-500 mt-1">User akan diarahkan ke sini saat klik notifikasi.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Prioritas</label>
                        <select name="priority"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                            <option value="normal" selected>Normal (Biru)</option>
                            <option value="high">Penting / High (Merah)</option>
                            <option value="low">Info / Low (Abu-abu)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.notifications.index') }}"
                    class="px-6 py-3 border border-gray-300 text-slate-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition-all shadow-lg flex items-center gap-2">
                    <span class="material-symbols-rounded">send</span>
                    Kirim Broadcast
                </button>
            </div>
        </form>
    </div>
@endsection