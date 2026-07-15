<!-- Header / Navigasi Utama -->
<header class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-brand-900">
                        Ngajar.id
                    </span>
                </a>
            </div>

            <!-- Navigasi Desktop -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                    Beranda
                </a>
                <a href="{{ route('programs') }}" class="{{ request()->is('programs') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                    Kelas Belajar
                </a>
                <a href="{{ route('mentors') }}" class="{{ request()->is('mentors') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                    Cari Pengajar
                </a>
                <a href="{{ route('tentang-kami') }}" class="{{ request()->is('tentang-kami') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                    Tentang Kami
                </a>
                <a href="{{ url('/donasi') }}" class="{{ request()->is('donasi') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-medium' }} transition-colors">
                    Donasi
                </a>
            </nav>

            <!-- Tombol Otentikasi / Menu User -->
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <!-- Menu User yang Sudah Login -->
                    <div class="flex items-center gap-4">
                        <!-- Gamification Badge -->
                        <div class="hidden md:flex flex-col items-end mr-2">
                            <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full border border-brand-100">
                                {{ auth()->user()->xp }} XP
                            </span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold mt-0.5">
                                Level {{ auth()->user()->level }}
                            </span>
                        </div>

                        <div class="h-8 w-px bg-slate-200 hidden md:block"></div>

                        <span class="text-sm text-slate-600 hidden md:inline">Halo,
                            <strong>{{ auth()->user()->name }}</strong></span>

                        @if(auth()->user()->isMurid())
                            <a href="{{ route('student.dashboard') }}" class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                                Dashboard
                            </a>
                        @elseif(auth()->user()->isPengajar())
                            <a href="{{ route('teacher.dashboard') }}" class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                                Dashboard
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="/admin" class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                                Admin Panel
                            </a>
                        @endif

                        <a href="{{ route('profile') }}" class="p-2 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-full transition-all" title="Profil Saya">
                            <span class="material-symbols-rounded text-xl">account_circle</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-all" title="Logout">
                                <span class="material-symbols-rounded text-xl">logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Tombol untuk Tamu (Belum Login) -->
                    <a href="{{ url('/login') }}" class="text-slate-600 hover:text-brand-600 font-medium transition-colors">
                        Masuk
                    </a>
                    <a href="{{ url('/register') }}" class="px-5 py-2.5 bg-brand-600 text-white font-medium rounded-full shadow-lg shadow-brand-500/30 hover:bg-brand-700 hover:shadow-brand-500/40 transition-all transform hover:-translate-y-0.5">
                        Daftar Sekarang
                    </a>
                @endauth
            </div>

            <!-- Tombol Menu Mobile (Hamburger) -->
            <div class="md:hidden">
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen" aria-label="Buka menu navigasi" class="text-gray-500 hover:text-brand-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-rounded text-2xl" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden py-4 border-t border-gray-100" @click.outside="mobileMenuOpen = false">

            <nav class="space-y-1 mb-4">
                <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('/') ? 'bg-brand-50 text-brand-600 font-bold' : 'text-slate-700 hover:bg-gray-50 font-medium' }} transition-colors">
                    <span class="material-symbols-rounded text-xl">home</span>
                    Beranda
                </a>
                <a href="{{ route('programs') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('programs') ? 'bg-brand-50 text-brand-600 font-bold' : 'text-slate-700 hover:bg-gray-50 font-medium' }} transition-colors">
                    <span class="material-symbols-rounded text-xl">library_books</span>
                    Kelas Belajar
                </a>
                <a href="{{ route('mentors') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('mentors') ? 'bg-brand-50 text-brand-600 font-bold' : 'text-slate-700 hover:bg-gray-50 font-medium' }} transition-colors">
                    <span class="material-symbols-rounded text-xl">co_present</span>
                    Cari Pengajar
                </a>
                <a href="{{ route('tentang-kami') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('tentang-kami') ? 'bg-brand-50 text-brand-600 font-bold' : 'text-slate-700 hover:bg-gray-50 font-medium' }} transition-colors">
                    <span class="material-symbols-rounded text-xl">info</span>
                    Tentang Kami
                </a>
                <a href="{{ url('/donasi') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('donasi') ? 'bg-brand-50 text-brand-600 font-bold' : 'text-slate-700 hover:bg-gray-50 font-medium' }} transition-colors">
                    <span class="material-symbols-rounded text-xl">volunteer_activism</span>
                    Donasi
                </a>
            </nav>

            <div class="border-t border-gray-100 pt-4 space-y-2">
                @auth
                    <div class="px-4 py-2 flex items-center gap-3">
                        <span class="material-symbols-rounded text-brand-600">account_circle</span>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-brand-600 font-semibold">{{ auth()->user()->xp }} XP · Level {{ auth()->user()->level }}</p>
                        </div>
                    </div>
                    @if(auth()->user()->isMurid())
                        <a href="{{ route('student.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-gray-50 font-medium transition-colors">
                            <span class="material-symbols-rounded text-xl">dashboard</span>
                            Dashboard
                        </a>
                    @elseif(auth()->user()->isPengajar())
                        <a href="{{ route('teacher.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-gray-50 font-medium transition-colors">
                            <span class="material-symbols-rounded text-xl">dashboard</span>
                            Dashboard
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="/admin" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-gray-50 font-medium transition-colors">
                            <span class="material-symbols-rounded text-xl">admin_panel_settings</span>
                            Admin Panel
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 font-medium transition-colors">
                            <span class="material-symbols-rounded text-xl">logout</span>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" @click="mobileMenuOpen = false" class="flex items-center justify-center gap-2 w-full py-3 border-2 border-slate-200 rounded-xl text-slate-700 font-semibold hover:border-brand-500 hover:text-brand-600 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ url('/register') }}" @click="mobileMenuOpen = false" class="flex items-center justify-center gap-2 w-full py-3 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition-colors">
                        Daftar Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
