@extends('layouts.app')

@section('title', 'Ngajar.ID - Platform Edukasi Gratis #1 Indonesia')

@section('content')
    {{-- Bagian Hero: Desain Bersih & Humanis --}}
    <section
        class="relative bg-gradient-to-br from-teal-50 via-white to-amber-50 pt-16 pb-24 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Kiri: Konten Teks Hero --}}
                <div class="space-y-6 lg:pr-8">
                    <h1 class="text-4xl lg:text-5xl xl:text-6xl font-black text-slate-900 leading-tight">
                        Belajar Gratis. <br>
                        <span class="text-teal-600">Mengajar dengan</span><br>
                        <span class="text-teal-600">Dampak Nyata.</span>
                    </h1>

                    <p class="text-lg text-slate-600 leading-relaxed">
                        Bergabunglah dengan ribuan pelajar dan relawan yang sedang membangun masa depan pendidikan Indonesia
                        yang lebih inklusif dan bermakna.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="{{ url('/register?role=murid') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 transform hover:-translate-y-0.5 transition-all">
                            Mulai Belajar Gratis
                            <span class="material-symbols-rounded ml-2">arrow_forward</span>
                        </a>
                        <a href="{{ url('/register?role=pengajar') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white text-teal-600 font-bold rounded-xl border-2 border-teal-600 hover:bg-teal-50 transform hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-rounded mr-2">volunteer_activism</span>
                            Jadi Relawan Pengajar
                        </a>
                    </div>

                    {{-- Indikator Kepercayaan (Testimoni & Statistik) --}}
                    <div class="flex items-center gap-6 pt-6 border-t border-gray-200">
                        <div class="flex -space-x-2">
                            @foreach($volunteers->take(4) as $vol)
                                <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden bg-gray-200">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($vol->name) }}&background=random"
                                        alt="{{ $vol->name }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">{{ number_format($stats['pelajar_active']) }}+ Pelajar
                                Aktif</div>
                            <div class="flex items-center gap-1 text-sm text-amber-600">
                                <span class="material-symbols-rounded text-base text-amber-500">star</span>
                                <span class="font-semibold">{{ $stats['rating'] }}/5 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Ilustrasi / Statistik Visual --}}
                <div class="relative lg:pl-8">
                    <div class="relative bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                        {{-- Kartu Statistik Kecil --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-teal-50 rounded-2xl p-6 text-center">
                                <div class="w-12 h-12 mx-auto mb-3 bg-teal-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-teal-600 text-3xl">library_books</span>
                                </div>
                                <div class="text-2xl font-black text-teal-900">{{ $stats['modul_count'] }}+</div>
                                <div class="text-sm text-teal-700 font-medium">Modul Gratis</div>
                            </div>

                            <div class="bg-amber-50 rounded-2xl p-6 text-center">
                                <div
                                    class="w-12 h-12 mx-auto mb-3 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-amber-600 text-3xl">groups</span>
                                </div>
                                <div class="text-2xl font-black text-amber-900">{{ $stats['relawan_active'] }}+</div>
                                <div class="text-sm text-amber-700 font-medium">Relawan Aktif</div>
                            </div>
                        </div>

                        {{-- Placeholder Ilustrasi --}}
                        <div
                            class="bg-gradient-to-br from-teal-100 to-amber-100 rounded-2xl h-64 flex items-center justify-center">
                            <div class="text-center text-slate-600">
                                <span class="material-symbols-rounded text-teal-600 text-9xl mx-auto mb-4">school</span>
                                <p class="font-semibold">Belajar Bersama<br>Ribuan Siswa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Seksi Dampak Nyata --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Dampak Nyata untuk Indonesia</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">Bukan hanya angka, tapi cerita perubahan dari setiap
                    individu yang terbantu.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Stat 1: Pelajar --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100/50 border border-teal-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-teal-600 text-4xl">face_4</span>
                    </div>
                    <div class="text-3xl font-black text-teal-900 mb-2">{{ number_format($stats['pelajar_active']) }}+</div>
                    <div class="text-sm font-semibold text-teal-700">Pelajar Terbantu</div>
                    <div class="text-xs text-slate-600 mt-1">Mendapat akses pendidikan gratis</div>
                </div>

                {{-- Stat 2: Relawan --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-amber-600 text-4xl">volunteer_activism</span>
                    </div>
                    <div class="text-3xl font-black text-amber-900 mb-2">{{ number_format($stats['relawan_active']) }}+
                    </div>
                    <div class="text-sm font-semibold text-amber-700">Relawan Aktif</div>
                    <div class="text-xs text-slate-600 mt-1">Berbagi ilmu dengan tulus</div>
                </div>

                {{-- Stat 3: Modul --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100/50 border border-teal-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-teal-600 text-4xl">auto_stories</span>
                    </div>
                    <div class="text-3xl font-black text-teal-900 mb-2">{{ $stats['modul_count'] }}+</div>
                    <div class="text-sm font-semibold text-teal-700">Modul Pembelajaran</div>
                    <div class="text-xs text-slate-600 mt-1">Gratis & berkualitas tinggi</div>
                </div>

                {{-- Stat 4: Donasi --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 border border-purple-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-amber-600 text-4xl">savings</span>
                    </div>
                    <div class="text-3xl font-black text-amber-900 mb-2">Rp
                        {{ number_format($stats['total_donasi'] / 1000000, 1) }}jt+
                    </div>
                    <div class="text-sm font-semibold text-amber-700">Donasi Tersalurkan</div>
                    <div class="text-xs text-slate-600 mt-1">Untuk keberlanjutan platform</div>
                </div>
            </div>

            {{-- Kutipan Testimoni Tim --}}
            <div class="mt-16 max-w-4xl mx-auto">
                <div class="bg-teal-50 rounded-2xl p-8 border-l-4 border-teal-600">
                    <p class="text-lg text-slate-700 italic leading-relaxed mb-4">
                        "Setiap hari, puluhan pelajar menemukan harapan baru melalui platform ini. Setiap relawan memberikan
                        lebih dari sekadar ilmu—mereka memberikan masa depan."
                    </p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-full bg-teal-600 flex items-center justify-center text-white font-bold">
                            T</div>
                        <div>
                            <div class="font-bold text-slate-900">— Tim Ngajar.id</div>
                            <div class="text-sm text-slate-600">Founder</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Seksi Pilih Peran (Pelajar/Relawan) --}}
    <section class="py-20 bg-gradient-to-br from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">
                    Pilih Peranmu di <span class="text-teal-600">Ngajar.id</span>
                </h2>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Baik sebagai pelajar yang ingin berkembang atau relawan yang ingin berkontribusi, kami menyambut
                    kehidirMu dengan tangan terbuka.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                {{-- Kartu Pilihan: Pelajar --}}
                <div
                    class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-transparent hover:border-teal-600">
                    <div
                        class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-teal-100 transition-colors">
                        <span class="material-symbols-rounded text-teal-600 text-4xl">school</span>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Saya Pelajar</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Akses ratusan modul pembelajaran dari berbagai mata pelajaran. Belajar dengan pengajar
                        berpengalaman, di mana saja.
                    </p>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-teal-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Akses modul gratis tanpa batas</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-teal-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Kelas live dengan pengajar berpengalaman</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-teal-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Diskusi & komunitas supportif</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-teal-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Sertifikat setelah menyelesaikan kursus</span>
                        </li>
                    </ul>

                    <a href="{{ url('/register?role=murid') }}"
                        class="block w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-center transition-colors">
                        Mulai Belajar Sekarang
                        <span class="material-symbols-rounded align-middle ml-2 text-lg">arrow_forward</span>
                    </a>
                </div>

                {{-- Kartu Pilihan: Relawan --}}
                <div
                    class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-transparent hover:border-amber-600">
                    <div
                        class-="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-100 transition-colors">
                        <span class="material-symbols-rounded text-amber-600 text-4xl">how_to_reg</span>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Saya Relawan</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Berbagi pengetahuanmu untuk membantu pelajar Indonesia. Jadilah bagian dari gerakan edukasi gratis.
                    </p>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Platform mengajar yang mudah digunakan</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Jadwal fleksibel sesuai waktu luangmu</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Komunitas pengajar yang inspiratif</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-600 text-xl">check_circle</span>
                            <span class="text-sm text-slate-700">Sertifikat & apresiasi kontribusi</span>
                        </li>
                    </ul>

                    <a href="{{ url('/register?role=pengajar') }}"
                        class="block w-full py-4 bg-amber-50 hover:bg-amber-100 text-amber-600 font-bold rounded-xl text-center border-2 border-amber-600 transition-colors">
                        Daftar Jadi Relawan
                        <span class="material-symbols-rounded align-middle ml-2 text-lg">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Seksi Fitur Unggulan Platform --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">
                    Semua yang Kamu Butuhkan<br>untuk <span class="text-teal-600">Berkembang</span>
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Platform lengkap dengan berbagai fitur yang dirancang untuk memaksimalkan pengalaman belajar dan
                    mengajarmu.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Fitur 1: Modul Gratis --}}
                <a href="{{ route('programs') }}"
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all group cursor-pointer block">
                    <div
                        class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">auto_stories</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-teal-600">Modul Gratis</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Akses ratusan modul pembelajaran dari berbagai mata pelajaran secara gratis tanpa waktu terbatas.
                    </p>
                </a>

                {{-- Fitur 2: Sistem Token --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all group">
                    <div
                        class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">token</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Sistem Token</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Dapatkan token dari aktivitas belajar untuk membuka modul premium dan fitur eksklusif.
                    </p>
                </div>

                {{-- Fitur 3: Kelas Live --}}
                <a href="{{ route('programs') }}"
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all group cursor-pointer block">
                    <div
                        class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">videocam</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-teal-600">Kelas Online Live</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Ikuti kelas live interaktif dengan pengajar berpengalaman dan diskusi langsung.
                    </p>
                </a>

                {{-- Fitur 4: Komunitas --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all group">
                    <div
                        class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">forum</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Diskusi & Komunitas</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Bergabung dengan komunitas pelajar dan relawan yang saling membantu dan mendukung.
                    </p>
                </div>

                {{-- Fitur 5: Mentoring --}}
                <a href="{{ route('mentors') }}"
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all group cursor-pointer block">
                    <div
                        class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">co_present</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-teal-600">Mentoring Personal</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Dapatkan bimbingan personal dari relawan untuk membantu perjalanan belajarmu.
                    </p>
                </a>

                {{-- Fitur 6: Sertifikat --}}
                <a href="{{ route('programs') }}"
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all group cursor-pointer block">
                    <div
                        class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">workspace_premium</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-teal-600">Sertifikat Digital</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Raih sertifikat resmi setelah menyelesaikan kursus sebagai bukti pencapaianmu.
                    </p>
                </a>
            </div>
        </div>
    </section>

    {{-- Seksi Ajakan Donasi --}}
    <section class="py-20 bg-gradient-to-br from-orange-50 via-white to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Kiri: Informasi Donasi --}}
                <div class="space-y-6">

                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 leading-tight">
                        Setiap Rupiah<br>
                        <span class="text-orange-600">Bermakna</span> untuk Pendidikan
                    </h2>

                    <p class="text-lg text-slate-600 leading-relaxed">
                        Bersama-sama, kita bisa membuka akses pendidikan untuk lebih banyak anak Indonesia. Donasimu akan
                        langsung membantu menyediakan modul gratis, pelatihan relawan, dan operasional platform.
                    </p>

                    <div class="bg-white rounded-2xl p-6 border-2 border-amber-200">
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-sm text-slate-600 font-medium">Terkumpul</span>
                            <span class="text-xl font-black text-amber-600">Rp
                                {{ number_format($stats['total_donasi'] / 1000000, 1) }}Jt</span>
                        </div>
                        <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden mb-2">
                            <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-full rounded-full"
                                style="width: {{ $donation_progress }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>{{ number_format($donation_progress, 0) }}% tercapai</span>
                            <span class="font-semibold">Target: Rp
                                {{ number_format($donation_target / 1000000, 0) }}Jt</span>
                        </div>
                    </div>

                    {{-- Pilihan Nominal Donasi (Interaktif) --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-slate-700 mb-3">Pilih Nominal Donasi:</label>

                        <div class="grid grid-cols-3 gap-4">
                            <button type="button" onclick="selectDonation(25000, this)"
                                class="donation-btn py-3 px-4 bg-white border-2 border-gray-200 rounded-xl hover:border-amber-500 hover:bg-amber-50 transition-all text-center group">
                                <div class="text-lg font-bold text-slate-900 group-hover:text-amber-600">Rp 25.000</div>
                                <div class="text-xs text-slate-600">Bantu 1 pelajar sebulan</div>
                            </button>

                            <button type="button" onclick="selectDonation(50000, this)"
                                class="donation-btn py-3 px-4 bg-amber-500 text-white border-2 border-amber-500 rounded-xl hover:bg-amber-600 transition-all text-center shadow-lg shadow-amber-500/30 transform hover:-translate-y-1 active">
                                <div class="text-lg font-bold">Rp 50.000</div>
                                <div class="text-xs opacity-90">Paling Sering Dipilih ✨</div>
                            </button>

                            <button type="button" onclick="selectDonation(100000, this)"
                                class="donation-btn py-3 px-4 bg-white border-2 border-gray-200 rounded-xl hover:border-amber-500 hover:bg-amber-50 transition-all text-center group">
                                <div class="text-lg font-bold text-slate-900 group-hover:text-amber-600">Rp 100.000</div>
                                <div class="text-xs text-slate-600">5 modul pembelajaran baru</div>
                            </button>
                        </div>

                        {{-- Input Nominal Manual --}}
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-600 mb-2">Atau masukkan nominal lain:</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-semibold">Rp</span>
                                <input type="number" id="customAmount" oninput="selectCustomDonation(this)"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all"
                                    placeholder="50.000" min="10000" step="1000">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Minimal donasi Rp 10.000</p>
                        </div>
                    </div>

                    <button type="button" onclick="proceedDonation()"
                        class="inline-flex items-center justify-center w-full py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-600/30 transition-all hover:scale-[1.02]">
                        <span class="material-symbols-rounded mr-2">favorite</span>
                        Donasi <span id="selectedAmountText">Rp 50.000</span>
                    </button>

                    <p class="text-xs text-slate-500 text-center">
                        Donasi aman & terpercaya. Laporan penggunaan tersedia setiap bulan.
                    </p>

                    {{-- Hidden input to store selected amount --}}
                    <input type="hidden" id="selectedAmount" value="50000">

                    <script>
                        let selectedAmount = 50000; // Default 50k

                        function formatRupiah(amount) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(amount).replace('IDR', 'Rp');
                        }

                        function selectDonation(amount, button) {
                            // Update selected amount
                            selectedAmount = amount;
                            document.getElementById('selectedAmount').value = amount;
                            document.getElementById('selectedAmountText').textContent = formatRupiah(amount);

                            // Clear custom input
                            document.getElementById('customAmount').value = '';

                            // Remove active class from all buttons
                            document.querySelectorAll('.donation-btn').forEach(btn => {
                                btn.classList.remove('active', 'bg-amber-500', 'text-white', 'border-amber-500', 'shadow-lg');
                                btn.classList.add('bg-white', 'border-gray-200');

                                // Reset text colors
                                const title = btn.querySelector('div:first-child');
                                const subtitle = btn.querySelector('div:last-child');
                                if (title) title.classList.remove('text-white');
                                if (subtitle) subtitle.classList.remove('opacity-90');
                            });

                            // Add active class to clicked button
                            button.classList.add('active', 'bg-amber-500', 'text-white', 'border-amber-500', 'shadow-lg', 'shadow-amber-500/30');
                            button.classList.remove('bg-white', 'border-gray-200', 'hover:bg-amber-50');

                            // Make text white for active button
                            const title = button.querySelector('div:first-child');
                            const subtitle = button.querySelector('div:last-child');
                            if (title) {
                                title.classList.remove('text-slate-900', 'group-hover:text-amber-600');
                                title.classList.add('text-white');
                            }
                            if (subtitle) {
                                subtitle.classList.remove('text-slate-600');
                                subtitle.classList.add('opacity-90');
                            }
                        }

                        function selectCustomDonation(input) {
                            const amount = parseInt(input.value) || 0;

                            if (amount >= 10000) {
                                selectedAmount = amount;
                                document.getElementById('selectedAmount').value = amount;
                                document.getElementById('selectedAmountText').textContent = formatRupiah(amount);

                                // Remove active class from preset buttons
                                document.querySelectorAll('.donation-btn').forEach(btn => {
                                    btn.classList.remove('active', 'bg-amber-500', 'text-white', 'border-amber-500', 'shadow-lg');
                                    btn.classList.add('bg-white', 'border-gray-200');

                                    const title = btn.querySelector('div:first-child');
                                    const subtitle = btn.querySelector('div:last-child');
                                    if (title) {
                                        title.classList.remove('text-white');
                                        title.classList.add('text-slate-900');
                                    }
                                    if (subtitle) subtitle.classList.remove('opacity-90');
                                });
                            }
                        }

                        function proceedDonation() {
                            if (selectedAmount < 10000) {
                                alert('Minimal donasi adalah Rp 10.000');
                                return;
                            }

                            // Redirect to donation page with selected amount
                            window.location.href = "{{ route('donasi') }}?nominal=" + selectedAmount;
                        }
                    </script>
                </div>

                {{-- Kanan: Statistik Dampak Donasi --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl p-6 border-2 border-teal-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-teal-600 text-3xl">accessibility_new</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Siswa Terbantu</div>
                                <div class="text-2xl font-black text-teal-900">{{ number_format($stats['pelajar_active']) }}
                                    siswa</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border-2 border-teal-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-teal-600 text-3xl">library_add</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Modul Gratis Ditambahkan</div>
                                <div class="text-2xl font-black text-teal-900">45 modul</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border-2 border-teal-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-teal-600 text-3xl">cloud_done</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Server & Infrastruktur Aman</div>
                                <div class="text-2xl font-black text-teal-900">99.9% Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection