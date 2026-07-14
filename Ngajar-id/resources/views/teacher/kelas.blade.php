@extends('layouts.dashboard')

@section('title', 'Kelas Saya - Pengajar')

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-6 flex justify-between items-center" data-aos="fade-down">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Kelas Saya</h1>
                <p class="text-slate-600">Daftar kelas yang kamu ajar</p>
            </div>
            <a href="{{ route('teacher.kelas.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition-colors">
                <span class="material-symbols-rounded">add</span>
                <span>Tambah Kelas Baru</span>
            </a>
        </div>

        <!-- Quick Tutorial Tip (Dismissible) -->
        <div data-aos="fade-up"
            class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start gap-4 relative">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0 text-blue-600">
                <span class="material-symbols-rounded">lightbulb</span>
            </div>
            <div class="flex-1 pr-8">
                <h4 class="font-bold text-blue-900 text-sm mb-1">Tips untuk Pengajar Baru</h4>
                <p class="text-blue-800 text-sm leading-relaxed">
                    Bingung mulai dari mana? Buat <strong>Kelas</strong> terlebih dahulu, lalu tambahkan
                    <strong>Materi</strong> (Video/PDF) di dalamnya. Jangan lupa bagikan link kelas ke siswa Anda!
                </p>
            </div>
            <button class="absolute top-4 right-4 text-blue-400 hover:text-blue-600">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        @if($kelasList->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-12 px-6 flex justify-center">
                <div class="max-w-md w-full">
                    <x-empty-state 
                        icon="school" 
                        title="Belum Ada Kelas" 
                        description="Mulai perjalanan mengajar Anda dengan membuat kelas pertama. Bagikan ilmu Anda dan inspirasi ribuan siswa."
                        actionLabel="Buat Kelas Pertama"
                        actionUrl="{{ route('teacher.kelas.create') }}"
                        actionIcon="add_circle"
                    />
                </div>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kelasList as $kelas)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <x-cards.course-card 
                            title="{{ $kelas['judul'] }}"
                            author="{{ $kelas['total_siswa'] }} Siswa • {{ $kelas['total_materi'] }} Materi"
                            category="{{ ucfirst($kelas['status']) }}"
                            image="https://ui-avatars.com/api/?name={{ urlencode($kelas['judul']) }}&background=0d9488&color=fff&size=400"
                            url="{{ route('teacher.kelas.edit', $kelas['kelas_id'] ?? 1) }}"
                        >
                            <x-slot name="footer">
                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        <x-buttons.secondary href="{{ route('belajar.show', ['kelas_id' => $kelas['kelas_id']]) }}" target="_blank" class="px-3" title="Lihat Kelas (Preview)">
                                            <x-icons.material name="visibility" size="sm" class="-mx-1" />
                                        </x-buttons.secondary>
                                        <x-buttons.primary href="{{ route('teacher.kelas.edit', $kelas['kelas_id']) }}" class="flex-1 py-2">
                                            <x-icons.material name="edit" size="sm" class="mr-1 -ml-1" /> Edit
                                        </x-buttons.primary>
                                        <x-buttons.primary href="{{ route('kelas.live', $kelas['kelas_id']) }}" target="_blank" color="red" class="px-3" title="Mulai Kelas Live">
                                            <x-icons.material name="videocam" size="sm" class="-mx-1" />
                                        </x-buttons.primary>
                                    </div>
                                    <div class="flex gap-2 mt-1 border-t border-gray-100 pt-3">
                                        <x-buttons.secondary href="{{ route('teacher.kelas.students', $kelas['kelas_id']) }}" class="flex-1 border-blue-200 text-blue-700 hover:bg-blue-50 py-1.5 px-0">
                                            <x-icons.material name="group" size="sm" class="mr-1" /> Siswa
                                        </x-buttons.secondary>
                                        <x-buttons.secondary href="{{ route('teacher.kelas.analytics', $kelas['kelas_id']) }}" class="flex-1 border-amber-200 text-amber-700 hover:bg-amber-50 py-1.5 px-0">
                                            <x-icons.material name="bar_chart" size="sm" class="mr-1" /> Analitik
                                        </x-buttons.secondary>
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
