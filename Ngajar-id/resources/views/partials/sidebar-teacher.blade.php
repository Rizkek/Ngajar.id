<nav class="mt-6 flex-1 space-y-2 px-4 pb-6">
    <a href="{{ url('/teacher/dashboard') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('teacher/dashboard') || request()->is('pengajar/dashboard') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">dashboard</span>
        <span class="text-base">Dashboard</span>
    </a>
    <a href="{{ route('teacher.kelas') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('teacher/courses*') || request()->is('pengajar/kelas*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">school</span>
        <span class="text-base">Kelas Saya</span>
    </a>
    <a href="{{ route('teacher.materi') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('teacher/lessons*') || request()->is('pengajar/materi*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">sell</span>
        <span class="text-base">Produk Digital</span>
    </a>
</nav>
