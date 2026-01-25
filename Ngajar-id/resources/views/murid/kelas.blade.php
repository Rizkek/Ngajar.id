@extends('layouts.dashboard')

@section('title', 'Kelas Saya - Murid')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Kelas Saya</h1>
            <p class="text-slate-600">Daftar kelas yang sedang kamu ikuti</p>
        </div>

        @if($kelasList->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="material-symbols-rounded text-slate-300 text-6xl mb-4">school</span>
                <h3 class="text-xl font-semibold text-slate-700 mb-2">Belum Ada Kelas</h3>
                <p class="text-slate-500 mb-4">Kamu belum terdaftar di kelas manapun.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kelasList as $kelas)
                    <div
                        class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden relative">
                        <!-- Card Header -->
                        <div class="h-32 bg-gradient-to-br from-teal-500 to-teal-700 relative overflow-hidden">
                            <div
                                class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20">
                            </div>
                            <div class="absolute top-4 right-4">
                                <span
                                    class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider rounded-full border border-white/20">
                                    {{ ucfirst($kelas['status']) }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 -mt-12 relative z-10">
                            <!-- Icon -->
                            <div
                                class="w-16 h-16 bg-white rounded-2xl shadow-md flex items-center justify-center mb-4 transition-transform group-hover:scale-110 duration-300">
                                <span class="material-symbols-rounded text-teal-600 text-3xl">school</span>
                            </div>

                            <h3
                                class="text-xl font-bold text-slate-900 mb-2 line-clamp-1 group-hover:text-teal-600 transition-colors">
                                {{ $kelas['judul'] }}</h3>

                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-rounded text-slate-400 text-sm">person</span>
                                <span class="text-sm text-slate-500 font-medium">{{ $kelas['pengajar_name'] }}</span>
                            </div>

                            <p class="text-slate-600 text-sm mb-6 line-clamp-2 leading-relaxed h-10">{{ $kelas['deskripsi'] }}</p>

                            <!-- Progress Placeholder -->
                            <div class="mb-6">
                                <div class="flex justify-between text-xs text-slate-400 mb-1">
                                    <span>Progress</span>
                                    <span class="font-bold text-teal-600">0%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-teal-500 h-1.5 rounded-full w-0 group-hover:w-[5%] transition-all duration-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Footer/Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                    <span class="material-symbols-rounded text-base">calendar_today</span>
                                    {{ \Carbon\Carbon::parse($kelas['tanggal_daftar'])->format('d M') }}
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('kelas.live', $kelas['kelas_id']) }}" target="_blank"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all"
                                        title="Live Class">
                                        <span class="material-symbols-rounded text-lg">videocam</span>
                                    </a>
                                    <a href="{{ route('belajar.show', ['kelas_id' => $kelas['kelas_id']]) }}"
                                        class="px-4 py-2 bg-slate-900 text-white text-sm font-bold rounded-lg hover:bg-teal-600 hover:shadow-lg hover:shadow-teal-500/30 transition-all flex items-center gap-2">
                                        Lanjut
                                        <span class="material-symbols-rounded text-base">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection