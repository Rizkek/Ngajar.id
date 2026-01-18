@extends('layouts.app')

@section('title', 'Ngajar.ID - Platform Edukasi Gratis #1 Indonesia')

@section('content')
    {{-- Hero Section: Clean & Humanist --}}
    <section
        class="relative bg-gradient-to-br from-teal-50 via-white to-amber-50 pt-16 pb-24 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left: Text Content --}}
                <div class="space-y-6 lg:pr-8">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-100 text-teal-700 text-sm font-semibold">
                        <span class="material-symbols-rounded text-base">verified</span>
                        Platform Edukasi Gratis #1 Indonesia
                    </div>

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
                        <a href="{{ url('/register') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 transform hover:-translate-y-0.5 transition-all">
                            Mulai Belajar Gratis
                            <span class="material-symbols-rounded ml-2">arrow_forward</span>
                        </a>
                        <a href="{{ url('/register') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white text-teal-600 font-bold rounded-xl border-2 border-teal-600 hover:bg-teal-50 transform hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-rounded mr-2">volunteer_activism</span>
                            Jadi Relawan Pengajar
                        </a>
                    </div>

                    {{-- Trust Indicators --}}
                    <div class="flex items-center gap-6 pt-6 border-t border-gray-200">
                        <div class="flex -space-x-2">
                            <div
                                class="w-8 h-8 rounded-full bg-teal-600 border-2 border-white flex items-center justify-center text-white text-xs font-bold">
                                A</div>
                            <div
                                class="w-8 h-8 rounded-full bg-amber-600 border-2 border-white flex items-center justify-center text-white text-xs font-bold">
                                B</div>
                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 border-2 border-white flex items-center justify-center text-white text-xs font-bold">
                                C</div>
                            <div
                                class="w-8 h-8 rounded-full bg-purple-600 border-2 border-white flex items-center justify-center text-white text-xs font-bold">
                                D</div>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">5,000+ Pelajar Aktif</div>
                            <div class="flex items-center gap-1 text-sm text-amber-600">
                                <span class="material-symbols-rounded text-base text-amber-500">star</span>
                                <span class="font-semibold">4.9/5 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Illustration / Stats --}}
                <div class="relative lg:pl-8">
                    <div class="relative bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                        {{-- Stats Cards --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-teal-50 rounded-2xl p-6 text-center">
                                <div class="w-12 h-12 mx-auto mb-3 bg-teal-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-teal-600 text-3xl">library_books</span>
                                </div>
                                <div class="text-2xl font-black text-teal-900">200+</div>
                                <div class="text-sm text-teal-700 font-medium">Modul Gratis</div>
                            </div>

                            <div class="bg-amber-50 rounded-2xl p-6 text-center">
                                <div
                                    class="w-12 h-12 mx-auto mb-3 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-amber-600 text-3xl">groups</span>
                                </div>
                                <div class="text-2xl font-black text-amber-900">500+</div>
                                <div class="text-sm text-amber-700 font-medium">Relawan Aktif</div>
                            </div>
                        </div>

                        {{-- Illustration Placeholder --}}
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

    {{-- Dampak Nyata Section --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Dampak Nyata untuk Indonesia</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">Bukan hanya angka, tapi cerita perubahan dari setiap
                    individu yang terbantu.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Stat 1 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100/50 border border-teal-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-teal-600 text-4xl">face_4</span>
                    </div>
                    <div class="text-3xl font-black text-teal-900 mb-2">5,234+</div>
                    <div class="text-sm font-semibold text-teal-700">Pelajar Terbantu</div>
                    <div class="text-xs text-slate-600 mt-1">Mendapat akses pendidikan gratis</div>
                </div>

                {{-- Stat 2 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-amber-600 text-4xl">volunteer_activism</span>
                    </div>
                    <div class="text-3xl font-black text-amber-900 mb-2">1,089+</div>
                    <div class="text-sm font-semibold text-amber-700">Relawan Aktif</div>
                    <div class="text-xs text-slate-600 mt-1">Berbagi ilmu dengan tulus</div>
                </div>

                {{-- Stat 3 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-blue-600 text-4xl">auto_stories</span>
                    </div>
                    <div class="text-3xl font-black text-blue-900 mb-2">450+</div>
                    <div class="text-sm font-semibold text-blue-700">Modul Pembelajaran</div>
                    <div class="text-xs text-slate-600 mt-1">Gratis & berkualitas tinggi</div>
                </div>

                {{-- Stat 4 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-purple-600 text-4xl">savings</span>
                    </div>
                    <div class="text-3xl font-black text-purple-900 mb-2">Rp 125jt+</div>
                    <div class="text-sm font-semibold text-purple-700">Donasi Tersalurkan</div>
                    <div class="text-xs text-slate-600 mt-1">Untuk keberlanjutan platform</div>
                </div>
            </div>

            {{-- Testimonial Quote --}}
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

    {{-- Pilih Peranmu Section --}}
    <section class="py-20 bg-gradient-to-br from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-block px-4 py-2 bg-teal-100 text-teal-700 rounded-full text-sm font-semibold mb-4">
                    MULAI PERJALANANMU
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">
                    Pilih Peranmu di <span class="text-teal-600">Ngajar.id</span>
                </h2>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Baik sebagai pelajar yang ingin berkembang atau relawan yang ingin berkontribusi, kami menyambut
                    kehidirMu dengan tangan terbuka.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                {{-- Saya Pelajar --}}
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

                    <a href="{{ url('/register') }}"
                        class="block w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-center transition-colors">
                        Mulai Belajar Sekarang
                        <span class="material-symbols-rounded align-middle ml-2 text-lg">arrow_forward</span>
                    </a>
                </div>

                {{-- Saya Relawan --}}
                <div
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

                    <a href="{{ url('/register') }}"
                        class="block w-full py-4 bg-white hover:bg-amber-50 text-amber-600 font-bold rounded-xl text-center border-2 border-amber-600 transition-colors">
                        Daftar Jadi Relawan
                        <span class="material-symbols-rounded align-middle ml-2 text-lg">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur Unggulan Section --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-4">
                    FITUR UNGGULAN
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">
                    Semua yang Kamu Butuhkan<br>untuk <span class="text-teal-600">Berkembang</span>
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Platform lengkap dengan berbagai fitur yang dirancang untuk memaksimalkan pengalaman belajar dan
                    mengajarmu.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">auto_stories</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Modul Gratis</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Akses ratusan modul pembelajaran dari berbagai mata pelajaran secara gratis tanpa waktu terbatas.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-orange-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-orange-600 text-3xl">token</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Sistem Token</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Dapatkan token dari aktivitas belajar untuk membuka modul premium dan fitur eksklusif.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-blue-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-blue-600 text-3xl">videocam</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Kelas Online Live</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Ikuti kelas live interaktif dengan pengajar berpengalaman dan diskusi langsung.
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-purple-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-purple-600 text-3xl">forum</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Diskusi & Komunitas</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Bergabung dengan komunitas pelajar dan relawan yang saling membantu dan mendukung.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-teal-600 text-3xl">co_present</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Mentoring Personal</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Dapatkan bimbingan personal dari relawan untuk membantu perjalanan belajarmu.
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-pink-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-pink-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-pink-600 text-3xl">workspace_premium</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Sertifikat Digital</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Raih sertifikat resmi setelah menyelesaikan kursus sebagai bukti pencapaianmu.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Donation CTA Section --}}
    <section class="py-20 bg-gradient-to-br from-orange-50 via-white to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left: Donation Info --}}
                <div class="space-y-6">
                    <div class="inline-block px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold">
                        DUKUNG MISI KAMI
                    </div>

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
                            <span class="text-xl font-black text-amber-600">Rp 127.5Jt</span>
                        </div>
                        <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden mb-2">
                            <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-full rounded-full"
                                style="width: 64%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>64% tercapai • 127 donatur</span>
                            <span class="font-semibold">Target: Rp 200Jt</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <button
                            class="py-3 px-4 bg-white border-2 border-gray-200 rounded-xl hover:border-amber-500 hover:bg-amber-50 transition-colors text-center">
                            <div class="text-lg font-bold text-slate-900">Rp 25.000</div>
                            <div class="text-xs text-slate-600">1 pelajar belajar gratis selama 1 bulan</div>
                        </button>
                        <button
                            class="py-3 px-4 bg-amber-500 text-white border-2 border-amber-500 rounded-xl hover:bg-amber-600 transition-colors text-center">
                            <div class="text-lg font-bold">Rp 50.000</div>
                            <div class="text-xs opacity-90">Pilih nominal lain</div>
                        </button>
                        <button
                            class="py-3 px-4 bg-white border-2 border-gray-200 rounded-xl hover:border-amber-500 hover:bg-amber-50 transition-colors text-center">
                            <div class="text-lg font-bold text-slate-900">Rp 100.000</div>
                            <div class="text-xs text-slate-600">5 modul baru untuk pelajar pelosok</div>
                        </button>
                    </div>

                    <a href="{{ url('/donasi') }}"
                        class="inline-flex items-center justify-center w-full py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-600/30 transition-all">
                        <span class="material-symbols-rounded mr-2">favorite</span>
                        Donasi Sekarang
                    </a>

                    <p class="text-xs text-slate-500 text-center">
                        Donasi aman & terpercaya. Laporan penggunaan tersedia setiap bulan.
                    </p>
                </div>

                {{-- Right: Impact Stats --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl p-6 border-2 border-teal-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-teal-600 text-3xl">accessibility_new</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Siswa Terbantu Bulan Ini</div>
                                <div class="text-2xl font-black text-teal-900">1,234 siswa</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border-2 border-blue-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-blue-600 text-3xl">library_add</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Modul Gratis Ditambahkan</div>
                                <div class="text-2xl font-black text-blue-900">45 modul</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border-2 border-purple-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-purple-600 text-3xl">cloud_done</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 mb-1">Server & Infrastruktur Aman</div>
                                <div class="text-2xl font-black text-purple-900">99.9% Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="py-16 bg-slate-900 text-white">
        <div class="max-w-4xl mx-auto px-4 text-center space-y-6">
            <h2 class="text-3xl lg:text-4xl font-black">Mulai Perjalanan Kebaikanmu Hari Ini</h2>
            <p class="text-lg text-slate-300">Entah sebagai pelajar, pengajar, atau pendukung dana—peranmu sangat berarti.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ url('/register') }}"
                    class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition-colors">
                    Daftar Gratis Sekarang
                </a>
                <a href="{{ url('/donasi') }}"
                    class="px-8 py-4 bg-white hover:bg-gray-100 text-slate-900 font-bold rounded-xl transition-colors">
                    Lihat Cara Donasi
                </a>
            </div>
        </div>
    </section>
@endsection