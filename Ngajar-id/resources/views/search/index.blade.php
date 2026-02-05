@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">
            Hasil Pencarian: <span class="text-indigo-600">"{{ $query }}"</span>
        </h1>

        @if($kelasResults->isEmpty() && $materiResults->isEmpty())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-yellow-700">Tidak ditemukan hasil yang cocok.</p>
            </div>
        @else
            <!-- Hasil Kelas -->
            @if($kelasResults->isNotEmpty())
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Kelas Ditemukan ({{ $kelasResults->count() }})</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($kelasResults as $kelas)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <div class="h-32 bg-gray-200 flex items-center justify-center">
                                @if($kelas->thumbnail)
                                    <img src="{{ asset('storage/' . $kelas->thumbnail) }}" alt="{{ $kelas->judul }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-400 text-4xl">📚</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg mb-1">{{ $kelas->judul }}</h3>
                                <p class="text-sm text-gray-500 mb-2">Oleh: {{ $kelas->pengajar->name ?? 'Admin' }}</p>
                                <a href="{{ route('kelas.live', $kelas->kelas_id) }}"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Lihat Kelas &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Hasil Materi -->
            @if($materiResults->isNotEmpty())
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Materi Ditemukan ({{ $materiResults->count() }})</h2>
                <ul class="space-y-3">
                    @foreach($materiResults as $materi)
                        <li class="bg-white p-4 rounded shadow-sm hover:bg-gray-50 transition border border-gray-100">
                            <a href="{{ route('belajar.show', ['kelas_id' => $materi->kelas_id, 'materi_id' => $materi->materi_id]) }}"
                                class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $materi->judul }}</h4>
                                    <span class="text-xs text-gray-500">Tipe: {{ ucfirst($materi->tipe) }}</span>
                                </div>
                                <span class="text-gray-400">&rarr;</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
@endsection