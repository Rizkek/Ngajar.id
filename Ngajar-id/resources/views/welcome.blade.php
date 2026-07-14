@extends('layouts.app')

@section('title', 'Ngajar.ID - Platform Edukasi Berbasis Komunitas')

@section('content')
    {{-- 1. Hero Section --}}
    <section class="relative bg-gradient-to-br from-teal-50 via-white to-amber-50 pt-16 pb-24 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Kiri: Konten Teks Hero --}}
                <div class="space-y-6 lg:pr-8">
                    <h1 class="text-4xl lg:text-5xl xl:text-6xl font-black text-slate-900 leading-tight">
                        Belajar Bersama, <br>
                        <span class="text-teal-700">Berkembang Bersama.</span>
                    </h1>

                    <p class="text-lg text-slate-600 leading-relaxed max-w-xl">
                        Platform belajar berbasis komunitas yang menghubungkan relawan pengajar dengan murid untuk saling berbagi ilmu melalui kelas online yang mudah diakses.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <x-buttons.primary href="{{ url('/register?role=murid') }}" size="lg" color="teal" class="shadow-lg shadow-teal-700/30 transform hover:-translate-y-0.5">
                            Mulai Belajar Gratis
                            <x-icons.material name="arrow_forward" size="sm" class="ml-2 -mr-1" />
                        </x-buttons.primary>
                        <x-buttons.secondary href="{{ url('/register?role=pengajar') }}" size="lg" color="teal" class="border-2 transform hover:-translate-y-0.5 border-teal-700">
                            <x-icons.material name="volunteer_activism" size="sm" class="mr-2 -ml-1" />
                            Jadi Relawan Pengajar
                        </x-buttons.secondary>
                    </div>

                    {{-- Trust Indicator --}}
                    @if(isset($volunteers) && $volunteers->isNotEmpty())
                    <div class="flex items-center gap-6 pt-6 mt-4 border-t border-gray-200">
                        <div class="flex -space-x-2">
                            @foreach($volunteers->take(4) as $vol)
                                <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden bg-gray-200">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($vol->name) }}&background=random"
                                        alt="Pengajar {{ $vol->name }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <div class="font-bold text-slate-900"><span id="stat-pelajar">...</span>+ Pelajar Aktif</div>
                            <div class="text-sm text-slate-500">Bergabunglah dengan komunitas pembelajar</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Kanan: Ilustrasi Visual --}}
                <div class="relative lg:pl-8 hidden lg:block">
                    <div class="relative bg-white rounded-3xl shadow-2xl p-8 border border-gray-100 transform rotate-1 hover:rotate-0 transition-transform duration-500">
                        <div class="absolute -top-4 -right-4 w-24 h-24 bg-amber-100 rounded-full blur-2xl opacity-60"></div>
                        <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-teal-100 rounded-full blur-2xl opacity-60"></div>
                        
                        {{-- Mockup/Ilustrasi Sederhana --}}
                        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl h-80 flex flex-col items-center justify-center border border-slate-200 relative overflow-hidden">
                            <div class="w-full px-6 flex justify-between items-center mb-6 z-10">
                                <div class="flex gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                </div>
                                <div class="h-4 w-32 bg-white rounded-md opacity-50"></div>
                            </div>
                            <span class="material-symbols-rounded text-teal-700/20 text-[120px] mb-4 z-10" aria-hidden="true">local_library</span>
                            <div class="h-6 w-48 bg-teal-700/10 rounded-lg mb-3 z-10"></div>
                            <div class="h-4 w-32 bg-slate-300/30 rounded-lg z-10"></div>
                            
                            {{-- Decorative elements --}}
                            <div class="absolute top-1/4 left-10 w-12 h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce" style="animation-duration: 3s;">
                                <span class="material-symbols-rounded text-amber-500 text-xl" aria-hidden="true">emoji_events</span>
                            </div>
                            <div class="absolute bottom-1/4 right-10 w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce" style="animation-duration: 4s; animation-delay: 1s;">
                                <span class="material-symbols-rounded text-teal-600 text-2xl" aria-hidden="true">groups</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Impact Stats --}}
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-100" data-aos="fade-up">
                <div class="px-4">
                    <div class="text-3xl lg:text-4xl font-black text-teal-800 mb-2">
                        <span id="impact-pelajar" aria-label="Jumlah pelajar">...</span>+
                    </div>
                    <div class="text-sm font-semibold text-slate-600">Pelajar Aktif</div>
                </div>
                <div class="px-4">
                    <div class="text-3xl lg:text-4xl font-black text-amber-600 mb-2">
                        <span id="impact-relawan" aria-label="Jumlah pengajar">...</span>+
                    </div>
                    <div class="text-sm font-semibold text-slate-600">Pengajar Relawan</div>
                </div>
                <div class="px-4">
                    <div class="text-3xl lg:text-4xl font-black text-teal-800 mb-2">
                        <span id="impact-modul" aria-label="Jumlah kelas">...</span>+
                    </div>
                    <div class="text-sm font-semibold text-slate-600">Kelas Tersedia</div>
                </div>
                <div class="px-4">
                    <div class="text-3xl lg:text-4xl font-black text-amber-600 mb-2 flex items-center justify-center gap-1">
                        <span id="impact-rating" aria-label="Rata-rata rating">...</span>
                        <span class="material-symbols-rounded text-2xl" aria-hidden="true">star</span>
                    </div>
                    <div class="text-sm font-semibold text-slate-600">Rata-rata Rating</div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Why Ngajar.id (Value Propositions) --}}
    <section class="py-20 bg-slate-50" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Kenapa Memilih <span class="text-teal-700">Ngajar.id?</span></h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Kami berdedikasi menyediakan akses pendidikan yang berkualitas dan interaktif untuk semua kalangan tanpa batasan.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Value 1 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-teal-50 text-teal-700 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-2xl" aria-hidden="true">schedule</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Belajar Fleksibel</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Akses materi kelas kapan saja dan di mana saja sesuai dengan ritme belajarmu sendiri.
                    </p>
                </div>

                {{-- Value 2 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-2xl" aria-hidden="true">forum</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Komunitas Aktif</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Diskusikan materi secara langsung dengan pengajar dan sesama murid di forum kelas.
                    </p>
                </div>

                {{-- Value 3 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-teal-50 text-teal-700 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-2xl" aria-hidden="true">workspace_premium</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Pengajar Relawan</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Belajar langsung dari para praktisi dan relawan yang mendedikasikan ilmu mereka.
                    </p>
                </div>

                {{-- Value 4 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-symbols-rounded text-2xl" aria-hidden="true">verified</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Sertifikat Belajar</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Dapatkan bukti pencapaianmu setelah menyelesaikan kelas dan kuis dengan baik.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Explore Courses --}}
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6" data-aos="fade-up">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Mulai <span class="text-teal-700">Eksplorasi</span></h2>
                    <p class="text-lg text-slate-600">Temukan kelas yang sesuai dengan minat dan tujuanmu.</p>
                </div>
                <a href="{{ route('programs') }}" class="inline-flex items-center text-teal-700 font-bold hover:text-teal-800" aria-label="Lihat Semua Kelas">
                    Lihat Semua Kelas
                    <span class="material-symbols-rounded ml-1" aria-hidden="true">arrow_forward</span>
                </a>
            </div>

            {{-- Categories Filter Chips --}}
            @if(isset($categories) && $categories->isNotEmpty())
            <div class="flex overflow-x-auto pb-6 mb-8 gap-3 scrollbar-hide" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('programs') }}" class="whitespace-nowrap px-6 py-2.5 bg-teal-700 text-white rounded-full font-medium shadow-md">
                    Semua Kategori
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('programs', ['kategori' => $cat]) }}" class="whitespace-nowrap px-6 py-2.5 bg-slate-50 text-slate-700 hover:bg-teal-50 hover:text-teal-700 rounded-full font-medium border border-slate-200 transition-colors">
                    {{ $cat }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- Course Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="200">
                @if(isset($featuredCourses) && $featuredCourses->isNotEmpty())
                    @foreach($featuredCourses as $kelas)
                    <a href="{{ route('programs') }}" class="group flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all overflow-hidden h-full">
                        <div class="p-6 flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full">
                                    {{ $kelas->kategori ?? 'Umum' }}
                                </span>
                                <div class="flex items-center text-amber-500 text-sm font-bold">
                                    <span class="material-symbols-rounded text-sm mr-1" aria-hidden="true">star</span>
                                    {{ number_format($kelas->ulasans_avg_rating ?? 5.0, 1) }}
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-teal-700 transition-colors line-clamp-2">
                                {{ $kelas->judul }}
                            </h3>
                            <p class="text-slate-600 text-sm line-clamp-2 mb-4">
                                {{ $kelas->deskripsi }}
                            </p>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-gray-50 mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($kelas->pengajar->name ?? 'Pengajar') }}&background=0f766e&color=fff" alt="{{ $kelas->pengajar->name ?? 'Pengajar' }}" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm font-medium text-slate-700 truncate max-w-[120px]">{{ $kelas->pengajar->name ?? 'Pengajar' }}</span>
                            </div>
                            <div class="flex items-center text-slate-500 text-sm">
                                <span class="material-symbols-rounded text-sm mr-1" aria-hidden="true">group</span>
                                {{ $kelas->peserta_count ?? 0 }}
                            </div>
                        </div>
                    </a>
                    @endforeach
                @else
                    {{-- Placeholder jika kosong --}}
                    @for($i=1; $i<=3; $i++)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 h-64 flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-rounded text-slate-300 text-4xl mb-3" aria-hidden="true">auto_stories</span>
                        <div class="h-4 w-3/4 bg-slate-200 rounded animate-pulse mb-2"></div>
                        <div class="h-3 w-1/2 bg-slate-100 rounded animate-pulse"></div>
                    </div>
                    @endfor
                @endif
            </div>
        </div>
    </section>

    {{-- 5. How It Works --}}
    <section class="py-24 bg-slate-900 text-white overflow-hidden relative">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#0f766e 1px, transparent 1px); background-size: 32px 32px;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl lg:text-4xl font-black mb-4">Cara Kerja <span class="text-amber-400">Ngajar.id</span></h2>
                <p class="text-lg text-slate-400">Proses belajar yang dirancang sesederhana mungkin untukmu.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8 relative">
                {{-- Connecting line (desktop only) --}}
                <div class="hidden md:block absolute top-12 left-[12%] right-[12%] h-0.5 bg-slate-700"></div>

                {{-- Step 1 --}}
                <div class="relative text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-24 h-24 mx-auto bg-slate-800 border-4 border-slate-900 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="material-symbols-rounded text-4xl text-teal-400" aria-hidden="true">how_to_reg</span>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center font-bold text-slate-900 border-2 border-slate-900">1</div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Daftar Akun</h3>
                    <p class="text-slate-400 text-sm">Buat akun gratis hanya dalam 1 menit tanpa kartu kredit.</p>
                </div>

                {{-- Step 2 --}}
                <div class="relative text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-24 h-24 mx-auto bg-slate-800 border-4 border-slate-900 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="material-symbols-rounded text-4xl text-teal-400" aria-hidden="true">search</span>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center font-bold text-slate-900 border-2 border-slate-900">2</div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Pilih Kelas</h3>
                    <p class="text-slate-400 text-sm">Eksplorasi dan ikuti kelas yang sesuai dengan minat belajarmu.</p>
                </div>

                {{-- Step 3 --}}
                <div class="relative text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-24 h-24 mx-auto bg-slate-800 border-4 border-slate-900 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="material-symbols-rounded text-4xl text-teal-400" aria-hidden="true">play_lesson</span>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center font-bold text-slate-900 border-2 border-slate-900">3</div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Mulai Belajar</h3>
                    <p class="text-slate-400 text-sm">Pelajari materi, tonton video, dan ikuti diskusi dengan aktif.</p>
                </div>

                {{-- Step 4 --}}
                <div class="relative text-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-24 h-24 mx-auto bg-slate-800 border-4 border-slate-900 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="material-symbols-rounded text-4xl text-teal-400" aria-hidden="true">workspace_premium</span>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center font-bold text-slate-900 border-2 border-slate-900">4</div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Dapat Sertifikat</h3>
                    <p class="text-slate-400 text-sm">Selesaikan semua modul dan raih sertifikat digitalmu.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Meet the Mentors --}}
    <section class="py-24 bg-slate-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Pengajar <span class="text-teal-700">Inspiratif</span></h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Belajar dari para praktisi dan relawan yang mendedikasikan waktu mereka untuk pendidikan.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                @if(isset($volunteers) && $volunteers->isNotEmpty())
                    @foreach($volunteers->take(3) as $mentor)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all text-center group" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="w-24 h-24 mx-auto rounded-full overflow-hidden mb-4 border-4 border-teal-50 group-hover:border-teal-100 transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&size=200&background=0f766e&color=fff" alt="Foto {{ $mentor->name }}" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $mentor->name }}</h3>
                        <p class="text-teal-700 text-sm font-medium mb-4">Relawan Pengajar</p>
                        
                        <div class="flex items-center justify-center gap-6 text-sm">
                            <div class="text-slate-600">
                                <span class="font-bold text-slate-900">{{ $mentor->kelas_ajar_count ?? 0 }}</span> Kelas
                            </div>
                            <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                            <div class="text-amber-500 font-bold flex items-center gap-1">
                                <span class="material-symbols-rounded text-base" aria-hidden="true">star</span>
                                {{ number_format($mentor->kelas_ajar_avg_rating ?? 5.0, 1) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    {{-- Placeholder --}}
                    @for($i=1; $i<=3; $i++)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm text-center">
                        <div class="w-24 h-24 mx-auto rounded-full bg-slate-200 animate-pulse mb-4"></div>
                        <div class="h-5 w-2/3 mx-auto bg-slate-200 rounded animate-pulse mb-2"></div>
                        <div class="h-4 w-1/2 mx-auto bg-teal-100 rounded animate-pulse mb-4"></div>
                    </div>
                    @endfor
                @endif
            </div>

            <div class="text-center" data-aos="fade-up">
                <a href="{{ route('mentors') }}" class="inline-flex items-center justify-center px-8 py-3 bg-white text-teal-700 font-bold rounded-xl border-2 border-slate-200 hover:border-teal-700 hover:bg-teal-50 transition-colors" aria-label="Lihat Semua Pengajar">
                    Lihat Semua Pengajar
                </a>
            </div>
        </div>
    </section>

    {{-- 7. Testimonials (Dinamis dari DB) --}}
    @if(isset($testimonials) && $testimonials->isNotEmpty())
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Apa Kata <span class="text-teal-700">Mereka?</span></h2>
                <p class="text-lg text-slate-600">Pengalaman nyata dari pelajar yang telah bergabung di Ngajar.id.</p>
            </div>

            <div class="swiper testimonialSwiper pb-12" data-aos="fade-up" data-aos-delay="200">
                <div class="swiper-wrapper">
                    @foreach($testimonials as $testi)
                    <div class="swiper-slide p-4 h-auto">
                        <div class="bg-slate-50 rounded-3xl p-8 border border-gray-100 h-full flex flex-col justify-between hover:border-teal-200 transition-colors">
                            <div class="mb-6">
                                <div class="flex text-amber-400 mb-4" aria-label="Rating {{ $testi->rating }} bintang">
                                    @for($i=1; $i<=5; $i++)
                                        <span class="material-symbols-rounded {{ $i <= $testi->rating ? '' : 'text-slate-300' }}" aria-hidden="true">star</span>
                                    @endfor
                                </div>
                                <p class="text-slate-700 italic leading-relaxed">"{{ $testi->ulasan }}"</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($testi->user->name ?? 'User') }}&background=0f766e&color=fff"
                                    alt="Foto {{ $testi->user->name ?? 'User' }}" class="w-12 h-12 rounded-full border-2 border-white">
                                <div>
                                    <div class="font-bold text-slate-900">{{ $testi->user->name ?? 'Pengguna Anonim' }}</div>
                                    <div class="text-xs text-teal-700 font-medium truncate max-w-[200px]">{{ $testi->kelas->judul ?? 'Kelas' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    @endif

    {{-- 8. FAQ --}}
    <section class="py-24 bg-slate-50 border-t border-gray-100" data-aos="fade-up">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">Pertanyaan yang Sering Diajukan</h2>
            </div>

            <div class="space-y-4" role="region" aria-label="FAQ List">
                {{-- FAQ 1 --}}
                <div class="faq-item bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group focus-visible:outline-teal-700"
                        onclick="toggleFaq(this)" aria-expanded="false" aria-controls="faq-content-1">
                        <span class="font-bold text-slate-800 group-hover:text-teal-700 transition-colors">Apakah semua kelas di Ngajar.id benar-benar gratis?</span>
                        <span class="material-symbols-rounded text-slate-400 group-hover:text-teal-700 transition-transform duration-300" aria-hidden="true">expand_more</span>
                    </button>
                    <div id="faq-content-1" class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-100">
                            Ya, sebagian besar kelas dasar hingga menengah dapat diakses 100% gratis. Kami didukung oleh relawan pengajar dan donatur yang peduli pada pendidikan Indonesia. Ada juga modul premium khusus untuk pendalaman materi tertentu.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="faq-item bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group focus-visible:outline-teal-700"
                        onclick="toggleFaq(this)" aria-expanded="false" aria-controls="faq-content-2">
                        <span class="font-bold text-slate-800 group-hover:text-teal-700 transition-colors">Apakah saya akan mendapatkan sertifikat?</span>
                        <span class="material-symbols-rounded text-slate-400 group-hover:text-teal-700 transition-transform duration-300" aria-hidden="true">expand_more</span>
                    </button>
                    <div id="faq-content-2" class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-100">
                            Tentu! Setelah Anda menyelesaikan seluruh materi pembelajaran dan lulus kuis pada suatu kelas, Anda berhak mengunduh sertifikat digital sebagai bukti penyelesaian kelas tersebut.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="faq-item bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group focus-visible:outline-teal-700"
                        onclick="toggleFaq(this)" aria-expanded="false" aria-controls="faq-content-3">
                        <span class="font-bold text-slate-800 group-hover:text-teal-700 transition-colors">Apakah belajarnya harus sesuai jadwal tertentu?</span>
                        <span class="material-symbols-rounded text-slate-400 group-hover:text-teal-700 transition-transform duration-300" aria-hidden="true">expand_more</span>
                    </button>
                    <div id="faq-content-3" class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-100">
                            Pembelajaran di Ngajar.id bersifat mandiri (self-paced). Artinya, Anda bisa mengakses materi video maupun teks kapan saja dan di mana saja. Namun, terkadang pengajar juga bisa mengadakan sesi live secara opsional.
                        </div>
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="faq-item bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-300">
                    <button class="w-full px-6 py-5 flex items-center justify-between text-left group focus-visible:outline-teal-700"
                        onclick="toggleFaq(this)" aria-expanded="false" aria-controls="faq-content-4">
                        <span class="font-bold text-slate-800 group-hover:text-teal-700 transition-colors">Bagaimana cara menjadi Relawan Pengajar?</span>
                        <span class="material-symbols-rounded text-slate-400 group-hover:text-teal-700 transition-transform duration-300" aria-hidden="true">expand_more</span>
                    </button>
                    <div id="faq-content-4" class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-slate-50/50">
                        <div class="px-6 py-5 text-slate-600 leading-relaxed border-t border-gray-100">
                            Siapa pun yang memiliki keahlian dan keinginan berbagi dapat menjadi relawan. Anda cukup mendaftar dan memilih role "Pengajar". Tim kami akan melakukan verifikasi sederhana sebelum Anda dapat membuat kelas pertama.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 9. Final CTA --}}
    <section class="py-24 bg-gradient-to-br from-teal-800 to-slate-900 relative overflow-hidden">
        {{-- Dekorasi background --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-teal-600 rounded-full blur-3xl opacity-20 -translate-y-1/2 translate-x-1/3" aria-hidden="true"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-amber-500 rounded-full blur-3xl opacity-20 translate-y-1/2 -translate-x-1/3" aria-hidden="true"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center" data-aos="zoom-in">
            <h2 class="text-3xl lg:text-5xl font-black text-white mb-6 leading-tight">
                Siap Memulai Perjalanan<br>Belajarmu Hari Ini?
            </h2>
            <p class="text-lg text-teal-100 mb-10 max-w-2xl mx-auto">
                Bergabung dengan ribuan pengguna lainnya yang telah mengembangkan diri mereka bersama Ngajar.id.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                <a href="{{ url('/register?role=murid') }}"
                    class="px-8 py-4 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg hover:shadow-amber-500/40 transform hover:-translate-y-1 transition-all" aria-label="Daftar Sekarang Sebagai Murid">
                    Daftar Sekarang — Gratis
                </a>
                <a href="{{ route('programs') }}"
                    class="px-8 py-4 bg-white/10 text-white hover:bg-white/20 font-bold rounded-xl backdrop-blur-sm border border-white/20 transition-colors" aria-label="Jelajahi Kelas">
                    Jelajahi Kelas Dulu
                </a>
            </div>
            
            <p class="text-sm text-teal-200/60">
                Ingin mendukung misi kami? <a href="{{ url('/donasi') }}" class="text-amber-400 hover:underline hover:text-amber-300 font-medium">Berdonasi di sini →</a>
            </p>
        </div>
    </section>

    @push('scripts')
    <script>
        // Initialize Swiper (hanya jalan jika ada elemen testimonialSwiper)
        document.addEventListener('DOMContentLoaded', () => {
            if(document.querySelector('.testimonialSwiper')) {
                new Swiper(".testimonialSwiper", {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                        dynamicBullets: true,
                    },
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 30 },
                        1024: { slidesPerView: 3, spaceBetween: 40 },
                    },
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                });
            }
        });

        // FAQ Accordion dengan ARIA updates
        function toggleFaq(btn) {
            const item = btn.parentElement;
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('.material-symbols-rounded');
            
            const isOpening = !item.classList.contains('faq-active');

            // Close all
            document.querySelectorAll('.faq-item').forEach(otherItem => {
                const otherBtn = otherItem.querySelector('button');
                otherBtn.setAttribute('aria-expanded', 'false');
                otherItem.classList.remove('faq-active', 'border-teal-500', 'shadow-md');
                otherItem.querySelector('.faq-content').style.maxHeight = '0';
                
                const otherIcon = otherItem.querySelector('.material-symbols-rounded');
                otherIcon.style.transform = 'rotate(0deg)';
            });

            // Open target
            if (isOpening) {
                btn.setAttribute('aria-expanded', 'true');
                item.classList.add('faq-active', 'border-teal-500', 'shadow-md');
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.style.transform = 'rotate(180deg)';
            }
        }

        // Stats Async Loader
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route("landing.stats") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(function (res) {
                if(res.success && res.data) {
                    const stats = res.data;
                    setStatWithAnimation('impact-pelajar', stats.total_students);
                    setStatWithAnimation('impact-relawan', stats.total_teachers);
                    setStatWithAnimation('impact-modul', stats.total_courses);
                    
                    // Update rating (no animation for float)
                    const ratingEl = document.getElementById('impact-rating');
                    if(ratingEl) ratingEl.textContent = stats.avg_course_rating;
                    
                    // Hero stats
                    const heroPelajar = document.getElementById('stat-pelajar');
                    if(heroPelajar) heroPelajar.textContent = (stats.total_students).toLocaleString('id-ID');
                }
            })
            .catch(function () {
                ['impact-pelajar', 'impact-relawan', 'impact-modul', 'stat-pelajar'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el && el.textContent === '...') el.textContent = '0';
                });
                const rEl = document.getElementById('impact-rating');
                if(rEl && rEl.textContent === '...') rEl.textContent = '5.0';
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
