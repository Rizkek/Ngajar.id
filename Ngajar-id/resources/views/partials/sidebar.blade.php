<aside class="fixed top-0 left-0 w-64 h-screen bg-teal-600 text-white flex flex-col z-50">
    <div class="flex items-center justify-center px-6 py-6 border-b border-white/20">
        <h1 class="text-2xl font-bold font-robotoSlab text-center">Ngajar.Id</h1>
    </div>

    <nav class="mt-6 flex-1 space-y-2 px-4">
        {{-- Menu Pengajar --}}
        @if(request()->is('pengajar*') || (Auth::user() && Auth::user()->role == 'pengajar'))
            <a href="{{ url('/pengajar/dashboard') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('pengajar/dashboard') ? 'bg-teal-700' : '' }}">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-base">Dashboard</span>
            </a>
            <a href="#" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-book-open w-5 text-center"></i>
                <span class="text-base">Kelas Saya</span>
            </a>
            <a href="#" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-book w-5 text-center"></i>
                <span class="text-base">Materi</span>
            </a>
            <a href="{{ route('donasi') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-donate w-5 text-center"></i>
                <span class="text-base">Donasi</span>
            </a>

            {{-- Menu Murid --}}
        @elseif(request()->is('murid*') || (Auth::user() && Auth::user()->role == 'murid'))
            <a href="{{ url('/murid/dashboard') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/dashboard') ? 'bg-teal-700' : '' }}">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-base">Dashboard</span>
            </a>
            <a href="#" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-book-open w-5 text-center"></i>
                <span class="text-base">Kelas Saya</span>
            </a>
            <a href="#" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-book w-5 text-center"></i>
                <span class="text-base">Materi</span>
            </a>
            <a href="{{ route('donasi') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-donate w-5 text-center"></i>
                <span class="text-base">Donasi</span>
            </a>

            {{-- Default / Guest (Fallback) --}}
        @else
            <a href="{{ url('/') }}" class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-base">Home</span>
            </a>
            <a href="{{ route('login') }}"
                class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition">
                <i class="fas fa-sign-in-alt w-5 text-center"></i>
                <span class="text-base">Login</span>
            </a>
        @endif
    </nav>

    <div class="px-6 py-4 mt-auto border-t border-white/20">
        <form action="{{ route('login') }}" method="GET">
            <!-- Nanti ganti route logout POST -->
            <button type="submit" class="flex items-center space-x-4 text-white hover:text-white/80 w-full text-left">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="text-base">Logout</span>
            </button>
        </form>
    </div>
</aside>