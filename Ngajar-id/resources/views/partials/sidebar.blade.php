<aside class="w-full h-full bg-teal-600 text-white flex flex-col overflow-y-auto">
    <div class="flex items-center justify-center px-6 py-6 border-b border-white/20">
        <h1 class="text-2xl font-bold font-robotoSlab text-center">Ngajar.Id</h1>
    </div>

    @if(auth()->check() && auth()->user()->isPengajar())
        @include('partials.sidebar-teacher')
    @elseif(auth()->check() && auth()->user()->isAdmin())
        @include('partials.sidebar-admin')
    @elseif(auth()->check() && auth()->user()->isMurid())
        @include('partials.sidebar-student')
    @else
        <nav class="mt-6 flex-1 space-y-2 px-4 pb-6">
            <a href="{{ url('/') }}" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">home</span>
                <span class="text-base">Home</span>
            </a>
            <a href="{{ route('login') }}" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">login</span>
                <span class="text-base">Login</span>
            </a>
        </nav>
    @endif

    <div class="px-4 py-4 mt-auto border-t border-white/20 space-y-2">
        @if(auth()->check())
            <a href="{{ route('profile') }}" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('profile') ? 'bg-teal-700' : '' }}">
                <span class="material-symbols-rounded w-6 text-center">person</span>
                <span class="text-base">Profil Saya</span>
            </a>
            <a href="{{ route('donasi') }}" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <span class="material-symbols-rounded w-6 text-center">volunteer_activism</span>
                <span class="text-base">Donasi</span>
            </a>

            <a href="{{ route('logout.page') }}" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center space-x-4 px-4 py-3 text-white hover:bg-red-600 rounded transition">
                <span class="material-symbols-rounded w-6 text-center">logout</span>
                <span class="text-base">Logout</span>
            </a>
        @endif
    </div>
</aside>
