@extends('layouts.app')

@section('title', 'Ngajar.ID - Platform Edukasi Gratis #1 Indonesia')

@section('content')
    {{-- Bagian Hero: Desain Bersih & Humanis --}}
    <section
        class="relative bg-gradient-to-br from-teal-50 via-white to-amber-50 pt-16 pb-24 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Kiri: Konten Teks Hero --}}
                <div class="space-y-6 lg:pr-8" data-aos="fade-right">
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
                            <div class="font-bold text-slate-900"><span id="stat-pelajar">...</span>+ Pelajar Aktif</div>
                            <div class="flex items-center gap-3 mt-1">
                                <div class="flex items-center gap-1 text-sm text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">
                                    <span class="material-symbols-rounded text-base">star</span>
                                    <span class="font-bold"><span id="stat-rating">...</span></span>
                                    <span class="text-[10px] text-amber-700 opacity-70">Materi</span>
                                </div>
                                <div class="flex items-center gap-1 text-sm text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-100">
                                    <span class="material-symbols-rounded text-base">verified</span>
                                    <span class="font-bold"><span id="stat-relawan-rating">...</span></span>
                                    <span class="text-[10px] text-teal-700 opacity-70">Relawan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Ilustrasi / Statistik Visual --}}
                <div class="relative lg:pl-8" data-aos="fade-left" data-aos-delay="200">
                    <div class="relative bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                        {{-- Kartu Statistik Kecil --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-teal-50 rounded-2xl p-6 text-center">
                                <div class="w-12 h-12 mx-auto mb-3 bg-teal-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-teal-600 text-3xl">library_books</span>
                                </div>
                                <div class="text-2xl font-black text-teal-900">
                                    <span id="stat-modul" class="count-up" data-value="0">0</span>+
                                </div>
                                <div class="text-sm text-teal-700 font-medium">Modul Gratis</div>
                            </div>

                            <div class="bg-amber-50 rounded-2xl p-6 text-center">
                                <div
                                    class="w-12 h-12 mx-auto mb-3 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-amber-600 text-3xl">groups</span>
                                </div>
                                <div class="text-2xl font-black text-amber-900">
                                    <span id="stat-relawan" class="count-up" data-value="0">0</span>+
                                </div>
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

    {{-- Seksi Partner / Authority (Professional standard) --}}
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-8" data-aos="fade-up">Telah
                diliput & didukung oleh</p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-700"
                data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center gap-2 group">
                    <span
                        class="material-symbols-rounded text-3xl text-slate-400 group-hover:text-blue-600 transition-colors">corporate_fare</span>
                    <span
                        class="font-black text-xl text-slate-400 group-hover:text-slate-900 transition-colors">METRO<span>TV</span></span>
                </div>
                <div class="flex items-center gap-2 group">
                    <span
                        class="material-symbols-rounded text-3xl text-slate-400 group-hover:text-red-600 transition-colors">news</span>
                    <span
                        class="font-black text-xl text-slate-400 group-hover:text-slate-900 transition-colors">KOMPAS</span>
                </div>
                <div class="flex items-center gap-2 group">
                    <span
                        class="material-symbols-rounded text-3xl text-slate-400 group-hover:text-amber-600 transition-colors">apartment</span>
                    <span class="font-black text-xl text-slate-400 group-hover:text-slate-900 transition-colors">UNDP</span>
                </div>
                <div class="flex items-center gap-2 group">
                    <span
                        class="material-symbols-rounded text-3xl text-slate-400 group-hover:text-teal-600 transition-colors">account_balance</span>
                    <span
                        class="font-black text-xl text-slate-400 group-hover:text-slate-900 transition-colors">KEMENDIKBUD</span>
                </div>
                <div class="flex items-center gap-2 group">
                    <span
                        class="material-symbols-rounded text-3xl text-slate-400 group-hover:text-blue-500 transition-colors">newspaper</span>
                    <span
                        class="font-black text-xl text-slate-400 group-hover:text-slate-900 transition-colors">TRIBUNE</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Seksi Dampak Nyata --}}
    <section class="py-20 bg-white" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Dampak Nyata untuk Indonesia</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">Bukan hanya angka, tapi cerita perubahan dari setiap
                    individu yang terbantu.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Stat 1: Pelajar --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100/50 border border-teal-200 hover:shadow-xl transition-all group overflow-hidden relative">
                    <div
                        class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-125 transition-transform duration-500">
                        <span class="material-symbols-rounded text-8xl text-teal-600">face_4</span>
                    </div>
                    <div class="w-16 h-16 mx-auto mb-4 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-teal-600 text-4xl">face_4</span>
                    </div>
                    <div class="text-3xl font-black text-teal-900 mb-2">
                        <span id="impact-pelajar">...</span>+
                    </div>
                    <div class="text-sm font-semibold text-teal-700">Pelajar Terbantu</div>
                    <div class="text-xs text-slate-600 mt-1">Mendapat akses pendidikan gratis</div>
                </div>

                {{-- Stat 2: Relawan --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 hover:shadow-xl transition-all group overflow-hidden relative">
                    <div
                        class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-125 transition-transform duration-500">
                        <span class="material-symbols-rounded text-8xl text-amber-600">volunteer_activism</span>
                    </div>
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-amber-600 text-4xl">volunteer_activism</span>
                    </div>
                    <div class="text-3xl font-black text-amber-900 mb-2">
                        <span id="impact-relawan">...</span>+
                    </div>
                    <div class="text-sm font-semibold text-amber-700">Relawan Aktif</div>
                    <div class="text-xs text-slate-600 mt-1">Berbagi ilmu dengan tulus</div>
                </div>

                {{-- Stat 3: Modul --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100/50 border border-teal-200 hover:shadow-xl transition-all group overflow-hidden relative">
                    <div
                        class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-125 transition-transform duration-500">
                        <span class="material-symbols-rounded text-8xl text-teal-600">auto_stories</span>
                    </div>
                    <div class="w-16 h-16 mx-auto mb-4 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-teal-600 text-4xl">auto_stories</span>
                    </div>
                    <div class="text-3xl font-black text-teal-900 mb-2">
                        <span id="impact-modul">...</span>+
                    </div>
                    <div class="text-sm font-semibold text-teal-700">Modul Pembelajaran</div>
                    <div class="text-xs text-slate-600 mt-1">Gratis & berkualitas tinggi</div>
                </div>

                {{-- Stat 4: Donasi --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 hover:shadow-xl transition-all group overflow-hidden relative">
                    <div
                        class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-125 transition-transform duration-500">
                        <span class="material-symbols-rounded text-8xl text-amber-600">savings</span>
                    </div>
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-amber-600 text-4xl">savings</span>
                    </div>
                    <div class="text-3xl font-black text-amber-900 mb-2">Rp
                        <span id="impact-donasi">...</span>jt+
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
    <section id="pilih-peran" class="py-20 bg-gradient-to-br from-slate-50 to-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
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
                <div data-aos="fade-right" data-aos-delay="100"
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
                <div data-aos="fade-left" data-aos-delay="200"
                    class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-transparent hover:border-amber-600">
                    <div
                        class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-100 transition-colors">
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

    {{-- Seksi "Belajar di Mana Saja" (App Promo Style) --}}
    <section class="py-24 bg-teal-600 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-teal-600/10 blur-3xl rounded-full translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-1/4 h-full bg-amber-500/10 blur-3xl rounded-full -translate-x-1/2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl lg:text-5xl font-black text-white leading-tight mb-6">
                        Belajar Jadi Lebih <span class="text-teal-400">Mudah & Fleksibel</span> dari Mana Saja
                    </h2>
                    <p class="text-lg text-white mb-10 leading-relaxed">
                        Nikmati pengalaman belajar yang mulus di perangkat apapun. Ngajar.id dirancang untuk memberikan
                        kenyamanan maksimal baik di desktop maupun smartphone.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#"
                            class="flex items-center gap-3 bg-white hover:bg-slate-100 text-slate-900 px-6 py-3 rounded-2xl transition-all group shadow-xl">
                            <span class="material-symbols-rounded text-3xl">play_store</span>
                            <div class="text-left">
                                <div class="text-[10px] uppercase font-bold text-slate-500 leading-none">Get it on</div>
                                <div class="text-lg font-black leading-tight">Google Play</div>
                            </div>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 bg-slate-800 hover:bg-slate-700 text-white px-6 py-3 rounded-2xl transition-all group border border-slate-700">
                            <span class="material-symbols-rounded text-3xl text-white">apple</span>
                            <div class="text-left">
                                <div class="text-[10px] uppercase font-bold text-slate-400 leading-none">Download on the
                                </div>
                                <div class="text-lg font-black leading-tight">App Store</div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="relative" data-aos="fade-left">
                    {{-- Mockup HP --}}
                    <div
                        class="relative mx-auto w-72 h-[580px] bg-slate-800 rounded-[3rem] border-8 border-slate-700 shadow-2xl overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-6 bg-slate-700 flex justify-center items-end pb-1">
                            <div class="w-20 h-4 bg-slate-800 rounded-full"></div>
                        </div>
                        <div class="p-4 pt-10 h-full bg-white space-y-4">
                            <div class="w-12 h-12 bg-brand-100 rounded-xl shadow-sm"></div>
                            <div class="h-4 bg-slate-100 rounded w-3/4"></div>
                            <div class="h-4 bg-slate-100 rounded w-1/2"></div>
                            <div class="grid grid-cols-2 gap-2 mt-8">
                                <div class="aspect-video bg-slate-50 rounded-xl border border-slate-100"></div>
                                <div class="aspect-video bg-slate-50 rounded-xl border border-slate-100"></div>
                                <div class="aspect-video bg-slate-100 rounded-xl"></div>
                                <div class="aspect-video bg-slate-100 rounded-xl"></div>
                            </div>
                            <div class="pt-8 space-y-3">
                                <div class="h-12 bg-brand-600 rounded-xl"></div>
                                <div class="h-12 bg-slate-50 border border-slate-100 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Floating Badges --}}
                    <div
                        class="absolute -right-4 top-20 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 flex items-center gap-3 animate-bounce">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-rounded">verified</span>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Terakreditasi
                            </div>
                            <div class="text-sm font-bold text-slate-800 leading-none">Materi Terverifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Seksi Fitur Unggulan Platform --}}
    <section class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
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
                <a href="{{ route('programs') }}" data-aos="zoom-in" data-aos-delay="100"
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
                <div data-aos="zoom-in" data-aos-delay="200"
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
                <a href="{{ route('programs') }}" data-aos="zoom-in" data-aos-delay="300"
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
                <div data-aos="zoom-in" data-aos-delay="100"
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
                <a href="{{ route('mentors') }}" data-aos="zoom-in" data-aos-delay="200"
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
                <a href="{{ route('programs') }}" data-aos="zoom-in" data-aos-delay="300"
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

    {{-- Seksi Testimoni Slider (Premium UI) --}}
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-brand-600 font-bold uppercase tracking-widest text-sm">Cerita Sukses</span>
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mt-2">Ribuan Mimpi yang Terwujud</h2>
            </div>

            <div class="swiper testimonialSwiper pb-12" data-aos="fade-up" data-aos-delay="200">
                <div class="swiper-wrapper">
                    <!-- Testi 1 -->
                    <div class="swiper-slide p-4 h-auto">
                        <div
                            class="bg-slate-50 rounded-3xl p-8 border border-gray-100 h-full flex flex-col justify-between hover:border-brand-200 transition-colors">
                            <div class="mb-6">
                                <div class="flex text-amber-400 mb-4">
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                </div>
                                <p class="text-slate-700 italic leading-relaxed">"Ngajar.id membantu saya menguasai dasar
                                    pemrograman Python dari nol sampai bisa buat aplikasi sederhana. Semuanya gratis dan
                                    mentornya sangat sabar."</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name=Andi+Saputra&background=2DD4BF&color=fff"
                                    alt="Andi" class="w-12 h-12 rounded-full border-2 border-white">
                                <div>
                                    <div class="font-bold text-slate-900">Andi Saputra</div>
                                    <div class="text-xs text-slate-500">Murid SMK, Jakarta</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testi 2 -->
                    <div class="swiper-slide p-4 h-auto">
                        <div
                            class="bg-slate-50 rounded-3xl p-8 border border-gray-100 h-full flex flex-col justify-between hover:border-brand-200 transition-colors">
                            <div class="mb-6">
                                <div class="flex text-amber-400 mb-4">
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                </div>
                                <p class="text-slate-700 italic leading-relaxed">"Menjadi relawan pengajar di sini adalah
                                    pengalaman hidup paling berharga. Saya tidak hanya berbagi ilmu, tapi juga belajar arti
                                    ketulusan dari para murid."</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name=Siska+Kohl&background=F59E0B&color=fff"
                                    alt="Siska" class="w-12 h-12 rounded-full border-2 border-white">
                                <div>
                                    <div class="font-bold text-slate-900">Siska Kohl</div>
                                    <div class="text-xs text-slate-500">Relawan Pengajar, Bandung</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testi 3 -->
                    <div class="swiper-slide p-4 h-auto">
                        <div
                            class="bg-slate-50 rounded-3xl p-8 border border-gray-100 h-full flex flex-col justify-between hover:border-brand-200 transition-colors">
                            <div class="mb-6">
                                <div class="flex text-amber-400 mb-4">
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                    <span class="material-symbols-rounded">star</span>
                                </div>
                                <p class="text-slate-700 italic leading-relaxed">"Anak saya sekarang jauh lebih rajin
                                    belajar sejak ikut kelas Bahasa Inggris di Ngajar.id. Modul interaktifnya tidak
                                    membosankan untuk anak SD."</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name=Budi+W&background=0284C7&color=fff" alt="Budi"
                                    class="w-12 h-12 rounded-full border-2 border-white">
                                <div>
                                    <div class="font-bold text-slate-900">Ibu Budi</div>
                                    <div class="text-xs text-slate-500">Orang Tua Murid, Surabaya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    {{-- Seksi FAQ Interaktif --}}
    <section class="py-20 bg-slate-50" data-aos="fade-up">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Punya Pertanyaan?</h2>
                <p class="text-lg text-slate-600">Semua yang perlu kamu ketahui tentang Ngajar.id</p>
            </div>

            <div class="space-y-4">
                {{-- FAQ 1 --}}
                <div
                    class="faq-item bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group"
                        onclick="toggleFaq(this)">
                        <span class="font-bold text-slate-800 group-hover:text-brand-600 transition-colors">Apakah semua
                            modul di Ngajar.id benar-benar gratis?</span>
                        <span
                            class="material-symbols-rounded text-slate-400 group-hover:text-brand-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-50">
                            Ya, 100% gratis! Ngajar.id didirikan dengan misi mendemokrasikan pendidikan. Seluruh akses modul
                            dasar hingga menengah tidak dipungut biaya sepeser pun karena didukung oleh sistem relawan dan
                            donasi publik.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div
                    class="faq-item bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group"
                        onclick="toggleFaq(this)">
                        <span class="font-bold text-slate-800 group-hover:text-brand-600 transition-colors">Siapa saja yang
                            bisa menjadi Relawan Pengajar?</span>
                        <span
                            class="material-symbols-rounded text-slate-400 group-hover:text-brand-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-50">
                            Siapa saja yang memiliki keahlian di bidang tertentu dan memiliki semangat untuk mengajar. Mulai
                            dari mahasiswa, profesional, hingga guru aktif. Kami akan melakukan verifikasi data diri untuk
                            memastikan keamanan komunitas.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div
                    class="faq-item bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group"
                        onclick="toggleFaq(this)">
                        <span class="font-bold text-slate-800 group-hover:text-brand-600 transition-colors">Bagaimana dana
                            donasi disalurkan?</span>
                        <span
                            class="material-symbols-rounded text-slate-400 group-hover:text-brand-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-50">
                            Dana donasi dialokasikan untuk 3 pilar utama: Operasional infrastruktur server agar platform
                            tetap bisa diakses gratis, pengembangan fitur baru lms, dan bantuan alat belajar/kuota bagi
                            murid yang paling membutuhkan. Laporan transparansi tersedia setiap bulan.
                        </div>
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div
                    class="faq-item bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group"
                        onclick="toggleFaq(this)">
                        <span class="font-bold text-slate-800 group-hover:text-brand-600 transition-colors">Bagaimana cara
                            mendapatkan sertifikat setelah selesai belajar?</span>
                        <span
                            class="material-symbols-rounded text-slate-400 group-hover:text-brand-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-50">
                            Kamu perlu menyelesaikan seluruh kuis di setiap materi modul. Setelah mencapai skor minimal
                            kelulusan, sistem akan otomatis menerbitkan Sertifikat Digital atas namamu yang bisa diunduh
                            langsung dari dashboard.
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="text-center mt-12 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm inline-flex items-center gap-4 mx-auto w-full justify-center">
                <span class="text-slate-600">Pertanyaan tidak ada di sini?</span>
                <a href="https://wa.me/6281234567890" target="_blank"
                    class="px-6 py-3 bg-brand-50 text-brand-700 font-bold rounded-xl hover:bg-brand-100 transition-colors">Tanya
                    Admin Langsung</a>
            </div>
        </div>
    </section>

    <script>
        // Initialize Swiper
        document.addEventListener('DOMContentLoaded', () => {
            new Swiper(".testimonialSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                    },
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
            });
        });

        // JS untuk FAQ Accordion
        function toggleFaq(btn) {
            const item = btn.parentElement;
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('.material-symbols-rounded');

            // Check if open
            const isOpening = !item.classList.contains('faq-active');

            // Close all others
            document.querySelectorAll('.faq-item').forEach(otherItem => {
                otherItem.classList.remove('faq-active', 'border-brand-500', 'shadow-lg');
                otherItem.querySelector('.faq-content').style.maxHeight = '0';
                const otherIcon = otherItem.querySelector('.material-symbols-rounded');
                otherIcon.style.transform = 'rotate(0deg)';
                otherIcon.classList.replace('text-brand-600', 'text-slate-400');
            });

            if (isOpening) {
                item.classList.add('faq-active', 'border-brand-500', 'shadow-lg');
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.style.transform = 'rotate(180deg)';
                icon.classList.replace('text-slate-400', 'text-brand-600');
            }
        }

        // JS untuk Count Up Animation
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = progress * (end - start) + start;

                if (end >= 1000000) {
                    obj.innerHTML = (value / 1000000).toFixed(1).toLocaleString('id-ID');
                } else if (end >= 1000) {
                    obj.innerHTML = Math.floor(value).toLocaleString('id-ID');
                } else {
                    obj.innerHTML = (end % 1 === 0) ? Math.floor(value) : value.toFixed(1);
                }

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Observer untuk trigger animasi saat terlihat
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseFloat(counter.getAttribute('data-value'));
                    animateValue(counter, 0, target, 2000);
                    statsObserver.unobserve(counter);
                }
            });
        }, { threshold: 0.2 });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.count-up').forEach(counter => {
                statsObserver.observe(counter);
            });
        });
    </script>

    {{-- Seksi Ajakan Donasi --}}
    <section class="py-20 bg-gradient-to-br from-orange-50 via-white to-teal-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Kiri: Informasi Donasi --}}
                <div class="space-y-6" data-aos="fade-right">

                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 leading-tight">
                        Setiap Rupiah<br>
                        <span class="text-orange-600">Bermakna</span> untuk Pendidikan
                    </h2>

                    <p class="text-lg text-slate-600 leading-relaxed">
                        Bersama-sama, kita bisa membuka akses pendidikan untuk lebih banyak anak Indonesia. Donasimu akan
                        langsung membantu menyediakan modul gratis, pelatihan relawan, dan operasional platform.
                    </p>

                    <div class="bg-white rounded-2xl p-8 border-2 border-amber-200 shadow-sm">
                        <div class="text-center">
                            <span class="text-sm text-slate-600 font-bold uppercase tracking-wide block mb-2">Total Dana
                                Pendidikan Tersalurkan</span>
                            <div class="text-4xl font-black text-amber-600 mb-2">
                                <span id="stat-donasi-full">...</span>
                            </div>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">
                                Dana ini telah digunakan untuk operasional server gratis, sertifikat digital, dan bantuan
                                kuota bagi pelajar.
                            </p>
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
                            selectedAmount = amount;
                            document.getElementById('selectedAmount').value = amount;
                            document.getElementById('selectedAmountText').textContent = formatRupiah(amount);
                            document.getElementById('customAmount').value = '';

                            document.querySelectorAll('.donation-btn').forEach(btn => {
                                btn.classList.remove('active', 'bg-amber-500', 'text-white', 'border-amber-500', 'shadow-lg');
                                btn.classList.add('bg-white', 'border-gray-200', 'hover:bg-amber-50');
                                const title = btn.querySelector('div:first-child');
                                const subtitle = btn.querySelector('div:last-child');
                                if (title) title.classList.add('text-slate-900');
                                if (title) title.classList.remove('text-white');
                                if (subtitle) subtitle.classList.add('text-slate-600');
                                if (subtitle) subtitle.classList.remove('opacity-90');
                            });

                            button.classList.add('active', 'bg-amber-500', 'text-white', 'border-amber-500', 'shadow-lg', 'shadow-amber-500/30');
                            button.classList.remove('bg-white', 'border-gray-200', 'hover:bg-amber-50');

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

                                document.querySelectorAll('.donation-btn').forEach(btn => {
                                    btn.classList.remove('active', 'bg-amber-500', 'text-white', 'border-amber-500', 'shadow-lg');
                                    btn.classList.add('bg-white', 'border-gray-200');
                                    const title = btn.querySelector('div:first-child');
                                    if (title) {
                                        title.classList.remove('text-white');
                                        title.classList.add('text-slate-900');
                                    }
                                });
                            }
                        }

                        function proceedDonation() {
                            if (selectedAmount < 10000) {
                                alert('Minimal donasi adalah Rp 10.000');
                                return;
                            }

                            // Panggil fungsi dari partial
                            openDonationModal();
                        }
                    </script>
                </div>

                {{-- Kanan: Statistik Dampak Donasi --}}
                <div class="space-y-4" data-aos="fade-left" data-aos-delay="200">
                    <div
                        class="bg-white rounded-2xl p-6 border-2 border-teal-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-teal-600 text-3xl">accessibility_new</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Siswa Terbantu</div>
                                <div class="text-2xl font-black text-teal-900"><span id="sidebar-pelajar">...</span> siswa</div>
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
                                <div class="text-2xl font-black text-teal-900"><span id="sidebar-modul">...</span> modul</div>
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

    {{-- Modal Donasi: harus di luar semua section agar overlay benar --}}
    @include('partials.donation-modal')

    @push('scripts')
        <script>
            // ===== Async Stats Loader =====
            // Halaman sudah render, sekarang ambil statistik di background
            document.addEventListener('DOMContentLoaded', function () {
                fetch('{{ route("landing.stats") }}', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(r => r.json())
                    .then(function (stats) {
                        // Update Hero Section
                                setStatWithAnimation('stat-pelajar', stats.pelajar_active);
                                setStatWithAnimation('stat-relawan', stats.relawan_active);
                                setStatWithAnimation('stat-modul', stats.modul_count);

                                // Update Impact Section
                                setStatWithAnimation('impact-pelajar', stats.pelajar_active);
                                setStatWithAnimation('impact-relawan', stats.relawan_active);
                                setStatWithAnimation('impact-modul', stats.modul_count);
                                setStatWithAnimation('impact-donasi', Math.floor(stats.total_donasi / 1000000));

                                // Update Donation Full
                                const donasiFull = document.getElementById('stat-donasi-full');
                                if (donasiFull) {
                                    const formatted = new Intl.NumberFormat('id-ID', { 
                                        style: 'currency', 
                                        currency: 'IDR', 
                                        maximumFractionDigits: 0 
                                    }).format(stats.total_donasi);
                                    donasiFull.textContent = formatted;
                                }

                                // Update Sidebar Donasi
                                const sidePelajar = document.getElementById('sidebar-pelajar');
                                if (sidePelajar) sidePelajar.textContent = stats.pelajar_active.toLocaleString('id-ID');
                                
                                const sideModul = document.getElementById('sidebar-modul');
                                if (sideModul) sideModul.textContent = stats.modul_count.toLocaleString('id-ID');

                                // Rating & Relawan Rating
                                const rating = document.getElementById('stat-rating');
                                if (rating) rating.textContent = stats.rating;

                                const relawanRating = document.getElementById('stat-relawan-rating');
                                if (relawanRating) relawanRating.textContent = stats.relawan_rating;
                            })
                            .catch(function () {
                                // Jika gagal, tampilkan tanda tanya (tidak crash)
                                ['stat-pelajar', 'stat-relawan', 'stat-modul'].forEach(function (id) {
                                    const el = document.getElementById(id);
                                    if (el && el.textContent === '...') el.textContent = '0';
                                });
                            });
                    });

                    function setStatWithAnimation(id, value) {
                        const el = document.getElementById(id);
                        if (!el) return;
                        const target = parseInt(value) || 0;
                        const duration = 1200;
                        const step = Math.ceil(target / (duration / 16));
                        let current = 0;
                        const timer = setInterval(function () {
                            current += step;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            el.textContent = current.toLocaleString('id-ID');
                        }, 16);
                    }
                </script>
    @endpush

@endsection