@extends('layouts.dashboard')

@section('title', 'Materi - Pengajar')

@section('dashboard-content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Materi Pembelajaran</h1>
                <p class="text-slate-600">Semua materi dari kelas yang kamu ajar</p>
            </div>
            <button
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition-colors">
                <span class="material-symbols-rounded">add</span>
                <span>Upload Materi Baru</span>
            </button>
        </div>

        @if(empty($materiList))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="material-symbols-rounded text-slate-300 text-6xl mb-4">menu_book</span>
                <h3 class="text-xl font-semibold text-slate-700 mb-2">Belum Ada Materi</h3>
                <p class="text-slate-500 mb-4">Mulai menambahkan materi pembelajaran untuk kelasmu.</p>
            </div>
        @else
            <div class="grid gap-4">
                @foreach($materiList as $materi)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($materi['tipe'] === 'video')
                                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-rounded text-red-600 text-3xl">videocam</span>
                                    </div>
                                @elseif($materi['tipe'] === 'pdf')
                                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-rounded text-blue-600 text-3xl">picture_as_pdf</span>
                                    </div>
                                @else
                                    <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-rounded text-gray-600 text-3xl">description</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $materi['judul'] }}</h3>
                                <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                                    <span class="material-symbols-rounded text-base">school</span>
                                    <span>{{ $materi['kelas_judul'] }}</span>
                                </div>
                                @if(!empty($materi['deskripsi']))
                                    <p class="text-slate-600 text-sm mb-3">{{ $materi['deskripsi'] }}</p>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                        {{ strtoupper($materi['tipe']) }}
                                    </span>
                                    @if($materi['file_url'])
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Belum Upload
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-shrink-0 flex gap-2">
                                <button
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    <span class="material-symbols-rounded text-lg">edit</span>
                                    <span>Edit</span>
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    <span class="material-symbols-rounded text-lg">delete</span>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection