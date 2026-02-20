@extends('layouts.app')

@section('title', 'Dampak & Transparansi - Ngajar.ID')

@section('content')
    {{-- Hero Section: Vision & Mission --}}
    <section class="relative bg-gradient-to-b from-teal-50 to-white py-20 overflow-hidden">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-teal-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-amber-100 rounded-full blur-3xl opacity-50"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-teal-100 text-teal-700 text-sm font-bold mb-4">
                Tentang Ngajar.ID
            </span>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 leading-tight">
                Mewujudkan Akses Pendidikan<br>
                <span class="text-teal-600">Berkualitas Bagi Seluruh Anak Bangsa</span>
            </h1>
            <p class="text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Ngajar.ID hadir sebagai jembatan yang menghubungkan semangat relawan pengajar dengan para pelajar di
                seluruh pelosok negeri. Kami percaya bahwa pendidikan berkualitas adalah hak setiap anak, bukan privilese
                segelintir orang. Bersama, kita bangun masa depan Indonesia yang lebih cerdas dan inklusif.
            </p>
        </div>
    </section>

    {{-- Core Mechanics: Simulasi Ekosistem --}}
    <section class="py-16 bg-white border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Ekosistem Kolaborasi</h2>
                <p class="text-slate-600 mt-2">Sinergi antara teknologi, relawan, dan pelajar untuk menciptakan dampak
                    nyata.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                {{-- Arrow Connector (Hidden on Mobile) --}}
                <div
                    class="hidden md:block absolute top-1/2 left-1/3 w-16 h-1 bg-gray-200 -mt-0.5 z-0 transform -translate-x-1/2">
                </div>
                <div
                    class="hidden md:block absolute top-1/2 right-1/3 w-16 h-1 bg-gray-200 -mt-0.5 z-0 transform translate-x-1/2">
                </div>
                <div class="hidden md:block absolute top-1/2 right-1/2 transform translate-x-1/2 -translate-y-1/2 z-10">
                    <span class="material-symbols-rounded text-gray-300 text-4xl">arrow_forward</span>
                </div>

                {{-- Step 1 --}}
                <div
                    class="relative z-10 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center group hover:shadow-md transition-all">
                    <div
                        class="w-16 h-16 mx-auto bg-teal-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">school</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Akses Tanpa Batas</h3>
                    <p class="text-sm text-slate-600">Pelajar dapat mengakses ribuan materi berkualitas dan kelas live
                        secara gratis kapan saja.</p>
                </div>

                {{-- Step 2 --}}
                <div
                    class="relative z-10 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center group hover:shadow-md transition-all">
                    <div
                        class="w-16 h-16 mx-auto bg-amber-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-amber-600 text-3xl">token</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Apresiasi Keaktifan</h3>
                    <p class="text-sm text-slate-600">Setiap semangat belajar dan kontribusi mengajar dihargai dengan sistem
                        gamifikasi yang memotivasi.</p>
                </div>

                {{-- Step 3 --}}
                <div
                    class="relative z-10 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center group hover:shadow-md transition-all">
                    <div
                        class="w-16 h-16 mx-auto bg-purple-50 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-purple-600 text-3xl">lock_open</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">Pendidikan Inklusif</h3>
                    <p class="text-sm text-slate-600">Menghilangkan kesenjangan dengan memberikan fasilitas premium secara
                        cuma-cuma bagi yang membutuhkan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Transparansi Donasi Real-time --}}
    <section class="py-20 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-black mb-6">
                        Transparansi Adalah<br>
                        <span class="text-teal-400">Kunci Kepercayaan</span>
                    </h2>
                    <p class="text-slate-300 text-lg mb-8 leading-relaxed">
                        Kami percaya bahwa setiap rupiah donasi publik harus dipertanggungjawabkan.
                        Sistem kami mencatat donasi masuk dan alokasi dana secara real-time.
                    </p>

                    <div class="space-y-6">
                        @foreach($donation_stats['allocation'] as $alloc)
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-2">
                                    <span class="text-slate-300">{{ $alloc['label'] }}</span>
                                    <span class="text-white">{{ $alloc['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2.5">
                                    <div class="{{ $alloc['color'] }} h-2.5 rounded-full"
                                        style="width: {{ $alloc['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-slate-800 rounded-3xl p-8 border border-slate-700">
                    <div class="text-center mb-8">
                        <p class="text-slate-400 text-sm font-semibold uppercase tracking-wider">Total Donasi Terkumpul</p>
                        <h3 class="text-4xl lg:text-5xl font-black text-white mt-2">
                            Rp {{ number_format($donation_stats['total_collected'], 0, ',', '.') }}
                        </h3>
                        <p class="text-teal-400 text-sm mt-2 font-medium">dari target Rp
                            {{ number_format($donation_stats['target'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-slate-900/50 rounded-2xl p-6 border border-slate-700">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-white">Donatur Terbaru</h4>
                            <a href="{{ route('donasi') }}" class="text-xs text-teal-400 hover:text-teal-300">Lihat
                                Semua</a>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-r from-teal-400 to-blue-500 flex items-center justify-center text-xs font-bold">
                                    H</div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-white">Hamba Allah</div>
                                    <div class="text-xs text-slate-500">Baru saja</div>
                                </div>
                                <div class="text-sm text-teal-400 font-bold">+Rp 50.000</div>
                            </li>
                            <li class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-r from-amber-400 to-red-500 flex items-center justify-center text-xs font-bold">
                                    B</div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-white">Budi Santoso</div>
                                    <div class="text-xs text-slate-500">5 menit lalu</div>
                                </div>
                                <div class="text-sm text-teal-400 font-bold">+Rp 100.000</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Wall of Impact (Relawan & Tim) --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Top Relawan --}}
            {{-- Top Relawan --}}
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Relawan Pengajar Teraktif</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    Dengan dedikasi tinggi, mereka telah membantu ratusan siswa mencapai impiannya.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-24">
                @php
                    $gradients = [
                        'from-teal-400 to-emerald-500',
                        'from-blue-400 to-indigo-500',
                        'from-orange-400 to-pink-500'
                    ];
                @endphp

                @foreach($top_relawan as $index => $relawan)
                    <div
                        class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group ring-1 ring-slate-200/50">
                        {{-- Abstract Header Pattern --}}
                        <div class="relative h-32 bg-gradient-to-r {{ $gradients[$loop->index % count($gradients)] }}">
                            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-20 transition-opacity"></div>

                            {{-- Decorative Circles --}}
                            <div class="absolute -right-4 -top-8 w-24 h-24 rounded-full bg-white/20 blur-xl"></div>
                            <div class="absolute left-8 bottom-4 w-12 h-12 rounded-full bg-white/20 blur-lg"></div>
                        </div>

                        <div class="px-6 pb-8 text-center relative">
                            {{-- Avatar --}}
                            <div class="relative -mt-12 mb-6 inline-block">
                                <div
                                    class="w-24 h-24 rounded-full border-[5px] border-white shadow-lg overflow-hidden bg-white">
                                    <img src="{{ $relawan['image'] }}" alt="{{ $relawan['name'] }}"
                                        class="w-full h-full object-cover">
                                </div>
                                {{-- Verification Badge --}}
                                <div class="absolute bottom-0 right-0 bg-blue-500 text-white p-1 rounded-full border-2 border-white"
                                    title="Terverifikasi">
                                    <span class="material-symbols-rounded text-sm block">verified</span>
                                </div>
                            </div>

                            <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $relawan['name'] }}</h3>
                            <p class="text-teal-600 font-medium text-sm mb-4">{{ $relawan['role'] }}</p>

                            {{-- Stats --}}
                            <div
                                class="flex items-center justify-center gap-2 px-4 py-2 bg-slate-50 border border-slate-100 rounded-full mx-auto group-hover:bg-amber-50 group-hover:border-amber-100 group-hover:text-amber-700 transition-colors">
                                <span
                                    class="material-symbols-rounded text-lg text-slate-400 group-hover:text-amber-500">schedule</span>
                                <span
                                    class="text-sm font-bold text-slate-600 group-hover:text-amber-700">{{ $relawan['hours'] }}
                                    Jam Mengajar</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Team Developer --}}
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-slate-900">Di Balik Layar Ngajar.ID</h2>
                <p class="text-slate-500 mt-2">Tim pengembang yang mewujudkan platform ini.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                @foreach($teams as $team)
                    <div class="text-center group">
                        <div
                            class="w-24 h-24 mx-auto rounded-full overflow-hidden mb-4 border-2 border-gray-200 group-hover:border-teal-500 transition-colors">
                            <img src="{{ asset($team['image']) }}" alt="{{ $team['name'] }}"
                                class="w-full h-full object-cover bg-gray-100">
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $team['name'] }}</h4>
                        <p class="text-xs text-slate-500">{{ $team['role'] }}</p>
                        <p class="text-[10px] text-teal-600 font-mono mt-1">{{ $team['nim'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection