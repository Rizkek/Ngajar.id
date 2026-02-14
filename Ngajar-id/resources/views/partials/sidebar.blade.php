<aside class="fixed top-0 left-0 w-64 h-screen bg-teal-600 text-white flex flex-col z-50">
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

            {{-- Menu Admin --}}
        @elseif(auth()->check() && (request()->is('admin*') || auth()->user()->isAdmin()))
            <a href="{{ url('/admin') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin*') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">admin_panel_settings</span>
                <span class="text-base">Admin Panel</span>
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
            <a href="{{ route('murid.materi') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/materi') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">menu_book</span>
                <span class="text-base">Materi</span>
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