@extends('layouts.dashboard')

@section('title', 'Kelas Saya - Murid')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6" data-aos="fade-down">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Kelas Saya</h1>
            <p class="text-slate-600">Daftar kelas yang sedang kamu ikuti</p>
        </div>

        @if($kelasList->isEmpty())
            <x-empty-state 
                icon="school" 
                title="Belum Ada Kelas" 
                description="Kamu belum terdaftar di kelas manapun. Yuk jelajahi katalog kelas kami dan temukan skill baru."
                actionLabel="Jelajah Kelas"
                actionUrl="{{ route('student.katalog') }}"
                actionIcon="manage_search"
            />
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kelasList as $kelas)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <x-cards.course-card 
                            title="{{ $kelas['judul'] }}"
                            author="{{ $kelas['pengajar_name'] }}"
                            category="{{ $kelas['kategori'] ?? 'Umum' }}"
                            image="{{ $kelas['thumbnail'] ? asset('storage/' . $kelas['thumbnail']) : 'https://ui-avatars.com/api/?name='.urlencode($kelas['judul']).'&background=0d9488&color=fff&size=400' }}"
                            url="{{ route('belajar.show', ['kelas_id' => $kelas['kelas_id']]) }}"
                        >
                            <x-slot name="footer">
                                <div class="mb-4">
                                    <x-progress-bar :percentage="0" color="teal" showLabel="true" />
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                        <x-icons.material name="calendar_today" size="sm" />
                                        {{ \Carbon\Carbon::parse($kelas['tanggal_daftar'])->format('d M') }}
                                    </div>
                                    <div class="flex gap-2">
                                        <x-buttons.primary href="{{ route('kelas.live', $kelas['kelas_id']) }}" size="sm" color="red" class="animate-pulse px-3 py-1.5" target="_blank" title="Live Class Sedang Berlangsung">
                                            <x-icons.material name="videocam" size="sm" class="mr-1 -ml-1" />
                                            Join Live
                                        </x-buttons.primary>
                                        <x-buttons.primary href="{{ route('belajar.show', ['kelas_id' => $kelas['kelas_id']]) }}" size="sm" class="px-3 py-1.5 bg-slate-900 text-white hover:bg-teal-600">
                                            Lanjut
                                            <x-icons.material name="arrow_forward" size="sm" class="ml-1 -mr-1" />
                                        </x-buttons.primary>
                                    </div>
                                </div>
                            </x-slot>
                        </x-cards.course-card>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection