@extends('layouts.app')

@section('title', 'Ngajar.ID - Platform Edukasi Gratis #1 Indonesia')

@section('content')
    {{-- Hero Section: Clean & Humanist --}}
    <section
        class="relative bg-gradient-to-br from-teal-50 via-white to-orange-50 pt-16 pb-24 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left: Text Content --}}
                <div class="space-y-6 lg:pr-8">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-100 text-teal-700 text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                            </path>
                        </svg>
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
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="{{ url('/register') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white text-teal-600 font-bold rounded-xl border-2 border-teal-600 hover:bg-teal-50 transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
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
                                class="w-8 h-8 rounded-full bg-orange-600 border-2 border-white flex items-center justify-center text-white text-xs font-bold">
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
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
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
                                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-black text-teal-900">200+</div>
                                <div class="text-sm text-teal-700 font-medium">Modul Gratis</div>
                            </div>

                            <div class="bg-orange-50 rounded-2xl p-6 text-center">
                                <div
                                    class="w-12 h-12 mx-auto mb-3 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="text-2xl font-black text-orange-900">500+</div>
                                <div class="text-sm text-orange-700 font-medium">Relawan Aktif</div>
                            </div>
                        </div>

                        {{-- Illustration Placeholder --}}
                        <div
                            class="bg-gradient-to-br from-teal-100 to-orange-100 rounded-2xl h-64 flex items-center justify-center">
                            <div class="text-center text-slate-600">
                                <svg class="w-24 h-24 mx-auto mb-4 text-teal-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
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
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-3xl font-black text-teal-900 mb-2">5,234+</div>
                    <div class="text-sm font-semibold text-teal-700">Pelajar Terbantu</div>
                    <div class="text-xs text-slate-600 mt-1">Mendapat akses pendidikan gratis</div>
                </div>

                {{-- Stat 2 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-orange-50 to-orange-100/50 border border-orange-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-3xl font-black text-orange-900 mb-2">1,089+</div>
                    <div class="text-sm font-semibold text-orange-700">Relawan Aktif</div>
                    <div class="text-xs text-slate-600 mt-1">Berbagi ilmu dengan tulus</div>
                </div>

                {{-- Stat 3 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <div class="text-3xl font-black text-blue-900 mb-2">450+</div>
                    <div class="text-sm font-semibold text-blue-700">Modul Pembelajaran</div>
                    <div class="text-xs text-slate-600 mt-1">Gratis & berkualitas tinggi</div>
                </div>

                {{-- Stat 4 --}}
                <div
                    class="text-center p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
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
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Saya Pelajar</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Akses ratusan modul pembelajaran dari berbagai mata pelajaran. Belajar dengan pengajar
                        berpengalaman, di mana saja.
                    </p>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Akses modul gratis tanpa batas</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Kelas live dengan pengajar berpengalaman</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Diskusi & komunitas supportif</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Sertifikat setelah menyelesaikan kursus</span>
                        </li>
                    </ul>

                    <a href="{{ url('/register') }}"
                        class="block w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-center transition-colors">
                        Mulai Belajar Sekarang
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>

                {{-- Saya Relawan --}}
                <div
                    class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all border-2 border-transparent hover:border-orange-600">
                    <div
                        class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-100 transition-colors">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Saya Relawan</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Berbagi pengetahuanmu untuk membantu pelajar Indonesia. Jadilah bagian dari gerakan edukasi gratis.
                    </p>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Platform mengajar yang mudah digunakan</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Jadwal fleksibel sesuai waktu luangmu</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Komunitas pengajar yang inspiratif</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-slate-700">Sertifikat & apresiasi kontribusi</span>
                        </li>
                    </ul>

                    <a href="{{ url('/register') }}"
                        class="block w-full py-4 bg-white hover:bg-orange-50 text-orange-600 font-bold rounded-xl text-center border-2 border-orange-600 transition-colors">
                        Daftar Jadi Relawan
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
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
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
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
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
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
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
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
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Diskusi & Komunitas</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Bergabung dengan komunitas pelajar dan relawan yang saling membantu dan mendukung.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div
                    class="bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-green-500 hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
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
                        <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
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

                    <div class="bg-white rounded-2xl p-6 border-2 border-orange-200">
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-sm text-slate-600 font-medium">Terkumpul</span>
                            <span class="text-xl font-black text-orange-600">Rp 127.5Jt</span>
                        </div>
                        <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden mb-2">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-full rounded-full"
                                style="width: 64%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>64% tercapai • 127 donatur</span>
                            <span class="font-semibold">Target: Rp 200Jt</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <button
                            class="py-3 px-4 bg-white border-2 border-gray-200 rounded-xl hover:border-orange-500 hover:bg-orange-50 transition-colors text-center">
                            <div class="text-lg font-bold text-slate-900">Rp 25.000</div>
                            <div class="text-xs text-slate-600">1 pelajar belajar gratis selama 1 bulan</div>
                        </button>
                        <button
                            class="py-3 px-4 bg-orange-500 text-white border-2 border-orange-500 rounded-xl hover:bg-orange-600 transition-colors text-center">
                            <div class="text-lg font-bold">Rp 50.000</div>
                            <div class="text-xs opacity-90">Pilih nominal lain</div>
                        </button>
                        <button
                            class="py-3 px-4 bg-white border-2 border-gray-200 rounded-xl hover:border-orange-500 hover:bg-orange-50 transition-colors text-center">
                            <div class="text-lg font-bold text-slate-900">Rp 100.000</div>
                            <div class="text-xs text-slate-600">5 modul baru untuk pelajar pelosok</div>
                        </button>
                    </div>

                    <a href="{{ url('/donasi') }}"
                        class="inline-flex items-center justify-center w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-600/30 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd"></path>
                        </svg>
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
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
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
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
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
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
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