@extends('layouts.dashboard')

@section('title', 'Detail Learning Path - ' . $path->judul)
@section('header_title', 'Detail Learning Path')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content (Left) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Header -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <span class="material-symbols-rounded text-[10rem] text-teal-600 rotate-12">route</span>
                    </div>

                    <div class="relative z-10">
                        <span
                            class="inline-block px-3 py-1 bg-teal-50 text-teal-700 text-xs font-bold uppercase tracking-wider rounded-lg mb-4">
                            {{ $path->level }}
                        </span>
                        <h1 class="text-3xl font-black text-slate-900 mb-4 leading-tight">{{ $path->judul }}</h1>
                        <p class="text-lg text-slate-600 mb-6">{{ $path->deskripsi }}</p>

                        <div class="flex items-center gap-6 text-sm text-slate-500 mb-8">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded text-teal-500">schedule</span>
                                {{ $path->estimated_hours ?? 10 }} Jam Estimasi
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded text-teal-500">school</span>
                                {{ $path->kelas->count() }} Kelas
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded text-teal-500">group</span>
                                {{ $path->total_enrolled }} Peserta
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Syllabus / Classes List -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-800">Kurikulum Path</h2>
                        <span class="text-sm text-slate-500">{{ $path->kelas->count() }} Modul Pembelajaran</span>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @forelse($path->kelas as $index => $kelas)
                            @php
                                $isUnlocked = $isEnrolled || $index === 0; // First usually free or if enrolled
                                // Adjust logic based on real progress tracking if needed
                                $isCompleted = false; // TODO: Check via progress
                            @endphp
                            <div class="p-6 hover:bg-slate-50 transition group {{ $isEnrolled ? 'cursor-pointer' : '' }}">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg {{ $isCompleted ? 'bg-green-100 text-green-600' : ($isUnlocked ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 text-gray-400') }}">
                                        @if($isCompleted)
                                            <span class="material-symbols-rounded">check</span>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-teal-600 transition">
                                            {{ $kelas->judul }}
                                        </h3>
                                        <p class="text-sm text-slate-500 mb-2">{{ Str::limit($kelas->deskripsi, 100) }}</p>

                                        <div class="flex items-center gap-4 text-xs text-slate-400">
                                            <span class="flex items-center gap-1">
                                                <span class="material-symbols-rounded text-sm">person</span>
                                                {{ $kelas->pengajar->name ?? 'Tim Pengajar' }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <span class="material-symbols-rounded text-sm">schedule</span>
                                                {{ $kelas->durasi_menit ?? 60 }} menit
                                            </span>
                                        </div>
                                    </div>

                                    @if($isEnrolled)
                                        <a href="{{ route('belajar.show', $kelas->kelas_id) }}"
                                            class="flex-shrink-0 px-4 py-2 bg-white border border-teal-200 text-teal-700 rounded-lg hover:bg-teal-50 transition text-sm font-bold shadow-sm">
                                            Mulai
                                        </a>
                                    @else
                                        <span class="material-symbols-rounded text-gray-300">lock</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-500">
                                Belum ada kelas dalam learning path ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right) -->
            <div class="space-y-6">
                <!-- Enrollment Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-teal-100 p-6 sticky top-24">
                    @if($isEnrolled)
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                                <span class="material-symbols-rounded text-3xl">check_circle</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Anda Terdaftar!</h3>

                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                <div class="bg-teal-600 h-2.5 rounded-full"
                                    style="width: {{ $progress->progress_percentage ?? 0 }}%"></div>
                            </div>
                            <p class="text-sm text-slate-500 mb-6">{{ $progress->progress_percentage ?? 0 }}% Selesai</p>

                            <a href="{{ route('murid.learning-paths.index') }}"
                                class="block w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition shadow-lg shadow-teal-200">
                                Lanjutkan Belajar
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Mulai Perjalanan Anda</h3>
                            <p class="text-slate-500 mb-6 text-sm">Dapatkan akses penuh ke kurikulum terstruktur dan sertifikat
                                kompetensi.</p>

                            <!-- Pricing Info -->
                            <div class="mb-6 bg-slate-50 rounded-xl p-4 border border-slate-100">
                                @if(Auth::user()->hasBeasiswa())
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-green-600 uppercase tracking-widest mb-1">Beasiswa
                                            Aktif</span>
                                        <span class="text-3xl font-black text-slate-900">GRATIS</span>
                                        <span class="text-xs text-slate-400 line-through mt-1">
                                            {{ $path->harga_token > 0 ? $path->harga_token . ' Token' : 'Berbayar' }}
                                        </span>
                                    </div>
                                @elseif($path->harga_token > 0)
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Investasi
                                            Belajar</span>
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-rounded text-yellow-500 text-2xl">Generating_tokens</span>
                                            <span class="text-3xl font-black text-slate-900">{{ $path->harga_token }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-slate-500">Token</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-teal-600 uppercase tracking-widest mb-1">Akses
                                            Terbuka</span>
                                        <span class="text-3xl font-black text-slate-900">GRATIS</span>
                                    </div>
                                @endif
                            </div>

                            <form action="{{ route('learning-paths.enroll', $path->path_id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition shadow-lg shadow-teal-200 transform hover:-translate-y-1 mb-3 flex items-center justify-center gap-2">
                                    @if($path->harga_token > 0 && !Auth::user()->hasBeasiswa())
                                        <span>Beli Akses Sekarang</span>
                                    @else
                                        <span>Daftar Sekarang - Gratis</span>
                                    @endif
                                </button>
                            </form>
                            <p class="text-xs text-slate-400">Akses seumur hidup • Sertifikat Digital</p>
                        </div>
                    @endif
                </div>

                <!-- Instructor Info -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-slate-800 mb-4">Tim Kurator</h3>
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('storage/' . ($path->creator->avatar ?? 'default-avatar.png')) }}"
                            class="w-12 h-12 rounded-full bg-gray-200 object-cover"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($path->creator->name ?? 'Admin') }}&background=random'">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $path->creator->name ?? 'Tim Ngajar.ID' }}</h4>
                            <p class="text-xs text-slate-500">Learning Architect</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection