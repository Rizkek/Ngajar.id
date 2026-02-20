<aside class="w-full h-full bg-teal-600 text-white flex flex-col overflow-y-auto">
    <div class="flex items-center justify-center px-6 py-6 border-b border-white/20">
        <h1 class="text-2xl font-bold font-robotoSlab text-center">Ngajar.Id</h1>
    </div>

    <nav class="mt-6 flex-1 space-y-2 px-4">
        {{-- Menu Pengajar --}}
        @if(auth()->check() && (request()->is('pengajar*') || auth()->user()->isPengajar()))
            <a href="{{ url('/pengajar/dashboard') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('pengajar/dashboard') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">dashboard</span>
                <span class="text-base">Dashboard</span>
            </a>
            <a href="{{ route('pengajar.kelas') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('pengajar/kelas') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">school</span>
                <span class="text-base">Kelas Saya</span>
            </a>
            <a href="{{ route('pengajar.materi') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('pengajar/materi') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">sell</span>
                <span class="text-base">Produk Digital</span>
            </a>

            {{-- Menu Admin - Expanded --}}
        @elseif(auth()->check() && (request()->is('admin*') || auth()->user()->isAdmin()))
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">dashboard</span>
                <span class="text-base">Dashboard</span>
            </a>

            <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide">Manajemen User</div>
            <a href="{{ route('admin.pengajar.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/pengajar*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">person_book</span>
                <span class="text-base">Pengajar</span>
            </a>
            <a href="{{ route('admin.murid.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/murid*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">school</span>
                <span class="text-base">Murid</span>
            </a>

            <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide mt-4">Konten & Kurikulum</div>
            <a href="{{ route('admin.kelas.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/kelas*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">class</span>
                <span class="text-base">Kelas</span>
            </a>
            <a href="{{ route('admin.learning-paths.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/learning-paths*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">route</span>
                <span class="text-base">Learning Paths</span>
            </a>
            <a href="{{ route('admin.kategori.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/kategori*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">category</span>
                <span class="text-base">Kategori</span>
            </a>
            <a href="{{ route('admin.materi.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/materi*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">menu_book</span>
                <span class="text-base">Materi</span>
            </a>

            <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide mt-4">Keuangan</div>
            <a href="{{ route('admin.donasi.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/donasi*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">volunteer_activism</span>
                <span class="text-base">Donasi</span>
            </a>
            <a href="{{ route('admin.laporan.revenue') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/laporan/revenue*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">monetization_on</span>
                <span class="text-base">Revenue</span>
            </a>

            <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide mt-4">Sistem</div>
            <a href="{{ route('admin.notifications.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/notifications*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">notifications</span>
                <span class="text-base">Notifikasi</span>
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/settings*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">settings</span>
                <span class="text-base">Pengaturan</span>
            </a>

            {{-- Menu Murid --}}
        @elseif(auth()->check() && (request()->is('murid*') || auth()->user()->isMurid()))
            <a href="{{ url('/murid/dashboard') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/dashboard') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">dashboard</span>
                <span class="text-base">Dashboard</span>
            </a>
            <a href="{{ route('murid.kelas') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/kelas') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">school</span>
                <span class="text-base">Kelas Saya</span>
            </a>
            <a href="{{ route('murid.learning-paths.index') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/learning-paths*') || request()->is('learning-paths*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">route</span>
                <span class="text-base">Learning Path</span>
            </a>
            <a href="{{ route('murid.sertifikat') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/sertifikat') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">workspace_premium</span>
                <span class="text-base">Sertifikat</span>
            </a>


            {{-- Default / Guest (Fallback) --}}
        @else
            <a href="{{ url('/') }}" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">home</span>
                <span class="text-base">Home</span>
            </a>
            <a href="{{ route('login') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">login</span>
                <span class="text-base">Login</span>
            </a>
        @endif
    </nav>

    <div class="px-4 py-4 mt-auto border-t border-white/20 space-y-2">
        @if(auth()->check())
            <a href="{{ route('profile') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('profile') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">person</span>
                <span class="text-base">Profil Saya</span>
            </a>
            <a href="{{ route('donasi') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">volunteer_activism</span>
                <span class="text-base">Donasi</span>
            </a>

            <a href="{{ route('logout.page') }}" onclick="return confirm('Yakin ingin keluar?')"
                class="flex items-center space-x-4 px-4 py-3 text-white hover:bg-red-600 rounded transition">
                <span class="material-symbols-rounded w-6 text-center">logout</span>
                <span class="text-base">Logout</span>
            </a>
        @else
            <a href="{{ route('login') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">login</span>
                <span class="text-base">Login</span>
            </a>
        @endif
    </div>
</aside>